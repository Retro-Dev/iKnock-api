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

        .label {
            color: #bfbfbf;
        }

        .btn-warning {
            /* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#bf1e2e+0,f6931e+100 */
            background: #bf1e2e; /* Old browsers */
            background: -moz-linear-gradient(left, #bf1e2e 0%, #f6931e 100%); /* FF3.6-15 */
            background: -webkit-linear-gradient(left, #bf1e2e 0%, #f6931e 100%); /* Chrome10-25,Safari5.1-6 */
            background: linear-gradient(to right, #bf1e2e 0%, #f6931e 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#bf1e2e', endColorstr='#f6931e', GradientType=1); /* IE6-9 */
            border: 0;
            border-radius: 50px;
            padding: 8px 30px;
            font-weight: 600;
        }

        .form-control {
            border: 0;
            border-radius: 0;
            border-bottom: 1px solid #eee;
            box-shadow: none !important;
        }

        .form-control:hover, .form-control:focus {
            border-color: #bf1e2e
        }

        .label{
            color: #000;
    font-weight: bold;
        }
        .btn-success{
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="content col-sm-3">
        <img src="{{asset('image/nav-logo.png')}}"class="img-responsive">
        <div class="m-b-md">
            <form class="form-horizontal" method="post" action=""
                  style="background:#fff/*rgba(124,177,180,0.5)*/; padding:30px; border-radius:20px;font-weight: 600;color:#000;">
                {{ csrf_field() }}
                <input type="hidden" name="hash" value="{{ $request->token }}">

                <div class="form-group">
                    <p style="/*border-bottom: 1px solid #fff; */margin:0;color:#000;text-align:left;/*padding-bottom: 20px;*/">
                        Please fill out the form below to change your password</p>
                </div>
                <div class="form-group">
                    @if(isset($error))
                        <div class="alert alert-danger alert-dismissible text-left" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <ul>
                                @foreach($error as $row)
                                    <li style="font-size:12px;">{{$row}}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <div class="row">
                        <hr/>
                    </div>
                </div>
                {{--<div class="form-group {{ $errors->has('old_password') ? ' has-error' : '' }}">
                    <label class="label col-sm-2" for="old_password">Old Password</label>
                    <input class="form-control" id="old_password" name="old_password" value=""/>
                    @if ($errors->has('old_password'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('old_password') }}</strong>
                                    </span>
                    @endif
                </div>--}}
                <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                    <label class="label col-sm-2" for="password">New Password</label>
                    <input type="password" class="form-control" id="password" name="password" value=""/>
                    @if ($errors->has('password'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                    <label class="label col-sm-2" for="password_confirmation">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" value=""/>
                    @if ($errors->has('password_confirmation'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                    @endif
                </div>
                <div class="form-group"></div>
                <div class="form-group text-right">
                    <button name="submit" type="submit" class="btn btn-success">Change Password</button>
                </div>
            </form>
        </div>

    </div>
</div>
</body>
</html>
