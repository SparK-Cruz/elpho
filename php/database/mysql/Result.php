<?php
	class Result{
		private $result;
		
		public function Result($result){
			$this->result = $result;
		}
		
		public function fetchArray($type){
			return mysql_fetch_array($this->result,$type);
		}
		public function dataSeek($row){
			return mysql_data_seek($this->result,$row);
		}
		public function fetchAssociative(){
			return mysql_fetch_assoc($this->result);
		}
		public function fetchField($field){
			return mysql_fetch_field($this->result,$field);
		}
		public function fetchLengths(){
			return mysql_fetch_lengths($this->result);
		}
		public function fetchObject($classname='',$params=array()){
			$list = array();
			$list[] = $this->result;
			if($classname) $list[] = $classname;
			if(count($params)) $list[] = $params;
			
			return call_user_func_array("mysql_fetch_object",$list);
		}
		public function fetchRow(){
			return mysql_fetch_row($this->result);
		}
		public function getFieldFlags($field){
			return mysql_field_flags($this->result,$field);
		}
		public function getFieldLength($field){
			return mysql_field_len($this->result,$field);
		}
		public function getFieldName($field){
			return mysql_field_name($this->result,$field);
		}
		public function getFieldSeek($field){
			return mysql_field_seek($this->result,$field);
		}
		public function getFieldTable($field){
			return mysql_field_table($this->result,$field);
		}
		public function getFieldType($field){
			return mysql_field_type($this->result,$field);
		}
		public function getFieldCount(){
			return mysql_num_fields($this->result);
		}
		public function getRowCount(){
			return mysql_num_rows($this->result);
		}
		public function getResultInfo($row,$field=0){
			return mysql_result($this->result,$row,$field);
		}
		public function getTableName($index){
			return mysql_tablename($this->result,$index);
		}
		
		public function freeResult(){
			return mysql_free_result($this->result);
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