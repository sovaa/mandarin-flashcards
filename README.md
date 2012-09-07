## Mandarin Flashcards ##

## About ##

Flashcards for Mandarin Chinese using the standardized HSK levels.

## Install ##

Go into the `install/` folder and check the contents of `tables.sql`. These are MySQL queries, you might have to modify them to suit your needs.

When you've created the tables, update the `include/config.php` file with the correct values. You need the database login information and a Facebook API key and secret for logging in (used only to identify a user so their progress can be saved).

Now run the `install/import.php` file, either by visiting it with your browser or from the command line. It will import all the HSK words from the `install/hsk-level-all.csv` file into the `word` table.

Now you're done! Visit the `index.php`, login and then start learning!

## Usage ##

### Word selection ###

The "learning algorithm" is very simple as of now. It gives you six words to begin with, and as long as your overall ratio is above 80 %, it will keep giving you a new word everytime you submit an answer.

The word that gets selected is decided by three different methods, each with its own chance of being used:

* 20 % chance of getting a random word in your current list,
* 40 % chance of getting a word you haven't had in a long time,
* 40 % chance of getting a word that you have a low clearing rate for.

These percentages and methods are probably going to be changed pretty soon to allow a better learning experience.

### Answering ###

Answers are writen in pinyin. I don't know which flavor of pinyin it is; it is the one used by the HSK lists.

You may answer in pinyin with or without tone marks, and with or without spaces. These answers are equivelant:

* zhongguo
* Zhōng guó
* zhōngguó
