CREATE TABLE roles (
	id 		INT 					AUTO_INCREMENT PRIMARY KEY,
	name 	VARCHAR(60) 	NOT NULL
);

CREATE TABLE users (
  id 				INT 					AUTO_INCREMENT PRIMARY KEY,
	role_id		INT						NOT NULL DEFAULT 3,
  login 		VARCHAR(60) 	NOT NULL UNIQUE,
  password 	VARCHAR(60) 	NOT NULL,
  email 		VARCHAR(60) 	NOT NULL UNIQUE,
  created 	DATETIME,
	FOREIGN KEY (role_id) REFERENCES roles(id)	
);

CREATE TABLE domains (
  id 			INT 					AUTO_INCREMENT PRIMARY KEY,
  url 		TEXT 					NOT NULL,
	alias 	VARCHAR(255),
	banned 	TINYINT(1) 		DEFAULT 0
);

CREATE TABLE promotions (
  id 										INT 						AUTO_INCREMENT PRIMARY KEY,
  user_id 							INT 						NOT NULL,
  domain_id 						INT 						NOT NULL,
  title 								VARCHAR(255) 		NOT NULL,
	url 									TEXT 						NOT NULL,
  original_price_low 		DECIMAL(6,2),
  original_price_high 	DECIMAL(6,2),
  sale_price_low 				DECIMAL(6,2),
  sale_price_high 			DECIMAL(6,2),	
  created 							DATETIME,
  expired 							DATE,
  thumbs_up 						INT 						DEFAULT 0,
  thumbs_down 					INT 						DEFAULT 0,
  published 						TINYINT(1) 			DEFAULT 0,
  FOREIGN KEY (user_id) 	REFERENCES users(id) 		ON DELETE CASCADE,
  FOREIGN KEY (domain_id) REFERENCES domains(id) 	ON DELETE CASCADE
);

CREATE TABLE comments (
  id 						INT 				AUTO_INCREMENT PRIMARY KEY,
  user_id 			INT 				NOT NULL,
  promotion_id 	INT 				NOT NULL,
  content 			TEXT 				NOT NULL,
  created 			DATETIME,
  modified 			DATETIME,
  thumbs_up 		INT 				DEFAULT 0,
  published 		TINYINT(1) 	DEFAULT 0,	
  FOREIGN KEY (user_id) 			REFERENCES users(id) 			ON DELETE CASCADE,
  FOREIGN KEY (promotion_id) 	REFERENCES promotions(id) ON DELETE CASCADE
);

CREATE TABLE promotion_thumbs (
	id 						INT 				AUTO_INCREMENT PRIMARY KEY,
	user_id 			INT 				NOT NULL,
	promotion_id 	INT 				NOT NULL,
	created 			DATETIME,
	modified 			DATETIME,
	vthumbs 			TINYINT(1) 	DEFAULT 0,
  FOREIGN KEY (user_id) 			REFERENCES users(id) 			ON DELETE CASCADE,
  FOREIGN KEY (promotion_id) 	REFERENCES promotions(id) ON DELETE CASCADE
);

CREATE TABLE comment_thumbs (
	id 						INT 				AUTO_INCREMENT PRIMARY KEY,
	user_id 			INT 				NOT NULL,
	comment_id 		INT 				NOT NULL,
	created 			DATETIME,
	modified 			DATETIME,
	vthumbs 			TINYINT(1) 	DEFAULT 0,
  FOREIGN KEY (user_id) 			REFERENCES users(id) 			ON DELETE CASCADE,
  FOREIGN KEY (comment_id) 		REFERENCES comments(id) 	ON DELETE CASCADE
);

CREATE TABLE messages (
	id 						INT 				AUTO_INCREMENT PRIMARY KEY,
	from_user			INT 				NOT NULL,
	to_user				INT					NOT NULL,
	content				TEXT				NOT NULL,
	created				DATETIME,
	is_read				TINYINT(1) 	DEFAULT 0,
	FOREIGN KEY (from_user) 	REFERENCES users(id) 	ON DELETE CASCADE,
	FOREIGN KEY (to_user) 		REFERENCES users(id) 	ON DELETE CASCADE
);

INSERT INTO roles(name) 
VALUES 	('administrator'),
				('moderator'),
				('user');