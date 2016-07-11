<?php
header('Location: ../index.php');
include("database.php");
$db = new PDO("mysql:host=".$DB_HOST, $DB_USER, $DB_PASSWORD, array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));
$db->query("CREATE DATABASE ".$DB_NAME);
$db->query("USE ".$DB_NAME);

//Création de la table account
$db->query("CREATE TABLE account (id INT AUTO_INCREMENT PRIMARY KEY NOT NULL, login VARCHAR(40) COLLATE utf8_general_ci, mail VARCHAR(255) COLLATE utf8_general_ci, pass VARCHAR(255) COLLATE utf8_general_ci, clef VARCHAR(255) COLLATE utf8_general_ci, date_inscription DATETIME, id_icon TINYINT, role VARCHAR(40) COLLATE utf8_general_ci, type VARCHAR(40) COLLATE utf8_general_ci, pictures_dir VARCHAR(255) COLLATE utf8_general_ci, actif TINYINT NOT NULL)");

//Création de la table config
$db->query("CREATE TABLE config (id TINYINT AUTO_INCREMENT PRIMARY KEY NOT NULL, pattern VARCHAR(40) COLLATE utf8_general_ci, pass_pattern VARCHAR(40) COLLATE utf8_general_ci)");
$db->query("INSERT INTO config (pattern, pass_pattern) VALUES ('/[1-9a-zA-Z]{3,12}/', '/[1-9a-zA-Z]{6,18}/')");

//Création de la table comments
$db->query("CREATE TABLE comments (id INT AUTO_INCREMENT PRIMARY KEY NOT NULL, pics_id INT NOT NULL, user_id INT NOT NULL, content TEXT COLLATE utf8_general_ci, date_add DATETIME)");

//Création de la table emotes
$db->query("CREATE TABLE emotes (id TINYINT AUTO_INCREMENT PRIMARY KEY NOT NULL, name VARCHAR(40) COLLATE utf8_general_ci, url VARCHAR(255) COLLATE utf8_general_ci)");
$db->query("INSERT INTO emotes (name, url) VALUES ('Angel','img/emote/angel.png'), ('Angry', 'img/emote/angry.png'), ('Big Smile', 'img/emote/big_smile.png'), ('Fear', 'img/emote/fear.png'), ('Heart', 'img/emote/heart.png'), ('Kiss', 'img/emote/kiss.png'), ('Lol', 'img/emote/lol.png'), ('Love', 'img/emote/love.png'), ('Smile', 'img/emote/smile.png'), ('Thug', 'img/emote/thug.png'), ('Xptdr', 'img/emote/xptdr.png'), ('Zen', 'img/emote/zen.png')");

//Création de la table filters
$db->query("CREATE TABLE filters (id TINYINT AUTO_INCREMENT PRIMARY KEY NOT NULL, name VARCHAR(40) COLLATE utf8_general_ci, url VARCHAR(255) COLLATE utf8_general_ci)");
$db->query("INSERT INTO filters (name, url) VALUES ('Aquarel', 'img/filter/aquarel.jpg'), ('Dark Wood', 'img/filter/darkwood.jpg'), ('Dead Flag', 'img/filter/dead_flag.jpg'), ('Fantasy', 'img/filter/fantasy.jpg'), ('Film', 'img/filter/film.jpg'), ('Film 2', 'img/filter/film2.jpg'), ('Film 3', 'img/filter/film3.jpg'), ('Frostbo', 'img/filter/frostbo.jpg'), ('Grunge', 'img/filter/grunge.jpg'), ('Muddy Waters', 'img/filter/muddy_waters.jpg'), ('Ocean', 'img/filter/ocean.jpg'), ('Psy', 'img/filter/psy.jpg'), ('Sirius', 'img/filter/sirius.jpg'), ('Star', 'img/filter/star.jpg')");

//Création de la table icons
$db->query("CREATE TABLE icons (id TINYINT AUTO_INCREMENT PRIMARY KEY NOT NULL, name VARCHAR(40) COLLATE utf8_general_ci, url VARCHAR(255) COLLATE utf8_general_ci)");
$db->query("INSERT INTO icons (name, url) VALUES ('Default', 'img/icons/default.png'), ('Smith', 'img/icons/smith.png'), ('Tux', 'img/icons/tux.png'), ('Ubuntu', 'img/icons/ubuntu.png'), ('Baracuda', 'img/icons/baracuda.png'), ('Pirate', 'img/icons/pirate_ship.png'), ('Joy', 'img/icons/joy.png'), ('Smile', 'img/icons/smile.png')");

//Création de la table pictures
$db->query("CREATE TABLE pictures (id INT AUTO_INCREMENT PRIMARY KEY NOT NULL, url VARCHAR(255) COLLATE utf8_general_ci, user_id INT NOT NULL, title VARCHAR(255) COLLATE utf8_general_ci, comment TEXT COLLATE utf8_general_ci, date_ajout DATETIME, published TINYINT NOT NULL)");

//Création de la table tablk
$db->query("CREATE TABLE IF NOT EXISTS tablk (id INT AUTO_INCREMENT PRIMARY KEY NOT NULL, pics_id INT NOT NULL, user_id INT NOT NULL)");
?>