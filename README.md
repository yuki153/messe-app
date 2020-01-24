# messe

## 使用技術

Server lang：php7.2  
Server Framework: [Slim 3](https://www.slimframework.com/)  
Front App: [Polrymer 2.0](https://docs.polymer-jp.org/2.0/docs/devguide/feature-overview)

## ディレクトリ構造

```bash
.
├── README.md
├── db
│   ├── data # Auto create by docker
│   ├── my.cnf
│   └── sql
│       └── init.sql
├── docker-compose.yml
├── php7.2
│   ├── dockerfile
│   └── php.ini
├── phpmyadmin # Auto create by docker
└── src
    ├── app
    │   ├── controllers
    │   ├── models
    │   └── views/messe # messe frontend modules
    ├── composer.json
    ├── composer.lock
    ├── conf
    │   └── settings.php # messe server side define
    ├── images # messe frontend images
    ├── logs
    │   └── app.log
    ├── public
    │   └── index.php # routing
    ├── vendor # Auto create by composer
    └── .htacsess # mapping url
```

## 開発環境の仕様について

### 準備

下記コマンドを使用できるようにしておく

* docker
* composer
* bower
* yarn

### 起動

後述のコマンドを打ち、http://localhost:8000/messe/login へアクセス  
※ PCブラウザで確認の際は devTool で touch イベントをシュミレートできる chrome で行う  
※ 開発時に node version v8.1x.0 で動作確認済み（他未検証）

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

### 補足

./src/app/views/messe/app.php の拡張子を html に変更し、下記コマンドを実行する事で polymer の開発環境を個別に立てることができるが、controller から値を渡したりすることはできないので、基本使用しない。

```bash
cd src/app/views/messe/
yarn polymer serve
```

## 開発について

### Google ログイン

[Google OAuth 2.0 認証を使ったログインの実装](https://qiita.com/kite_999/items/bddd62c395f260e745bc) 等を参考に設定を行った後、  
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

## 実環境への反映について

### ① アプリケーションビルド

./src/app/views/messe/app.php をコピーして同じ階層に app.html を作成。  
その後下記コマンドによりビルドを行う。

```bash
# Build setting by polymer.json
polymer build
```

### ② 動作確認

① のアプリケーションビルドにより、build/ ディレクトリが作成される。  
build/default/ の中にある app.html（ビルド後ファイル）の拡張子を .php に変更した後、
./src/app/views/messe/app.php と置き換え、http://localhost:8000/messe/ で動作確認を行う。  
※ 置き換えの際、app.php（ビルド前ファイル） は _app.php 等に名前変更して退避させておく

### ③ 設定ファイルの書き換え

実際の環境用に ./src/conf/settings.php の下記部分を書き換える

```php
...
'messe' => [
    ...
    'CALLBACK_URL' => 'https://localhost:8000/messe/',
    ...
    'DB_DATABASE' => 'messe_db',
    'DB_USERNAME' => 'root',
    'DB_PASSWORD' => 'root',
    'DB_HOST' => 'mysql'
]
...
```

### ④ 反映

.src/ 配下の下記ディレクトリ及びファイルを任意のサーバーに反映する。

```bash
.
├── app
│   ├── controllers
│   │   └── *
│   ├── models
│   │   └── *
│   └── views/messe
│       ├── bower_components # build/default/bower_components
│       │   └── *
│       ├── app.php # build/default/app.html（拡張子を php へ変更）
│       └── login.php
├── conf
│   └── settings.php
├── images
│   └── *
├── logs
│   └── app.log
├── public
│   └── index.php
├── vendor
│   └── *
└── .htacsess
```