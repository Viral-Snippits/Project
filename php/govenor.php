<?php
	include 'utility.php';

	$data = array();
	if(isset($_POST['action'])) $data['action'] = $_POST['action']; else die("No action given.");
	if(isset($_POST['json'])) $data['json'] = $_POST['json']; else die("No action given.");
	if(isset($_POST['file'])) $data['file'] = $_POST['file'];
	//if(isset($_POST['']))

	new Governor($data);
	$rtn = '';
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

	class UserManager
	{
		private $util;

		public function __construct($_util){
			$this->util = $_util;
		}
		/*
			NEED:
					email
					password
		*/
		public function login($data){
			if(isset($data['email']) && isset($data['password'])){
				$profile = $this->util->retrieve(array(
					"TABLE"=>"USERS",
					"WANT"=>array("USERS.*"),
					"GIVE"=>array("USERS.EMAIL"=>$data['email'])
				));
				//print_r($profile);
				$profile = $profile[0];
				//check if results exist
				if(isset($data['password'])){
					$password = $data['password'].$profile['SALT'];
					//hash password with salt
					$password = hash('sha256', $password);
					//return compare passwords
					if($profile['HASH'] === $password)
						return $profile['USER_ID'];
					else
						return false;
				}
				else die("ERROR: Email does not exist");

			}
			else
				die('ERROR: Data missing');
		}

		public function post($data){
			if(isset($data['email']) && isset($data['password']) && isset($data['username'])){
				$salt = $this->util->generate_key();
				$password = $data['password'].$salt;
				$password = hash('sha256', $password);
				$stmt = $this->util->con->prepare('INSERT INTO USERS (EMAIL, HASH, SALT, USERNAME) VALUES (?, ?, ?, ?)');
				$stmt->execute(array($data['email'], $password, $salt, $data['username']));
			}
			else die("ERROR: Data missing");

		}

		public function put($data){
			$change = array();
			if(isset($data['email'])) $change['EMAIL'] = $data['email'];
			if(isset($data['username'])) $change['USERNAME'] = $data['username'];
			$this->util->update(
				array(
					"TABLE"=>"USERS",
					"WHAT"=>$change,
					"GIVE"=>array("USER_ID"=>$data['user_id'])
				)
			);
		}

		public function change_pass($data){
			print_r($data);
			if(isset($data['user_id']) && isset($data['password'])){
				$salt = $this->util->generate_key();
				$password = $data['password'].$salt;
				$password = hash('sha256', $password);
				$stmt = $this->util->con->prepare('UPDATE USERS SET HASH=?, SALT=? WHERE USER_ID=?');
				$stmt->execute(array($password, $salt, $data['user_id']));
			}
			else die('ERROR: Data missing');
		}

		public function forgot_pass($data){
			$ran_pass = "";
			for($i=0; $i<10; $i++){
				$ran_pass .= rand(0, 9);
			}
			$stmt = $this->util->con->prepare('SELECT USER_ID FROM USERS WHERE EMAIL = ?');
			$stmt->execute(array($data['email']));
			$res = $stmt->fetchAll();
			$res = $res[0]['USER_ID'];
			$this->change_pass(array("user_id"=>$res, "password"=>$ran_pass));
			$headers = 'From: noreply@bobville.org' . "\r\n" .
						    'Reply-To: noreply@bobville.org'. "\r\n" .
						    'X-Mailer: PHP/' . phpversion();
			mail($data['email'], "New Password", $ran_pass, $headers);
		}

		public function remove($data){
			$this->util->delete(array("TABLE"=>"USERS", "GIVE"=>array("USER_ID"=>$data['user_id'])));
		}

		public function addsub($data){
			if(isset($data['suber']) && isset($data['subee'])){
				$stmt = $this->util->con->prepare("INSERT INTO SUBSCRIPTIONS (SUBER, SUBEE) VALUES (?, ?)");
				$stmt->execute(array($data['suber'], $data['subee']));
			}
			else die('ERROR: Data missing');
		}

		public function remsub($data){
			if(isset($data['suber']) && isset($data['subee'])){
				$stmt = $this->util->con->prepare("DELETE FROM SUBSCRIPTIONS WHERE SUBER = ? AND SUBEE = ?");
				$stmt->execute(array($data['suber'], $data['subee']));
			}
			else die('ERROR: Data missing');
		}

		public function getsubs($data){
			if(isset($data['user_id'])){
				//print_r($data);
				$stmt = $this->util->con->prepare('SELECT SUBSCRIPTIONS.SUBEE FROM SUBSCRIPTIONS WHERE SUBSCRIPTIONS.SUBER = ?');
				$stmt->execute(array($data['user_id']));
				$subs = $stmt->fetchAll();
				//print_r($subs);
				$nsubs = array();
				foreach($subs as $key=>$val){
					unset($val[0]);
					$stmt = $this->util->con->prepare('SELECT USERNAME FROM USERS WHERE USER_ID = ?');
					$stmt->execute(array($val['SUBEE']));
					$unames = $stmt->fetchAll();
					//print_r($unames);
					$val['uname'] = $unames[0]['USERNAME'];
					array_push($nsubs, $val);
				}
				//print_r($nsubs);
				return json_encode($nsubs);

			}else die('ERROR: Data missing');
		}
	}

	class PostManager
	{
		private $util;

		public function __construct($_util){
			$this->util = $_util;
		}

		public function post($data){
			if(preg_match('^.*(<script>|<\/script>).*$', $data['title']) || preg_match('^.*(<script>|<\/script>).*$', $data['content']))
				die('NO XSS HERE');
			$stmt = $this->util->con->prepare("INSERT INTO POSTS (USER_ID, SOUND_ID, TITLE, CONTENT) VALUES (?, ?, ?, ?)");
			$stmt->execute(array($data['user_id'], $data['sound_id'], $data['title'], $data['content']));
		}

		public function get($data){
			switch($data['action']){
				case 'recent':
					$stmt = $this->util->con->prepare("SELECT * FROM POSTS ORDER BY POST_ID DESC LIMIT 50");
					$stmt->execute();
					$posts = $stmt->fetchAll();
					//print_r($posts);
					$nposts = array();
					foreach($posts as $key=>$val){
						unset($val[0]);
						unset($val[1]);
						unset($val[2]);
						unset($val[3]);
						unset($val[4]);
						unset($val[5]);
						$stmt = $this->util->con->prepare("SELECT USERNAME FROM USERS WHERE USER_ID = ?");
						$stmt->execute(array($val['USER_ID']));
						$uname = $stmt->fetchAll();
						$val['USERNAME'] = $uname[0]['USERNAME'];
						array_push($nposts, $val);
					}
					return json_encode($nposts);
				break;
				case 'user_id':
					$stmt = $this->util->con->prepare("SELECT * FROM POSTS WHERE USER_ID = ? ORDER BY POST_ID DESC LIMIT 50");
					$stmt->execute(array($data['user_id']));
					$posts = $stmt->fetchAll();
					$nposts = array();
					$stmt = $this->util->con->prepare("SELECT USERNAME FROM USERS WHERE USER_ID = ?");
					$stmt->execute(array($data['user_id']));
					$uname = $stmt->fetchAll();
					foreach($posts as $key=>$val){
						unset($val[0]);
						unset($val[1]);
						unset($val[2]);
						unset($val[3]);
						unset($val[4]);
						unset($val[5]);
						$val['USERNAME'] = $uname[0]['USERNAME'];
						array_push($nposts, $val);
					}
					return json_encode($nposts);
				break;
				case 'subs':
					$stmt = $this->util->con->prepare("SELECT SUBEE FROM SUBSCRIPTIONS WHERE SUBER = ?");
					$stmt->execute(array($data['user_id']));
					$subs = $stmt->fetchAll();
					$nposts = array();
					for($i=0; $i<(count($subs)); $i++){
						$stmt = $this->util->con->prepare("SELECT * FROM POSTS WHERE USER_ID= ? ORDER BY POST_ID DESC LIMIT 3");
						$stmt->execute(array($subs[$i][0]));
						$posts = $stmt->fetchAll();
						foreach($posts as $key=>$val){
							unset($val[0]);
							unset($val[1]);
							unset($val[2]);
							unset($val[3]);
							unset($val[4]);
							unset($val[5]);
							$stmt = $this->util->con->prepare("SELECT USERNAME FROM USERS WHERE USER_ID = ?");
							$stmt->execute(array($val['USER_ID']));
							$uname = $stmt->fetchAll();
							$val['USERNAME'] = $uname[0]['USERNAME'];
							array_push($nposts, $val);
						}
					}
					return json_encode($nposts);
				break;
			}
		}

		public function put($data){
			switch($data['action']){
				case 'rate':
					$stmt = $this->util->con->prepare('UPDATE POSTS SET RATING = RATING + 1 WHERE POST_ID = ?');
					$stmt->execute(array($data['post_id']));
				break;
			}
			
		}

		public function remove($data){
			$stmt = $this->util->con->prepare('DELETE FROM POSTS WHERE POST_ID = ?');
			$stmt->execute(array($data['post_id']));
		}
	}

	class CommentManager
	{
		private $util;

		public function __construct($_util){
			$this->util = $_util;
		}

		public function post($data){
			if(isset($data['user']) && isset($data['post']) && isset($data['content'])){
				$stmt = $this->util->con->prepare('INSERT INTO COMMENTS (USER_ID, POST_ID, CONTENT) VALUES (?, ?, ?)');
				$stmt->execute(array($data['user'], $data['post'], $data['content']));
			}else die('ERROR: Data missing');
		}
			

		public function get($data){
			$stmt = $this->util->con->prepare("SELECT * FROM COMMENTS WHERE POST_ID = ? ORDER BY ID DESC LIMIT 50");
			$stmt->execute(array($data['post_id']));
			return json_encode($stmt->fetchAll());
		}

		public function put($data){

		}

		public function remove($data){

		}
	}

	class SoundFileManager
	{
		private $util;

		public function __construct($_util){
			$this->util = $_util;
		}

		public function post($file){

		}

		public function remove($data){}
	}
?>