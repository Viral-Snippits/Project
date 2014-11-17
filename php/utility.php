<?php 

class utility {

	public $con;


	public function __construct(){

		
	}


	public function isJson($string) {
		json_decode($string);
	 	return (json_last_error() == JSON_ERROR_NONE);
	}
	

	public function mysql_connection($dbname, $host, $user, $pass){
		try {
			$this->con = new PDO('mysql:dbname='.$dbname.';host='.$host, $user, $pass);
			return true;
		}
		catch (PDOException $e) {
			return ('Database error' . $e);
		}
	}
	public function postgresql_connection($dbname, $host, $user, $pass){
		try {
			$this->con = new PDO('postgresql:dbname='.$dbname.';host='.$host, $user, $pass);
			return true;
		}
		catch (PDOException $e) {
			return ('Database error' . $e);
		}
	}
	public function generate_key(){
		$bytes = openssl_random_pseudo_bytes(25);
		$hex = bin2hex($bytes) . uniqid();

		return hash('sha256', $hex);
	}

	/*
		Insert data into a meta table and the corresponding practical table, or just 
		into the practical table. 

		We insert a meta record into the meta table, retreive its automatically
		generated ID, and then apply that ID to the corresponding other table's inserts.

		The incoming non-meta content needs to have the meta-table's foreign key FIRST.
	*/
	public function insert($_data)
	{
		try
		{
			$this->con->beginTransaction();

			if(isset($_data["meta"]) 					&& 
			   isset($_data["content"]) 				&&

			   isset($_data["meta"]["table_name"])		&&
			   isset($_data["meta"]["columns"])			&&
			   isset($_data["meta"]["values"])			&&

			   isset($_data["content"]["table_name"])	&&
			   isset($_data["content"]["columns"])		&&
			   isset($_data["content"]["values"])
			){

				/*============If everything is set============*/


				/*-------Meta Table Insertion-------*/

				//begin constructing the statement
				$meta_statement = 'INSERT INTO';
				$meta_statement.= ' '.$_data["meta"]["table_name"].' ';
				$meta_statement.= '(';

				//Add the column names, clip the final comma
				foreach($_data["meta"]["columns"] as $key=>$val)
				{
					$meta_statement.=$val.',';
				}
				$meta_statement = substr($meta_statement, 0, strlen($meta_statement)-1);

				//Add the value spots, clip the final comma
				$meta_statement.=') VALUES (';
				foreach($_data["meta"]["values"] as $key=>$val)
				{
					$meta_statement.='?,';
				}
				$meta_statement = substr($meta_statement, 0, strlen($meta_statement)-1);
				$meta_statement.=')';
				
				//Execute the statement
				$meta_statement = $this->con->prepare($meta_statement);
				if(!$meta_statement->execute($_data["meta"]["values"]))
				{
					throw new Exception("There was an error making the meta entry.");
				}


				//Get our last_insert_id
				$meta_statement = $this->con->prepare('SELECT LAST_INSERT_ID()');
				$meta_id = $meta_statement->execute() ? $meta_statement->fetch() : false;
				$meta_id = $meta_id[0];
				if(!$meta_id)
				{
					throw new Exception("There was an error retrieving the Id");
				}

				/*-------Meta Insertion End------*/


				/*-------Practical Insertion-------*/

				$statement = 'INSERT INTO';
				$statement.= ' '.$_data["content"]["table_name"].' ';
				$statement.= '(';

				//Add the column names, clip the final comma
				foreach($_data["content"]["columns"] as $key=>$val)
				{
					$statement.=$val.',';

				}
				$statement = substr($statement, 0, strlen($statement)-1);

				//Add the value spots, clip the final comma
				$statement.=') VALUES (';

				$statement.=$meta_id.',';
				foreach($_data["content"]["values"][0] as $key=>$val)
				{
						$statement.='?,';
				}
				$statement = substr($statement, 0, strlen($statement)-1);
				$statement.=')';

				//Execute the statement as many times as there are values
				$statement = $this->con->prepare($statement);
				foreach($_data["content"]["values"] as $key=>$val)
				{
					if(!$statement->execute($val))
					{
						throw new Exception("There was an error inserting the values.");
					}
				}

				/*-------Practical Insertion End-------*/

				//Close the Db connection
				$this->con->commit();
				return "Success";

			} else {
				throw new Exception("Incorrect input format.");
			}

		} catch(Exception $e)
		{
			$this->con->rollback();
			return $e->getMessage();
		}
	}
	/*
	Parameters:

		$_data => {
			TABLE=>someString,
			WANT=>{
				i=>columnName,
				...
			},
			GIVE=>{
				columnName=>columnValue,
				...
			},
			ORDERBY=>{
				COLUMNS=>{
					0:columnName,
					1:columnName,
					etc...
				}
			},
			LIMIT=>{
				FIRST=>firstInt,
				SECOND=>secondInt //Not required
			}
		}
	*/
	public function retrieve($_data){
		try{
			$query = "SELECT ";
			foreach($_data['WANT'] as $i=>$column)
				$query .= ''.$column.', ';
			$query = substr($query, 0, strlen($query)-2);
			$query .= " FROM ".$_data['TABLE'];
			if(isset($_data['GIVE'])){
				$query .= " WHERE ";
				$values = array();
				foreach($_data['GIVE'] as $column=>$value){
					$query .= $column.'=?, ';
					array_push($values, $value);
				}
				$query = substr($query, 0, strlen($query)-2);
			}
			if(isset($_data['ORDERBY'])){
				$query .= " ORDER BY ";
				foreach($_data['ORDERBY']['COLUMNS'] as $col){
					$query .= $col.", ";
				}
				$query = substr($query, 0, strlen($query)-2);
				$query .=' '.$_data['ORDERBY']['HOW'];
			}
			if(isset($_data['LIMIT'])){
				$query .= " LIMIT ".$_data['LIMIT']['FIRST'];
				if(isset($_data['SECOND']))
					$query .= ", ".$_data['SECOND'];
			}
			$stmt = $this->con->prepare($query);
			if(isset($values)){
				foreach($values as $key=>$val)
					$stmt->bindParam($key+1, $val);
			}
			if($stmt->execute() === true) return $stmt->fetchAll();
			else throw new Exception($stmt->errorCode());
		}catch(Exception $e){
			if($e->getMessage() === "42S02")
				return "Table does not exist";
			else
				return $e->getMessage();
		}
	}
	/*
		TABLE:
		WHAT:{
			key->value
		}
		GIVE:{
			key->value
		}
	*/
	public function update($_data){
		$this->con->beginTransaction();
		try{
			$query = "UPDATE ".$_data['TABLE']." SET ";
			$values = array();
			foreach($_data['WHAT'] as $column=>$value){
				$query .= $column.'=?, ';
				array_push($values, $value);
			}
			$query = substr($query, 0, strlen($query)-2);
			if(isset($_data['GIVE'])){
				$query .= " WHERE ";
				foreach($_data['GIVE'] as $column=>$value){
					$query .= $column.'=?, ';
					array_push($values, $value);
				}
				$query = substr($query, 0, strlen($query)-2);
			}
			$stmt = $this->con->prepare($query);
			if($stmt->execute($values)){
				$this->con->commit();
				return "Success";
			}
			else{
				$this->con->rollback();
				throw new Exception($stmt->errorCode());
			}
		}catch(Exception $e){
			$this->con->rollback();
			return $e->getMessage();
		}
	}

	/*
		Parameters:

			$_data => {
				TABLE=>someString,
				GIVE=>{
					columnName=>columnValue,
					...
				}
			}
	*/
	public function delete($_data){
		$this->con->beginTransaction();
		try{
			$query = "DELETE FROM ".$_data['TABLE']. " WHERE ";
			$columns = array();
			foreach($_data['GIVE'] as $column=>$value){
				$query .= $column.'=?, ';
				array_push($columns, $value);
			}
			$query = substr($query, 0, strlen($query)-2);
			$stmt = $this->con->prepare($query);
			
			if($stmt->execute($columns)){
				$this->con->commit();
				return "Success";
			}
			else{
				$this->con->rollback();
				throw new Exception($stmt->errorCode());
			}
		}catch(Exception $e){
			$this->con->rollback();
			return $e->getMessage();
		}
	}
}
?>