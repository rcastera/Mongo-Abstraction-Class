<?php
/**
 * @uses Abstraction class for Mongo - For the 10gen-supported PHP driver for MongoDB.
 * @author Richard Castera
 * @link http://www.richardcastera.com/projects/code/php-mongo-abstraction-class
 * @see http://www.php.net/manual/en/book.mongo.php
 * @license GNU LESSER GENERAL Public LICENSE
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
   * The current cursor.
   * @var Object
   */
  private $cursor;

  /**
   * Constructor.
   * @param Array $server - 4 parameters for the connection string. See @example
   * @param Array $options:
   *        connect - if the constructor should connect before returning. default is TRUE. 
   *        timeout - for how long the driver should try to connect to the database (in milliseconds).
   *        replicaSet - the name of the replica set to connect to. if this is given, the master will be determined by using the ismaster database command on the seeds, so the driver may end up connecting to a server that was not even listed. see the replica set example below for details.
   *        username - the username can be specified here, instead of including it in the host list. this is especially useful if a username has a ":" in it. this overrides a username set in the host list.
   *        password - the password can be specified here, instead of including it in the host list. this is especially useful if a password has a "@" in it. this overrides a password set in the host list.
   *        db - the database to authenticate against can be specified here, instead of including it in the host list. this overrides a database given in the host list.
   * @return None.
   * @example $Mongo - new Mongo(array('username'=>'username', 'password'=>'password', 'host'=>'localhost', 'port'=>'27017'));
   */
  public function __construct($server = array(), $options = array()) {
    if (empty($server)) {
      $host = $server['host'] = 'mongodb://localhost:27017';
    }
    else if (!empty($server['username']) && !empty($server['password'])) {
      $host = "mongodb://${server['username']}:${server['password']}@${server['host']}:${server['port']}";
    }
    else {
      $host = "mongodb://${server['host']}:${server['port']}";
    }

    try {
      $this->connection = new Mongo($host, $options);
    }
    catch (MongoConnectionException $e) {
      die('Error connecting to Mongo Server ' . $server['host']);
    } 
    catch (MongoException $e) {
      throw new Exception('Error connecting to Mongo Server @' . $server['host'], $e->getCode(), $e->getMessage());
    }
  }

  /**
   * Destructor.
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
      throw new Exception('Could not select Database ' . $database, $e->getCode(), $e->getMessage());
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
      throw new Exception('Could not select Collection ' . $collection, $e->getCode(), $e->getMessage());
    }
    return $this;
  }

  /**
   * Find something within a collection.
   * @param Array $what - the fields for which to search.
   * @param Array $fields - fields of the results to return. The array is in the format array('fieldname' => true, 'fieldname2' => true). The _id field is always returned.
   * @param Boolean $one - find only one?
   * @param Array $slave - sets whether this query can be done on a slave.
   * @return Array $data - an array of document objects.
   */
  public function find($what = array(), $fields = array(), $one = FALSE, $slave = TRUE) {
    try {
      $this->cursor = $one ? $this->collection->findOne($what, $fields) : $this->collection->find($what, $fields);
      $this->cursor->slaveOkay($slave);
    }
    catch (MongoCursorException $e) {
      throw new Exception('Failed finding ' . print_r($what, TRUE), $e->getCode(), $e->getMessage());
    }
    return $this;
  }

  /**
   * Sorts the results by given fields.
   * @var Array $direction - an array of fields by which to sort.
   * @return Object - this.
   */
  public function sort($direction = array()) {
    if (!empty($direction)) {
      try {
        $this->cursor->sort($direction);
      }
      catch (MongoCursorException $e) {
        throw new Exception('Failed sorting ' . print_r($direction, TRUE), $e->getCode(), $e->getMessage());
      }
    }
    return $this;
  }

  /**
   * Limits the number of results returned.
   * @var Integer $number - the number of records to limit the result.
   * @return Object - this.
   */
  public function limit($number = 0) {
    if (!empty($number)) {
      try {
        $this->cursor->limit($number);
      }
      catch (MongoCursorException $e) {
        throw new Exception('Failed limiting results by ' . $number, $e->getCode(), $e->getMessage());
      }
    }
    return $this;
  }

  /**
   * Counts the number of results for this query.
   * @var Boolean $found - send cursor limit and skip information to the count function, if applicable. 
   */
  public function count($found = FALSE) {
    try {
      return $this->cursor->count($found);
    }
    catch (MongoConnectionException $e) {
      throw new Exception('Failed retrieving count', $e->getCode(), $e->getMessage());
    }
  }

  /**
   * Return the result as an array.
   * @return Array of documents.
   */
  public function asArray() {
    $data = array();
    if ($this->cursor->hasNext()) {
      while ($this->cursor->hasNext()) {
        $data[] = $this->cursor->getNext();
      }
      return $data;
    }
    else {
      return $data[] = $this->cursor;
    }
  }

  /**
   * Return the result as an object.
   * @return Array of documents.
   */
  public function asObject() {
    $data = array();
    if ($this->cursor->hasNext()) {
      while ($this->cursor->hasNext()) {
        $data[] = (object) $this->cursor->getNext();
      }
      return $data;
    }
    else {
      return $data[] = (object) $this->cursor;
    }
  }

  /**
   * Get the number of documents within a collection.
   * @param Array $query - associative array or object with fields to match.
   * @param Int $limit - specifies an upper limit to the number returned.
   * @param Int $skip - specifies a number of results to skip before starting the count.
   * @return Long - the number of documents in the current collection.
   */
  public function collectionCount($query = array(), $limit = 0, $skip = 0) {
    try {
      return $this->collection->count($query, $limit, $skip);
    }
    catch (MongoCursorException $e) {
      throw new Exception('Failed counting documents for ' . print_r($query, TRUE), $e->getCode(), $e->getMessage());
    }
  }

  /**
   * Inserts an array into the collection.
   * @param Array $document - an array containing key values pairs.
   * @param Array $options:
   *        safe - can be a boolean or integer, defaults to FALSE. If FALSE, the program continues executing without waiting for a database response. if TRUE, the program will wait for the database response and throw a MongoCursorException if the insert did not succeed.
   *        fsync - boolean, defaults to FALSE. Forces the insert to be synced to disk before returning success. If TRUE, a safe insert is implied and will override setting safe to FALSE.
   *        timeout - integer, defaults to MongoCursor::$timeout. if "safe" is set, this sets how long (in milliseconds) for the client to wait for a database response. if the database does not respond within the timeout period, a MongoCursorTimeoutException will be thrown.
   * @return Mixed - If safe was set, returns an array containing the status of the insert (http://www.php.net/manual/en/mongocollection.insert.php#refsect1-mongocollection.insert-returnvalues). otherwise, returns a boolean representing if the array was not empty (an empty array will not be inserted).
   */
  public function insertDocument($document = array(), $options = array()) {
    try {
      return $this->collection->insert($document, $options);
    }
    catch (MongoCursorException $e) {
      throw new Exception('Failed inserting documents ' . print_r($document, TRUE), 0, $e->getMessage()); 
    }
    catch (MongoCursorTimeoutException $e) {
      throw new Exception('Database does not respond within the timeout period', $e->getCode(), $e->getMessage());
    }
  }

  /**
   * Update records based on a given criteria.
   * @param Array $criteria - an array containing key values pairs.
   * @param Array $object - the object with which to update the matching records.
   * @param Array $options
   *        upsert - if no document matches $criteria, a new document will be created from $criteria.
   *        multiple - all documents matching $criteria will be updated.
   *        safe - can be a boolean or integer, defaults to FALSE. If FALSE, the program continues executing without waiting for a database response. if TRUE, the program will wait for the database response and throw a MongoCursorException if the update did not succeed.
   *        fsync - boolean, defaults to FALSE. Forces the update to be synced to disk before returning success. If TRUE, a safe update is implied and will override setting safe to FALSE.
   *        timeout - integer, defaults to MongoCursor::$timeout. if "safe" is set, this sets how long (in milliseconds) for the client to wait for a database response. If the database does not respond within the timeout period, a MongoCursorTimeoutException will be thrown.
   * @return Mixed - If safe was set, returns an array containing the status of the update. Otherwise, returns a boolean representing if the array was not empty (an empty array will not be inserted).
   */
  public function updateDocument($criteria = array(), $object = array(), $options = array()) {
    try {
      return $this->collection->update($criteria, $object, $options);
    }
    catch (MongoCursorException $e) {
      throw new Exception('Failed updating object with criteria ' . print_r($criteria, TRUE), 0, $e->getMessage());
    }
    catch (MongoCursorTimeoutException $e) {
      throw new Exception('Database does not respond within the timeout period', $e->getCode(), $e->getMessage());
    }
  }

  /**
   * If the object is from the database, update the existing database object, otherwise insert this object.
   * @param Array $object - saves an object to the current collection.
   * @param Array $options:
   *        safe - can be a boolean or integer, defaults to FALSE. if FALSE, the program continues executing without waiting for a database response. if TRUE, the program will wait for the database response and throw a MongoCursorException if the insert did not succeed.
   *        fsync - boolean, defaults to FALSE. Forces the insert to be synced to disk before returning success. if TRUE, a safe insert is implied and will override setting safe to FALSE.
   *        timeout - integer, defaults to MongoCursor::$timeout. if "safe" is set, this sets how long (in milliseconds) for the client to wait for a database response. if the database does not respond within the timeout period, a MongoCursorTimeoutException will be thrown.
   * @return Mixed - If safe was set, returns an array containing the status of the save. Otherwise, returns a boolean representing if the array was not empty (an empty array will not be inserted).
   */
  public function saveDocument($object = array(), $options = array()) {
    try {
      return $this->collection->save($object, $options);
    }
    catch (MongoCursorException $e) {
      throw new Exception('Failed saving object ' . print_r($object, TRUE), $e->getCode(), $e->getMessage());
    }
    catch (MongoCursorTimeoutException $e) {
      throw new Exception('Database does not respond within the timeout period', $e->getCode(), $e->getMessage());
    }
  }

  /**
   * Remove records from this collection
   * @param Array $criteria - description of records to remove.
   * @param Array $options:
   *        justOne - remove at most one record matching this criteria.
   *        safe - can be a boolean or integer, defaults to FALSE. If FALSE, the program continues executing without waiting for a database response. if TRUE, the program will wait for the database response and throw a MongoCursorException if the update did not succeed.
   *        fsync - boolean, defaults to FALSE. Forces the update to be synced to disk before returning success. if TRUE, a safe update is implied and will override setting safe to FALSE.
   *        timeout - integer, defaults to MongoCursor::$timeout. if "safe" is set, this sets how long (in milliseconds) for the client to wait for a database response. if the database does not respond within the timeout period, a MongoCursorTimeoutException will be thrown.
   * @return Mixed - if safe was set, returns an array containing the status of the remove. Otherwise, returns a boolean representing if the array was not empty (an empty array will not be inserted).
   */
  public function deleteDocument($criteria = array(), $options = array()) {
    try {
      return $this->collection->remove($criteria, $options);
    }
    catch (MongoCursorException $e) {
      throw new Exception('Failed deleting object with criteria ' . print_r($criteria, TRUE), $e->getCode(), $e->getMessage());
    }
    catch (MongoCursorTimeoutException $e) {
      throw new Exception('Database does not respond within the timeout period', $e->getCode(), $e->getMessage());
    }
  }
}
