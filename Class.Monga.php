<?php
/**
 * @uses        Abstraction class for Mongo - For the 10gen-supported PHP driver for MongoDB.
 * @author      Richard Castera
 * @link        http://www.richardcastera.com/projects/code/php-mongo-abstraction-class
 * @see         http://www.php.net/manual/en/book.mongo.php
 * @license     GNU LESSER GENERAL Public LICENSE
 */

class Monga {

  /**
   * Holds the connection object.
   * @var String
   */
  private $connection;

  /**
   * Holds the database object.
   * @var String
   */
  private $database;

  /**
   * Holds the collection object.
   * @var String
   */
  private $collection;


  /**
   * Constructor
   * @param Array $server - 4 parameters for the connection string. Look at @example
   * @param Array $options
   * @param Boolean $slave
   * @return None.
   * @example $Mongo - new Mongo(array('username'=>'username', 'password'=>'password', 'host'=>'localhost', 'port'=>'27017'));
   */
  public function __construct($server = array(), $options = array(), $slave = FALSE) {
    if(empty($server)) {
      $host = 'mongodb://' . $server['host'];
    }
    else {
      $host = "mongodb://${server['username']}:${server['password']}@${server['host']}:${server['port']}";
    }

    try {
      $this->connection = new Mongo($host, $options);
      $this->connection->setSlaveOkay($slave);
    }
    catch (MongoConnectionException $e) {
      die('Error connecting to Mongo Server @' . $server['host']);
    } 
    catch (MongoException $e) {
      throw new Exception('Error connecting to Mongo Server @' . $server['host'], 0, $e->getMessage());
    }
  }

  /**
   * Destructor
   * @param None.
   * @return None.
   */
  public function __destruct() {
    $this->connection->close();   
  }

  /**
   * Sets the database to use. (use database)
   * @param String $database - the database name to connect.
   * @return Object $this.
   */
  public function setDatabase($database = '') {
    try {
      $this->database = $this->connection->{$database};
    }
    catch (MongoException $e) {
      throw new Exception('Could not select Database ' . $database, 0, $e->getMessage());  
    }
    return $this;
  }

  /**
   * Sets the collection to use. (analogous to a relational database's table)
   * @param String $collection - the collection name to work with.
   * @return Object $this.
   */
  public function setCollection($collection = '') {
    try {
      $this->collection = $this->database->{$collection};
    } 
    catch (MongoException $e) {
      throw new Exception('Could not select Collection ' . $collection, 0, $e->getMessage());   
    }
    return $this;
  }

  /**
   * Find something within a collection.
   * @param Array $what - the query.
   * @param Array $one - find only one.
   * @return Array $data - an array of document objects.
   */
  public function find($what = array(), $one = FALSE) {
    try {
      $cursor = $one?$this->collection->findOne($what):$this->collection->find($what);  
    }
    catch (MongoCursorException $e) {
      throw new Exception('Failed finding ' . $what, 0, $e->getMessage());   
    }
    
    $data = array();
    while ($cursor->hasNext()) {
      $data[] = $cursor->getNext();
    }

    return $data;
  }

  /**
   * Get all documents within a collection.
   * @param None.
   * @return Array $data - an array of all document objects related to collection.
   */
  public function all() {
    try {
      $cursor = $this->collection->find();
    }
    catch (MongoCursorException $e) {
      throw new Exception('Failed retrieving all documents', 0, $e->getMessage());
    }
    
    $data = array();
    foreach ($cursor as $document) {
      $data[] = $document;
    }

    return $data;
  }

  /**
   * Get the number of documents within a collection.
   * @param None.
   * @return Long - the number of documents in the current collection.
   */
  public function count($query = array(), $limit = 0, $skip = 0) {
    try {
      return $this->collection->count($query, $limit, $skip); 
    }
    catch (MongoCursorException $e) {
      throw new Exception('Failed counting documents for ' . print_r($query, TRUE), 0, $e->getMessage());
    }
  }

  /**
   * Inserts an array into the collection.
   * @param Array $document.
   * @param Array $options.
   * @return .
   */
  public function insertDocument($document = array(), $options = array()) {
    try {
      return $this->collection->insert($document, $options);  
    }
    catch (MongoCursorException $e) {
      throw new Exception('Failed inserting documents ' . print_r($document, TRUE), 0, $e->getMessage());   
    }
    catch (MongoCursorTimeoutException $e) {
      throw new Exception('Database does not respond within the timeout period', 0, $e->getMessage());    
    }
  }

  /**
   * Update records based on a given criteria.
   * @param Array $criteria.
   * @param Array $object.
   * @param Array $options - 
   * @return .
   */
  public function updateDocument($criteria = array(), $object = array(), $options = array()) {
    try {
      return $this->collection->update($criteria, $object, $options);  
    }
    catch (MongoCursorException $e) {
      throw new Exception('Failed updating object with criteria ' . print_r($criteria, TRUE), 0, $e->getMessage());   
    }
    catch (MongoCursorTimeoutException $e) {
      throw new Exception('Database does not respond within the timeout period', 0, $e->getMessage());    
    }
  }

  /**
   * Saves an object to this collection
   */
  public function saveDocument($object = array()) {
    try {
      return $this->collection->save($object, $options);  
    }
    catch (MongoCursorException $e) {
      throw new Exception('Failed saving object ' . print_r($object, TRUE), 0, $e->getMessage());   
    }
    catch (MongoCursorTimeoutException $e) {
      throw new Exception('Database does not respond within the timeout period', 0, $e->getMessage());    
    }
  }

  public function deleteDocument($criteria = array(), $options = array()) {
    try {
      return $this->collection->remove($criteria, $options);  
    }
    catch (MongoCursorException $e) {
      throw new Exception('Failed deleting object with criteria ' . print_r($criteria, TRUE), 0, $e->getMessage());   
    }
    catch (MongoCursorTimeoutException $e) {
      throw new Exception('Database does not respond within the timeout period', 0, $e->getMessage());    
    }
  }
}
