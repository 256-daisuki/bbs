# bbs
userデータベースを作成し、
SQL文
`CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(64) NOT NULL
);

ALTER TABLE users ADD COLUMN username VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;`
を実行 