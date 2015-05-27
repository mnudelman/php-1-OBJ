<?php
/**
 * Выбирает компонент контроллера, которому надо передать управление
 * Контроллер должен погружаться по autoload
 *  rooter только запускает контроллер.
 * Все остальное(обработка $_GET, $_POST) выполняет контроллер.
 * User: mnudelman@yandex.ru
 * Date: 22.05.15
 *
 */
class Router {
    private $contName = 'cnt_default' ;
    private $paramListGet = [] ;
    private $paramListPost = [] ;
    private $msg ;
    //-----------------------------------//
    public function __construct() {
        if (isset($_GET['cnt'])) {
            $this->contName = $_GET['cnt'] ;
        }elseif (isset($_POST['cnt'])) {
            $this->contName = $_POST['cnt'] ;
        }
        $this->paramListGet = $_GET ;
        $this->paramListPost = $_POST ;
        $this->msg = TaskStore::getMessage() ;
    }

    public function controllerGo() {
        while (true) {
            $class = $this->contName ;
            $pListGet = $this->paramListGet ;
            $pListPost = $this->paramListPost ;
            $cntr = new $class($pListGet,$pListPost) ;
            $newCnt = $cntr->getForwardCntName($pListGet,$pListPost) ;
            if (!empty($newCnt)  ) {
                $this->contName = $newCnt ;
                $this->paramListGet = $pListGet ;
                $this->paramListPost = $pListPost ;
                continue ;
            }
            break ;
        }
        $cntr->viewGo() ;   // вывод формы контроллера
    }

}