<?php
  import(php.lang.String);
  import(php.lang.ArrayList);
  import(php.lang.Dynamic);
  import(php.event.EventHandler);
  import(database.event);

  abstract class Beam extends EventHandler{
    private $tabela;
    private $indice;
    private $chave;
    private $isLocked;
    private $readOnly;
    private $fieldList;

    protected $connection;

    protected $registro;
    protected $registros;
    protected $hasRegistros;
    protected $isPosicionado;

    private $statements;

    protected $separator = "`";

    //constructor
    protected function Beam(PDO $connection,$fieldList=array()){
      $this->setWritable(false);
      if(!$this->chave) $this->chave = "id";

      if(!$this->fieldList) $this->fieldList = $fieldList;
      $this->connection = $connection;
      $this->isLocked = false;
      $this->statements = new stdClass();

      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $this->prepareStatements();
      $this->clear();
    }

    //set
    public function setWritable($writable){
      if(is_bool($this->readOnly)) return;
      $this->readOnly = !$writable;
    }

    public function setRegistre($reg){
      $this->registro = $reg;
      $this->isPosicionado = !!$this->getId();
    }
    public function setId($id){
      $this->registro->{$this->chave} = $id;
    }
    protected function setKeyField($field){
      $this->chave = $field;
    }
    protected function setTable($tableName){
      $this->tabela = $tableName;
    }
    protected function setFieldList($list,$_=null){
      if(is_array($list)){
        $this->fieldList = $list;
        return;
      }
      $this->fieldList = func_get_args();
    }

    //get
    public function getKeyField(){
      return $this->chave;
    }
    public function getTable(){
      return $this->tabela;
    }
    public function getRegistre(){
      return $this->registro;
    }
    public function isAvailable(){
      return $this->isPosicionado;
    }
    public function hasItens(){
      return $this->hasRegistros;
    }
    public function getId(){
      if(!isset($this->registro->{$this->chave})) return;
      return $this->registro->{$this->chave};
    }
    public function getCount(){
      return count($this->registros);
    }
    public function getIndex(){
      return $this->indice;
    }
    public function getFieldList(){
      return new ArrayList_from($this->fieldList);
    }

    //buscar
    public function find($options=null){
      if($this->isLocked) return;
      if(!$options) $options = array();

      if(!is_array($options) and !is_object($options)) return;
      if(is_object($options)){
        if(!$options instanceof ArrayAccess) return;

        $valid = false;
        if($options instanceof Iterator) $valid = true;
        if($options instanceof IteratorAggregate) $valid = true;
        if($options instanceof stdClass) $valid = true;

        if(!$valid) return;
      }

      $prepared = new stdClass();

      if(!empty($options["order"])){
        $prepared->order = $options["order"];
        unset($options["order"]);
      }
      if(isset($options["filter"])){
        if(empty($options["filter"])) return;
        $prepared->filter = $options["filter"];
        unset($options["filter"]);
      }
      if(!empty($options["limit"])){
        $prepared->limit = $options["limit"];
      }
      if(!empty($options["start"])){
        $prepared->start = $options["start"];
      }

      $this->prepareStatements($prepared);

      $query = "read";
      if(isset($options["limit"])) $query = "readRange";

      $result = $this->queryResults($query,$options);
      $this->dispatchEvent(new Find($result,$options));
      $this->fetchSelect($result);
      $result->closeCursor();
    }
    private function fetchSelect($result){
      $this->clear();

      $numeroRegistros = $result->rowCount();
      if($numeroRegistros == 0) return;

      while($registro = $result->fetchObject()){
        $this->registros[] = $registro;
      }
      $this->hasRegistros = true;
    }
    public function findId($id,$fetch=true){
      $chave = $this->getKeyField();
      $this->find(array("filter"=>$chave."='".$id."'"));
      if($fetch) return $this->first();
    }
    public function findIds(ArrayAccess $lista=null){
      if(!$lista) $lista = array();
      if(count($lista) == 0) return $this->clear();

      $where = array();
      foreach($lista as $item){
        $where[] = $this->chave." = '".$item."'";
      }
      $this->find(array("filter"=>implode(" OR ",$where)));
    }

    //positioning
    public function clear(){
      if($this->isLocked) return false;

      $this->resetIndex();
      $this->registros = array();
      $this->hasRegistros = false;
    }

    public function resetIndex(){
      $this->indice = -1;
      $this->isPosicionado = false;
      $this->registro = new stdClass();
    }
    public function first(){
      $this->indice = 0;
      return $this->fetchRegistre();
    }
    public function prev(){
      if($this->indice == -1) $this->indice = $this->getCount();
      $this->indice--;
      return $this->fetchRegistre();
    }
    public function next(){
      $this->indice++;
      return $this->fetchRegistre();
    }
    public function last(){
      $this->indice = $this->getCount()-1;
      return $this->fetchRegistre();
    }
    public function get($index){
      $this->indice = $index;
      return $this->fetchRegistre();
    }

    protected function fetchRegistre(){
      if($this->isLocked) return false;

      $this->isPosicionado = false;
      if(!$this->hasRegistros) return false;
      if(!isset($this->registros[$this->indice])){
        $this->resetIndex();
        return false;
      }
      $this->registro = $this->registros[$this->indice];

      $this->dispatchEvent(new Read($this->indice));

      $this->isPosicionado = true;
      return true;
    }
    public function lockRegistre(){
      $this->isLocked = true;
    }
    public function invert(){
      $this->resetIndex();
      $this->registros = array_reverse($this->registros,false);
    }

    //writting
    public function save(){
      if($this->readOnly) return;
      if(empty($this->tabela)) throw new Exception("No table set.");

      $options = new stdClass();

      $query = 'create';
      $isNew = ((!$this->isPosicionado) or ($this->getId() == ""));

      $serie = $this->toArray($this->chave);

      foreach($this->fieldList as $field){
        $options->{$field} = "";
      }
      foreach($serie as $key => $value){
        $options->{$key} = $value;
      }

      if(!$isNew) $query = 'update';

      if($this->getId() != "")
        $options->{$this->chave} = $this->getId();

      $this->prepareStatements($options);

      $this->connection->beginTransaction();
      try{
        $result = $this->queryResults($query,$options);
        if($isNew) $this->registro->{$this->chave} = $this->connection->lastInsertId();
        $this->connection->commit();
      }catch(Exception $e){
        $this->connection->rollBack();
        throw $e;
      }

      $eventClass = $isNew?"Create":"Update";
      $this->dispatchEvent(new $eventClass($result,$options));

      return $isNew;
    }
    public function delete($remove=true){
      if(!$this->isPosicionado) throw new Exception("No index positioned.");
      if($this->readOnly) return;

      $key = $this->chave;

      $query = 'delete';
      $options = new stdClass();
      $options->{$key} = $this->getId();

      $this->connection->beginTransaction();
      try{
        $result = $this->queryResults($query,$options);
        $this->connection->commit();
      }catch(Exception $e){
        $this->connection->rollBack();
        throw $e;
      }

      $this->dispatchEvent(new Delete($result,$options));

      if($remove) $this->remove();
    }
    public function remove(){
      array_splice($this->registros,$this->indice,1);
      $this->prev();
    }

    //extra
    public function __get($attribute){
      $trace = debug_backtrace();
      if(!is_a($trace[1]["object"],get_class($this))) return;

      if(!isset($this->registro->{$attribute})) return;
      $valor = $this->registro->{$attribute};
      return new String(mb_detect_encoding($valor, 'UTF-8', true)?$valor:utf8_encode($valor));
    }
    public function __set($attribute,$value){
      $trace = debug_backtrace();
      if(!is_a($trace[1]["object"],get_class($this))) return;

      $this->registro->{$attribute} = $value;
    }
    private function queryResults($query,$options=array()){
      $defaults = new stdClass();
      $defaults->order = $this->chave." ASC";

      $result = $this->statements->{$query};

      $tokens = array();
      $string = $result->queryString;
      foreach(explode(" ",$string) as $part){
        $part = preg_replace('/[^a-z:_]/','',$part);
        if(substr($part,0,1) !== ":") continue;
        $tokens[] = substr($part,1);
      }

      $params = new stdClass();
      foreach($defaults as $key => $value){
        $params->{$key} = $value;
      }
      foreach($options as $key => $value){
        $params->{$key} = $value;
      }
      $params->table = $this->tabela;

      foreach($tokens as $token){
        //echo "token ".$token."<br/>";
        $result->bindValue(':'.$token,$params->{$token});
      }

      $result->execute();

      return $result;
    }
    private function prepareStatements($options=null){
      if(!$options) $options = new stdClass();
      $prepared = new Dynamic();

      $separator = $this->separator;

      $prepared->order = $this->chave." ASC";
      $prepared->filter = "1=1";
      $prepared->start = 0;
      $prepared->limit = 0;

      foreach($options as $key => $value){
        $prepared->{$key} = $value;
      }

      $fields = $this->fieldList;
      if(isset($options->{$this->chave}))
        $fields[] = $this->chave;

      $filter = $prepared->filter;
      $order = $prepared->order;
      $start = $prepared->start;
      $limit = $prepared->limit;
      //C.R.U.D.
      $this->statements->read = $this->connection->prepare("SELECT ".$separator.$this->chave.$separator.", ".$separator.implode($separator.", ".$separator,$fields).$separator." FROM ".$separator.$this->tabela.$separator." WHERE ".$filter." ORDER BY ".$order);
      try{
        $this->statements->readRange = $this->connection->prepare("SELECT ".$separator.$this->chave.$separator.", ".$separator.implode($separator.", ".$separator,$fields).$separator." FROM ".$separator.$this->tabela.$separator." WHERE ".$filter." ORDER BY ".$order." LIMIT ".$start.", ".$limit);
      }catch(Exception $e){
        $this->statements->readRange = $this->connection->prepare("SELECT FIRST ".$limit." SKIP ".$start." ".$separator.$this->chave.$separator.", ".$separator.implode($separator.", ".$separator,$fields).$separator." FROM ".$separator.$this->tabela.$separator." WHERE ".$filter." ORDER BY ".$order);
      }

      if($this->readOnly) return;
      $this->statements->create = $this->connection->prepare("INSERT INTO ".$separator.$this->tabela.$separator." (".$separator.implode($separator.", ".$separator,$fields).$separator.") VALUES(".implode(", ",array_map(function($field){ return ":".$field; },$fields)).")");
      $this->statements->update = $this->connection->prepare("UPDATE ".$separator.$this->tabela.$separator." SET ".implode(", ",array_map(function($field) use($separator){ return $separator.$field.$separator." = :".$field; },$fields))." WHERE ".$separator.$this->chave.$separator." = :".$this->chave);
      $this->statements->delete = $this->connection->prepare("DELETE FROM ".$separator.$this->tabela.$separator." WHERE ".$separator.$this->chave.$separator." = :".$this->chave);
    }
    public function toArray($exceptions=null,$_=null){
      $exceptions = func_get_args();
      $retorno = array();
      foreach($this->registro as $var => $value){
        if(in_array($var,$exceptions)) continue;
        $retorno[$var] = $value;
      }
      return $retorno;
    }
  }
?>