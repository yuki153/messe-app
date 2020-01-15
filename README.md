# messe

## 使用技術

Server lang：php7.2  
Server Framework: Slim3  
Front App: Polrymer 2.0

## 準備

下記コマンドを使用できるようにしておく

* docker
* composer
* bower
* yarn

## 開発環境起動

docker で起動した port 8000 で開発を行う  
※ link タグの import 機能を polymer2.0 では用いる為、Chromeで確認する

### プロジェクト clone 後最初の１回目

```bash
# 1.Start the PHP dev environment. You can connect with port 8000
docker-compose up -d --build
# 2.Bower_components install
cd src/app/views/messe
bower install
# 3.Polymer-CLI install
yarn
```

### 以降のプロジェクト起動

```bash
# 1.Start the PHP dev environment. You can connect with port 8000
docker-compose up -d
```

## アプリケーションビルド

ビルド前に src/app/views/messe/app.php に下記の変更を加える
その後ビルドコマンドを打つ

* app.php の拡張子を html に変更
* base タグをコメントアウト
* php コードの削除

```bash
# Build setting by polymer.json
polymer build
```
