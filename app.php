<?php

require_once 'init.php';
$router = new Router();

$router->map('GET', '', function() {
    loadSmarty();
    show_page('index', 'Home');
});
$router->map('GET', '/news/:id', function($id) {
    return Adapter::invokeAdapter('News', 'obtaionNews', array('id' => $id));
});
$router->map('GET', '/news/', function() {
    return Adapter::invokeAdapter('News', 'obtaionNews');
});
$router->map('POST', '/news/:id', function($id) {
    return Adapter::invokeAdapter('News', 'editNews', array('id' => $id));
});
$router->map('POST', '/news/', function() {
    return Adapter::invokeAdapter('News', 'addNews');
});
$router->map('DELETE', '/news/:id', function($id) {
    return Adapter::invokeAdapter('News', 'deleteNews', array('id' => $id));
});
$router->map('GET', '/users/:userId/', function($userId) {
    return Adapter::invokeAdapter('Users', 'getUser', array('userId' => $userId));
});

$router->map('GET', '/users/:userId/comments/[:commentId]', function($userId, $commentId = 0) {
    return Adapter::invokeAdapter('Users', 'getUserComments', array('userId' => $userId, 'commentId' => $commentId));
});

echo $router->match();
?>
