<?php

function getConnection() 
{
	$host = "ec2-54-83-204-159.compute-1.amazonaws.com";
	$username = "tfhrclfaivalxp";
	$password = "T9S53TjBmuENidtFglOR2X2MYO";
	$database = "dd7hp9quurpihj";
	
	$conn = null;
	
	try {
		$conn = new PDO("pgsql:host=$host;dbname=$database", $username, $password); 
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch(PDOException $e) {
		echo 'ERROR: ' . $e.getMessage();
	}
	
	return $conn;
}

function getAll($table = null, $order = 'name') {
	$sql = 'select * FROM '.$table.' ORDER BY ' . $order;
	try {
		$db = getConnection();
		$stamte = $db->query($sql);  
		$result = $stamte->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"data": ' . json_encode($result) . '}';
	} catch(PDOException $e) {
		echo '{"error":'. $e->getMessage() .'}'; 
	}
}

function getId($table = null, $id = null) {
	$sql = 'SELECT * FROM '.$table.' WHERE id=:id';
	try {
		$db = getConnection();
		$stamte = $db->prepare($sql);
		$stamte->bindParam('id', $id);
		$stamte->execute();
		$result = $stamte->fetchObject();
		$db = null;
		echo json_encode($result);
	} catch(PDOException $e) {
		echo '{"error":'. $e->getMessage() .'}';
	}
}

function search($table = null, $query = null, $order = 'name') {
	$sql = 'SELECT * FROM '.$table.' WHERE WHERE UPPER(name) LIKE :query ORDER BY ' . $order;
	try {
		$db = getConnection();
		$stamte = $db->prepare($sql);
		$query = "%".$query."%";
		$stamte->bindParam('query', $query);
		$stamte->execute();
		$result = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"data": ' . json_encode($result) . '}';
	} catch(PDOException $e) {
		echo '{"error":'. $e->getMessage() .'}';
	}
}

function insert($table = null) {
	$request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	$sql = 'INSERT INTO '.$table.' (name) VALUES (:name)';
	try {
		$db = getConnection();
		$stamte = $db->prepare($sql);
		$stamte->bindParam('name', $body->name);
		$stamte->execute();
		$result->id = $db->lastInsertId();
		$db = null;
		echo json_encode($result);
	} catch(PDOException $e) {
		echo '{"error":'. $e->getMessage() .'}';
	}
}

function update($table = null, $id = null) {
	$request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	$sql = 'UPDATE '.$table.' SET name=:name WHERE id=:id';
	try {
		$db = getConnection();
		$stamte = $db->prepare($sql);
		$stamte->bindParam('name', $body->name);
		$stamte->bindParam('id', $id);
		$stamte->execute();
		$db = null;
		echo json_encode($body);
	} catch(PDOException $e) {
		echo '{"error":'. $e->getMessage() .'}'; 
	}
}

function delete($table = null, $id = null) {
	$sql = 'DELETE FROM '.$table.' WHERE id=:id';
	try {
		$db = getConnection();
		$stamte = $db->prepare($sql);
		$stamte->bindParam('id', $id);
		$stamte->execute();
		$db = null;
	} catch(PDOException $e) {
		echo '{"error":'. $e->getMessage() .'}';
	}
}
