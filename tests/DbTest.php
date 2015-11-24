<?php

require_once 'classes/ArrayApi_DbUnit_ArrayDataSet.php';

class DbTest extends \PHPUnit_Extensions_Database_TestCase
{

    public static $host = 'localhost';
    public static $username = 'root';
    public static $password = '';
    private $pdo;

    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function getConnection()
    {
        $dbname = 'apidb_test';
        $this->pdo = new PDO('mysql:host=' . self::$host . ';dbname=' . $dbname . ';charset=utf8', self::$username, self::$password);
        return $this->createDefaultDBConnection($this->pdo);
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */

    public function getDataSet()
    {

        return new ArrayApi_DbUnit_ArrayDataSet(array(
            'users' => array(
                array('id' => 1, 'name' => 'phpway test1'),
                array('id' => 2, 'name' => 'phpway test2'),
            ),
            'comments' => array(
                array('id' => 1, 'text' => 'Comment1', 'userid' => 1),
                array('id' => 2, 'text' => 'Comment12', 'userid' => 1),
                array('id' => 3, 'text' => 'Comment13', 'userid' => 1),
                array('id' => 4, 'text' => 'Comment14', 'userid' => 1),
                array('id' => 5, 'text' => 'Comment15', 'userid' => 1),
                array('id' => 6, 'text' => 'Comment21', 'userid' => 2),
                array('id' => 7, 'text' => 'Comment22', 'userid' => 2),
            ),

        ));
    }

    public function testDbRecords()
    {

        $query = "SELECT name FROM users ";

        $stmt = $this->pdo->prepare($query);

        $stmt->execute();
        $actual = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $expected = array(array('name' => 'phpway test1'), array('name' => 'phpway test2'));
        $this->assertEquals($expected, $actual);
    }

    public function testDbComments()
    {

        $user = new Users();
        list($method, $reflection) = $this->mockMethod($user, 'getUserCommentsAction');

        $property = $reflection->getProperty('dbInstance');
        $property->setAccessible(true);
        $property->setValue($user, $this->pdo);

        $parameters = array(1,0);
        $comments = $method->invokeArgs($user, $parameters);
        $expected = 5;
        $this->assertEquals($expected, count($comments));

        $parameters = array(2,0);
        $comments = $method->invokeArgs($user, $parameters);
        $expected = 2;
        $this->assertEquals($expected, count($comments));

    }

    public function testDbCommentsSpecific()
    {

        $user = new Users();
        list($method, $reflection) = $this->mockMethod($user, 'getUserCommentsAction');

        $property = $reflection->getProperty('dbInstance');
        $property->setAccessible(true);
        $property->setValue($user, $this->pdo);

        $parameters = array(1,4);
        $comments = $method->invokeArgs($user, $parameters);
        $expected = 1;
        $this->assertEquals($expected, count($comments));

        $parameters = array(1,7);
        $comments = $method->invokeArgs($user, $parameters);
        $expected = 0;
        $this->assertEquals($expected, count($comments));


    }

    public function mockMethod(&$object, $methodName)
    {

        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);


        return array($method, $reflection);
    }
}

