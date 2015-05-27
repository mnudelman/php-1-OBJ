<?php
/**
 * класс - редактирование профиля
 * Date: 25.05.15
 */

class cnt_profile extends cnt_base {
    protected $msg ;    // сообщения класса - объект Message
    protected $parListGet = [] ;  // параметры класса
    protected $parListPost = [] ;  // параметры класса
    protected $msgTitle = '' ;
    protected $msgName = '' ;
    protected $modelName = 'mod_user' ;
    protected $mod ;
    protected $parForView = [] ; //  параметры для передачи view
    protected $nameForView = 'cnt_profile' ; // имя для передачи в ViewDriver
    protected $nameForStore = 'cnt_profile'; // имя строки параметров в TaskStore
    protected $ownStore = [] ;     // собственные сохраняемые параметры
    protected $forwardCntName = false ; // контроллер, которому передается управление
    //-----------------------------------//
    private $CNT_HOME = 'cnt_default' ;
    private $profile = [] ;
    private $login ;
    private $password ;
    private $profileEditFlag = false ;      // true -> редактировать существующий профиль
    private $successfulSave = false ;       // удачное сохранение профиля
    private $profileError = false ;         // ошибка формирования профиля
    private $URL_TO_PROFILE ;               // адрес перехода на  cnt_profile
    private $URL_TO_DEFAULT ;               // адрес перехода на  cnt_default
    //-----------------------------------//
    public function __construct($getArray,$postArray) {
     parent::__construct($getArray,$postArray) ;
     $this->URL_TO_PROFILE = TaskStore::$htmlDirTop.'/index.php?cnt=cnt_profile' ;
     $this->URL_TO_DEFAULT = TaskStore::$htmlDirTop.'/index.php?cnt=cnt_default' ;
    }
    protected function prepare() {

        if (is_array($this->parListPost)) {
            foreach ($this->parListPost as $key => $mean) {
                $this->msg->addMessage('DEBUG:POST:' . $key . '-' . $mean);
            }
        }
        // при редактировании профиля поля login,password убираются
        $this->profileEditFlag = isset($this->parListGet['edit']) || // редакт существующий
            (isset($this->parListPost['save']) &&
                !isset($this->parListPost['login']) && !isset($this->parListPost['password'])) ;
        //-----------------------------------------------
        $this->login = ($this->profileEditFlag) ?
            TaskStore::getParam('userLogin') : $this->parListPost['login'];
        $this->password = ($this->profileEditFlag) ?
            TaskStore::getParam('userPassword') :$this->parListPost['password'];

        if ($this->profileEditFlag) {  // Изменить существующий профиль
            $this->profile = $this->getProfileForEdit() ;   // получить профиль из БД
            if ( false === $this->profile ) {
                $this->$profileError = true;
                $this->msg->addMessage('ERROR:Не пройдена регистрация.Профиль не доступен!') ;
                $this->$forwardCntName = $this->CNT_HOME ;
}
        }
        if (isset($this->parListPost['exit'])) {    // выйти
            $this->forwardCntName = $this->CNT_HOME ;
        }elseif (isset($this->parListPost['save'])) {    // создать / обновить профиль пользователя
            $this->saveProfile() ;
        }
        parent::prepare() ;
    }
    /**
     *  построить массив $ownStore - собственные параметры
     */
    protected function buildOwnStore() {
        parent::buildOwnStore() ;
    }

    /**
     * сохранить массив параметров
     */
    protected function saveOwnStore() {
        parent::saveOwnStore() ;
    }
    private function defineLoginPassword() {

    }
    /**
     *   Читать существующий профиль
     * @return bool
     */
    private function getProfileForEdit() {
        if ( empty(TaskStore::getParam('enterSuccessful')) ) {
            return false ;
        } else {      // читаем существующий профиль
            return  $this->mod->getProfile($this->login);
        }
    }

    /**
     * сохраняет профиль при первичной регистрации или изменениях
     */
    private function saveProfile() {
        $this->profile =  $this->mod->profileIni();   // массив - список полей
        if ((empty($this>login) || empty($this->password)) && !$this->profileEditFlag) { // при редактировании не учитывается
            $this->msg->addMessage('ERROR: Поля "login", "password" обязательны для заполнения! ') ;
            return false;

        } else {
            $userPassw = $this->mod->getUser($this->login);
            if (false === $userPassw || $this->profileEditFlag) {    // новый пользователь - это хорошо
                if (!$this->profileEditFlag) {
                    $this->userToDataBase() ;  // пользователя занести в БД
                }
                $this->profileToDataBase() ;
            } else {   //
                $this->msg->addMessage('ERROR: Введенный "login" зарегистрирован ранее. Измените "login" !') ;
                return false;
            }

        }
     }
    private function userToDataBase() {
        $login = $this->login ;
        $password = $this->password ;
        $this->mod->putUser($login, md5($password));    // пользователя занести в БД
         TaskStore::setParam('userLogin',$login);
        TaskStore::setParam('userName',$login) ;
        TaskStore::setParam('userPassword',$password) ;
        TaskStore::setParam('enterSuccessful',true) ;
        TaskStore::setParam('userStat',USER_STAT_USER) ;
    }
    private function profileToDataBase() {
        $this->profile['firstname'] = $this->parListPost['firstname'];
        $this->profile['middlename'] = $this->parListPost['middlename'];
        $this->profile['lastname'] = $this->parListPost['lastname'];
        $this->profile['email'] = $this->parListPost['email'];
        $this->profile['sex'] = $this->parListPost['sex'];
        $year = $this->parListPost['birthday_year'];
        $month = $this->parListPost['birthday_month'];
        $day = $this->parListPost['birthday_day'];
        $tm = mktime(0, 0, 0, $month, $day, $year);
        $this->profile['birthday'] = date('c', $tm);       // это хранится в БД

        $this->successfulSave = $this->mod->putProfile($this->login, $this->profile);   // profile БД
        // эти поля не заносятся
        $this->profile['birthday_year'] = $this->parListPost['birthday_year'];
        $this->profile['birthday_month'] = $this->parListPost['birthday_month'];
        $this->profile['birthday_day'] = $this->parListPost['birthday_day'];
        foreach ($this->profile as $key=>$mean) {
            $this->msg->addMessage('DEBUG:profile:'.$key.'-'.$mean) ;
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
        $this->parForView = [
            'login'    => $this->login,
            'password' => $this->password ,
            'profileEditFlag' => $this->profileEditFlag,
            'successfulSave'  => $this->successfulSave,
            'urlToProfile'    => $this->URL_TO_PROFILE,
            'urlToDefault'    => $this->URL_TO_DEFAULT,
            'profile'         => $this->profile,
            'profileError'    => $this->profileError ] ;
        parent::viewGo() ;
    }
}