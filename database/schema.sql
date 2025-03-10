-- Create the database and switch to it.
CREATE DATABASE article_aggregator_co;
USE article_aggregator_co;

-- Users table.
CREATE TABLE users (
   id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
   password_digest VARCHAR(255),
   email VARCHAR(255) NOT NULL UNIQUE,
   name VARCHAR(255) NOT NULL,
   profile_picture VARCHAR(255) DEFAULT 'images/profiles/default.jpg'
);

-- Articles table.
CREATE TABLE articles (
   id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
   title VARCHAR(255) NOT NULL,
   url VARCHAR(255) NOT NULL,
   created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
   updated_at DATETIME DEFAULT NULL,
   author_id INT NOT NULL,
   FOREIGN KEY (author_id) REFERENCES users(id)
);

-- Comments table with nested comment support.
CREATE TABLE comments (
   id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
   description VARCHAR(255) NOT NULL,
   created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
   author_id INT NOT NULL,
   article_id INT NOT NULL,
   parent_id INT DEFAULT NULL, -- For nested comments: NULL for root comments.
   FOREIGN KEY (author_id) REFERENCES users(id),
   FOREIGN KEY (article_id) REFERENCES articles(id),
   FOREIGN KEY (parent_id) REFERENCES comments(id)
);

-- Votes table with composite primary key.
CREATE TABLE votes (
   value INT NOT NULL,
   author_id INT NOT NULL,
   article_id INT NOT NULL,
   PRIMARY KEY (author_id, article_id),
   FOREIGN KEY (author_id) REFERENCES users(id),
   FOREIGN KEY (article_id) REFERENCES articles(id)
);


