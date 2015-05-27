<?php
/**
 *  абстрактный класс контроллера
 * Date: 23.05.15
 */

abstract class cnt_base {
    protected $msg ;    // сообщения класса - объект Message
    protected $parListGet = [] ;  // параметры класса
    protected $parListPost = [] ;  // параметры класса
    protected $msgTitle = '' ;
    protected $msgName = '' ;
    protected $modelName = '' ;
    protected $mod ;
    protected $parForView = [] ;   // параметры для передачи view
    protected $nameForView = false ;  // имя для передачи в ViewDriver
    protected $nameForStore = '' ; // имя строки параметров в TaskStore
    protected $ownStore = [] ;     // собственные сохраняемые параметры
    protected $forwardCntName = false ; // контроллер, которому передается управление
    public function __construct($getArray,$postArray) {
        //$this->msg = new Message($this->msgTitle) ;
        $this->msg = TaskStore::getParam('message') ;
        $class = $this->modelName ;
        if (!empty($class)) {
            $this->mod = new $class();
        }
        if (!empty($this->nameForStore)) {
           $this->ownStore = TaskStore::getParam($this->nameForStore) ; //  взять параметры из TaskStore
        }
        $this->parListGet = $getArray ;
        $this->parListPost = $postArray ;
        $this->prepare() ;
    }
    protected function prepare() {
        //------- работа   ------------//
        $this->buildOwnStore() ; // построить массив параметров
        $this->saveOwnStore() ;  //  сохранить параметры
    }

    /**
     *  построить массив $ownStore - собственные параметры
     */
    protected function buildOwnStore() {

    }
    protected function saveOwnStore() {
        if (!empty($this->nameForStore)) {
            TaskStore::setParam($this->nameForStore,$this->ownStore) ; //  сохранить параметры из TaskStore
        }
    }
    /**
     * выдает имя контроллера для передачи управления
     * альтернатива viewGo
     * Через  $pListGet , $pListPost можно передать новые параметры
     */
    public function getForwardCntName(&$plistGet,&$pListPost) {
        $plistGet = [] ;
        $plistPost = [] ;
        return $this->forwardCntName ;
    }
    public function viewGo() {
        $vd = new ViewDriver($this->nameForView) ;
        $vd->viewExec($this->parForView) ;
    }
}