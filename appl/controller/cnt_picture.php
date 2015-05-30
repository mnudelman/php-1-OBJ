<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 25.05.15
 * Time: 23:21
 */

class cnt_picture extends cnt_base {
    protected $msg ;    // сообщения класса - объект Message
    protected $parListGet = [] ;  // параметры класса
    protected $parListPost = [] ;  // параметры класса
    protected $msgTitle = '' ;
    protected $msgName = '' ;
    protected $modelName = 'mod_gallery' ;
    protected $mod ;
    protected $parForView = [] ;   // параметры для передачи view
    protected $nameForView = 'cnt_picture' ;  // имя для передачи в ViewDriver
    protected $nameForStore = 'cnt_pictureStore' ; // имя строки параметров в TaskStore
    protected $ownStore = false ;     // собственные сохраняемые параметры
    protected $forwardCntName = false ; // контроллер, которому передается управление
    //-----------------------------------------------------------//
    private $nameForViewShow = 'cnt_pictureShow' ;   // альтернативное имя для просмотра альбома
    private $pictureStatEdit;                        // режим (редакт - просмотр)
    private $imgFiles = [] ;                         // список файлов-картинок
    private $dirPict ;                               // директорий файлов
    private $filesBuffer = [] ;                      // список файлов в буфере
    private $currentGalleryId ;                      // Id текущей галереи
    private $userLogin ;
    private $userStat ;
    private $statError = false ;                    //   Ошибка, связанная со статусом
    private $gallerySelectError = false;            // ошибка выбора альбома
    private $FORWARD_CNT_NAVIGATOR = 'cnt_navigator' ; // имя для передачи управления
    private $NEW_COMMENT = 'new!' ;                 //  комментарий к новому изображению
    private $URL_TO_PICTURE ;
    private $htmlDirTop ;
    //---------------------------------------------------------------//

    public function __construct($getArray,$postArray) {
        $this->pictureStatEdit = (isset($this->parListGet['edit'])) ?
            TaskStore::PICTURE_STAT_EDIT : TaskStore::PICTURE_STAT_SHOW ;

        $this->URL_TO_PICTURE = TaskStore::$htmlDirTop.'/index.php?cnt=cnt_picture' ;
        $this->htmlDirTop = TaskStore::$htmlDirTop ;
        parent::__construct($getArray,$postArray) ;

    }
    protected function prepare() {
        //------- работа   ------------//
        if (is_array($this->ownStore) ){             // буфер из памяти контроллера
            if (!empty($this->ownStore['buffer'])) {
                $this->filesBuffer = $this->ownStore['buffer'] ;
            }
        }
        $this->currentGalleryId = TaskStore::getParam('galleryId') ;
        $this->userLogin = TaskStore::getParam('userLogin') ;
        $userStat = TaskStore::getParam('userStatus') ;

        if (isset($this->parListPost['show']) || isset($this->parListGet['show'])) {   // просмотр
           // $this->nameForView = $this->nameForViewShow ;

            $this->forwardCntName = $this->FORWARD_CNT_NAVIGATOR ;  // передача управления для

        }elseif ($userStat < TaskStore::USER_STAT_USER) {
            $this->statError = true;
        }
        if (empty($this->currentGalleryId)) {
            $this->gallerySelectError = true ;
        }

        if (isset($this->parListPost['save'])) {   // сохранить и выйти
// сохраняем только отмеченные по  checkbox
            $this->savePict() ;
        }

        if (isset($this->parListPost['add'])) {   // добавить картинки
            $this->addPict() ;
        }

        if (isset($this->parListPost['addFrom'])) {   // добавить картинки из буфера
            $this->addFromBuffer() ;
        }


        if (isset($this->parListPost['del'])) {   // удалить отмеченные
            $this->delCheckedPict() ;
        }

        if (isset($this->parListPost['copyTo'])) {   // копировать отмеченные в буфер
            $this->copyPictToBuffer() ;
        }
        $this->imgFiles = $this->mod->getImages($this->currentGalleryId) ;
        parent::prepare() ;
    }
////////////////////////////////////////////////////////////////////////////////////////
    /**
     * из общего списка файлов выбирает отмеченные в форме
     * @param $listFiles - общий список
     * @return array - списокОтмеченных
     */
    private function getCheckedList($listFiles)
    {   // список отмеченных файлов
        $checkedFiles = [];     // отмеченные файлы
        if (empty($listFiles)) {
            return $checkedFiles;
        }
        foreach ($listFiles as $imgFile) {  // оставим только отмеченные check-<file>

            $file = $imgFile['file'];
            $file_ = str_replace('.', '_', $file);
            $chkName = 'check-' . $file_;

            if (isset($this->parListPost[$chkName])) {
                $cmtName = 'comment-' . $file_;    // имя поля комментария
                $comment = $this->parListPost[$cmtName];
                $checkedFiles[] = ['file' => $file, 'comment' => $comment];
            }

        }
        return $checkedFiles;
    }

    /**
     * Сохранить отмеченные картинки
     * @param $pdo
     * @param $galleryId
     */
    private function savePict() {
        $galleryId = $this->currentGalleryId ;
        $imgFiles = $this->mod->getImages($galleryId); //
        $fileForSave = $this->getCheckedList($imgFiles);
        $this->mod->putImages($galleryId, $fileForSave);
    }


    private function addPict() {
        $galleryId = $this->currentGalleryId ;
        $addImages = []; // ['file' => file,'comment' => comment] -  для загрузки в БД
        $filesNorm = $this->mod->filesTransform('pictures');  // преобразовать в нормальную форму

        $dirHeap = TaskStore::$dirPictureHeap ;
        $nLoaded = 0;

        foreach ($filesNorm as $fdes) {
            $name = $fdes['name'];
            $tmpName = $fdes['tmp_name'];
            $error = $fdes['error'];

            if (!0 == $error) {
                $this->msg->addMessage("ERROR: Ошибка выбора файла:" . $name . " код ошибки: " . $error) ;
                continue;
            }
            $addImages[] = ['file' => $name, 'comment' => $this->NEW_COMMENT];
            if ($this->mod->doubleLoad($dirHeap, $name)) {
                $this->msg->addMessage("INFO: Попытка повторной загрузки файла :" . $name) ;
            }
            $fileTo = $dirHeap . '/' . basename($name);
            if (is_uploaded_file($tmpName)) {
                $res = move_uploaded_file($tmpName, $fileTo);
                $nLoaded++;
            }

        }
        $newOnly = true;   // блокирует изменение имеющихся в БД
        $this->mod->putImages($galleryId, $addImages, $newOnly);    // добавить в БД/обновить комментарий
        $this->msg->addMessage('INFO:Загружено  файлов:' . $nLoaded) ;
    }
    private function addFromBuffer() {
        $galleryId =$this->currentGalleryId ;
        $newOnly = true;   // блокирует изменение имеющихся в БД
        $addImages = $this->filesBuffer ;
        $this->mod->putImages($galleryId, $addImages, $newOnly);    // добавить в БД/обновить комментарий
    }
    private function delCheckedPict() {
        $galleryId = $this->currentGalleryId ;
        $imgFiles = $this->mod->getImages($galleryId); //
        $fileForSave = $this->getCheckedList($imgFiles);
        $this->mod->delImages($galleryId, $fileForSave);
    }
    private function copyPictToBuffer() {
        $galleryId = $this->currentGalleryId ;
        $imgFiles = $this->mod->getImages($galleryId); //
        $this->filesBuffer = $this->getCheckedList($imgFiles);
    }
    /////////////////////////////////////////////////////////////////////////////////

    /**
     *  построить массив $ownStore - собственные параметры
     */
    protected function buildOwnStore() {    // в памяти контроллера сохраняется список картинок
                                            // для копирования
       $this->ownStore = ['buffer' => $this->filesBuffer ] ;
    }
    protected function saveOwnStore() {
        parent::saveOwnStore() ;
    }
    /**
     * выдает имя контроллера для передачи управления
     * альтернатива viewGo
     * Через  $pListGet , $pListPost можно передать новые параметры
     */
    public function getForwardCntName(&$plistGet,&$plistPost) {
        $plistGet = [] ;
       $plistPost = [] ;
        return $this->forwardCntName ;


//        parent::getForwardCntName($plistGet,$plistPost) ;
    }
    public function viewGo() {
        $this->parForView = [
            'imgFiles'    => $this->imgFiles ,
            'dirPict' => TaskStore::$htmlDirTop.'/pictureHeap' ,
            'urlPictEdit' => $this->URL_TO_PICTURE,
            'isNotEmptyBuffer' => !empty($this->filesBuffer),
            'htmlDirTop' => $this->htmlDirTop
            ] ;

        parent::viewGo() ;
    }

}