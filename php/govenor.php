<?php
	include 'utility.php';
	foreach (glob("managers/*.php") as $filename)
		include $filename;

	/*
		Data conversion into anticipated data from POST variables
	*/
	$data = array();
	if(isset($_POST['action'])) $data['action'] = $_POST['action']; else die("No action given.");
	if(isset($_POST['json'])) $data['json'] = $_POST['json']; else die("No json given.");
	if(isset($_POST['file'])) $data['file'] = $_POST['file'];
	//if(isset($_POST['']))

	/*
		Instantiate the Governor object
	*/
	new Governor($data);
	$rtn = '';

	/*
		
	*/
	class Governor
	{
		private $util, $user, $post, $comment, $file;

		public function __construct($_data){
			$this->util = new utility();
			$this->util->mysql_connection('SOUND_MEDIA', 'localhost','root','Apple A Day');
			$this->user = new UserManager($this->util);
			$this->post = new PostManager($this->util);
			$this->comment = new CommentManager($this->util);
			$this->file = new SoundFileManager($this->util);

			switch($_data['action']){
				
				case "login":
					session_start();
					$_SESSION['USER-ID'] = $this->user->login($_data['json']);//
				break;
				case "new_user":
					$rtn = $this->user->post($_data['json']);//
				break;
				case "new_post":
					$path = $this->file->post($_data['file']);//
					$this->post->post($_data['json']);//
				break;
				case "new_comment":
					$this->comment->post($_data['json']);//
				break;
				case "new_file":
					echo $this->file->post($_data['json']);//
				break;
				case "subscribe":
					$this->user->addsub($_data['json']);
				break;
				case "unsubscribe":
					$this->user->remsub($_data['json']);
				break;
				case "get_subscriptions":
					echo $this->user->getsubs($_data['json']);
				break;
				case "get_posts":
					echo $this->post->get($_data['json']);//
				break;
				case "get_comments":
					echo $this->comment->get($_data['json']);//
				break;
				case "change_post":
					$this->post->put($_data['json']);//
				break;
				case "rate":
					$this->post->put($_data['json']);
				break;
				case "change_comment":
					$this->comment->put($_data['json']);//
				break;
				case "change_user":
					$this->user->put($_data['json']);//
				break;
				case "change_password":
					$this->user->change_pass($_data['json']);//
				break;
				case "recover_password":
					$this->user->forgot_pass($_data['json']);//
				break;
				case "delete_account":
					$this->user->remove($_data['json']);//
				break;
				case "delete_post":
					$this->post->remove($_data['json']);//
				break;
				case "delete_comment":
					$this->comment->remove($_data['json']);//
				break;
			}

		}
	}
?>