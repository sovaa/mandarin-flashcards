<?php

require '../include/config.php';

function get_connection() {
    $dbhost = DBHOST;
    $dbname = DBNAME;
    $dbuser = DBUSER;
    $dbpass = DBPASS;

    $conn = null;

    try {  
        $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);  
    }  
    catch (PDOException $e) {  
        echo $e->getMessage();  
    }

    return $conn;
}

function printr($text) {
    echo("<pre>");
    print_r($text);
    echo("</pre>");
}

function get_word($conn, $level, $word) {
    $query = 'select * from word where word = :word and level = :level';

    $q = $conn->prepare($query);
    $q->execute(array(
        ':level' => $level,
        ':word' => $word
    ));

    return $q->fetch();
}

function create_word($conn, $level, $word, $pinyin, $meaning) {
    $query = "INSERT INTO word (level, word, pinyin, meaning) VALUES " .
        "(:level, :word, :pinyin, :meaning)";

    $q = $conn->prepare($query);
    $q->execute(array(
            ':level' => $level,
            ':word' => $word,
            ':pinyin' => $pinyin,
            ':meaning' => $meaning
    ));
}

function get_file_handle($file) {
    $handle = fopen($file, 'r');

    if (!$handle) {
        die("could not open file");
    }

    return $handle;
}

$file = "hsk-level-all.csv";
$handle = get_file_handle($file);
$conn = get_connection();

while (($buffer = fgets($handle, 4096)) !== false) {
    $attrs = explode(',', $buffer, 4);

    $level = $attrs[0];
    $word = $attrs[1];
    $pinyin = $attrs[2];
    $meaning = $attrs[3];

    $result = get_word($conn, $level, $word);

    if ($result != null && count($result) > 0) {
        continue;
    }

    create_word($conn, $level, $word, $pinyin, $meaning);
}

if (!feof($handle)) {
    echo "Error: unexpected fgets() fail\n";
}

fclose($handle);

?>
