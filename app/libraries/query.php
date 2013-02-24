<?php
/*
http://www.designosis.com/PDO_class/
*/

class query extends PDO {

	private $error = '';
	public  $querycount = 0;

	public function __construct($dsn, $user='', $passwd='') {
		$options = array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_PERSISTENT => true
		);
		try {
			parent::__construct($dsn, $user, $passwd, $options);
		} catch (PDOException $e) {
			$this->error = $e->getMessage();
		}
	}

	public function run($query, $bind=false, $handler=false) {
		$this->querycount++;
		try {
			if ($bind !== false) {
				$bind = (array) $bind;
				$dbh = $this->prepare( trim($query) );
				$dbh->execute( $bind );
			} else {
				$dbh = $this->query( trim($query) ); // because query is 3x faster than prepare+execute
			}
			if (preg_match('/^(select|describe|pragma)/i', $query)) {
				// if $query begins with select|describe|pragma, either return handler or fetch
				return ($handler) ? $dbh : $dbh->fetchAll();
			} else if (preg_match('/^(delete|insert|update)/i', $query)) {
				// if $query begins with delete|insert|update, return count
				return $dbh->rowCount();
			} else {
				return true;
			}
		} catch (PDOException $e) {
			$this->error = $e->getMessage();
			return false;
		}
	}

	private function prepBind($pairs, $glue) {
		$parts = array();
		foreach ($pairs as $k=>$v) { $parts[] = "`$k` = ?"; }
		return implode($glue, $parts);
	}

	public function update($table, $data, $where, $limit=false) {
		if (is_array($data) && is_array($where)) {

			$dataStr  = $this->prepBind( $data, ', ' );
			$whereStr = $this->prepBind( $where, ' AND ' );
			$bind = array_merge( $data, $where );
			$bind = array_values( $bind );

			$sql = "UPDATE `$table` SET $dataStr WHERE $whereStr";
			if ($limit && is_int($limit)) { $sql .= ' LIMIT '. $limit; }
			return $this->run($sql, $bind);
		}
		return false;
	}

	public function insert($table, $data) {
		if (is_array($data)) {

			$dataStr = $this->prepBind( $data, ', ' );
			$bind = array_values( $data );

			$sql = "INSERT `$table` SET  $dataStr";
			return $this->run($sql, $bind);
		}
		return false;
	}

	public function delete($table, $where, $limit=false) {
		if (is_array($where)) {

			$whereStr = $this->prepBind( $where, ' AND ' );
			$bind = array_values( $where );

			$sql = "DELETE FROM `$table` WHERE $whereStr";
			if ($limit && is_int($limit)) { $sql .= ' LIMIT '. $limit; }
			return $this->run($sql, $bind);
		}
		return false;
	}
	
	public function escape($input) {
		return $this->quote($input);
	}

}