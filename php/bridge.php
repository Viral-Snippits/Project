<?php
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email']) && isset($_POST['cpassword'])){
		if($_POST['password'] !== $_POST['cpassword']){
			session_start();
			$_SESSION['error'] = 'Passwords must match.';
			header('Location: /project/signup.php');
		} else if(!preg_match("/[a-z|0-9|A-Z]+@+[a-z|A-Z]+.+[a-z]/", $_POST['email'])){
			session_start();
			$_SESSION['error'] = 'Email must be valid.';
			header('Location: /project/signup.php');
		} else {
			$_POST['action'] = "new_user";
			$_POST['json'] = array('username'=>$_POST['username'], 'password'=>$_POST['password'], 'email'=>$_POST['email']);
			include 'govenor.php';
			session_start();
			$_SESSION['acctMade'] = '<div class="alert alert-success">Account Made! Please Login.</div>';
			header('Location: /project/login.php');
		}
	}
	else if(isset($_POST['password']) && isset($_POST['email'])){
		$_POST['action'] = "login";
		$_POST['json'] = array("email"=>$_POST['email'], "password"=>$_POST['password']);
		include 'govenor.php';
		if($_SESSION['USER-ID'] !== false){
			//echo $rtn;
			//$_SESSION['USER-ID'] = $rtn;
			echo $_SESSION['USER-ID'];
			header('Location: /project/index.php');
		}
		else{
			//session_start();
			if(isset($_SESSION['USER-ID']))
				unset($_SESSION['USER-ID']);
			$_SESSION['ERROR'] = "Account not valid!";
			header('Location: /project/login.php');
		}
	}
	else if(isset($_POST['email'])){
		$_POST['action'] = "recover_password";
		$_POST['json'] = array('email'=>$_POST['email']);
		include 'govenor.php';
		session_start();
		$_SESSION['ERROR'] = 'Your new password has been sent to '.$_POST['email'];
		header('Location: /project/login.php');
	}
	else if(isset($_FILES['soundfile']) && isset($_POST['title']) && isset($_POST['content']) && isset($_POST['user-id'])){
		$_POST['json']['title'] = $_POST['title'];
		$_POST['json']['content'] = $_POST['content'];
		$_POST['json']['user-id'] = $_POST['user-id'];
		$_POST['json']['soundfile'] = $_FILES['soundfile'];
		$_POST['action'] = 'new_post';
		include 'govenor.php';
		header('Location: /project/index.php');
	}
?>