CREATE DATABASE IF NOT EXISTS `phplogin`;
USE `phplogin`;

CREATE TABLE IF NOT EXISTS `accounts` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
  	`username` varchar(50) NOT NULL,
  	`password` varchar(255) NOT NULL,
  	`email` varchar(100) NOT NULL,
	`admin` bit NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS icons (
    id int(11),
    image blob not null,
    PRIMARY KEY (id),
    FOREIGN KEY (id) REFERENCES accounts (id) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO `accounts` (`id`, `username`, `password`, `email`, `admin`) VALUES (1, 'test', '$2y$10$SfhYIDtn.iOuCW7zfoFLuuZHX6lja4lF4XA4JqNmpiH/.P3zB8JCa', 'test@test.com', 1);