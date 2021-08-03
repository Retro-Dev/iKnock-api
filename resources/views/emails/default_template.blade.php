<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <title>IKnock</title>

    <style>

        body {
        @import url('https://fonts.googleapis.com/css?family=Roboto');
            background: #f9f7f8;
            overflow-wrap: break-word;
            word-wrap: break-word;
            -ms-word-break: break-all;
            word-break: break-all;
            word-break: break-word;
            -ms-hyphens: auto;
            -moz-hyphens: auto;
            -webkit-hyphens: auto;
            hyphens: auto;
        }

        section {
            padding: 2% 0px 0px;
        }

        .bg-white {
            background: #fff;
            border: 1px solid #e5e3e4;
            padding: 50px;
        }

        .content-wrap a,
        .footer-wrap a.pp-link {
            font-size: 18px;
            text-decoration: none;
            color: #fe6701;
            margin-bottom: 10px;
            display: block;
            font-family: 'Roboto', sans-serif;
        }

        .content-wrap p {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
            font-family: 'Roboto', sans-serif;
        }

        .content-wrap span.hr-line {
            border: 1px solid #dfdfdd;
            width: 50px;
            display: block;
            margin: 50px 0px;
        }

        .footer-wrap {
            padding: 25px 0px;
        }

        .footer-wrap p {
            font-size: 16px;
            color: #8c8c8c;
            font-family: 'Roboto', sans-serif;
        }

        .footer-wrap p a {
            font-size: 16px;
            text-decoration: none;
            color: #ff6700;
            opacity: 1;
            font-family: 'Roboto', sans-serif;
        }

        .logo-wrap {
            padding-bottom: 25px;
        }
       

    </style>
</head>
<body>

<section>
    <div class="container">

        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-8 col-lg-offset-2">
                <div class="text-center logo-wrap">
                    <!-- <a href="javascript:;"><img src="images/logo.png" alt=""></a> -->
                    <a href="javascript:;">
                        <img src="{!! URL::to("image/logo.png") !!}" alt="Iknock" style="width:200px;"/>
                    </a>
                </div>
                <div class="bg-white">
                    <div class="content-wrap">
                        {!! $content !!}
                    </div>
                </div>
                <div class="footer-wrap text-center">
                    <p>&copy; {{date('Y')}} IKnock. All Rights reserved. <a target="_blank"
                                                                          href="{{ URL::to('/privacy-policy') }}">Privacy
                            Policy</a> / <a target="_blank" href="{{ URL::to('/terms-conditions') }}">Terms &
                            Conditions</a></p>
                    <a class="pp-link" target="_blank" href="{{ URL::to('') }}">IKnock.com</a>
                </div>
            </div>
        </div>

    </div>
</section>
</body>
</html>
