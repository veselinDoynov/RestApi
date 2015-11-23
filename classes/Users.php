<?php

class Users {

    private $dbInstance;

    public function __construct() {
        $this->dbInstance = DB::getInstance('dopamine');
    }

    public function getUser($parameters = array()) {

        $userId = Adapter::getParameter('userId', $parameters);
        $result = $this->getUserAction($userId);

        return json_encode(array('result' => $result, 'status' => !empty($result) ? 200 : 204));
        exit;
    }

    public function getUserComments($parameters = array()) {

        $userId = Adapter::getParameter('userId', $parameters);
        $commentId = Adapter::getParameter('commentId', $parameters);
        $result = $this->getUserCommentsAction($userId, (int) $commentId);
        return json_encode(array('result' => $result, 'status' => !empty($result) ? 200 : 204));
        exit;
    }

    private function getUserAction($userId) {

        $query = "SELECT * FROM users Where id = :userid ";
        $stmt = $this->dbInstance->prepare($query);
        $stmt->bindValue(':userid', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getUserCommentsAction($userId, $commentId) {

        $query = "SELECT * FROM comments Where userid = :userid ";
        if ($commentId)
            $query .= " AND id = :commentId ";
        $stmt = $this->dbInstance->prepare($query);
        $stmt->bindValue(':userid', $userId, PDO::PARAM_INT);
        if ($commentId)
            $stmt->bindValue(':commentId', $commentId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
