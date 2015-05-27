<?php
/**
 * класс - контроллер альбомов
 * Date: 25.05.15
 * Time: 23:21
 */

class cnt_gallery extends cnt_base {
    protected $msg ;    // сообщения класса - объект Message
    protected $parListGet = [] ;  // параметры класса
    protected $parListPost = [] ;  // параметры класса
    protected $msgTitle = '' ;
    protected $msgName = '' ;
    protected $modelName = 'mod_gallery' ;
    protected $mod ;
    protected $parForView = [] ;   // параметры для передачи view
    protected $nameForView = 'cnt_gallery' ;  // имя для передачи в ViewDriver
    protected $nameForStore = 'cnt_gallery' ; // имя строки параметров в TaskStore
    protected $ownStore = [] ;     // собственные сохраняемые параметры
    protected $forwardCntName = false ; // контроллер, которому передается управление
    //-----------------------------------------------//
    //-------параметры передачи в Представление---//
    private $galleryList ;               // список доступных альбомов
    private $currentGalleryId ;          // Id текущей галереи
    private $galleryEditStat ;           // редактирование/просмотр
    private $galleryStatName ;           // тоже, только имя
    //-------- url ------------------------------//
    private $CNT_HOME = 'cnt_default' ;      // контроллер пустой
    private $CNT_PICTURE = 'cnt_picture' ;   // контроллер изображений
    private $URL_TO_GALLERY ;         // адрес для перехода из формы в контроллер
    //--------------------------------------------//
    private $pictureStatEdit ;

    public function __construct($getArray,$postArray) {
        $this->galleryEditStat = TaskStore::GALLERY_STAT_SHOW ;   // по умолчанию - просмотр
        $this->currentGalleryId = TaskStore::getParam('galleryId') ;
        $this->URL_TO_GALLERY = TaskStore::$htmlDirTop.'/index.php?cnt=cnt_gallery' ;

        parent::__construct($getArray,$postArray) ;
    }
    protected function prepare() {
        $this->galleryEditStat = (isset($this->parListPost['galleryEditStat'])) ?
            $this->parListPost['galleryEditStat'] : TaskStore::GALLERY_STAT_SHOW ;

        if (isset($this->parListPost['exit'])) {      // выход (в "главный" index )
            $this->forwardCntName = $this->CNT_HOME ;
        }

        if (isset($this->parListPost['changeStat'])) {       // сменить режим ( SHOW <-> EDIT )
            $this->galleryChangeStat();
        }

        $this->defGalleryList() ;      // список доступных галерей
        if (isset($this->parListPost['currentGalleryId'])) {          // текущая галерея
            $curId = $this->parListPost['currentGalleryId'] ;
            $this->currentGallerySave($curId) ;     // сохранить атрибуты тек альбома
        }

        if (isset($this->parListPost['goShow'])) {   //   в просмотр альбома
            $this->forwardCntName = $this->CNT_PICTURE;
            $this->pictureStatEdit = TaskStore::PICTURE_STAT_SHOW;
        }
        if (isset($this->parListPost['editGallery'])) {   //   редактировать
            $this->forwardCntName = $this->CNT_PICTURE;
            $this->pictureStatEdit = TaskStore::PICTURE_STAT_EDIT ;
        }

        if (isset($this->parListPost['addGalleryExec'])) {   //   добавить в список новыйАльбом
            $this->addGallery();
        }

        parent::prepare() ;
    }
    /**
     * формирует списокДоступныхАльбомов
     * для просмотра доступны все, для редактирования только свои
     */
    private  function defGalleryList() {
        $owner = (TaskStore::GALLERY_STAT_SHOW == $this->galleryEditStat) ?
            '' : $_SESSION['userName'];
        $this->galleryList = $this->mod->getGallery($owner);
     }
    /**
     * Сохранить атрибуты тек альбома
     * @param $curId - Id тек альбома
     */
    private function currentGallerySave($curId) {
        // ['owner' => $userlogin,'galleryid' => $galleryId,'galleryname' =>..]
        $curGallery = $this->galleryList[$curId] ;
        $owner = $curGallery['owner'] ;
        $gId = $curGallery['galleryid'] ;
        $gName = $curGallery['galleryname'] ;
        TaskStore::setParam('galleryId',$gId) ;
        TaskStore::setParam('galleryOwner',$owner) ;
        TaskStore::setParam('galleryName',$gName) ;
        $this->currentGalleryId = $gId ;
    }

    private function galleryChangeStat() {
        $userStat = TaskStore::getParam('userStatus');
        if ($userStat < TaskStore::USER_STAT_USER) {        //  если не зарегистрирован, то только просмотр
            $this->galleryEditStat = TaskStore::GALLERY_STAT_SHOW;
        }else {
            $this->galleryEditStat = ($this->galleryEditStat == TaskStore::GALLERY_STAT_SHOW) ?
                TaskStore::GALLERY_STAT_EDIT : TaskStore::GALLERY_STAT_SHOW ;
        }
    }
    private function addGallery() {
        $owner = TaskStore::getParam('userLogin') ;
        $newG = $this->parListPost['addGallery'] ;
        $gId = $this->mod->putGallery($owner,$newG) ;
        $this->galleryList = $this->mod->getGallery($owner) ;
        $this->currentGallerySave($gId)  ;
    }











    ////////////////////////////////////////////////////////////////////////////////
    /**
     *  построить массив $ownStore - собственные параметры
     */
    protected function buildOwnStore() {
        parent::buildOwnStore() ;
    }
    protected function saveOwnStore() {
        parent::saveOwnStore() ;
    }
    /**
     * выдает имя контроллера для передачи управления
     * альтернатива viewGo
     * Через  $pListGet , $pListPost можно передать новые параметры
     */
    public function getForwardCntName(&$plistGet,&$pListPost) {
        $plistGet = [] ;
        $plistPost = [] ;
        if ($this->forwardCntName == $this->CNT_PICTURE) {
            if ($this->pictureStatEdit == TaskStore::PICTURE_STAT_SHOW) {
                $plistGet = ['show' => true] ;
            }else {
                $plistGet = ['edit' => true] ;
            }
        }
        return $this->forwardCntName ;
//        parent::getForwardCntName($plistGet,$pListPost) ;
    }
    public function viewGo() {
        $this->galleryStatName = ($this->galleryEditStat == TaskStore::GALLERY_STAT_SHOW) ?
            TaskStore::STAT_SHOW_NAME : TaskStore::STAT_EDIT_NAME ;
        $editFlag = ($this->galleryEditStat == TaskStore::GALLERY_STAT_EDIT) ;
        $this->parForView = [
            'galleryList'    => $this->galleryList ,
            'currentGalleryId' => $this->currentGalleryId ,
            'urlToGallery' => $this->URL_TO_GALLERY ,
            'galleryEditStat' => $this->galleryEditStat,
            'galleryStatName' => $this->galleryStatName,
            'editFlag'        => $editFlag ] ;


        parent::viewGo() ;
    }
}