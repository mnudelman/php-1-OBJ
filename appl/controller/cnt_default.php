<?php
/**
 * контроллер регистрации
 * Date: 23.05.15
 */

class cnt_default extends cnt_base {
    protected $msg ;    // сообщения класса - объект Message
    protected $parListPost = [] ;  // параметры класса
    protected $parListGet = [] ;  // параметры класса
    protected $msgTitle = '' ;
    protected $modelName = '' ;
    protected $mod ;             // объект - модель
    protected $parForView = [] ; //  параметры для передачи view
    protected $nameForView = 'cnt_default' ; // имя для передачи в ViewDriver
    protected $forwardCntName = false ; // контроллер, которому передается управление
    //--------------------------------//
    public function __construct($getArray,$postArray) {
        parent::__construct($getArray,$postArray) ;
    }
    protected function prepare() {
        $this->msg->addMessage('Для начала работы из меню выберите для просмотра любой альбом из имеющихся');
        $this->msg->addMessage('Для создания собственных альбомов надо пройти регисрацию');
        $this->msg->addMessage('Подробности о работе сайта пункт меню about');
    }
    /**
     * выдает имя контроллера для передачи управления
     * альтернатива viewGo
     * Через  $pListGet , $pListPost можно передать новые параметры
     */
    public function getForwardCntName(&$plistGet,&$pListPost) {
        parent::getForwardCntName($plistGet,$pListPost) ;
    }
    /**
     * переход на собственную форму
     */
    public function viewGo() {
        parent::viewGo() ;
    }
}