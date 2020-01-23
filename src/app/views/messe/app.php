<!doctype html>
<html lang="jp">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, minimum-scale=1, initial-scale=1, user-scalable=yes">

    <title>messe</title>
    <meta name="description" content="双方向にリアルタイムでコミュニケーション（チャット）ができるWebサービスです">
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
    <link rel="apple-touch-icon" href="../images/apple-messe-ico-180x180.png">
    <link rel="shortcut icon" href="../images/messe-ico-192x192.png">
    
    <!-- <script>
    // Force all polyfills on
    if (window.customElements) window.customElements.forcePolyfill = true;
    ShadyDOM = { force: true };
    ShadyCSS = { shimcssproperties: true};
    </script> -->

    <script src="./bower_components/webcomponentsjs/webcomponents-loader.js"></script>

    <link rel="import" href="./modules/messe-app.html">

    <style>
        * {
            margin: 0;
            padding: 0;
            line-height: 1;
        }
        body {
            padding-top: 70px;
            background-color: #2055a8;
            font-family: -apple-system, BlinkMacSystemFont, 'Helvetica Neue', 'Helvetica', 'Hiragino Kaku Gothic ProN', 'ヒラギノ角ゴ ProN W3', 'メイリオ', Meiryo, sans-serif;
        }
    </style>
</head>

<body>
    <messe-app user-data='<?php echo $googleUserData; ?>'></messe-app>
</body>
</html>