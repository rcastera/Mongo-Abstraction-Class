<?php
	//error_reporting(E_ALL);
  //ini_set('display_errors', '1');
  header('Content-type: text/plain');
  
	require_once('Class.MongoMe.php');
	$Monga = new Monga(array('username'=>'rcastera', 'password'=>'bryanna', 'host'=>'localhost', 'port'=>'27017')); // or empty for localhost

	print_r($Monga->setDatabase('test')->setCollection('foo')->all());
	print_r($Monga->setDatabase('test')->setCollection('test')->all());

	// $document = array(
	// 	'name' => 'Elisabeth Castera',
	// 	'age' => '26'
	// );

	// print_r($Monga->insert($document));

	unset($Monga);