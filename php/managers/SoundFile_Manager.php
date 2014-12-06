<?php
	class SoundFileManager
	{
		private $util;

		public function __construct($_util){
			$this->util = $_util;
		}

		public function get($id){
			$stmt = $this->util->con->prepare('SELECT `PATH` FROM SOUNDFILES WHERE SOUND_ID = ?');
			$stmt->execute(array($id));
			$ret = $stmt->fetchAll();
			return $ret[0][0];
		}

		public function post($file, $uid){
			$target_dir = "/var/www/html/project/php/managers/uploads/";
			//$target_file = $target_dir . basename($file["name"]);
			echo $file['type'];
			if($file['type'] === 'audio/mpeg3')
				$target_file = $target_dir . hash('sha256', $file['name'].time()) . '.mp3';
			else if($file['type'] === 'audio/mpeg')
				$target_file = $target_dir . hash('sha256', $file['name'].time()) . '.mp3';
			else if($file['type'] === 'audio/wav')
				$target_file = $target_dir . hash('sha256', $file['name'].time()) . '.wav';
			$uploadOk = 1;
			$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			// Check if image file is a actual image or fake image
			if(isset($_POST["submit"])) {
			    $check = getimagesize($file["tmp_name"]);
			    if($check !== false) {
			        echo "File is an image - " . $check["mime"] . ".";
			        $uploadOk = 1;
			    } else {
			        echo "File is not an image.";
			        $uploadOk = 0;
			    }
			}
			// Check if file already exists
			if (file_exists($target_file)) {
			    echo "Sorry, file already exists.";
			    $uploadOk = 0;
			}
			// Check file size
			if ($file["size"] > 500000) {
			    echo "Sorry, your file is too large.";
			    $uploadOk = 0;
			}
			// Allow certain file formats
			if($imageFileType != "mp3" && $imageFileType != "wav") {
			    echo "Sorry, only MP3 and WAV files are allowed.";
			    $uploadOk = 0;
			}
			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
			    echo "Sorry, your file was not uploaded.";
			// if everything is ok, try to upload file
			} else {
			    if (move_uploaded_file($file["tmp_name"], $target_file)) {
			        $stmt = $this->util->con->prepare('INSERT INTO SOUNDFILES (`USER_ID`,`PATH`) VALUES (?, ?)');
					if($stmt->execute(array($uid, $target_file)) !== true) die('Upload error');
					$stmt = $this->util->con->prepare('SELECT SOUND_ID FROM SOUNDFILES WHERE `PATH`=?');
					if($stmt->execute(array($target_file)) !== true) die ('Upload error');
					$res = $stmt->fetchAll();
					$res = $res[0][0];
					echo json_encode($res);
					return $res;
			    } else {
			        echo "Sorry, there was an error uploading your file.";
			    }
			}
			
		}

		public function remove($data){}
	}

?>