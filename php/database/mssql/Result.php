<?php
	class Result{
		private $result;
		
		public function Result($result){
			$this->result = $result;
		}
		
		public function dataSeek($row){
			return mssql_data_seek($this->result,$row);
		}
		public function fieldSeek($field){
			return mssql_field_seek($this->result,$field);
		}
		public function fetchAssociative(){
			return mssql_fetch_assoc($this->result);
		}
		public function fetchArray($type){
			return mssql_fetch_array($this->result,$type);
		}
		public function fetchObject($classname='',$params=array()){
			$list = array();
			$list[] = $this->result;
			if($classname) $list[] = $classname;
			if(count($params)) $list[] = $params;
			
			return call_user_func_array("mssql_fetch_object",$list);
		}
		public function fetchRow(){
			return mssql_fetch_row($this->result);
		}
		public function fetchField($offset=false){
			if($offset === false) unset($offset);
			return mssql_fetch_field($this->result,$offset);
		}
		public function getFieldType($offset){
			return mssql_field_type($this->result,$offset);
		}
		public function getFieldLength($field){
			return mssql_field_length($this->result,$field);
		}
		public function getFieldName($field){
			return mssql_field_name($this->result,$field);
		}
		public function getFieldCount(){
			return mssql_num_fields($this->result);
		}
		public function getRowCount(){
			return mssql_num_rows($this->result);
		}
		public function getResultInfo($row,$field=0){
			return mssql_result($this->result,$row,$field);
		}
		
		public function freeResult(){
			return mssql_free_result($this->result);
		}
		public function cleanUp(){
			return $this->freeResult();
		}
		
		//php crap
		public function __destructor(){
			return $this->cleanUp();
		}
	}
?>