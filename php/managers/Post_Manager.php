<?php
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
					$stmt = $this->util->con->prepare("SELECT * FROM POSTS ORDER BY RATING DESC LIMIT 50");
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
?>