<?php

function show_page($tpl, $title, $pathPrefixFromRoot = '') {
    global $smarty;
    $smarty->assign('path', PATH);
    $smarty->assign('PATH_TO_ROOT', PATH_TO_ROOT);
    $smarty->assign('title', TITLE_PREFIX . ' - ' . $title);
    $smarty->display('header.html');
    $smarty->display($tpl . '.html');
    $smarty->display('footer.html');
}

function show_page_noHeader($tpl, $title) {
    global $smarty;
    $smarty->assign('title', TITLE_PREFIX . ' - ' . $title);
    $smarty->display($tpl . '.html');
}

function redirect($page) {
    header("Location: $page");
    die();
}

function __autoload($className) {
    if (file_exists('classes/' . $className . '.php')) {
        require_once 'classes/' . $className . '.php';
        return true;
    }
    return false;
}

function dbRequired($dbname) {


    try {
        DB::getInstance($dbname);
    } catch (PDOException $Exception) {
        echo 'Database "' . $dbname . '"  is required.';
        exit;
    }
}

function loadSmarty() {
    
    global $smarty;
    require_once PATH . '/libs/Smarty.class.php';
    $smarty = new Smarty;
    $smarty->template_dir = PATH . '/tpl';
    $smarty->compile_dir = PATH . '/libs/templates_c';
    $smarty->cache_dir = PATH . '/libs/cache';
    $smarty->config_dir = PATH . '/libs/configs';
}

?>