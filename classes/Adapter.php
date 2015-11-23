<?php

class Adapter {
    
    public static function invokeAdapter($className, $methodName, $parameters = array()) {
        if (class_exists($className) && method_exists($className, $methodName)) {
            $obj = new $className;
            return $obj->$methodName($parameters);
        } else {
            return json_encode(array('result' => '', 'status' =>405));
        }
    }

    public static function getParameter($paramaterKey, $parameters) {

        $paramater = 0;
        if (isset($parameters[$paramaterKey]))
            $paramater = $parameters[$paramaterKey];
        return $paramater;
    }

}
