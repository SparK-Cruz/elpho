<?php
	import(php.lang.String);
	import(php.database.mssql.Result);
	
	/**
	 * Classe de Conexão com banco de dados
	 * implementada com o objetivo de substituir as funções mssql_ nativas e
	 * gerenciar o resource de conexão de forma limpa
	 * @author Roger 'SparK' Rodrigues da Cruz
	 * @package php.database.mssql
	 */
	class Connection{
		/**
		 * Atributo que quarda o link para o resource a ser usado
		 */
		private $link;
		
		/**
		 * Construtor, utiliza a mesma sintaxe do metodo connect
		 * @see connect
		 */
		public function Connection($server=false,$user=false,$pass=false,$flags=0){
			$this->connect($server,$user,$pass,$flags);
		}
		
		/**
		 * Inicializa uma conexão com o banco de dados
		 * @param string $server endereço ou nome do servidor
		 * @param string $user usuário do banco de dados a ser autenticado
		 * @param string $pass senha para autenticação
		 */
		public function connect($server=false,$user=false,$pass=false){
			$this->close();
			$this->link = @mssql_connect($server,$user,$pass,true);
			if(!$this->link) $this->throwError("Failed to connect. Check the host address, connectivity and passwords.","c00");
		}
		
		/**
		 * Inicializa uma conexão persistente com o banco de dados
		 * @param string $server endereço ou nome do servidor
		 * @param string $user usuário do banco de dados a ser autenticado
		 * @param string $pass senha para autenticação
		 */
		public function connectPersistent($server=false,$user=false,$pass=false){
			$this->close();
			$this->link = mssql_pconnect($server,$user,$pass);
			if(!$this->link) $this->throwError();
		}
		
		/**
		 * Fecha conexão com o banco de dados
		 * @return bool
		 */
		public function close(){
			if(!$this->link) return false;
			return @mssql_close($this->link);
		}
		
		/**
		 * Envia pedido de execução de comando para o banco de dados
		 * @param string $query linha de comando a ser executada
		 * @return Result|bool
		 */
		public function query($query){
			$result = mssql_query($query,$this->link);
			if(!$result) $this->throwError();
			return new Result($result);
		}
		
		/**
		 * Seleciona banco de dados no servidor e executa comando
		 * @param string $name nome do banco de dados
		 * @param string $query comando a ser executado
		 * @return Result|bool
		 */
		public function databaseQuery($name,$query){
			$this->selectDatabase($name);
			return $this->query($query);
		}
		
		/**
		 * Seleciona banco de dados no servidor para ser usado como padrão
		 * @param string $name nome do banco de dados a ser selecionado
		 * @return bool
		 */
		public function selectDatabase($name){
			return $this->checkSuccess(mssql_select_db($name,$this->link));
		}
		
		/**
		 * Retorna ultimo valor de chave inserido no banco
		 * @return int
		 */
		public function getInsertId(){
			$id = 0;
    		$res = $this->query("SELECT @@identity AS id");
    		if($row = $res->fetchAssociative()){
        		$id = $row["id"];
    		}
    		return $id;
		}
		
		/**
		 * Retorna o numero de linhas afetadas pelo ultimo comando executado no servidor
		 * @return int
		 */
		public function getAffectedRows(){
			return mssql_affected_rows($this->link);
		}
		
		/**
		 * Retorna o link interno para compatibilidade com funções futuras do PHP
		 * @return resource
		 */
		public function toString(){
			return $this->link;
		}
		
		private function checkSuccess($success){
			if(!$success) $this->throwError();
			return $success;
		}
		private function throwError($erro="Uknown Error"){
			if($this->link)
				$erro = mssql_get_last_message($this->link);
			
			$mensagem = String::format("MS-SQL: %s",$erro);
			
			throw new Exception($mensagem);
		}
		
		//php crap
		/**
		 * Metodo magico, redirecionado para toString
		 * @see toString
		 */
		public function __toString(){
			return $this->toString();
		}
	}
?>