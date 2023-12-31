<?php
/**
*	Database library
*	@author Agus Prawoto Hadi
*	@website https://jagowebdev.com
* 	@copyright 2021
*/
class Database
{
	private $pdo;
	private $stmt;
	private $trans_status;
	private $trans_message;
	
	public function __construct() {
		$this->connect();
	}
	
	private function connect() 
	{
		global $database;
		if ($database['driver'] == 'PDO') 
		{
			try {
				$this->pdo = new PDO('mysql:host=localhost;port=' . $database['port'] . ';dbname=' . $database['database'], $database['username'], $database['password']);
				$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch (PDOException $e) {
			
				include ('dbException.php');
				throw new dbException ($e, '<strong>Koneksi Error</strong> : Tidak dapat terhubung dengan server MySQL');
			}
		}
		return $this;
	}
	
	public function query($sql, $data = null) 
	{
		if ($data && !is_array($data)) {
			$data = [$data];
		}
		

		$this->trans_status = true;
		try {
			$this->stmt = $this->pdo->prepare($sql);
			
			/* if ($data) {
				foreach($data as $key => $val) {
					if (is_numeric($val)) {
						
						$this->stmt->bindValue($key + 1, $val, PDO::PARAM_INT);
					} else {
						$this->stmt->bindValue($key + 1, $val, PDO::PARAM_STR);
					}
				}
			} */
			// $exec = $this->stmt->execute();
			
			global $current_module;
			// if (strpos($sql, 'SELECT') !== false || $current_module['nama_module'] == 'setting') {
				$exec = $this->stmt->execute($data);
			// }

		} catch (PDOException $e) {
			
			$this->trans_message = $e->getMessage();
			include ('dbException.php');
			throw new dbException ($e, '<strong>SQL Query</strong> :' . $sql);
			
			$this->trans_status = false;
		}
		
		/* if (!$exec) {
			$this->trans_status = false;
		} */
		return $this;
	}
	
	public function fetch() {
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	public function result($type = 'assoc') {
		switch ($type) {
			case 'assoc':
				$fetch_type = PDO::FETCH_ASSOC;
				break;
			case 'object':
				$fetch_type = PDO::FETCH_OBJ;
				break;
		}
		
		return $this->stmt->fetchAll($fetch_type);
	}
	
	public function getResultArray($type = 'assoc') 
	{
		switch ($type) {
			case 'assoc':
				$fetch_type = PDO::FETCH_ASSOC;
				break;
			case 'object':
				$fetch_type = PDO::FETCH_OBJ;
				break;
			case 'num':
				$fetch_type = PDO::FETCH_NUM;
				break;
		}
		
		return $this->stmt->fetchAll($fetch_type);
	}
	
	public function row($type = 'assoc') {
		switch ($type) {
			case 'assoc':
				$fetch_type = PDO::FETCH_ASSOC;
				break;
			case 'object':
				$fetch_type = PDO::FETCH_OBJ;
				break;
		}
		
		return $this->stmt->fetch($fetch_type);
	}
	
	public function getRowArray($type = 'assoc') {
		
		switch ($type) {
			case 'assoc':
				$fetch_type = PDO::FETCH_ASSOC;
				break;
			case 'object':
				$fetch_type = PDO::FETCH_OBJ;
				break;
		}
		return $this->stmt->fetch($fetch_type);
	}
	
	public function beginTrans() {
		$this->pdo->beginTransaction();
	}
	
	public function commitTrans() 
	{
		return $this->pdo->commit();
	}
	
	public function rollbackTrans() 
	{
		return $this->pdo->rollBack();
	}
	
	public function completeTrans() 
	{
		if ($this->trans_status) {
			$this->pdo->commit();
			return true;
		} else {
			$this->pdo->rollBack();
			return false;
		}
	}
	
	public function insert($table, $data) 
	{
		$column = join(',', array_keys($data));
		foreach ($data as $v) {
			$q[] = '?';
		}
		$value_mask = join(',', $q);

		$sql = 'INSERT INTO ' . $table . '(' . $column . ') VALUES (' . $value_mask . ')';
		$this->query($sql, array_values($data));
		return $this->trans_status;
	}
	
	/*
	Contoh data :
	Array
(
    [0] => Array
        (
            [nama_penghadap] => Ahmad
            [gelar_depan] => Drs.
            [gelar_belakang] => S.H. MBA
            [jenis_kelamin] => L
        )

    [1] => Array
        (
            [nama_penghadap] => Imroni
            [gelar_depan] => Ir.
            [gelar_belakang] => 
            [jenis_kelamin] => L
        )

    [2] => Array
        (
            [nama_penghadap] => Dian
            [gelar_depan] => 
            [gelar_belakang] => 
            [jenis_kelamin] => L
        )

)
		*/
	public function insertBatch($table, $data) 
	{
		$column = join(',', array_keys($data[0]));
		foreach ($data as $arr) {
			$mask = [];
			foreach ($arr as $val) {
				$mask[] = '?';
				$values[] = $val;
			}
			$value_mask[] = '(' . join(',', $mask) . ')';
		}
		$value_mask = join(',', $value_mask);

		$sql = 'INSERT INTO ' . $table . '(' . $column . ') VALUES ' . $value_mask;
		// echo $sql; die;
		$this->query($sql, $values);
		return $this->trans_status;
	}
	
	public function getField($field_name, $database_name = false) 
	{
		global $database;
		if (!$database_name) {
			$database_name = $database['database'];
		}
		
		$sql  = 'SELECT column_name, data_type from INFORMATION_SCHEMA.COLUMNS where
				table_schema = "' . $database_name . '" and table_name = "' . $field_name  . '"';
			
		$query = $this->query($sql);
		$result = [];
		if ($query) {
			while($row = $this->fetch($query)) {
				$result[$row['column_name']] = $row;
			}
		}
		return $result;
	}
	
	public function update($table, $data, $where = false) 
	{
		foreach ($data as $field => $val) {
			$set[] = $field . ' = ?';
		}

		if ($where) {
			
			if (is_array($where)) 
			{ 
				$str_where = [];
				foreach ($where as $field => $val) {
					$str_where[] = $field . ' = ? ';  
				}
				
				$str_where = join(' AND ', $str_where); 
				$data = array_merge(array_values($data), array_values($where));
				
			} else {
			
				$str_where = $where;
			}
		}
		
		$add_where = $where ? ' WHERE ' . $str_where : '';
		$sql = 'UPDATE ' . $table . ' SET ' . join(',', $set) . $add_where;
		
		$this->query($sql, array_values($data));
		return $this->trans_status;
	}
	
	public function delete($table, $where = false) 
	{
		$data_where = [];
		
		$str_where = '';
		if ($where) {
			if (is_array($where)) 
			{ 
				$arr_where = [];
				foreach ($where as $field => $val) {
					$arr_where[] = $field . ' = ? ';  
				}
				
				$str_where = join(' AND ', $arr_where); 
				$data_where = array_values($where);
				
			} else {
			
				$str_where = $where;
			}
		}
		
		$sql_where = $str_where ? ' WHERE ' . $str_where : '';
		
		$sql = 'DELETE FROM ' . $table . $sql_where;
		$this->query($sql, $data_where);
		return $this->trans_status;
	}
	
	public function truncate($table){
		$sql = 'TRUNCATE TABLE ' . $table;
		$query = $this->query($sql);
		return $this->trans_status;
	}
	
	public function lastInsertId() {
		return $this->pdo->lastInsertId();
	}
}