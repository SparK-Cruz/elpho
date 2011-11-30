<?php
	import(php.lang.String);
	import(php.database.mysql.Result);
	
	/**
	 * Classe de Conexão com banco de dados
	 * implementada com o objetivo de substituir as funções mysql_ nativas e
	 * gerenciar o resource de conexão de forma limpa
	 * @author Roger 'SparK' Rodrigues da Cruz
	 * @package php.database.mysql
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
		 * @param int $flags modifica a conexão de acordo com as constantes do cliente mysql: {@link http://www.php.net/manual/pt_BR/mysql.constants.php#mysql.client-flags Mysql Client Flags}
		 */
		public function connect($server=false,$user=false,$pass=false,$flags=0){
			$this->close();
			$this->link = @mysql_connect($server,$user,$pass,true,$flags);
			
			if(!is_resource($this->link)) $this->throwError("Failed to connect. Check the host address, connectivity and passwords.","c00");
		}
		
		/**
		 * Inicializa uma conexão persistente com o banco de dados
		 * @param string $server endereço ou nome do servidor
		 * @param string $user usuário do banco de dados a ser autenticado
		 * @param string $pass senha para autenticação
		 * @param int $flags modifica a conexão de acordo com as constantes do cliente mysql: {@link http://www.php.net/manual/pt_BR/mysql.constants.php#mysql.client-flags Mysql Client Flags}
		 */
		public function connectPersistent($server=false,$user=false,$pass=false,$flags=0){
			$this->close();
			$this->link = mysql_pconnect($server,$user,$pass,$flags);
			if(!$this->link) $this->throwError();
		}
		
		/**
		 * Verifica se o link está ativo e, caso não esteja, re-abre a conexão
		 * @return bool
		 */
		public function ping(){
			return $this->checkSuccess(mysql_ping($this->link));
		}
		
		/**
		 * Fecha conexão com o banco de dados
		 * @return bool
		 */
		public function close(){
			if(!$this->link) return false;
			return @mysql_close($this->link);
		}
		
		/**
		 * Envia pedido de execução de comando para o banco de dados
		 * @param string $query linha de comando a ser executada
		 * @param bool $silent se o resultado deve ser retornado ou não
		 * @return Result|bool
		 */
		public function query($query,$silent=false){
			$function = $silent?"mysql_unbuffered_query":"mysql_query";
			$result = call_user_func($function,$query,$this->link);
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
		 * Cria banco de dados no servidor
		 * @param string $name nome do banco de dados a ser criado
		 * @return bool
		 */
		public function createDatabase($name){
			return $this->checkSuccess(mysql_create_db($name,$this->link));
		}
		
		/**
		 * Seleciona banco de dados no servidor para ser usado como padrão
		 * @param string $name nome do banco de dados a ser selecionado
		 * @return bool
		 */
		public function selectDatabase($name){
			return $this->checkSuccess(mysql_select_db($name,$this->link));
		}
		
		/**
		 * Elimina banco de dados no servidor
		 * @param string $name nome do banco de dados a ser eliminado
		 * @return bool
		 */
		public function drobDatabase($name){
			return $this->checkSuccess(mysql_drop_db($name,$this->link));
		}
		
		/**
		 * Trata string com caracteres especiais para ser usada em um comando ao servidor
		 * @param string $string string a ser tratada
		 * @return String|bool
		 */
		public function escapeString($string){
			$result = $this->checkSuccess(mysql_real_escape_string($string,$this->link));
			if(!$result) return false;
			return new String($result);
		}
		
		/**
		 * Modifica o conjunto de caracteres da conexão com o servidor
		 * @param string $charset nome do conjunto de caracteres a ser usado
		 * @return bool
		 */
		public function setCharset($charset){
			return $this->checkSuccess(mysql_set_charset($charset,$this->link));
		}
		
		/**
		 * Retorna ultimo valor de chave inserido no banco
		 * @return int
		 */
		public function getInsertId(){
			return mysql_insert_id($this->link);
		}
		
		/**
		 * Retorna o numero de linhas afetadas pelo ultimo comando executado no servidor
		 * @return int
		 */
		public function getAffectedRows(){
			return mysql_affected_rows($this->link);
		}
		
		/**
		 * Retorna uma lista de bancos de dados disponíveis no servidor
		 * @return Result|bool
		 */
		public function listDatabases(){
			$result = mysql_list_dbs($this->link);
			if(!$result) return false;
			return new Result($result);
		}
		
		/**
		 * Retorna uma lista de campos da tabela no banco de dados
		 * @param string $database nome do banco de dados
		 * @param string $table nome da tabela a ser buscada
		 * @return Result|bool
		 */
		public function listFields($database,$table){
			$result = mysql_list_fields($database,$table,$this->link);
			if(!$result) return false;
			return new Result($result);
		}
		
		/**
		 * Retorna uma lista de processos que estão sendo executados no servidor atravez desta conexão
		 * @return Result|bool
		 */
		public function listProcesses(){
			$result = mysql_list_processes($this->link);
			if(!$result) return false;
			return new Result($result);
		}
		
		/**
		 * Retorna uma lista de tabelas presentes no banco de dados
		 * @param string $database nome do banco de dados a ser buscado
		 * @return Result|bool
		 */
		public function listTables($database){
			$result = mysql_list_tables($database,$this->link);
			if(!$result) return false;
			return new Result($result);
		}
		
		/**
		 * Retorna o conjunto de caracteres padrão da conexão
		 * @return string
		 */
		public function getClientEncoding(){
			return mysql_client_encoding($this->link);
		}
		
		/**
		 * Retorna a versão do plugin MySQL
		 * @return string
		 */
		public function getClientInfo(){
			return mysql_get_client_info($this->link);
		}
		
		/**
		 * Retorna a versão do servidor MySQL
		 * @return string
		 */
		public function getServerInfo(){
			return mysql_get_server_info($this->link);
		}
		
		/**
		 * Retorna o protocolo MySQL sendo utilizado na conexão
		 * @return string
		 */
		public function getProtocolInfo(){
			return mysql_get_proto_info($this->link);
		}
		
		/**
		 * Retorna uma string descrevendo o tipo da conexão MySQL em uso
		 * @return string
		 */
		public function getHostInfo(){
			return mysql_get_host_info($this->link);
		}
		
		/**
		 * Retorna informações sobre o ultimo comando executado
		 * @return string
		 */
		public function getInfo(){
			return mysql_info($this->link);
		}
		
		/**
		 * Retorna uma lista de informações sobre o funcionamento do servidor
		 * @return array
		 */
		public function getStatus(){
			return mysql_stat($this->link);
		}
		
		/**
		 * Retorna o código do processo atual
		 * @return int
		 */
		public function getThreadId(){
			return mysql_thread_id($this->link);
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
		private function throwError($erro="Uknown Error",$numero="MissingNo."){
			if(is_resource($this->link)){
				$erro = mysql_error($this->link);
				$numero = mysql_errno($this->link);
			}
			$mensagem = String::format("MySQL: %s (%s)",$erro,$numero);
			
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