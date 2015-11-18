<?php
  require("../../startup.php");

  requireDirOnce("database");
  require_once("SampleTable.php");

  class Index{
    public static final function main($args=array()){
      $connection = self::connect();

      if ($connection == null)
        exit();

      $sample = new SampleTable($connection);
      $sample->find();

      if ($sample->getCount() === 0)
        self::populateDatabase($sample);

      $sample->each(function($entity){
        echo $entity->getName()." is ".$entity->getAge()." years old\n";
      });
    }

    private static final function populateDatabase($sample){
      $sample->clear();
      $sample->setName("Roger");
      $sample->setAge(23);
      $sample->save();

      $sample->clear();
      $sample->setName("Claudia");
      $sample->setAge(19);
      $sample->save();

      $sample->clear();
      $sample->setName("Gustavo");
      $sample->setAge(25);
      $sample->save();
    }

    private static final function connect(){
      $connection = null;

      try{
        $connection = new PDO("sqlite:db/sample.db3");
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }catch(PDOException $ex){
        echo "Falha ao conectar: ".utf8_encode($ex->getMessage());
      }

      return $connection;
    }
  }