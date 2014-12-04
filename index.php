<?php
  session_start();
	if(!isset($_SESSION['USER-ID'])){
    $_SESSION['ERROR'] = 'Please log in again';
		header('Location: /project/login.php');
	}
	if(isset($_GET['action'])){
		switch($_GET['action']){
			case 'signout':
				unset($_SESSION['USER-ID']);
        $_SESSION['ERROR'] = 'Thank you for using viral snippets';
        header('Location: /project/login.php');
			break;
		}
	}
  if(isset($_SESSION['ERROR'])) unset($_SESSION['ERROR']);
?>

<html>
	<head>
    <link rel="stylesheet" type='text/css' href='css/bootstrap.min.css'>
    <script src='js/bootstrap.min.js'></script>
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    <link rel="stylesheet" type='text/css' href='css/bootstrap.min.css'>
    <script src='js/bootstrap.min.js'></script>
  	<style>
        .sub{

      	}
        h1{
          color:white;
        }
      	#bullet{
      		list-style-type: none;
      		padding:10px;
      		font-size: 17px;
      	}
      	#content{
      		padding: 10px;
      	}

       .panel-heading{
          font-size: 200%
        }

        #deats{
        }

        body{
            background-image: url("back.jpg");
            /*background-repeat: no-repeat;*/
            background-attachment: fixed;
        }
    
  	</style>
	</head>

	<body>
  <input type='hidden' id='u-id' value='<?php echo $_SESSION['USER-ID']; ?>' />
	<nav class="navbar navbar-default" role="navigation" style="margin-bottom: 0px;">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/project/index.php">Viral Snippets</span></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="#" id='global'>Global Feed</a></li>
        <li><a href="#" id='subs'>Subscriptions</a></li>
        <li><a href="#" id='profile' data-id='<?php echo $_SESSION['USER-ID']; ?>'>My Profile</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="/project/index.php?action=signout">Sign Out</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->

</nav>
<div class="col-xs-1"></div>
<div class = "subs col-xs-2">
	
  <ul id="bullet">
		<li style = "padding-bottom: 8px;"><h1 style="font-size: 30px;">Subscriptions</h1></li>
	</ul>
	</div>
	<div class= "col-xs-6">
    <!--APPEND POSTS HERE -->
    <div><h1 id='content_head'>Global Feed</h1></div>
    <div id="content">
      <div class="panel panel-default">
        <div class="panel-heading"><span class="glyphicon glyphicon-remove"></span>Delete<b>Sound file title</b><span class='pull-right'><a href='#' data-value='3'> cody_richards</a></span></div>
        <div class="panel-body"> Sound file description</div>
        <div class= "panel-footer">
            <button type="button" class="btn btn-primary btn" id='getcomments' data-toggle="modal" data-target="#myModal">Show Comments</button>
            <div class="pull-right" style="font-size: 18px;  padding: 5px;">10</div>
            <button type="button" class="btn btn-primary btn pull-right">Rate</button>
        </div>
      </div>
    </div>
    <!--END POSTS-->
  </div>
  <div class='col-xs-2' id='deats'>
      <div class='row'></div>
      <h1> Actions </h1>
      <div class='btn-group-vertical'>
        <a href='#' id='makepost' class='btn btn-success btn-lg btn-block col-sm-12'>Post a Snippet!</a>
        <div id='postfields'></div>
        <a href='#' id='changepass' class='btn btn-warning btn-lg btn-block col-sm-12'>Change Password</a>
        <div id='passfields'></div>
        <a href='#' id='deleteacct' class='btn btn-danger btn-lg btn-block col-sm-12'>Delete Account</a>
      </div>
  </div>
    <div class="col-xs-1"></div>

<!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"></span>
            </button>
            <h4 class="modal-title" id="myModalLabel">User Comments</h4>
          </div>
          <div class="modal-body" id='commentsHolder'>
            
          </div>
          <div class="modal-footer">
            <textarea placeholder='Enter your comment here...' id='theComment' style='width:100%;'></textarea>
            <button type="button" class="btn btn-default" id='addcomment'>Submit</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

  </div>

	</body>
  <script src='js/custom_refactor.js'></script>
</html>
 