<!DOCTYPE html>
<html lang="jp">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>messe|ログインページ</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }


        .login {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            overflow: hidden;
            width: 100%;
            height: 100vh;
            background: rgb(83,109,254);
            background: linear-gradient(45deg, rgba(83,109,254,1) 0%, rgba(255,64,129,1) 100%);
        }

        .login__button {
            width: 158px;
            height: 50px;
            line-height: 50px;
            text-align: center;
            margin-top: 60px;
            background-color: #fff;
            border-radius: 25px;
            overflow: hidden;
        }

        .login__link {
            display: block;
            height: 100%;
            font-weight: bold;
            font-size: 14px;
            background: rgb(83,109,254);
            background: linear-gradient(45deg, rgba(83,109,254,1) 0%, rgba(255,64,129,1) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .logo {
        }

    </style>
</head>
<body>
    <div class="login">
        <h1 class="logo">
            <img src="../images/messe_logo_wh.svg" width="160" alt="messe">
        </h1>
        <div class=login__button>
            <a class="login__link" href="<?php echo $slim['link'] ?>">
                Google でログイン
            </a>
        </div>
    </div>
</body>
</html>