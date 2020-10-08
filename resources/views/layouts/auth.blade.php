<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TMC RGMC and CSMH C R Wadia Covid 19 testing facility</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.3/css/AdminLTE.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/skins/all.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,300i,400,400i,600,600i,700&display=swap&subset=latin-ext" rel="stylesheet" />
    <link href="{{ asset('public/css/custom.css') }}" rel="stylesheet" />
    <link rel="icon" href="{{ asset('public/images/tmc_logo.png') }}" type="image/gif" sizes="16x16">
    @yield('styles')
    <style type="text/css">
    	.justify-content-center{
    		justify-content: center !important;
    	}

    	.row{
    		display: flex;
    		flex-wrap: wrap;
    	}

    	.card-header:first-child {
		    border-radius: calc(0.25rem - 1px) calc(0.25rem - 1px) 0 0;
		}

		.card-header {
		    padding: 0.75rem 1.25rem;
		    margin-bottom: 0;
		    background-color: rgba(0, 0, 0, 0.03);
		    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
		}

		img{
			vertical-align: middle;
    		border-style: none;
		}

		.card-body {
		    flex: 1 1 auto;
		    min-height: 1px;
		}

		.login-box, .register-box {
		    width: 360px;
		    margin: 0 auto;
		}

		.card {
		    position: relative;
		    display: flex;
		    flex-direction: column;
		    min-width: 0;
		    word-wrap: break-word;
		    background-color: #fff;
		    background-clip: border-box;
		    border: 1px solid rgba(0, 0, 0, 0.125);
		    border-radius: 0.25rem;
		}
    </style>
</head>

<body class="hold-transition login-page">
	
	<main class="py-4" style="margin-top:60px;">

        <div class="container">
            <div class="container">
			    <div class="row justify-content-center">
			        <div class="col-md-8">
			        	<header class="main-header">
				            <nav class="navbar navbar-static-top" style="background-color: #d2d6de !important;margin-left: 0;">
				            	<div style="font-size: 28px;padding-bottom: 15px;color: #4386bc;text-align: center;">TMC RGMC and CSMH C R Wadia <br/>Covid 19 testing facility</div>            	
				            </nav>
				        </header>
			            <div class="card">
			                <div class="card-header" style="color: white;background-color: #4386bc;text-align: center;">
			                    <img src="{{ asset('public/images/tmc_logo.png') }}" style="height: 68px;width: 80px;" align='middle'>
			                    <p style="font-size: 20px;margin-top: 10px;">Thane Municipal Corporation</p>
			                </div>

			                <div class="card-body">
								@yield('content')
							</div>
			            </div>
			        </div>
			    </div>
			</div>
        </div>
	</main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
    @yield('scripts')
</body>

</html>
