Monga
=============

A simple, fully documented Abstraction Class in PHP5 for working with MongoDB.


Examples
-----------
    <?php
      require_once('Class.Monga.php');
      $Monga = new Monga();
    ?>
    
    Find all records in test collection.
    <?php print_r($Monga->setDatabase('test')->setCollection('test')->find()->asArray()); ?>

    Find all records in test collection and sort by name in ascending order.
    <?php print_r($Monga->setDatabase('test')->setCollection('test')->find()->sort(array('name' => 1))->asArray()); ?>

    Find all records in test collection where name = 'Richard Castera'
    <?php print_r($Monga->setDatabase('test')->setCollection('test')->find(array('name' => 'Richard Castera'))->asArray()); ?>

    Find "Richard Castera" in test collection and update the record's address
    <?php
      $criteria = array(
        'name' => 'Richard Castera'
      );
      $updates = array(
        '$set' => array(
          'address' => '2 Smith Lane'
        )
      );
      $options = array(
        'upsert' => FALSE
      );
      echo ($Monga->setDatabase('test')->setCollection('test')->updateDocument($criteria, $updates, $options)) ? 'Updated.':'Failed to Update.';
    ?>

    Add new document to the test collection.
    <?php
      $document = array(
        'name' => 'Isabella Castera',
        'age' => '3',
        'address' => '3 Smith Lane'
      );
      echo ($Monga->setDatabase('test')->setCollection('test')->insertDocument($document)) ? 'Inserted.':'Failed to Insert.';
    ?>

    <?php
      $criteria = array(
        'name' => 'Elisabeth Castera'
      );

      $options = array(
        'justOne' => TRUE
      );
      echo ($Monga->setDatabase('test')->setCollection('test')->deleteDocument($criteria, $options)) ? 'Deleted.':'Failed to Delete.';
    ?>

    Destroy the object.
    <?php unset($Monga); ?>

Contributing
------------

1. Fork it.
2. Create a branch (`git checkout -b my_branch`)
3. Commit your changes (`git commit -am "Added something"`)
4. Push to the branch (`git push origin my_branch`)
5. Create an [Issue][1] with a link to your branch
6. Enjoy a refreshing Coke and wait
