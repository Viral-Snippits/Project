<?php
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
			$headers = 'From: noreply@viralsnippets.com' . "\r\n" .
						    'Reply-To: noreply@viralsnippets.com'. "\r\n" .
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
				$stmt = $this->util->con->prepare('SELECT DISTINCT SUBSCRIPTIONS.SUBEE FROM SUBSCRIPTIONS WHERE SUBSCRIPTIONS.SUBER = ?');
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
?>