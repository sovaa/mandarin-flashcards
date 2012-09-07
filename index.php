<?php

header('Vary: Accept-Encoding');
header('Content-Language: en');
header('Content-Type: text/html; charset=utf-8');

include('include/config.php');
include('include/functions.php');
include('api/auth.php');

//$user = 'scorch';

if ($user) {
  $conn = get_connection();

  create_user_if_new($conn, $user);

  $current = get_current($conn, $user);
  $_char_id = -1;

  if (isset($_POST) && isset($_POST['answer'])) {
    $_answer = $_POST['answer'];
    $_raw_pinyin = $_POST['raw-pinyin'];
    $_pinyin = $_POST['pinyin'];
    $_word = $_POST['word'];
    $_meaning = $_POST['meaning'];
    $_char_id = $_POST['char-id'];

    $__raw_pinyin = strtolower(strip_pinyin($_raw_pinyin));
    $_answer = strtolower(strip_pinyin($_answer));

    $_correct = false;
    if ($__raw_pinyin == $_answer) {
      $_correct = true;
    }

    update_progress($conn, $user, $_correct, $current, $_char_id);
  }

  $result = get_word($conn, $current, $user, $_char_id);
  $word = $result['word'];
  $raw_pinyin = $result['pinyin'];
  $char_id = $result['id'];
  $pinyin = to_pinyin($raw_pinyin);
  $meaning = str_replace('"', '', $result['meaning']);

  $stats = get_word_stats($conn, $user, $char_id);
  $ostats = get_overall_stats($conn, $user);
}

?>
<html>
    <head>
        <title>Mandarin Flash Cards</title>
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <script type="text/javascript" src="js/main.js"></script> 

        <script type="text/javascript">
          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', 'UA-34467705-1']);
          _gaq.push(['_trackPageview']);

          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();
        </script>
    </head>
    <body onload="document.getElementById('answer').focus()">

      <?php
        if ($user) {
          include('template/loggedin.php');
        }
        else {
          include('template/loggedout.php');
        }
      ?>

    </body>
</html>
