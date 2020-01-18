/**
 * MEMO:
 * limit は予約語のため error になる
 * .sql 拡張子に mysql を記述すると syntax error になる
 * => mssql extension が自動で DL され？ mssql 構文でパースされるため
*/
CREATE DATABASE IF NOT EXISTS messe_db;
CREATE TABLE IF NOT EXISTS messe_db.chat_logs (
    user_id varchar(50),
    user_name varchar(20),
    text varchar(200),
    img_url varchar(60),
    time datetime
) DEFAULT CHARACTER SET utf8mb4;
