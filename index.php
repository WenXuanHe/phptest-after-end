<?php 
	header("ALLOW-CONTROL-ALLOW-ORIGIN:*");
	header("Content-type':text/html;charset=utf-8");

	class ConnectSQL{
		////protected，当前类和子类都可以访问，如设置为private，则子类不能访问
		protected $dbName;
		protected $tbName;

		function __construct($dbName, $tbName){
			$this->dbName = $dbName;
			$this->tbName = $tbName;
		}
		
		public function Open_mysql($sql){
			$con = mysql_connect('localhost', 'root', '');
			mysql_select_db($this->dbName, $con);
			mysql_query("set names 'utf8'");
			$result = mysql_query($sql, $con); 
			return $result;
		}
	}

	class QuerySQL extends ConnectSQL{
		private $needSelect = ['id','name', 'age', 'sex', 'birth', 'company', 'income'];

		function __construct($dbName, $tbName){
			parent::__construct($dbName, $tbName);
		}

		public function QueryByName($name){
			$need = implode(",",$this->needSelect);
			$sql = "SELECT ".$need." FROM ".$this->tbName." WHERE 1";
			if($name != ''){
				$sql = "SELECT ".$need." FROM ".$this->tbName." WHERE name like '%".$name."%'";
			}
			$result = parent::Open_mysql($sql);
			$array = array();
			while($row=mysql_fetch_array($result)){
				$item = array();
				
				foreach ($this->needSelect as $value) {
					$item[$value] = $row[$value];
				}
				// $item['count'] = $row['count(*)'];	
				array_push($array, $item);
			}
			return json_encode($array);	
		}
	}
	
	class DeleteSQL extends ConnectSQL{
		function __construct($dbName, $tbName){
			parent::__construct($dbName, $tbName);
		}

		public function DeleteById($id){
			$sql = "DELETE FROM ".$this->tbName." WHERE id=".$id;
			$result = parent::Open_mysql($sql);
			$res['status'] = $result;
			return json_encode($res);
		}
	}
	
	class InsertSQL extends ConnectSQL{
		function __construct($dbName, $tbName){
			parent::__construct($dbName, $tbName);
		}

		public function InsertMember($name, $age, $company, $income, $birth, $sex){
			$res = array();
			if($name == ''){
				$res['status'] = false; 
			}else{
				$sql = "INSERT INTO ".$this->tbName." (name,age,company,income,birth,sex) VALUES ('".$name."','".$age."','".$company."','".$income."','".$birth."','".$sex."')";
				$result = parent::Open_mysql($sql);
				$res['status'] = $result;
			}
			
			return json_encode($res);
		}
	}
	
	$serviceType = $_REQUEST['serviceInvoke'];
	$result;
	if($serviceType == "search"){
		$querySQL = new QuerySQL("phptest", "home");
		$name = $_REQUEST['name'];
		$result = $querySQL->QueryByName($name);
	}else if ($serviceType == "add"){
		$insertSQL = new InsertSQL("phptest", "home");
		$name = $_REQUEST['name'];
		$age = $_REQUEST['age'];
		$company = $_REQUEST['company'];
		$income = $_REQUEST['income'];
		$birth = $_REQUEST['birth'];
		$sex = $_REQUEST['sex'];
		$result = $insertSQL->InsertMember($name, $age, $company,$income,$birth,$sex);
	}else if ($serviceType == "delete"){
		$deleteSQL = new DeleteSQL("phptest", "home");
		$id = $_REQUEST['id'];
		$result = $deleteSQL->DeleteById($id);
	}
	echo $result;
 ?>