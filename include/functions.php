<?php

function get_current($conn, $user) {
    $query = "SELECT current FROM user WHERE nick = :nick";
    $q = $conn->prepare($query);

    $q->execute(array(':nick' => $user));
    $results = $q->fetch();
    
    return $results['current'];
}

function create_user_if_new($conn, $user) {
    $query = 'select nick from user where nick = :nick';
    $q = $conn->prepare($query);

    $q->execute(array(':nick' => $user));
    $results = $q->fetch();

    if ($results == null || count($results) == 0) {
        $query = 'insert into user (nick, correct, wrong, current) values(:nick, 0, 0, 6)';

        try {  
          $q = $conn->prepare($query);
          $q->execute(array(':nick' => $user));
        }  
        catch(PDOException $e) {  
            die($e->getMessage());
        }
    }
}

function strip_pinyin($words) {
    $words = str_replace(' ', '', $words);
    $words = str_split($words);
    $reversed = "";

    foreach ($words as $word) {
        if (is_numeric($word)) {
            continue;
        }

        $reversed .= $word;
    }

    return $reversed;
}

function replace_tone($word, $next) {
    $blank = "";

    global $vowels;
    global $tones;
    global $marks;

    $next_is_third_tone = false;
    if ($next != null && strpos($next, '3')) {
        $next_is_third_tone = true;
    }

    foreach ($tones as $tone) {
        if (strpos($word, $tone)) {
            foreach ($vowels as $vowel) {
                $word = str_replace($tone, $blank, $word);

                if (strpos($word, $vowel)) {
                    $replace = $marks[$tone][$vowel];

                    // third sandhi rule
                    if ($next_is_third_tone && $tone == '3') {
                        $replace = $marks['2'][$vowel];
                    }

                    $next = $tone;
                    return str_replace($vowel, $replace, $word);
                }
            }
        }
    }

    // no match
    return $word;
}

function to_pinyin($meta) {
    $result = "";
    $words = explode(' ', $meta);
    $word = null;

    foreach ($words as $next) {
        if ($word == null) {
            $word = $next;
            continue;
        }

        $word = replace_tone($word, $next);
        $word = preg_replace('/\\\\u0*([0-9a-fA-F]{1,5})/', '&#x\1;', $word);
        $result .= $word . " ";

        $word = $next;
    }

    // because we're looking ahead we need to do this one last time
    $word = replace_tone($word, null);
    $word = preg_replace('/\\\\u0*([0-9a-fA-F]{1,5})/', '&#x\1;', $word);
    $result .= $word . " ";

    return $result;
}

function get_overall_stats($conn, $nick) {
    $query = 'select * from user where nick = :nick';

    $q = $conn->prepare($query);
    $q->execute(array(
        ':nick' => $nick
    ));

    $result = $q->fetch();

    $ratio = 0;
    $correct = $result['correct'];
    $wrong = $result['wrong'];

    if ($correct > 0 || $wrong > 0) {
        $ratio = round(($correct / ($correct + $wrong)) * 100, 2);
    }

    return array(
        'ratio' => $ratio,
        'correct' => $correct,
        'wrong' => $wrong
    );
}

// debug function
function printr($array, $heading = null) {
    if ($array == null || count($array) == 0) {
        return;
    }

    echo("<div style='padding: 5px; background-color: #ccc;'><b>$heading</b>");

    $new = array();

    foreach ($array as $key => $value) {
        if (is_numeric($key)) {
            continue;
        }

        $new[$key] = $value;
    }

    echo("<pre>");
    print_r($new);
    echo("</pre></div>");
}

function update_word_stats($conn, $stats) {
    $query = 'update stats set correct = :correct, wrong = :wrong, ratio = :ratio ' .
             'where word = :char and nick = :nick';

    $ratio = 0.0;
    $correct = $stats['correct'];
    $wrong = $stats['wrong'];

    if ($correct > 0 || $wrong > 0) {
        $ratio = round(($correct / ($correct + $wrong)) * 100, 2);
    }

    $q = $conn->prepare($query);
    $q->execute(array(
        ':correct' => $correct,
        ':wrong' => $wrong,
        ':ratio' => $ratio,
        ':char' => $stats['word'],
        ':nick' => $stats['nick']
    ));
}

function update_progress($conn, $nick, $correct, $current, $char_id) {
    global $goal_ratio;

    $query = 'update user set correct = :correct, wrong = :wrong, ' .
             'current = :current where nick = :nick';

    $stats = get_overall_stats($conn, $nick);
    $wstats = get_word_stats($conn, $nick, $char_id);

    if ($correct) {
        $stats['correct'] += 1;
        $wstats['correct'] += 1;
    }
    else {
        $stats['wrong'] += 1;
        $wstats['wrong'] += 1;
    }

    if ($stats['correct'] > 0 || $stats['wrong'] > 0) {
        $ratio = ($stats['correct'] / ($stats['correct'] + $stats['wrong'])) * 100;
    }

    if ($wstats['correct'] > 0 || $wstats['wrong'] > 0) {
        $wstats['ratio'] = round(($wstats['correct'] / ($wstats['correct'] + $wstats['wrong'])) * 100, 2);
    }

    update_word_stats($conn, $wstats);

    // award new word, need better algo though
    if ($ratio > $goal_ratio) {
        $current += 1;
    }

    $q = $conn->prepare($query);
    $q->execute(array(
        ':correct' => $stats['correct'],
        ':wrong' => $stats['wrong'],
        ':current' => $current,
        ':nick' => $nick
    ));

    return array(
        'ratio' => round($ratio, 2),
        'correct' => $stats['correct'],
        'wrong' => $stats['wrong']
    );
}

function get_word_stats($conn, $nick, $char_id) {
    $wstats = get_word_stats_internal($conn, $nick, $char_id);

    if ($wstats == null || count($wstats) == 0) {
        $wstats = create_word_stats($conn, $nick, $char_id);
    }

    return $wstats;
}

function get_word_stats_internal($conn, $nick, $char_id) {
    $query = 'select * from stats where nick = :nick and word = :char';

    $q = $conn->prepare($query);
    $q->execute(array(
        ':nick' => $nick,
        ':char' => $char_id
    ));

    $result = $q->fetch();

    return $result;
}

function create_word_stats($conn, $nick, $char_id) {
    if ($char_id == null || !is_numeric($char_id)) {
        echo("<p>char_id is null ffs<p>");
        return;
    }

    $query = "insert into stats (nick, correct, wrong, ratio, word) " . 
             "values(:nick, :correct, :wrong, :ratio, :word)";

    $q = $conn->prepare($query);
    $q->execute(array(
        ':nick' => $nick,
        ':correct' => 0,
        ':wrong' => 0,
        ':ratio' => 0,
        ':word' => $char_id
    ));

    return array(
        'nick' => $nick,
        'correct' => 0,
        'wrong' => 0,
        'ratio' => 0,
        'word' => $char_id
    );
}

function get_training_word($conn, $nick) {
    $query = 'select * from stats where nick = :nick order by ratio limit 0, 10';

    $q = $conn->prepare($query);
    $q->execute(array(
        ':nick' => $nick
    ));

    $results = $q->fetchAll();

    if ($results == null || count($results) == 0) {
        return null;
    }

    $limit = count($results) - 1;

    if ($limit <= 0) {
        $limit = 0;
    }

    $result = $results[rand(0, count($results) - 1)];
    $char_id = $result['word'];

    return get_word_with_id($conn, $char_id);
}

function get_word_with_id($conn, $char_id) {
    $query = "select * from word where id = :id";

    $q = $conn->prepare($query);
    $q->execute(array(
        ':id' => $char_id
    ));

    return $q->fetch();
}

function get_connection() {
    $host = DBHOST;
    $dbname = DBNAME;
    $user = DBUSER;
    $pass = DBPASS;

    $conn = null;

    try {  
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);  
        $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    }  
    catch(PDOException $e) {  
        die($e->getMessage());
    }

    return $conn;
}

function debug($text) {
    global $debug;

    if (!$debug) {
        return;
    }

    echo($text);
}

function get_rare_word($conn, $nick, $last) {
    $max = rand(0, 10);
    $query = "select word from stats where id != :last and nick = :nick order by correct limit $max, 1";

    $q = $conn->prepare($query);
    $q->execute(array(
        ':last' => $last,
        ':nick' => $nick
    ));

    $word_id = $q->fetch();

    if ($word_id == null || count($word_id) == 0) {
        return null;
    }

    return get_word_with_id($conn, $word_id['word']);
}

function get_word($conn, $current, $nick, $last) {
    $method = rand(0, 5);

    if ($method == 0) {
        debug("<p><b>getting random word</b></p>");
        return get_random_word($conn, $current, $last);
    }
    else if ($method < 3) {
        $word = get_rare_word($conn, $nick, $last);

        // new user and doesn't have any training statistics yet
        if ($word == null || count($word) == 0) {
            debug("<p><b>getting random word</b></p>");
            return get_random_word($conn, $current, $last);
        }

        debug("<p><b>getting rare word</b></p>");
        return $word;
    }
    else {
        $word = get_training_word($conn, $nick, $last);

        // new user and doesn't have any training statistics yet
        if ($word == null || count($word) == 0) {
            debug("<p><b>getting random word</b></p>");
            return get_random_word($conn, $current, $last);
        }

        debug("<p><b>getting training word</b></p>");
        return $word;
    }
}

function get_random_word($conn, $current, $last) {
    $max = rand(1, $current);
    $query = "SELECT * FROM word where id != :last order by level, id LIMIT $max, 1";
    
    $q = $conn->prepare($query);
    $q->execute(array(':last' => $last));
    $result = $q->fetch();

    return $result;
}


?>
