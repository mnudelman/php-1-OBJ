<?php

session_start() ;
/**
 * по $_GET определяет передачу управления
 * контроллерам 2 уровня
 *  параметр: определяет имя контроллера 2 уровня {?user | ?gallery}
 */
?>
<?php
ini_set('display_errors', 1);
//error_reporting(E_ALL) ;
error_reporting(E_ALL ^ E_NOTICE);
header('Content-type: text/html; charset=utf-8');
include_once __DIR__ . '/local.php';
?>
<?php
// Загружаем router
$router = new Router();
// запускаем контроллер
$router->controllerGo() ;
