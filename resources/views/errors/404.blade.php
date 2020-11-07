<!DOCTYPE html>
<html>
    <head>
        <title>404 {{ trans('global.not_found') }}</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

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
                font-size: 72px;
                margin-bottom: 20px;
            }
            .desc {
                margin-bottom: 20px;
            }
            h1 {
                font-size: 20em;
                margin: 0;
            }
            .btn-back {
                border: 1px solid;
                border-radius: 4px;
                text-decoration: none;
                padding: 5px 10px;
                margin-top: 50px;
                color: #B0BEC5;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <h1>404</h1>
                <div class="title">{{ trans('global.not_found') }}</div>
                <div class="desc">{{ trans('global.sorry_not_found') }}</div>
                <a href="javascript:history.back()" class="btn-back">&#8592; {{ trans('global.back') }}</a>
            </div>
        </div>
    </body>
</html>
