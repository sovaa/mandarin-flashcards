    <div id="container">
        <div style="font-size: 400%;"><?= $word ?></div>
            <br />
    
            <form action="" method="post">
                <input type="hidden" name="word" value="<?=$word?>" />
                <input type="hidden" name="raw-pinyin" value="<?=$raw_pinyin?>" />
                <input type="hidden" name="pinyin" value="<?=$pinyin?>" />
                <input type="hidden" name="meaning" value="<?=$meaning?>" />
                <input type="hidden" name="char-id" value="<?=$char_id?>" />
        
                <input type="text" name="answer" id="answer" autocomplete="off" />
                <input type="submit" value="check" />
            </form>
        </div>

        <div style="position: absolute; top: 0; left: 0; padding: 10px;">
            Current<br />
            <? echo("Ratio: {$stats['ratio']} %<br />&#10003; {$stats['correct']}<br />X {$stats['wrong']}"); ?>
        </div>
        <div style="position: absolute; top: 0; right: 0; padding: 10px;">
            Overall<br />
            <!--Words in selection: <?= $current ?><br />
            Words known: TODO<br />-->
            Ratio: <?= $ostats['ratio'] ?> %<br />
            &#10003; <?= $ostats['correct'] ?><br />
            X <?= $ostats['wrong'] ?>
        </div>

        <?php
            if (isset($_POST) && isset($_POST['answer'])) {
        ?>

        <div id="prev" style="text-align: center;">
            <hr />

            <?php
                if ($_correct == true) {
                    echo("<span style='color: green; font-weight: bold; font-size: 150%;'>Correct!</span>");
                }
                else {
                    echo("<span style='color: red; font-weight: bold; font-size: 150%;'>Wrong!</span>");
        
                    if (strlen(trim($_answer)) > 0) {
                        echo(" <span style='font-size: 90%; color: gray;'>You said <b>'$_answer'</b>!</span>");
                    }
                }
            ?>

            <br /><br />
            <div style="font-size: 200%;"><?= $_word ?></div>
            <div><h3 title="<?= $_raw_pinyin ?>"><?= $_pinyin ?></h3></div>
            <div><?= $_meaning ?></div>
        </div>
        <?php
        }
        ?>

