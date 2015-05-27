<?php
/**
 * контроллер регистрации
 * Date: 23.05.15
 */

class cnt_user extends cnt_base {
    protected $msg ;    // сообщения класса - объект Message
    protected $parListPost = [] ;  // параметры класса
    protected $parListGet = [] ;  // параметры класса
    protected $subController = false ; // контроллер уровня +1
    protected $msgTitle = '' ;
    protected $modelName = 'mod_user' ;
    protected $mod ;             // объект - модель
    protected $parForView = [] ; //  параметры для передачи view
    protected $nameForView = 'cnt_user' ; // имя для передачи в ViewDriver
    protected $forwardCntName = false ; // контроллер, которому передается управление
    //--------------------------------//
    private $CNT_HOME = 'cnt_default' ;
    private $CNT_PROFILE = 'cnt_profile' ;
    private $URL_TO_PROFILE ;         // адрес для перехода по cnt_profile
    private $URL_TO_USER ;            // адрес для перехода по cnt_user
    //------------------------------------//
    public function __construct($getArray,$postArray) {
        parent::__construct($getArray,$postArray) ;
        $this->URL_TO_PROFILE = TaskStore::$htmlDirTop.'/index.php?cnt=cnt_profile' ;
        $this->URL_TO_USER = TaskStore::$htmlDirTop.'/index.php?cnt=cnt_user' ;

    }
    protected function prepare() {
        if (isset($this->parListPost['exit'])) {              // выход - возврат на главную
            $this->forwardCntName = $this->CNT_HOME ;
        }elseif (isset($this->parListPost['profile']) ) {    //  переход в профиль
            if ($this->isGoProfile()) {
                $this->forwardCntName = $this->CNT_PROFILE ;
            }
        }elseif (isset($this->parListPost['enter'])  ) {
            $userLoginSuccessful = $this->isUserLoginSuccessful() ;
        if ($userLoginSuccessful) {           // вход выполнен
                $this->forwardCntName = $this->CNT_HOME ;
            }
        }else {   // продолжение входа ->  формировать $parForView
            $this->parForView = [
                'login'    => '',
                'password' => '' ,
                'profileIsPossible' => false,
                'urlToProfile' => $this->URL_TO_PROFILE,
                'urlToUser' => $this->URL_TO_USER ] ;
        }
    }
    /**
     * Возможность перехода в профиль
     */
    private function isGoProfile() {
        $userStatus = TaskStore::getParam('userStatus') ;
        $userLogin = TaskStore::getParam('userLogin') ;

        $this->msg->addMessage('DEBUG:'.__METHOD__.'login:'.$userLogin.'-status:'.$userStatus) ;

        return( $userStatus >= TaskStore::USER_STAT_USER  &&  !empty($userLogin)) ;
    }
    private function isUserLoginSuccessful() {
        $isSuccessful = false;
        $login = $this->parListPost['login'];
        $password = $this->parListPost['password'];

        if (empty($login) || empty($password)) {
            $this->msg->addMessage('ERROR:Поля "Имя:" и "Пароль:" должны быть заполнены !');
        } else {
            $userPassw = $this->mod->getUser($login);
            if (false === $userPassw) { // $login отсутствует в БД
                $this->msg->addMessage('ERROR: Недопустимое имя пользователя.Повторите ввод!');
            } else {  // проверяем пароль
                $fromDBPassw = $userPassw['password'];
                if ($fromDBPassw !== md5($password)) {
                    $this->msg->addMessage('ERROR: Неверный пароль.Повторите ввод !');
                } else {
                    $isSuccessful = true;
                    TaskStore::setParam('userLogin', $login);
                    TaskStore::setParam('userName', $login);
                    TaskStore::setParam('userPassword', $password);
                    TaskStore::setParam('enterSuccessful', true);
                    TaskStore::setParam('userStatus', TaskStore::USER_STAT_USER);
                    if ('admin' == $login) {
                        TaskStore::setParam('userStatus', TaskStore::USER_STAT_ADMIN);
                    }
                }
            }
        }
        return $isSuccessful;
    }
    /**
     * выдает имя контроллера для передачи управления
     * альтернатива viewGo
     * Через  $pListGet , $pListPost можно передать новые параметры
     */
    public function getForwardCntName(&$plistGet,&$plistPost) {
        $plistGet = [] ;
        $plistPost = [] ;
        if ($this->forwardCntName == $this->CNT_PROFILE) { // редактирование существующего профиля
            $plistGet = ['edit' => true] ;
        }
        return $this->forwardCntName ;
      //  parent::getForwardCntName($plistGet,$plistPost) ;
    }
    /**
     * переход на собственную форму
     */
    public function viewGo() {
        $login = $this->parListPost['login'];
        $password = $this->parListPost['password'];

        $login = (empty($login)) ? TaskStore::getParam('userLogin') : $login ;
        $password = (empty($password)) ? TaskStore::getParam('userPassword') : $password ;

        $profileIsPossible = ($login === TaskStore::getParam('userLogin')) ?
            TaskStore::getParam('enterSuccessful') : false ;

        $this->parForView = [
            'login'    => $login,
            'password' => $password ,
            'profileIsPossible' => $profileIsPossible,
            'urlToProfile' => $this->URL_TO_PROFILE,
            'urlToUser' => $this->URL_TO_USER ] ;
       parent::viewGo() ;
    }
}