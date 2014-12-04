<!--Taken from http://getbootstrap.com/examples/signin/ November 7th, 2014 -->
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <!--<link rel="icon" href="../../favicon.ico">-->

        <title>Please, Log in</title>

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
        }
    </style>

<body>

<div class="container">
    <div class="row">
        <div class="jumbotron col-sm-6 col-sm-offset-3">
            <div class="row">
                <h1 class="col-sm-12" id='contenthead'> Viral Snippets </h1>
            </div>
            <div class="row" id='warnings'><?php session_start(); if(isset($_SESSION['USER-ID'])) header('Location: /project/index.php'); if(isset($_SESSION['ERROR'])) echo '<div class="alert alert-danger" role="alert">'.$_SESSION['ERROR'].'</div>'; unset($_SESSION['ERROR']); ?></div>
            <div class="row" id='content'>
                <form role="form" class="form-vertical" id='loginform' action='php/bridge.php' method='POST'>
                    <div class='form-group'>
                        <input id="email" class="form-control input-lg" type="email" placeholder="Email" name="email" />
                    </div>
                    <div class='form-group' id='fg-pass'>
                        <input type="password" class="form-control input-lg" id="password" placeholder="Password" name="password" />
                    </div>
                    <div class='form-group' id='fg-btn'>
                        <button type="submit" id='login' class="btn btn-primary btn-lg btn-block col-sm-12" />Login</button>
                        <button type="submit" id='subforgotpassw' class='btn btn-primary btn-lg btn-block col-sm-12'>Submit</button>
                    </div>
                </form>
                <div class='form-group' id='fg-link'>
                    <a class='' id='signup' href='signup.php'>Sign Up!</a>
                    <a class='pull-right' id='forgotpassw' href='#'>Forgot your password?</a>
                    <a class='pull-right' id='rtnlogin' href='#'>Login here</a>
                </div>
            </div>
        </div>
    </div>
</div>
         

</body>
<script>
    $( document ).ready(function() {
         var fields = {};

        $('a#forgotpassw').click(function(){
            $('h1#contenthead').html(' Password Recovery ');
            fields.password = $('input#password').detach();
            fields.loginbutton = $('button#login').detach();
            fields.subforgotpassw.appendTo('div#fg-btn');
            fields.rtnlogin.appendTo('div#fg-link');
            fields.forgotpassw = $('a#forgotpassw').detach();
        })

        $('a#rtnlogin').click(function(){
            $('h1#contenthead').html(' Login ');
            fields.password.appendTo('div#fg-pass');
            fields.loginbutton.appendTo('div#fg-btn');
            fields.subforgotpassw = $('button#subforgotpassw').detach();
            fields.rtnlogin = $('a#rtnlogin').detach();
            fields.forgotpassw.appendTo('div#fg-link');
        })


        fields.subforgotpassw = $('button#subforgotpassw').detach();
        fields.rtnlogin = $('a#rtnlogin').detach();
    });
   
 
</script>
</html>
