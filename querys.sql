create database twitter_clone;

use twitter_clone;

create table users(
	id int not null primary key AUTO_INCREMENT,
	`name` varchar(100) not null,
	email varchar(150) not null,
	`password` varchar(32) not null
);

create table tweets(
	id int not null PRIMARY KEY AUTO_INCREMENT,
	id_user int not null,
	tweet varchar(140) not null,
	`date` datetime default current_timestamp
);

create table users_followers(
	id int not null primary key auto_increment,
	id_user int not null,
	id_following_user int not null
);