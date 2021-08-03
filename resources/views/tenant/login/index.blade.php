<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Log in</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{asset('assets/css/tenant-stylesheet/tenant-style.css')}}">
</head>
<body class="bg-img">
    <div class="row nomargin">
        <div class="col-md-12" style="text-align:center;">
           <img src="{{asset('image/login-logo.png')}}" class="img-responsive logo-img">
        </div>
    </div>
    <div class="row nomargin">
        <div class="col-md-4"></div>
        <div class="col-md-4">
           <div  class="card">
                @include('tenant.error')
                <form action="{{ url('/tenant/login') }}" method="post" class="login">
                {{ csrf_field() }}    
                <div class="input-group form-group fields" id="custom-form1">

                    <i class="fa fa-user icon1" id="icon"></i>
                    <input type="email" name="email" class="form-control" placeholder="User Name" id="custom-input1" required="required">
                </div>

                <div class="input-group form-group" id="custom-form2">
                    <i class="fa fa-key icon2" id="icon"></i>
                    <input type="Password" name="password" class="form-control" placeholder="Password" id="custom-input2" required="required">
                </div>
                <input type="submit"  name="submit" value="Login" class="btn btn-default btn-lg" id="login-btn">
                <div class="flex-sb-m w-full">
                    <div class="contact100-form-checkbox">
                        <input class="input-checkbox100" id="ckb1" type="checkbox" name="remember-me">
                        <label class="label-checkbox100" for="ckb1">
                            Remember me
                        </label>
                    </div>
                    <div>
                        <a href="{{URL::to('tenant/login/forget_password')}}" class="txt1">
                            Forgot Password?
                        </a>
                    </div>
                </div>
            </form>
            </div>
        </div>
        <div class="col-md-4"></div>
    </div>
</body>
<script src="{{ asset('assets/js/jquery.min.js')}}"></script>
<script src="{{ asset('assets/js/bootstrap.js') }}"></script>
<script src="{{asset('assets/js/tenant-js/login.js')}}"></script>
</html>
