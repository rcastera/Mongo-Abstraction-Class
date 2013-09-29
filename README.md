Monga
=============

A simple class for working with MongoDB. Those new to Mongo, should go through
the [SQL to Mongo Mapping Chart](http://www.php.net/manual/en/mongo.sqltomongo.php)


### Setup
-----------------
 Add a `composer.json` file to your project:

```javascript
{
  "require": {
      "rcastera/mongo": "v1.0.0"
  }
}
```

Then provided you have [composer](http://getcomposer.org) installed, you can run the following command:

```bash
$ composer.phar install
```

That will fetch the library and its dependencies inside your vendor folder. Then you can add the following to your
.php files in order to use the library (if you don't already have one).

```php
require 'vendor/autoload.php';
```

Then you need to `use` the relevant class, and instantiate the class. For example:


### Getting Started
-----------------
```php
require 'vendor/autoload.php';

use rcastera\Database\Mongo\Monga;

$mongo = new Monga();
```


### Examples
-----------------

##### Find all documents from contacts collections.
```php
<?php
    require 'vendor/autoload.php';
    use rcastera\Database\Mongo\Monga;

    $mongo = new Monga();
?>
<?php $cursor = $mongo->setDatabase('test')->setCollection('contacts')->getCollection()->find()->limit(10); ?>
<?php if ($cursor->hasNext()): ?>
<ul>
    <?php while ($cursor->hasNext()): ?>
    <?php $contact = $cursor->getNext(); ?>
    <li><?php echo $contact['name']; ?></li>
    <?php endwhile; ?>
</ul>
<?php else: ?>
<p>No contacts found.</p>
<?php endif; ?>
<?php unset($mongo); ?>
```


##### Find all documents and sort by name in ascending order.
```php
<?php
    require 'vendor/autoload.php';
    use rcastera\Database\Mongo\Monga;

    $mongo = new Monga();
?>
<?php $cursor = $mongo->setDatabase('test')->setCollection('contacts')->getCollection()->find()->sort(array('name' => 1)); ?>
<?php if ($cursor->hasNext()): ?>
<ul>
    <?php while ($cursor->hasNext()): ?>
    <?php $contact = $cursor->getNext(); ?>
    <li><?php echo $contact['name']; ?></li>
    <?php endwhile; ?>
</ul>
<?php else: ?>
<p>No contacts found.</p>
<?php endif; ?>
<?php unset($mongo); ?>
```


##### Find all documents where name = 'Richard Castera'.
```php
<?php
    require 'vendor/autoload.php';
    use rcastera\Database\Mongo\Monga;

    $mongo = new Monga();
?>
<?php $cursor = $mongo->setDatabase('test')->setCollection('contacts')->getCollection()->find(array('name' => 'Richard Castera')); ?>
<?php if ($cursor->hasNext()): ?>
<ul>
    <?php while ($cursor->hasNext()): ?>
    <?php $contact = $cursor->getNext(); ?>
    <li><?php echo $contact['name']; ?></li>
    <?php endwhile; ?>
</ul>
<?php else: ?>
<p>No contacts found.</p>
<?php endif; ?>
<?php unset($mongo); ?>
```


##### Find where name = 'Richard Castera' and update address.
```php
<?php
    require 'vendor/autoload.php';
    use rcastera\Database\Mongo\Monga;

    $mongo = new Monga();

    $criteria = array(
        'name' => 'Richard Castera'
    );
    $updates = array(
        '$set' => array(
            'address' => '2 Smith Lane'
        )
    );
    $options = array(
        'upsert' => false
    );
?>
<?php $updated = $mongo->setDatabase('test')->setCollection('contacts')->getCollection()->update($criteria, $updates, $options); ?>
<?php if ($updated): ?>
<p>Contact updated.</p>
<?php else: ?>
<p>Contact not updated.</p>
<?php endif; ?>
<?php unset($mongo); ?>
```


##### Insert new document.
```php
<?php
    require 'vendor/autoload.php';
    use rcastera\Database\Mongo\Monga;

    $mongo = new Monga();

    $document = array(
        'name' => 'Isabella Castera',
        'age' => '3',
        'address' => '3 Smith Lane'
    );
?>
<?php $inserted = $mongo->setDatabase('test')->setCollection('contacts')->getCollection()->insert($document); ?>
<?php if ($inserted): ?>
<p>Contact inserted.</p>
<?php else: ?>
<p>Contact not inserted.</p>
<?php endif; ?>
<?php unset($mongo); ?>
```


##### Delete a document.
```php
<?php
    require 'vendor/autoload.php';
    use rcastera\Database\Mongo\Monga;

    $mongo = new Monga();

    $criteria = array(
        'name' => 'Elisabeth Castera'
    );

    $options = array(
        'justOne' => TRUE
    );
?>
<?php $deleted = $mongo->setDatabase('test')->setCollection('contacts')->getCollection()->delete($criteria, $options); ?>
<?php if ($deleted): ?>
<p>Contact deleted.</p>
<?php else: ?>
<p>Contact not deleted.</p>
<?php endif; ?>
<?php unset($mongo); ?>
```


### Contributing
-----------------
1. Fork it.
2. Create a branch (`git checkout -b my_branch`)
3. Commit your changes (`git commit -am "Added something"`)
4. Push to the branch (`git push origin my_branch`)
5. Create an Issue with a link to your branch
6. Enjoy a refreshing Coke and wait
