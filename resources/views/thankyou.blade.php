<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/bootstrap.css')}}" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
        html, body {
            background: url('{{config('app.url')}}/image/login-bg.jpg');
            background-size: cover;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .content {
            text-align: center;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="content col-sm-3">
        <div class="m-b-md">
            <div style="background:#fff/*rgba(124,177,180,0.5)*/; padding:30px; border-radius:20px;font-weight: 600;color:#000;">
                <h1 style="font-weight: bold; font-size: 20px;>You have successfully reset your password</h1>
                <div class="links">
                    <p style="font-weight: 600;">Back to <a style="color:#047b2a;" href="{{config('app.url')}}/tenant/login">home</a></p>
                </div>
            </div>

        </div>

    </div>
</div>
</body>
</html>
