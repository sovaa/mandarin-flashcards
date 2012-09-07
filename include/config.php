<?php

// database config
DEFINE('DBHOST', 'dbhost');
DEFINE('DBNAME', 'dbname');
DEFINE('DBUSER', 'user');
DEFINE('DBPASS', 'pass');

DEFINE('APP_ID', 'YOUR_APP_ID'); // facebook app id for login
DEFINE('APP_SECRET', 'YOUR_SECRET'); // facebook app secret for login
DEFINE('HOST', 'example.com'); // for cookie

$marks = array(
    '0' => array('a' => 'a', 'o' => 'o', 'e' => 'e', 'i' => 'i', 'u' => 'u', 'v' => '\u00fc'),
    '1' => array('a' => '\u0101', 'o' => '\u014d', 'e' => '\u0113', 'i' => '\u012b', 'u' => '\u016b', 'v' => '\u01d6'),
    '2' => array('a' => '\u00e1', 'o' => '\u00f3', 'e' => '\u00e9', 'i' => '\u00ed', 'u' => '\u00fa', 'v' => '\u01d8'),
    '3' => array('a' => '\u01ce', 'o' => '\u01d2', 'e' => '\u011b', 'i' => '\u01d0', 'u' => '\u01d4', 'v' => '\u01da'),
    '4' => array('a' => '\u00e0', 'o' => '\u00f2', 'e' => '\u00e8', 'i' => '\u00ec', 'u' => '\u00f9', 'v' => '\u01dc'),
    '5' => array('a' => 'a', 'o' => 'o', 'e' => 'e', 'i' => 'i', 'u' => 'u', 'v' => '\u00fc'),
);

$tones = array('0', '1', '2', '3', '4', '5');
$vowels = array('a', 'o', 'e', 'i', 'u');
$debug = false;

$goal_ratio = '80';

?>
