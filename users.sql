CREATE TABLE users (
    username VARCHAR(30)  NOT NULL PRIMARY KEY,
    password VARCHAR(255) NOT NULL
);


INSERT INTO users (username, password)
VALUES ('admin', 'admin');
