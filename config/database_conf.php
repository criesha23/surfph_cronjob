<?php
/****************************************************************************
File: database_conf.php
Author: Criesha Ann Borbajo



*****************************************************************************/
class Query{
	private static $dbh = null;
	
    public static function getPDO(){
	 if (is_null(self::$dbh)){
		self::$dbh = new PDO('mysql:host=localhost;dbname=db_surf_ph', 'root', '',
			   array(PDO::ATTR_PERSISTENT => false, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC)); 
	 }
	 return self::$dbh;
    }
	public static function pdo(){ return self::getPDO(); }
	//this will be the default function for all select statement
	public static function Query_selectStatement($fields,$table){
		$sql = "SELECT ".$fields." FROM ".$table;
		try{
			$sth = self::getPDO()->prepare($sql);
			$sth->execute();
			$result=$sth->fetchAll();
			return $result;
         }catch(PDOException $e){
             echo $e->getMessage();
              return FALSE;
         }	
	}
	//this will be the default function select statement with condition	
	public static function Query_selectStatementWhere($fields,$table,$where){
		$sql = "SELECT ".$fields." FROM ".$table." WHERE ".$where;
		try{
			$sth = self::getPDO()->prepare($sql);
			$sth->execute();
			$result=$sth->fetchAll();
			return $result;
         }catch(PDOException $e){
             echo $e->getMessage();
              return FALSE;
         }	
	}
	//this will be the default function for insert statement
	public static function Query_insertStatement($table, $fieldNames, $values){
		$sql = "INSERT INTO ".$table." (".$fieldNames.") VALUES(".$values.")";
		try{
			$sth = self::getPDO()->prepare($sql);
			$sth->execute();
			return TRUE;
	    }catch(PDOException $e){
	         echo $e->getMessage();
	          return FALSE;
	    }
	}
	//this will be the default function for update statement
	public static function Query_updateStatement($table, $fieldVal, $where){
		$sql = "UPDATE ".$table." SET ".$fieldVal." WHERE ".$where;
		try{
			$sth = self::getPDO()->prepare($sql);
			$sth->execute();
			return TRUE;
	    }catch(PDOException $e){
	         echo $e->getMessage();
	          return FALSE;
	    }

	}
	//this will be the default function for delete statement
	public static function Query_deleteStatement($table, $where){
		echo $sql = "DELETE FROM ".$table." WHERE ".$where;
		try{
			$sth = self::getPDO()->prepare($sql);
			$sth->execute();
			return TRUE;
	    }catch(PDOException $e){
	         echo $e->getMessage();
	          return FALSE;
	    }
	}
}
$surfPh = new Query;
?>