CREATE TABLE hsk (id INTEGER auto_increment PRIMARY KEY, level INTEGER, part INTEGER, word TEXT, pinyin TEXT, meaning TEXT);
CREATE TABLE stats (id INTEGER auto_increment primary key, nick TEXT, word INTEGER, ratio DOUBLE, correct INTEGER, wrong INTEGER);
CREATE TABLE user (id INTEGER auto_increment primary key, nick TEXT, correct INTEGER, wrong INTEGER, current INTEGER);
