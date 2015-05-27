<?php

/**
 *
 * Управление выводом
 * Date: 23.05.15
 */
class ViewDriver
{
    private $contView = [];       // таблица имяКонтроллера => формаОтображения
    private $viewLayout = [];     // таблица форма => шаблонСтраницы
    private $viewComponent = [];  // таблица форма => компонентСтраницы для вывода

    //--- тек атрибуты ---//
    private $curCnt = '';       // контроллерИмя
    private $curView = '';      // формаОтображения
    private $curLayOut = '';    // шаблонСтраницы
    private $curParams = [];    // парараметрыПодстановки в форму
    private $curComponent = [] ; // компонент страницы для вывода формы

    public function __construct($cntName) {
        $this->init();
        $this->curCnt = $cntName ;
        $this->curView = $this->contView[$this->curCnt] ;
        $this->curLayOut= $this->viewLayout[$this->curView] ;
        $this->curComponent = $this->viewComponent[$this->curView] ;

//        $lf = TaskStore::LINE_FEED ;
//        echo 'layOut:'.$this->curLayOut.$lf ;
//        echo 'ViewDriver:curCnt:'.$this->curCnt .$lf;
//        echo 'ViewDriver:curView:'.$this->curView .$lf ;
//        echo 'ViewDriver:curLayOut:'.$this->curLayOut.$lf ;

        //  подстановка curView
        foreach ($this->curComponent as $key=>$value) {
            if (true === $value) {
                $this->curComponent[$key] = $this->curView;
            }
        }


    }

    /**
     * Вводит таблицы соответствий
     */
    private function init() {
        $this->contView = [
            'cnt_user' => 'vw_userLogin',
            'cnt_profile' => 'vw_userProfile',
            'cnt_gallery' => 'vw_gallery',
            'cnt_picture' => 'vw_pictureEdit',
            'cnt_pictureShow' => 'vw_pictureShow',
            'cnt_default' => 'vw_default'];

        $this->viewLayout = [
            'vw_userLogin' => 'lt_footer',
            'vw_userProfile' => 'lt_footerNo',
            'vw_gallery' => 'lt_footer',
            'vw_pictureEdit' => 'lt_footerNo',
            'vw_pictureShow' => 'lt_footerNo',
            'vw_default' => 'lt_footerNo'];


        $this->viewComponent['vw_userLogin'] = [
            'content' => false,
            'footer' => true];
        $this->viewComponent['vw_userProfile'] = [
            'content' => true,
            'footer' => false];
        $this->viewComponent['vw_gallery'] = [
            'content' => false,
            'footer' => true];
        $this->viewComponent['vw_pictureEdit'] = [
            'content' => true,
            'footer' => false];
        $this->viewComponent['vw_pictureShow'] = [
            'content' => true,
            'footer' => false];
        $this->viewComponent['vw_pictureNav'] = [  // формы в 2 частях страницы
            'content' => 'vw_picture',
            'footer' => 'vm_navigator'];
        $this->viewComponent['vw_default'] = [  // форма отсутствует
            'content' => false,
            'footer' => false];
    }
    public function viewExec($paramList) {
        $dir = TaskStore::$dirView ;
        $footer = $this->curComponent['footer'] ;
        $content = $this->curComponent['content'] ;
        if (false !== $footer) {
            $footer = $dir.'/'.$footer.'.php' ;
        }
        if (false !== $content) {
            $content = $dir.'/'.$content.'.php' ;
        }
        //----  подстановка параметров ---- //
        if (is_array($paramList)) {
            foreach ($paramList as $parName => $parMean) {
                $$parName = $parMean;
            }
        }
         $dir = TaskStore::$dirLayout ;

//        $lf = TaskStore::LINE_FEED ;
//        echo 'dir:'.$dir.$lf ;
//        echo 'layOut:'.$this->curLayOut.$lf ;
//        echo 'ViewDriver:curCnt:'.$this->curCnt .$lf;
//        echo 'ViewDriver:curView:'.$this->curView .$lf ;
//        echo 'ViewDriver:curLayOut:'.$this->curLayOut.$lf ;


        include_once $dir.'/'.$this->curLayOut.'.php' ;
    }

}