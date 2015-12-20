<!DOCTYPE html>
<html>
    <head>
        <title>403 Forbidden!</title>

        <link href="//fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #B0BEC5;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 36px;
                margin-bottom: 40px;
            }

            .title strong {
                color: #000000;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">{!! $title or '您沒有足夠權限' !!}，五秒後系統將跳轉首頁</div>
            </div>
        </div>
        <script>
        setTimeout(function () {
            window.location.href = '/';
        }, 5000);
        </script>
    </body>
</html>
