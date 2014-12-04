<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <!--<link rel="icon" href="../../favicon.ico">-->

        <title>Sign Up!</title>

        <!-- Bootstrap core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <link href="css/signin.css" rel="stylesheet">

        <script src='js/jquery-1.11.0.js'></script>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>
    <style>
    body{
            background-image: url("back.jpg");
            /*background-repeat: no-repeat;*/
            background-attachment: fixed;
        }
    </style>

<body>

<div class="container">
    <div class="row">
        <div class="jumbotron col-sm-6 col-sm-offset-3">
            <div class="row">
                <h1 class="col-sm-12" id='contenthead'> Sign Up </h1>
            </div>
            <div class="row" id='warnings'></div>
            <div class="row" id='content'>
                <form role="form" class="form-vertical" id='contentform' action='php/bridge.php' method='POST'>
                    <div class='form-group'>
                        <input id='username' class="form-control input-lg" type='text' placeholder="Username" name="username" />
                    </div>
                    <div class='form-group'>
                        <input id="email" class="form-control input-lg" type="email" placeholder="Email" name="email" />
                    </div>
                    <div class='form-group' id='fg-pass'>
                        <input type="password" class="form-control input-lg" id="password" placeholder="Password" name="password" />
                    </div>
                    <div class='form-group'>
                        <input type="password" class="form-control input-lg" id="cpassword" placeholder="Confirm Password" name="cpassword" />
                    </div>
                    <div class='form-group' id='fg-btn'>
                        <input type='submit' class='btn btn-primary btn-lg btn-block col-sm-12' />
                    </div>
                    <div class='form-group' id='fg-link'>
                        <a class='pull-right' id='rtnlogin' href='login.php'>Login here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
         

</body>
<script>
    $( document ).ready(function() {
        
    });
   
 
</script>
</html>
