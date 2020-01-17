# messe

## 使用技術

Server lang：php7.2  
Server Framework: Slim3  
Front App: Polrymer 2.0

## 開発環境の仕様について

### 準備

下記コマンドを使用できるようにしておく

* docker
* composer
* bower
* yarn

### 起動

下記のコマンドを打ち、http://localhost:8000/messe/login へアクセス  
※ link タグの import 機能を polymer2.0 では用いる為、Chrome で確認する

#### プロジェクト clone 後の初回の起動コマンド

```bash
# 1.Grant permission to apply my.cnf settings to mysql
chmod 644 ./db/my.cnf
# 2.Start the PHP dev environment. You can connect with port 8000
docker-compose up -d --build
# 3.Create a vendor dir, install slim3 and dependency modules there
cd src/
composer install
# 4.Create a bower_components dir and install polymer components there
cd app/views/messe/
bower install
# 5.Create a node_modules dir and install polymer cli there
yarn
```

#### 以降のプロジェクト起動コマンド

```bash
# 1.Start the PHP dev environment. You can connect with port 8000
docker-compose up -d
```

### 終了

```bash
docker-compose down
```

### アプリケーションビルド

ビルド前に src/app/views/messe/app.php に下記の変更を加える
その後ビルドコマンドを打つ

* app.php の拡張子を html に変更
* base タグをコメントアウト
* php コードの削除

```bash
# Build setting by polymer.json
polymer build
```

## 開発について

### Google ログイン

./src/conf/settings.php の `CONSUMER_KEY`, `CONSUMER_SECRET` に任意の値を設定する

```php
# ./src/conf/settings.php
...
'messe' => [
    'CONSUMER_KEY' => 'xxxxxxxxxxxxxxxxxxx',
    'CONSUMER_SECRET' => 'xxxxxxxxxxxxxxxxxxxx',
    ...
]
...
```

### DB（MySQL）の確認

#### MySQL サーバーへのログイン

```bash
# mysql コンテナへ入る
docker-compose exec mysql bin/bash
# mysql サーバーへ root ユーザーでログイン
# password: root
mysql -u root -p

```

#### SQL例

```sql
-- DB の charset 設定確認
SHOW VARIABLES LIKE 'char%';
-- messe_db の chat_logs カラムのデータ確認
SELECT * FROM messe_db.chat_logs;
```
