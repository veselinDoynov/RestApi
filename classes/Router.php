<?php

class Router {

    private $mapping;
    private $requestedPath = '';
    private $currentRule = '';
    private $currentRequest = '';
    private $currentRouteVariables = array();
    private $currentRequestValid = false;
    private $app = 'app.php';
    public static $statusCodes = array(
        200 => 'success',
        204 => 'no content',
        400 => 'bad request',
        404 => 'not supported url',
        405 => 'wrong controller'
    );

    public function __construct($testsCase = false) {

        if ($testsCase)
            $this->app = 'tests.php';

        $requestedUri = explode($this->app, $_SERVER['REQUEST_URI']);
        if (count($requestedUri) != 2) {
            echo $this->mapResultStatus(json_encode(array('status' => 404)));
            exit;
        }
        if (isset($requestedUri[1]))
            $this->requestedPath = $requestedUri[1];
    }

    public function map($method = '', $urlPattern = '', $callback = '') {
        $this->mapping[] = array('method' => $method, 'urlPattern' => $urlPattern, 'calback' => $callback);
    }

    public function match() {

        $result = $this->mapResultStatus(json_encode(array('status' => 404)));
        foreach ($this->mapping as $map)
            if ($_SERVER['REQUEST_METHOD'] == $map['method']) {
                $this->processGettingRouterParameters($map);
                if ($this->currentRequestValid) {
                    $result = call_user_func_array($map['calback'], $this->currentRouteVariables);
                    return $this->mapResultStatus($result);
                    exit;
                }
            }
        return $result;
        exit;
    }

    private function processGettingRouterParameters($map) {

        $this->resetSettings($map);
        if ($this->validateRequestAgainstRulesLength())
            $this->getVariablesFromRoute();
    }

    private function setCurrentRuleAndRequest($map) {

        $this->addDynamicSlash($map);
        $this->currentRule = explode('/', $map['urlPattern']);
        $this->currentRequest = explode('/', $this->requestedPath);
    }

    private function addDynamicSlash($map) {

        if (substr($map['urlPattern'], -1) == '/' && substr($this->requestedPath, -1) != '/')
            $this->requestedPath .= '/';
        if (substr($map['urlPattern'], -1) != '/' && substr($this->requestedPath, -1) == '/')
            $this->requestedPath = substr($this->requestedPath, 0, -1);
    }

    private function resetSettings($map) {

        $this->currentRouteVariables = array();
        $this->currentRequestValid = false;
        $this->setCurrentRuleAndRequest($map);
    }

    private function validateRequestAgainstRulesLength() {

        if (count($this->currentRequest) < $this->getRequiredLengthOfUrlPattern($this->currentRule)) {
            $this->currentRequestValid = false;
            return false;
        }
        return true;
    }

    private function getVariablesFromRoute() {

        $this->currentRequestValid = true;
        for ($i = 0; $i < count($this->currentRequest); $i++) {
            if (!isset($this->currentRule[$i])) {
                $this->currentRequestValid = false;
                break;
            }
            $this->collectVariablesFromRoute($i);
            if (!$this->currentRequestValid)
                break;
        }
    }

    private function collectVariablesFromRoute($i) {

        if ($this->currentRequest[$i] != $this->currentRule[$i]) {
            $this->collectVariablesFromRouteProcess($i);
        } elseif (preg_match('/^:.+/', $this->currentRequest[$i]) || preg_match('/^\[:.+\]$/', $this->currentRequest[$i])) {
            //handle private cases when parameter have the same value us variable name ... ":varName" or "[:varName]"
            $this->collectVariablesFromRouteProcess($i);
        }
    }

    private function collectVariablesFromRouteProcess($i) {

        if (preg_match('/^:.+/', $this->currentRule[$i]) || preg_match('/^\[:.+\]$/', $this->currentRule[$i]))
            $this->currentRouteVariables[] = $this->currentRequest[$i];
        else
            $this->currentRequestValid = false;
    }

    /*
     *  count only variables with this format :var
     *  variables with format [:var] are optional
     */

    private function getRequiredLengthOfUrlPattern($rules) {

        $requiredCount = 0;
        foreach ($rules as $element) {
            if (!preg_match('/^\[:.+\]$/', $element))
                $requiredCount++;
        }
        return $requiredCount;
    }

    private function mapResultStatus($result) {

        if (!$result = json_decode($result, true))
            return $result;
        if (isset($result['status']) && isset(self::$statusCodes[$result['status']]))
            $result['status'] = self::$statusCodes[$result['status']];

        return json_encode($result);
    }

}
