<?php
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
			$stmt = $this->util->con->prepare("SELECT COMMENTS.*, USERS.USERNAME FROM COMMENTS, USERS WHERE POST_ID = ? AND COMMENTS.USER_ID = USERS.USER_ID ORDER BY ID DESC LIMIT 50");
			$stmt->execute(array($data['post_id']));
			return json_encode($stmt->fetchAll());
		}

		public function put($data){

		}

		public function remove($data){

		}
	}
?>