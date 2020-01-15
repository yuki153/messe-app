<!doctype html>
<html lang="jp">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, minimum-scale=1, initial-scale=1, user-scalable=yes">

    <title>messe</title>
    <meta name="description" content="messe description">

    <!-- polymer serve base url -->
    <base href="./messe/">

    <!-- See https://goo.gl/OOhYW5 -->
    <link rel="manifest" href="manifest.json">

    
    <!-- <script>
    // Force all polyfills on
    if (window.customElements) window.customElements.forcePolyfill = true;
    ShadyDOM = { force: true };
    ShadyCSS = { shimcssproperties: true};
    </script> -->

    <script src="https://cdn.rawgit.com/webcomponents/webcomponentsjs/cf46d7a0/webcomponents-loader.js"></script>

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
<!--index配下ではデータのやりとりができない-->
</html>