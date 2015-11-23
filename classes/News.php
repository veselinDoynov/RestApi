<?php

class News {

    private $dbInstance;

    public function __construct() {

        $this->dbInstance = DB::getInstance('apidb');
    }

    public function obtaionNews($parameters = array()) {

        $id = Adapter::getParameter('id', $parameters);
        $result = $this->obtaionNewsAction((int) $id);
        return json_encode(array('result' => $result, 'status' => !empty($result) ? 200 : 204));
        exit;
    }

    public function addNews() {

        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $date = date('Y-m-d');
        if (!empty($title) && !empty($content)) {
            $id = $this->addNewsAction($title, $content, $date);
            return json_encode(array('result' => array('id' => $id, 'title' => $title, 'content' => $content, 'date' => $date), 'status' => 200));
        } else {
            return json_encode(array('result' => '', 'status' => 400));
        }
        exit;
    }

    public function editNews($parameters = array()) {

        $id = Adapter::getParameter('id', $parameters);
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $date = date('Y-m-d');
        if (!empty($title) && !empty($content)) {
            if ($this->editNewsAction($title, $content, $date, (int) $id))
                return json_encode(array('result' => array('id' => $id, 'title' => $title, 'content' => $content, 'date' => $date), 'status' => 200));
            else
                return json_encode(array('result' => array('id' => $id, 'title' => $title, 'content' => $content, 'date' => $date, 'error' => 'wrong ID'), 'status' => 400));
        }
        exit;
    }

    public function deleteNews($parameters = array()) {

        $id = Adapter::getParameter('id', $parameters);
        if ($this->deleteNewsAction((int) $id))
            return json_encode(array('result' => array('id' => $id,), 'status' => 200));
        else
            return json_encode(array('result' => array('id' => $id, 'error' => 'wrong ID'), 'status' => 400));
        exit;
    }

    private function addNewsAction($title, $content, $date) {

        $query = "INSERT INTO news (title,date,text) VALUES(:title, :date, :text)";
        $stmt = $this->dbInstance->prepare($query);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':date', $date, PDO::PARAM_STR);
        $stmt->bindValue(':text', $content, PDO::PARAM_STR);

        if ($stmt->execute())
            return $this->dbInstance->lastInsertId();
    }

    private function obtaionNewsAction($id) {

        $query = "SELECT * FROM news ";
        if ($id)
            $query .= " Where id = :id";
        $stmt = $this->dbInstance->prepare($query);
        if ($id)
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function editNewsAction($title, $content, $date, $id) {

        if (!$this->checkIfRecordExists($id))
            return false;

        $query = "Update news Set title = :title,date = :date ,text = :text Where id = :id ";
        $stmt = $this->dbInstance->prepare($query);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':date', $date, PDO::PARAM_STR);
        $stmt->bindValue(':text', $content, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return true;
    }

    private function deleteNewsAction($id) {

        $query = "Delete From news Where id = :id";
        $stmt = $this->dbInstance->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $execute = $stmt->execute();
        if ($execute)
            if ($stmt->rowCount())
                return true;
        return false;
    }

    private function checkIfRecordExists($id) {

        $query = "SELECT * FROM news Where id = :id";
        $stmt = $this->dbInstance->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return count($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

}
