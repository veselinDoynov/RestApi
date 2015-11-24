<?php

class ApiTests {

    private static $tablesToClear = array(
        'news',
    );
    private $dbInstance;

    public function __construct() {
        $this->dbInstance = DB::getInstance('apidb_test');
    }

    public function run() {

        $this->testGetIncorrecRequestedUri();
        $this->testGetCorrectRequestedUriNoResults();
        $this->testGetCorrectRequestedUriPostResults();
        $this->testEditWrongId();
        $this->testEditCorrectId();
        $this->testDeleteRecordWrogId();
        $this->testDeleteRecordCorrectId();
        $this->testGetUsers();
        $this->testGetUsersWrongId();
        $this->testGetUserComments();
        $this->testGetUserAllComments();
        $this->testGetUserAllCommentsCounts();
        $this->testRequestedMethodValidity();
        $this->runPhpUnit();
        $this->clearTestDataBase();

    }

    private function testGetIncorrecRequestedUri() {

        $this->clearTestDataBase();
        $testStatus = "Fail";
        $_SERVER['REQUEST_METHOD'] = "GET";
        $_SERVER['REQUEST_URI'] = PATH . '/tests.php/';

        $router = new Router(true);
        $router->map('GET', '/news/:id', function($id) {
            return Adapter::invokeAdapter('News', 'obtaionNews', array('id' => $id));
        });

        $expectedcode = 404;

        $expected = array('status' => Router::$statusCodes[$expectedcode]);
        $actual = json_decode($router->match(), true);
        if ($expected == $actual) {
            $testStatus = "OK";
        }
        $this->printResult($testStatus, __FUNCTION__);
    }

    private function testGetCorrectRequestedUriNoResults() {

        $this->clearTestDataBase();
        $actual = $this->getNews(1);
        $testStatus = $this->getStatus(204, $actual);
        $this->printResult($testStatus, __FUNCTION__);
    }

    private function testGetCorrectRequestedUriPostResults() {

        $this->clearTestDataBase();
        $result = $this->postSimpleNews();
        if (Router::$statusCodes[200] == $result['status']) {
            $actual = $this->getNews(1);
            $testStatus = $this->getStatus(200, $actual);
            $this->printResult($testStatus, __FUNCTION__);
        }
    }

    private function testEditWrongId() {

        $this->clearTestDataBase();
        $_SERVER['REQUEST_METHOD'] = "POST";
        $_SERVER['REQUEST_URI'] = PATH . '/tests.php/news/1';
        $_POST['title'] = 'test title';
        $_POST['content'] = 'test content';
        $router = new Router(true);
        $router->map('POST', '/news/:id', function($id) {
            return Adapter::invokeAdapter('News', 'editNews', array('id' => $id));
        });
        $actual = json_decode($router->match(), true);
        $testStatus = $this->getStatus(400, $actual);
        $this->printResult($testStatus, __FUNCTION__);
    }

    private function testEditCorrectId() {

        $this->clearTestDataBase();
        $this->postSimpleNews();
        $_SERVER['REQUEST_METHOD'] = "POST";
        $_SERVER['REQUEST_URI'] = PATH . '/tests.php/news/1';
        $_POST['title'] = 'test title';
        $_POST['content'] = 'test content';
        $router = new Router(true);
        $router->map('POST', '/news/:id', function($id) {
            return Adapter::invokeAdapter('News', 'editNews', array('id' => $id));
        });
        $actual = json_decode($router->match(), true);
        $testStatus = $this->getStatus(200, $actual);
        $this->printResult($testStatus, __FUNCTION__);
    }

    private function testDeleteRecordWrogId() {

        $this->clearTestDataBase();
        $this->postSimpleNews();
        $_SERVER['REQUEST_METHOD'] = "DELETE";
        $_SERVER['REQUEST_URI'] = PATH . '/tests.php/news/2';
        $router = new Router(true);
        $router->map('DELETE', '/news/:id', function($id) {
            return Adapter::invokeAdapter('News', 'deleteNews', array('id' => $id));
        });
        $actual = json_decode($router->match(), true);
        $testStatus = $this->getStatus(400, $actual);
        $this->printResult($testStatus, __FUNCTION__);
    }

    private function testDeleteRecordCorrectId() {

        $this->clearTestDataBase();
        $this->postSimpleNews();
        $_SERVER['REQUEST_METHOD'] = "DELETE";
        $_SERVER['REQUEST_URI'] = PATH . '/tests.php/news/1';
        $router = new Router(true);
        $router->map('DELETE', '/news/:id', function($id) {
            return Adapter::invokeAdapter('News', 'deleteNews', array('id' => $id));
        });
        $actual = json_decode($router->match(), true);
        $testStatus = $this->getStatus(200, $actual);
        $this->printResult($testStatus, __FUNCTION__);
    }

    private function testGetUsers() {

        $actual = $this->getUserById(1);
        $testStatus = $this->getStatus(200, $actual);
        $this->printResult($testStatus, __FUNCTION__);
    }

    private function testGetUsersWrongId() {

        $actual = $this->getUserById(3);
        $testStatus = $this->getStatus(204, $actual);
        $this->printResult($testStatus, __FUNCTION__);
    }

    private function testGetUserComments() {

        $actual = $this->getUsersComment(1, 1);
        $testStatus = $this->getStatus(200, $actual);
        $this->printResult($testStatus, __FUNCTION__);
    }

    private function testGetUserAllComments() {

        $actual = $this->getUsersComment(2);
        $testStatus = $this->getStatus(200, $actual);
        $this->printResult($testStatus, __FUNCTION__);
    }

    private function testGetUserAllCommentsCounts() {

        $actual = $this->getUsersComment(1);
        $count = count($actual);
        //there are two records in db for this user
        if ($count != 2) {
            $testStatus = 'Fail';
        } else {
            $testStatus = 'OK';
        }
        $this->printResult($testStatus, __FUNCTION__);
    }

    private function testRequestedMethodValidity() {

        $testStatus = $this->getRequestedMethodValidity('GET', 'POST', 404);
        $additionalInfo = '<span>Requested method GET, routing Method POST. Failed successully</span>';
        $this->printResult($testStatus, __FUNCTION__, $additionalInfo);

        $testStatus = $this->getRequestedMethodValidity('POST', 'GET', 404);
        $additionalInfo = '<span>Requested method POST, routing Method GET. Failed successully</span>';
        $this->printResult($testStatus, __FUNCTION__, $additionalInfo);

        $testStatus = $this->getRequestedMethodValidity('DELETE', 'GET', 404);
        $additionalInfo = '<span>Requested method DELETE, routing Method GET. Failed successully</span>';
        $this->printResult($testStatus, __FUNCTION__, $additionalInfo);

        $testStatus = $this->getRequestedMethodValidity('GET', 'DELETE', 404);
        $additionalInfo = '<span>Requested method GET, routing Method DELETE. Failed successully</span>';
        $this->printResult($testStatus, __FUNCTION__, $additionalInfo);

        $testStatus = $this->getRequestedMethodValidity('POST', 'DELETE', 404);
        $additionalInfo = '<span>Requested method POST, routing Method DELETE. Failed successully</span>';
        $this->printResult($testStatus, __FUNCTION__, $additionalInfo);

        $testStatus = $this->getRequestedMethodValidity('DELETE', 'POST', 404);
        $additionalInfo = '<span>Requested method DELETE, routing Method POST. Failed successully</span>';
        $this->printResult($testStatus, __FUNCTION__, $additionalInfo);


        $testStatus = $this->getRequestedMethodValidity('GET', 'GET', 200);
        $additionalInfo = '<span>Requested method GET, routing Method GET. Match successully</span>';
        $this->printResult($testStatus, __FUNCTION__, $additionalInfo);
    }

    private function printResult($status, $functName, $additionalInfo = '') {

        $color = 'green';
        if ($status == 'Fail') {
            $color = 'red';
        }
        echo '<p>Test: <strong>' . $functName . '</strong> ... <span style="color:' . $color . '"><strong>' . $status . '</strong></span>   <span>' . $additionalInfo . '</span></p>';
    }

    private function getNews($newsId) {

        $_SERVER['REQUEST_METHOD'] = "GET";
        $_SERVER['REQUEST_URI'] = PATH . '/tests.php/news/' . $newsId;

        $router = new Router(true);
        $router->map('GET', '/news/:id', function($id) {
            return Adapter::invokeAdapter('News', 'obtaionNews', array('id' => $id));
        });


        return json_decode($router->match(), true);
    }

    private function getStatus($expectedcode, $actual) {

        $testStatus = "Fail";

        $expected = Router::$statusCodes[$expectedcode];

        if ($expected == $actual['status']) {
            $testStatus = "OK";
        }

        return $testStatus;
    }

    private function clearTestDataBase() {

        $tables = implode(',', self::$tablesToClear);

        $query = "TRUNCATE TABLE " . $tables;
        $stmt = $this->dbInstance->prepare($query);
        if (!$stmt->execute()) {
            echo 'Database not clear';
            exit;
        }
    }

    private function postSimpleNews() {

        $_SERVER['REQUEST_METHOD'] = "POST";
        $_SERVER['REQUEST_URI'] = PATH . '/tests.php/news/';
        $_POST['title'] = 'test title';
        $_POST['content'] = 'test content';

        $router = new Router(true);
        $router->map('POST', '/news/', function() {
            return Adapter::invokeAdapter('News', 'addNews');
        });
        return json_decode($router->match(), true);
    }

    private function getUserById($userId) {

        $_SERVER['REQUEST_METHOD'] = "GET";
        $_SERVER['REQUEST_URI'] = PATH . '/tests.php/users/' . $userId;
        $router = new Router(true);
        $router->map('GET', '/users/:userId/', function($userId) {
            return Adapter::invokeAdapter('Users', 'getUser', array('userId' => $userId));
        });
        return json_decode($router->match(), true);
    }

    private function getUsersComment($userId, $commentId = '') {

        $_SERVER['REQUEST_METHOD'] = "GET";
        $_SERVER['REQUEST_URI'] = PATH . '/tests.php/users/' . $userId . '/comments/' . $commentId;
        $router = new Router(true);
        $router->map('GET', '/users/:userId/comments/[:commentId]', function($userId, $commentId = 0) {
            return Adapter::invokeAdapter('Users', 'getUserComments', array('userId' => $userId, 'commentId' => $commentId));
        });
        return json_decode($router->match(), true);
    }

    private function getRequestedMethodValidity($requestedMethod, $routeRuleMethod, $expected) {

        $_SERVER['REQUEST_METHOD'] = $requestedMethod;
        $_SERVER['REQUEST_URI'] = PATH . '/tests.php/news/';

        $router = new Router(true);
        $router->map($routeRuleMethod, '/news/', function() {
            return Adapter::invokeAdapter('News', 'addNews');
        });
        $actual = json_decode($router->match(), true);
        $testStatus = $this->getStatus($expected, $actual);
        return $testStatus;
    }

    private function runPhpUnit(){
        echo 'Unit tests result:</br>';
        echo '<pre>'.shell_exec('phpunit --debug').'</pre>';
     }

}
