<?php

/**
 * класс навигатор управляет страницами просмотра картинок
 * Date: 29.05.15
 * Time: 10:54
 */
class cnt_navigator extends cnt_base
{
    protected $msg;    // сообщения класса - объект Message
    protected $parListGet = [];  // параметры класса
    protected $parListPost = [];  // параметры класса
    protected $msgTitle = '';
    protected $msgName = '';
    protected $modelName = 'mod_gallery';
    protected $mod;
    protected $parForView = [];   // параметры для передачи view
    protected $nameForView = 'cnt_navigator';  // имя для передачи в ViewDriver
    protected $nameForStore = 'cnt_navigatorStore'; // имя строки параметров в TaskStore
    protected $ownStore = [];     // собственные сохраняемые параметры
    protected $forwardCntName = false; // контроллер, которому передается управление
    //--------------------------------//
    private $galleryList = [] ;   // список альбомов
    private $imgFiles = [];       // список файлов-картинок
    private $pictPerPage = 10;    // картинок на странице
    private $NAV_PAGE_NUMBER = 10; // число ссылок на страницы навигатора
    private $realPageNumber;
    private $currentPage;         // тек страница
    private $newPage;             // новая страница
    private $maxPage;            // мах № страницы
    private $navPageMin;             // начальная стр навигатора
    private $navPageMax;              // мах страница навигатора
    private $pictMin;          // нач картинки на тек странице
    private $pictMax;          // конечный № картинки на тек странице
    private $navFlag;             // вывод или нет навигатора
    private $pagesList = [];      // список всех страниц с интервалами №№ картинок
    private $currentNavStore = [];       // список сохраняемых параметров по альбомам
    private $currentGalleryId;           // Id текущей галереи
    private $URL_TO_NAVIGATOR;          //  ссылка для формы

    public function __construct($getArray, $postArray)
    {
        parent::__construct($getArray, $postArray);
    }

    protected function prepare()
    {
        $this->URL_TO_NAVIGATOR = TaskStore::$htmlDirTop . '/index.php?cnt=cnt_navigator';
        $this->currentGalleryId = TaskStore::getParam('galleryId');
        $owner = '' ;
        $this->galleryList = $this->mod->getGallery($owner);
        if (isset($this->parListPost['gallerySelect'])) {
            $this->currentGalleryId = $this->parListPost['currentGalleryId'] ;
            $this->currentGallerySave() ;
        }
        $this->imgFiles = $this->mod->getImages($this->currentGalleryId); // список картинок
        $this->navRestore();
        if ( isset($this->parListPost['enter']) ) {
            if (isset($this->parListPost['pictPerPage'])) {    // смена числа картинок/страницу
                $this->pictPerPage = $this->parListPost['pictPerPage'];
                $this->navClear();  // очистить для пересчета
            }
        }
        $this->pagesListClc();       // разбиение картинок по страницам
        $this->navInit();
        $this->newPageClc(); // вычислить новую страницу
        $this->navParClc(); // вычислить параметры навигатора

        parent::prepare();
    }
    /**
     * Сохранить атрибуты тек альбома
     * @param $curId - Id тек альбома
     */
    private function currentGallerySave() {
        // ['owner' => $userlogin,'galleryid' => $galleryId,'galleryname' =>..]
        $curId = $this->currentGalleryId   ;
        $curGallery = $this->galleryList[$curId] ;
        $owner = $curGallery['owner'] ;
        $gId = $curGallery['galleryid'] ;
        $gName = $curGallery['galleryname'] ;
        TaskStore::setParam('galleryId',$gId) ;
        TaskStore::setParam('galleryOwner',$owner) ;
        TaskStore::setParam('galleryName',$gName) ;

    }
    /**
     * разнести картинки по страницам
     */
    private function pagesListClc()
    {
        $kPage = 1;
        $iMax = -1;
        $pictNumbers = count($this->imgFiles);
        while ($iMax < $pictNumbers - 1) {
            $iMin = $iMax + 1;
            $iMax = $iMin + $this->pictPerPage - 1;
            $iMax = min($iMax, $pictNumbers - 1);

            $this->pagesList[$kPage++] = ['min' => $iMin,
                'max' => $iMax];

        }
        $this->realPageNumber = min($this->NAV_PAGE_NUMBER, count($this->pagesList));

    }

    /**
     * Восстановить параметры навигатора
     */
    private function navRestore()
    {
        if (isset($this->ownStore[$this->currentGalleryId])) {
            $this->currentNavStore = $this->ownStore[$this->currentGalleryId];
            if (isset($this->currentNavStore['pictPerPage'])) {
                $this->pictPerPage = $this->currentNavStore['pictPerPage'];
            }
            if (isset($this->currentNavStore['currentPage'])) {
                $this->currentPage = $this->currentNavStore['currentPage'];
            }
            if (isset($this->currentNavStore['navPageMin'])) {
                $this->navPageMin = $this->currentNavStore['navPageMin'];
            }
            if (isset($this->currentNavStore['navPageMax'])) {
                $this->navPageMax = $this->currentNavStore['navPageMax'];
            }
        }
    }

    private function navClear()
    {
        $this->currentPage = 0;
        $this->navPageMin = 0;
        $this->navPageMax = 0;
    }

    /**
     * инициализация параметров навигатора
     */
    private function navInit()
    {
        if (empty($this->pictPerPage)) {
            $this->pictPerPage = 20;
        }
        if (empty($this->navPageMin) || empty($this->navPageMax) ||
            empty($this->currentPage)
        ) {
            $this->currentPage = 1;
            $this->navPageMin = 1;
            $this->navPageMax = min($this->NAV_PAGE_NUMBER, count($this->pagesList));
        }
    }

    /**
     * Вычислить новую страницу
     */
    private function newPageClc()
    {
        $this->newPage = $this->currentPage;
        $nPages = count($this->pagesList);
        if (isset($this->parListGet['page'])) {  // указатель для перехода через параметр
            //   page={first,prev,<i>,next,last}
            $nextPageCursor = $this->parListGet['page'];
            switch ($nextPageCursor) {
                case 'first' :
                    $this->newPage = 1;
                    break;
                case 'prev' :
                    $this->newPage = max(1, $this->currentPage - 1);
                    break;
                case 'next' :
                    $this->newPage = min($nPages, $this->currentPage + 1);
                    break;
                case 'last' :
                    $this->newPage = $nPages;
                    break;
                default :
                    $this->newPage = (int)$nextPageCursor;
            }
        }
        $this->currentPage = $this->newPage;
    }


    /**
     * // расчет текущих параметров
     */
    private function navParClc()
    {
        if ($this->newPage < $this->navPageMin) {
            $this->navPageMin = $this->newPage;
            $this->navPageMax = $this->navPageMin + $this->realPageNumber - 1;
        } elseif ($this->newPage > $this->navPageMax) {
            $this->navPageMax = $this->newPage;
            $this->navPageMin = $this->navPageMax - $this->realPageNumber + 1;
        }
        $this->pictMin = $this->pagesList[$this->currentPage]['min'];
        $this->pictMax = $this->pagesList[$this->currentPage]['max'];
    }


    /**
     *  построить массив $ownStore - собственные параметры
     */
    protected function buildOwnStore()
    {
        $this->currentNavStore = [                  // сохраняемые параметры
            'pictPerPage' => $this->pictPerPage,
            'currentPage' => $this->currentPage,
            'navPageMin' => $this->navPageMin,
            'navPageMax' => $this->navPageMax,
        ];
        // настройки разных альбомов могут быть разными
        $this->ownStore[$this->currentGalleryId] = $this->currentNavStore;
    }

    protected function saveOwnStore()
    {
        parent::saveOwnStore();
    }

    /**
     * выдает имя контроллера для передачи управления
     * альтернатива viewGo
     * Через  $pListGet , $pListPost можно передать новые параметры
     */
    public function getForwardCntName(&$plistGet, &$pListPost)
    {
        parent::getForwardCntName($plistGet, $pListPost);
    }

    public function viewGo()
    {
        $this->parForView = [              // параметры формы
            'galleryList'    => $this->galleryList ,
            'currentGalleryId' => $this->currentGalleryId ,
            'pictPerPage' => $this->pictPerPage,       // картинок на странице
            'currentPage' => $this->currentPage,       // № тек страницы
            'navPageMin' => $this->navPageMin,         // min N страницы в указателе навигатора
            'navPageMax' => $this->navPageMax,         // max N ---------""-------------------
            'pictMin' => $this->pictMin,               // №№ картинок для тек страницы
            'pictMax' => $this->pictMax,
            'urlNavigator' => $this->URL_TO_NAVIGATOR,  // адрес для передачи в контроллер
            'imgFiles' => $this->imgFiles,             // полный списк файлов-картинок
            'dirPict' => TaskStore::$htmlDirTop . '/pictureHeap', // директорий картинок
        ];
        parent::viewGo();
    }
}