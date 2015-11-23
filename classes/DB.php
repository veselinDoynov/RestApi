<?php

class DB {

    private static $instance;
    private static $configPath = 'config/config.ini';
    private static $host = '';
    private static $user = '';
    private static $password = '';

    private function __construct($dbname) {

        $this->getCredemtialsFromConfig();
        self::$instance = new PDO('mysql:host=' . self::$host . ';dbname=' . $dbname . ';charset=utf8', self::$user, self::$password);
    }

    private function getCredemtialsFromConfig() {
        if (is_file(self::$configPath)) {
            $configs = parse_ini_file(self::$configPath);
            self::$host = $configs['hostname'];
            self::$password = $configs['password'];
            self::$user = $configs['username'];
        }
    }

    public static function getInstance($dbname) {
        if (!isset(self::$instance))
            new DB($dbname);
        return self::$instance;
    }
    
    public static function runQuery($query){
        
        $stmt = self::$instance->prepare($query);
        $stmt->execute();
        return $smtp;
    }

}
