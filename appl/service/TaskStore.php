<?php
/**
 * Хранение  параметров задачи
 *
 * User: mnudelman@yandex.ru
 * Date: 22.05.15
 */
class TaskStore {
    public static $dirTop = false ;
    public static $dirController = false ;
    public static $dirModel = false ;
    public static $dirView = false ;
    public static $dirLayout = false ;
    public static $htmlDirTop = false ;
    public static $dirService = false ;
    public static $dirPictureHeap = false ;
    //-----------------------------------//

    //-- параметры состояния --//
    private static $userLogin = false ;
    private static $userPassword = false ;
    private static $userName = false ;
    private  static $enterSuccessful = false ;  // успешный вход
    private static $userStatus = false ;
    private static $galleryId = false ;
    private static $galleryName = false ;
    private static $galleryOwner = false ;
    private static $dbConnect= false ;
    private static $message = false ;
    //-----память контроллеров ---//
    private static $cnt_userStore = [];
    private static $cnt_profileStore = [];
    private static $cnt_galleryStore = [];
    private static $cnt_pictureStore = [];
    //-------Список сохраняемых параметров-------//
    private static $storedParams = [
        'userName',           // Имя пользователя
        'userLogin',          // login
        'userPassword',       // пароль
        'userStatus',         // статус пользователя (определяет доступные операции)
        'galleryId',          // id галереи
        'galleryName',        //  имя галереи
        'galleryOwner',       //  Владелец галереи(login)
        'enterSuccessful',    // успешный вход
        'cnt_userStore',      // память контроллера user
        'cnt_profileStore',   // память контроллера profile
        'cnt_galleryStore',   // память контроллера gallery
        'cnt_pictureStore',   // память контроллера picture
        ];

    //------ константы ------------//

    const LINE_FEED = '<br>';
    const LINE_END = '\n';
    const ADMIN_LOGIN = 'ADMIN';
    /** статус определяет функциональные возможности */
    const  USER_STAT_ADMIN = 99;  // создание новых разделов, групповое добавление картинок
    const  USER_STAT_USER = 10;        // добавление картинок по одной
    const  USER_STAT_GUEST = 5;      // только просмотр

    const  GALLERY_STAT_SHOW = 1;    // только просмотр
    const  GALLERY_STAT_EDIT = 2;    // редактирование

    const  STAT_SHOW_NAME = 'только просмотр';
    const  STAT_EDIT_NAME = 'редактирование';

    const  PICTURE_STAT_SHOW = 1;    // только просмотр
    const  PICTURE_STAT_EDIT = 2;    // редактирование
    const  PICTURE_STAT_COPY = 3;    // копирование из других альбомов

    public static function init($dirTop, $htmlDirTop) {
        self::$dirTop = $dirTop;
        self::$htmlDirTop = $htmlDirTop;

        self::$dirController = self::$dirTop . '/appl/controller';
        self::$dirModel = self::$dirTop . '/appl/model';
        self::$dirView = self::$dirTop . '/appl/view';
        self::$dirLayout = self::$dirView .'/layouts' ;
        self::$dirService = self::$dirTop . '/appl/service';
        self::$dirPictureHeap = self::$dirTop . '/pictureHeap';
        // восстановить параметры //
        $params = self::$storedParams ;
        foreach($params as $parName) {
            self::$$parName = self::restoreParam($parName) ;
        }
        if (empty(self::$userLogin)) {
            self::$userLogin = 'guest' ;
            self::$userName = 'Гость' ;
            self::$enterSuccessful = false ;
            self::$userStatus = USER_STAT_GUEST ;
        }
    }

    /**
     * Сохранить параметр
     * @param $paramName
     * @param $paramMean
     */
    private static function storeParam($paramName,$paramMean) {
        $_SESSION[$paramName] = $paramMean ;
    }

    /**
     * Восстановить параметр
     * @param $paramName
     * @return bool
     */
    private static function restoreParam($paramName) {
        return (!isset($_SESSION[$paramName])) ? false : $_SESSION[$paramName] ;
    }
    /**
     * @return array -  список директорий для поиска классов по __autoload
     */
    public static function getClassDirs()
    {
        return [self::$dirController,
            self::$dirModel,
            self::$dirService];
    }

    /**
     * получить параметр
     * @param $paramName
     * @return если (парамметрЕсть) ? ЗначениеПараметра : null
     */
    public static function getParam($paramName) {
        if (isset(self::$$paramName)) {
            return self::$$paramName ;
        }else {    // error:
            return null ;
        }
    }

    /**
     * Установить значение пераметра
     * @param $paramName
     * @param $paramMean
     * @return bool
     */
    public static function setParam($paramName,$paramMean) {
        if (isset(self::$$paramName)) {
            self::$$paramName = $paramMean ;
            self::storeParam($paramName,$paramMean) ;
            return true ;
        }else { // error:
            return false ;
        }
    }

    /**
     * dbConnect нельзя сохранять в $_SESSION
     * @param $dbConnect
     */
    public static function setDbConnect($dbConnect) {
        self::$dbConnect = $dbConnect ;
    }
    public static function getDbConnect() {
        return self::$dbConnect  ;
    }
    public static function setMessage($msg) {
        self::$message = $msg ;
    }
    public static function getMessage() {
        return self::$message ;
    }
}