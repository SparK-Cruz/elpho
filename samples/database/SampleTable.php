<?php
  requireDirOnce("database");

  class SampleTable extends Entity{
    public function SampleTable(PDO $connection=null){
      $this->setTable("sample_table");
      $this->setFieldList("name", "age");
      $this->setWritable(true);

      self::Entity($connection);
    }

    //get
    public function getName(){
      return $this->name;
    }
    public function getAge(){
      return $this->age;
    }

    //set
    public function setName($name){
      $this->name = $name;
    }
    public function setAge($age){
      $this->age = $age;
    }
  }