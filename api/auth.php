<?php

require 'include/config.php';
require 'api/facebook/facebook.php';
  
// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
  'appId'  => APP_ID,
  'secret' => APP_SECRET,
));

// Get User ID
$user = null;

try {
  $user = $facebook->getUser();
}
catch (FacebookApiException $e) {
  error_log($e);
  $user = null;
  setcookie('fbm_'.$facebook->getAppId(), '', time()-100, '/', '.' . HOST);
}

function get_url() {
  $pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
  if ($_SERVER["SERVER_PORT"] != "80")
  {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
  } 
  else 
  {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
  }
  return $pageURL;
}

$next = dirname(get_url()) . '/logout.php';

// Login or logout url will be needed depending on current user state.
if ($user) {
  $logoutUrl = $facebook->getLogoutUrl(array('next' => $next));
  $_SESSION['user'] = $user;
}
else {
  unset($_SESSION['user']);
  $loginUrl = $facebook->getLoginUrl();
}

?>
