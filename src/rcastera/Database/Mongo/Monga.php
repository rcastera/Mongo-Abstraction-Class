<?php
/**
 * Copyright (c) 2010 Richard Castera
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without li`ation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace rcastera\Database\Mongo;

class Monga
{
    /**
     * Holds the connection object.
     *
     * @var object
     */
    private $connection;

    /**
     * Holds the database object.
     *
     * @var object
     */
    private $database;

    /**
     * Holds the collection object.
     *
     * @var object
     */
    private $collection;

    /**
     * Holds the cursor object.
     *
     * @var object
     */
    private $cursor;

    /**
     * Constructor.
     *
     * @param Array $server - parameters for the connection string. See @example
     * @param Array $options
     *
     * @see http://www.php.net/manual/en/mongoclient.construct.php
     * @example $mongo - new Mongo(array('username'=>'username', 'password'=>'password', 'host'=>'localhost', 'port'=>'27017'));
     */
    public function __construct($server = array(), $options = array())
    {
        if (empty($server) || ! isset($server['host'])) {
            $host = $server['host'] = 'mongodb://localhost:27017';
        } else if (! empty($server['username']) && ! empty($server['password'])) {
            $host = "mongodb://${server['username']}:${server['password']}@${server['host']}:${server['port']}";
        } else if (! empty($server['port'])) {
            $host = "mongodb://${server['host']}:${server['port']}";
        } else {
            $host = "mongodb://${server['host']}";
        }

        try {
            $this->connection = new \MongoClient($host, $options);
        } catch (\MongoConnectionException $e) {
            throw new \Exception('Error connecting to Mongo Server @' . $server['host'] . ' Exception: ' . $e->getMessage());
        } catch (\MongoException $e) {
            throw new \Exception('Error connecting to Mongo Server @' . $server['host'], $e->getCode(), $e->getMessage());
        }
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        $this->connection->close();
    }

    /**
     * Retrieves an array of open connections.
     *
     * @return array
     */
    public function connections()
    {
        return $this->connection->getConnections();
    }

    /**
     * It returns the status of all of the hosts in the set.
     *
     * @return array
     */
    public function hosts()
    {
        return $this->connection->getHosts();
    }

    /**
     * Lists all of the databases available.
     *
     * @return array
     */
    public function databases()
    {
        return $this->connection->listDBs();
    }

    /**
     * Sets the database to use.
     *
     * @param string $database - database name.
     *
     * @return object $this.
     */
    public function setDatabase($database = '')
    {
        try {
            $this->database = $this->connection->selectDB($database);
        } catch (\Exception $e) {
            throw new \Exception('Could not select database ' . $database, $e->getCode(), $e->getMessage());
        }
        return $this;
    }

    /**
     * Gets the mongodb database object.
     *
     * @see http://www.php.net/manual/en/class.mongodb.php
     *
     * @return \MongoDB
     */
    public function getDatabase()
    {
        if (! $this->database instanceof \MongoDB) {
            throw new \Exception('Mongo database object not set.');
        }

        return $this->database;
    }

    /**
     * Sets the collection to use.
     *
     * @param string $collection - collection name.
     *
     * @return object $this.
     */
    public function setCollection($collection = '')
    {
        try {
            $this->collection = $this->database->selectCollection($collection);
        } catch (\Exception $e) {
            throw new \Exception('Could not select collection ' . $collection, $e->getCode(), $e->getMessage());
        }
        return $this;
    }

    /**
     * Gets the mongodb collection object.
     *
     * @see http://www.php.net/manual/en/class.mongocollection.php
     *
     * @return \MongoCollection
     */
    public function getCollection()
    {
        if (! $this->collection instanceof \MongoCollection) {
            throw new \Exception('Mongo collection object not set.');
        }

        return $this->collection;
    }
}
