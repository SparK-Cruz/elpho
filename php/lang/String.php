<?php
	import(php.lang.ArrayList);
	
	/**
	 * Classe String para PHP mapeada a partir da classe {@link http://download.oracle.com/javase/6/docs/api/java/lang/String.html String do JavaSE(tm)}
	 * @author Roger 'SparK' Rodrigues da Cruz
	 * @package php.lang
	 */
	class String{
		/**
		 * Tipo primitivo que guarda o valor
		 */
		private $value = "";
		
		/**
		 * Inicializa a string
		 * @param mixed $value (opcional) qualquer coisa que passar para value será convertida
		 */
		public function String($value=""){
			$this->value .= $value;
		}
		
		/**
		 * Concatena o valor ao final da String
		 * @param mixed $value valor a ser concatenado
		 */
		public function concat($value){
			return new String($this->value.$value);
		}
		
		/**
		 * Retorna o numero de caracteres contidos na String
		 * @return int
		 */
		public function length(){
			return strlen($this->value);
		}
		
		/**
		 * Retorna verdadeiro caso a String esteja vazia.
		 * @return bool
		 */
		public function isEmpty(){
			return ($this->length() == 0);
		}
		
		/**
		 * Retorna uma instância de String contendo um unico caractere da posição especificada
		 * @param int $index a posição do caractere dentro da String
		 * @return String
		 */
		public function charAt($index){
			return new String(substr($this->value,$index,1));
		}
		
		/**
		 * Retorna uma instância de String contendo um pedaço da String original
		 * @param int $start posição inicial dentro da string para o corte
		 * @param int $end (opcional) posição final do corte, por padrão é igual à posição final da String original
		 * @return String
		 */
		public function substring($start,$end=null){
			if($start < 0) $start = $this->length()-$start;
			if($end < 0) $end = $this->length()-1+$end;
			if(!$end) $end = $this->length()-1;
			
			$length = $end-$start;
			return new String(substr($this->value,$start,$end));
		}
		
		/**
		 * Retorna uma instância de String contendo um pedaço da String original
		 * @param int $start posição inicial dentro da string para o corte
		 * @param int $length (opcional) numero de characteres a serem retornados, por padrão é igual ao length da String original
		 * @return String
		 */
		public function substr($start,$length=null){
			if($start < 0) $start = $this->length()-$start;
			if(!$length) $length = $this->length();
			return new String(substr($this->value,$start,$length));
		}
		
		/**
		 * Retorna um array com instâncias de String quebradas pelo delimitador
		 * @param string $delimiter sequencia que identifica onde a String será quebrada
		 * @param int $limit (opcional) numero maximo de quebras permitidas
		 * @return ArrayList
		 */
		public function split($delimiter,$limit=false){
			if(!$delimiter) return array(new String($this->value));
			$limit = $limit ? $limit : strlen($this->value);
			$primitive = explode($delimiter,$this->value,$limit);
			$list = array();
			foreach($primitive as $str){
				$list[] = new String($str);
			}
			return ArrayList::create($list);
		}
		
		/**
		 * Retorna verdadeiro caso a sequencia seja encontrada dentro da String (case-sensitive)
		 * @param string $sequence Sequencia à ser procurada dentro da String
		 * @return bool
		 */
		public function contains($sequence){
			return (strpos($this->value,$sequence) !== false);
		}
		
		/**
		 * Retorna verdadeiro caso o valor seja igual ao da String
		 * @param string $string valor a ser comparado com a String
		 * @return bool
		 */
		public function contentEquals($string){
			return ($this->value == $string);
		}
		
		/**
		 * Retorna o resultado da comparação entre duas Strings (a nivel de classe)
		 * @param String $object classe a ser comparada com a String
		 * @return bool
		 */
		public function equals($object){
			return (strcmp($this, $object) === 0);
		}
		
		/**
		 * Retorna o resultado da comparação entre duas Strings (a nivel de classe, case-insensitive)
		 * @param String $object: classe a ser comparada com a String
		 * @return bool
		 */
		public function equalsIgnoreCase($object){
			return (strcasecmp($this, $object) === 0);
		}
		
		/**
		 * Retorna a primeira posição da sequencia dentro da string (falso se a sequencia não for encontrada)
		 * @param string|int $char sequencia a ser procurada dentro da String ou código do caractere na tabela ASCII
		 * @param int $fromIndex (opcional) numero de caracteres a serem ignorados a partir do 0
		 * @return int|bool
		 */
		public function indexOf($char,$fromIndex=0){
			if(is_object($char)) $char = $char->toString();
			if(is_integer($char)) $char = chr($char);
			return strpos($this->value,$char,$fromIndex);
		}
		
		/**
		 * Retorna a ultima posição da sequencia dentro da string (falso se a sequencia não for encontrada)
		 * @param string|int $char sequencia a ser procurada dentro da String ou código do caractere na tabela ASCII
		 * @param int $fromIndex (opcional) numero de caracteres a serem ignorados a partir do 0
		 * @return int|bool
		 */
		public function lastIndexOf($char,$fromIndex=0){
			if(is_integer($char)) $char = chr($char);
			return strrpos($this->value,$char,$fromIndex);
		}
		
		/**
		 * Retorna o numero de vezes que a sequencia se repetiu dentro da string
		 * @param string|int $char sequencia a ser procurada dentro da String ou código do caractere na tabela ASCII
		 * @return int
		 */
		public function count($char){
			$offset = 0;
			$count = 0;
			
			while(($found = $this->indexOf($char,$offset)) !== false){
				$offset = $found+1;
				$count++;
			}
			return $count;
		}
		
		/**
		 * Retorna verdadeiro se a String se adequa dentro dos critérios de uma expressão regular
		 * @param string $regex expressão regular a ser usada como critério
		 * @return bool
		 */
		public function matches($regex){
			return preg_match($regex,$this->value);
		}
		
		/**
		 * Retorna uma instância de String com valor convertido em caixa baixa
		 * @param string $charset (opcional) conjunto de caracteres a serem utilizados na conversão (padrão UTF-8)
		 * @return String
		 */
		public function toLowerCase($charset='UTF-8'){
			$string = $this->value;
			$string = htmlentities($string,ENT_COMPAT,$charset);
			$string = strtolower($string);
			$string = html_entity_decode($string,ENT_COMPAT,$charset);
			return new String($string);
		}
		
		/**
		 * Retorna uma instância de String com valor convertido em caixa alta
		 * @param string $charset (opcional) conjunto de caracteres a serem utilizados na conversão (padrão UTF-8)
		 * @return String
		 */
		public function toUpperCase($charset='UTF-8'){
			$string = $this->value;
			$string = htmlentities($string,ENT_COMPAT,$charset);
			$string = strtoupper($string);
			$string = String::fixUpperCaseEntities($string);
			$string = html_entity_decode($string,ENT_COMPAT,$charset);
			return new String($string);
		}
		
		/**
		 * Metodo complementar que retorna uma instância de String com as entidades html de caracteres maiusculos dentro das normas da w3C
		 * @param string $string string contendo entidades html com letras maiusculas ao invez de apenas a primeira
		 * @return String
		 */
		public static function fixUpperCaseEntities($string){
			$lastIndex = 0;
			while(true){
				$i = strpos($string,"&",$lastIndex);
				if($i === false) break;
				$lastIndex = $i+2;
				$end = strpos($string,";",$lastIndex) - ($i+1);
				$string = substr($string,0,$lastIndex).strtolower(substr($string,$lastIndex,$end)).substr($string,$lastIndex+$end);
			}
			return new String($string);
		}
		
		/**
		 * Retorna verdadeiro se o inicio da String for igual ao valor da sequencia
		 * @param string $sequence sequencia a ser comparada
		 * @return bool
		 */
		public function startsWith($sequence){
			return (substr($this->value,strlen($sequence)) == $sequence);
		}
		/**
		 * Retorna verdadeiro se o final da String for igual ao valor da sequencia
		 * @param string $sequence sequencia a ser comparada
		 * @return bool
		 */
		public function endsWith($sequence){
			return (substr($this->value,-strlen($sequence)) == $sequence);
		}
		
		/**
		 * Retorna uma cópia da instância de String com espaços em branco iniciais e finais omitidos
		 * @return String
		 */
		public function trim(){
			return new String(trim($this->value));
		}
		
		/**
		 * Retorna uma cópia da instância de String substituindo o numero de ocorrencias especificado de uma sequencia pela outra
		 * @param string $procura sequencia a ser encontrada
		 * @param string $substituto sequencia a ser usada no resultado final
		 * @param int $limite (opcional) limita quantas ocorrencias devem ser substituidas
		 * @return String
		 */
		public function replace($procura,$substituto,$limite=false){
			$limite = $limite?$limite+1:$this->length();
			return $this->split($procura,$limite)->join($substituto);
		}
		
		/**
		 * Retorna uma cópia da instância de String substituindo todas as ocorrencias que se enquadram no critério por uma sequencia de caracteres
		 * @param string $regex critério a ser usado para substituir o conteudo
		 * @param string $substituto sequencia a ser usada no resultado final
		 * @return String
		 */
		public function replaceExpression($regex,$substituto){
			return new String(preg_replace($regex,$substituto,$this->value));
		}
		
		/**
		 * Retorna uma instância de String montada a partir de um formato
		 * @param string $formatString formato a ser seguido
		 * @param mixed $args (opcional) valores a serem inseridos dentro do critério do formato
		 * @return String
		 */
		public static function format($formatString,$args=false){
			$args = func_get_args();
			return new String(call_user_func_array(sprintf,$args));
		}
		
		/**
		 * Retorna um hashcode de acordo com a fórmula s[0]*31^(n-1) + s[1]*31^(n-2) + ... + s[n-1]
		 * @link http://download.oracle.com/javase/6/docs/api/java/lang/String.html#hashCode%28%29
		 * @link http://download.oracle.com/javase/6/docs/api/java/util/Hashtable.html
		 * @return string
		 */
		public function hashCode(){
			$n = $this->length();
			$hash = 0;
			for($i=0;$i<$n;$i++){
				$hash += ord($this->charAt($i))*31^($n-1);
			}
			return $hash+ord($this->charAt($n-1));
		}
		
		/**
		 * Retorna valor contido na classe String, tipo primitivo string
		 * @return string
		 */
		public function toString(){
			return $this->value;
		}
		
		/**
		 * Metodo Magico do php para poder usar a classe como uma string qualquer, redirecionado para o metodo toString
		 * @return string
		 */
		public function __toString(){
			return $this->toString();
		}
	}
?>