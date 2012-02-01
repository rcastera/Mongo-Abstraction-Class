<?php
	error_reporting(E_ALL);
  ini_set('display_errors', '1');

  require_once('Class.Monga.php');
	$Monga = new Monga(array('username'=>'rcastera', 'password'=>'bryanna', 'host'=>'localhost', 'port'=>'27017')); // or empty for localhost
?>
<!DOCTYPE html>
<html>
<head>
<meta charset=utf-8 />
<title>Mongo Example</title>

</head>
<body>
	
	<h1>Database: test - collection: foo - get: all</h1>
	<pre><?php print_r($Monga->setDatabase('test')->setCollection('foo')->all()); ?></pre>

	<h1>Database: test - collection: test - get: all</h1>
	<pre><?php print_r($Monga->setDatabase('test')->setCollection('test')->all()); ?></pre>

	<h1>Database: test - collection: test - find: richard</h1>
	<pre><?php print_r($Monga->find(array('name'=>'richard'))); ?></pre>

	<h1>Database: test - collection: test - update: richard</h1>
	<pre>
	<?php
	$criteria = array(
		'name' => 'richard' 
	);
	$updates = array(
		'$set' => array(
			'address' => '1 Smith Lane'
		)
	);
	$options = array(
		'upsert' => FALSE
	);
	print_r($Monga->updateDocument($criteria, $updates, $options));
	?>
	</pre>

	<h1>Database: test - collection: test - insert: Elisabeth</h1>
	<pre>
	<?php
	$document = array(
		'name' => 'Elisabeth Castera',
		'age' => '26'
	);

	print_r($Monga->insertDocument($document));
	?>
	</pre>

	<h1>Database: test - collection: test - delete: Elisabeth</h1>
	<pre>
	<?php
	$criteria = array(
		'name' => 'Elisabeth Castera'
	);

	$options = array(
		'justOne' => TRUE
	);

	print_r($Monga->deleteDocument($criteria, $options));
	?>
	</pre>

	<?php unset($Monga); ?>

</body>
</html>