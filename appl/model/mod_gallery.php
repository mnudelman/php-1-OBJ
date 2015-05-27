<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 24.05.15
 * Time: 16:23
 */

class mod_gallery extends mod_base {
    public function __construct() {
        parent::__construct() ;
    }

    function getImages($galleryId) {
        $pdo = $this->pdo ;
        $images = [];   // ['file' => $file,'comment' => $comment]
        $sql = 'SELECT fileimg,
                        comment
                        FROM galleryContent
                        WHERE galleryid = :galleryId';

        try {
            $smt = $pdo->prepare($sql);
            $smt->execute(['galleryId' => $galleryId]);
        } catch (PDOException  $e) {
            $this->msg->addMessage('ERROR:'. __METHOD__ .':' . $e->getMessage() ) ;
            return false;
        }
        if ( 0 == $smt->rowCount() ){
            return false ;
        }
        foreach ($smt as $row) {
            $images[] = ['file' => $row['fileimg'],
                'comment' => $row['comment']
            ];
        }
        return $images ;

    }
    function findFileImg($galleryId,$fileImg) {
        $pdo = $this->pdo ;
        $sql = 'SELECT * FROM galleryContent
                WHERE galleryid = :galleryId AND fileimg = :fileImg ' ;
        try {
            $smt = $pdo->prepare($sql) ;
            $smt->execute(['galleryId' => $galleryId,
                'fileImg'   => $fileImg]) ;
            $row = $smt->fetch(PDO::FETCH_ASSOC) ;

        }catch (PDOException  $e){
            $this->msg->addMessage('ERROR:'. __METHOD__ .':' . $e->getMessage() ) ;
            return false ;
        }
        return ( false === $row) ? false : true ;
    }
    /**
     * Помещает в БД списокФайлов-изображений
     *
     * @param $galleryId
     * @param $images
     * @return int
     */
    function putImages($galleryId,$images,$newOnly = false) {
        $pdo = $this->pdo ;
        $n = 0 ;
        $sqlInsert = 'INSERT INTO galleryContent (galleryid,fileImg,comment )
                               VALUES (:galleryId ,:file ,:comment)';
        $sqlUpdate = 'UPDATE galleryContent
                        SET comment = :comment
                        WHERE galleryId = :galleryId AND
                              fileImg = :file ' ;
        try{
            $smtInsert = $pdo->prepare($sqlInsert) ;
            $smtUpdate = $pdo->prepare($sqlUpdate) ;
            foreach($images as $img) {
                $file = $img['file'] ;
                $comment = $img['comment'] ;
                $n = 0 ;
                if (false === $this->findFileImg($galleryId,$file)) {
                    $smtInsert->execute(['galleryId' => $galleryId,
                        'file'      => $file,
                        'comment'   => $comment]) ;
                }elseif ($newOnly){     // только добавление новых
                    continue ;
                }else {
                    $smtUpdate->execute(['galleryId' => $galleryId,
                        'file'     => $file,
                        'comment'  => $comment]) ;
                }
                $n ++ ;
            }
        }catch (PDOException $e){
            $this->msg->addMessage('ERROR:'. __METHOD__ .':' . $e->getMessage() ) ;
            return false ;
        }
        return $n ;
    }

    /**
     * Удалить из БД списокФайлов
     * @param $userId
     * @param $galleryId
     * @param $images
     * @return int
     */
    function delImages($galleryId,$images) {
        $pdo = $this->pdo ;
        $n = 0 ;
        $sql = 'DELETE FROM galleryContent WHERE galleryId = :galleryId AND fileImg = :file' ;
        try {
            $smt = $pdo->prepare($sql);
            foreach ($images as $img) {
                $file = $img['file'];
                if (true === $this->findFileImg($galleryId, $file)) {
                    $smt->execute(['galleryId'=> $galleryId,
                        'file'      => $file] ) ;
                    $n++ ;
                }
            }
        }catch (PDOException $e){
            $this->msg->addMessage('ERROR:'. __METHOD__ .':' . $e->getMessage() ) ;
            return false ;
        }
        return $n ;
    }

    /**
     * копировать список файловИзображений в другую галерею
     * @param $userFrom
     * @param $galleryFrom
     * @param $images
     * @param $userTo
     * @param $galleryTo
     * @return int
     */
    function copyImages($galleryIdFrom,$userTo,$images) {
        $n = 0 ;
        return $n ;
    }

    /**
     * возвращает список галерей(альбомов), принадлежащих $userOwner
     * если empty($userOwner), то все галереи из БД (для просмотра доступны все)
     * @param $userOwner - это userLogin
     * @return array
     */
    function getGallery ($userOwner) {
        $pdo = $this->pdo ;
        $galleryList = [] ; // ['owner' => $userlogin,'galleryid' => $galleryId,'galleryname' =>..]
        $sql = 'SELECT users.login,
                 gallery.galleryid,
                 gallery.themeName AS galleryname,
                 gallery.comment
                 from gallery,users
                 where gallery.userid = users.userid  '.
            ( (!empty($userOwner)) ?
                ' AND gallery.userid in (SELECT userid from users where login = :userOwner )' : ''
            ) .
            '  order by users.login' ;
        try{
            $smt = $pdo->prepare($sql) ;
            if (empty($userOwner)){
                $smt->execute() ;
            }else {
                $smt->execute(['userOwner'=>$userOwner]) ;
            }

        }catch (PDOException $e){
            $this->msg->addMessage('ERROR:'. __METHOD__ .':' . $e->getMessage() ) ;
            return false ;
        }
        foreach ($smt as $row) {
            $galleryId = $row['galleryid'] ;
            $galleryList[$galleryId] = ['owner' => $row['login'],
                'galleryid' => $row['galleryid'],
                'galleryname' => $row['galleryname']
            ] ;
        }
        return $galleryList ;
    }

    /**
     * наличие галереи с заданным именем у пользователя
     * @param $userOwner
     * @param $galleryName
     * @return bool
     */
    function findGallery($userOwner,$galleryName){
        $pdo = $this->pdo ;
        $sql = 'SELECT gallery.galleryid from gallery where gallery.themeName = :galleryName
                 AND gallery.userid IN (SELECT userid FROM users WHERE login = :userOwner )' ;
        try{
            $smt = $pdo->prepare($sql) ;
            $smt->execute(['galleryName' => $galleryName,
                'userOwner'  => $userOwner]) ;
        }catch (PDOException $e){
            addMessage('ERROR:'.__FUNCTION__.':' . $e->getMessage() ) ;
            return false ;
        }
        $row = $smt->fetch(PDO::FETCH_ASSOC);



        return  (false === $row) ? false : $row['galleryid']  ;
    }

    /**
     * опрелить userid по  login
     * @param $login
     * @return userid
     */
    function getUserid($login) {
        $pdo = $this->pdo ;
        $sql = 'SELECT * FROM users where login = :login' ;
        try{
            $smt = $pdo->prepare($sql) ;
            $smt->execute(['login'  => $login]) ;
        }catch (PDOException $e){
            addMessage('ERROR:'.__FUNCTION__.':' . $e->getMessage() ) ;
            return false ;
        }
        $row = $smt->fetch(PDO::FETCH_ASSOC);

        return  (false === $row) ? false : $row['userId']  ;
    }


    /**
     * Добавить галерею пользователя
     * @param $userOwner
     * @param $galleryName
     * @return bool
     */
    function putGallery ($userOwner,$galleryName) {
        $pdo = $this->pdo ;
        $galleryId = $this->findGallery($userOwner, $galleryName) ;
        if (false !== $galleryId) {
            return true;
        }
        $userid = $this->getUserId($userOwner);
        $sql = 'INSERT INTO gallery (userid,themeName) VALUES (:userid,:galleryName)';
        try {
            $smt = $pdo->prepare($sql);
            $smt->execute(['userid'     => $userid,
                'galleryName'=> $galleryName]);
        } catch (PDOException $e) {
            addMessage('ERROR:'.__FUNCTION__.':' . $e->getMessage() ) ;
            return false;
        }
        $galleryId = $this->findGallery($userOwner, $galleryName) ;
        return  $galleryId ;
    }

    /**
     * удалить галерею пользователя
     * @param $userOwner
     * @param $galleryName
     * @return bool
     */
    function delGallery ($userOwner,$galleryName) {
        $pdo = $this->pdo ;
        return true ;
    }

    /**
     * преобразует  $_FILES в нормальную форму
     * @param $topName
     * @return array
     */
    function filesTransform($topName)
    {
        /** переведем $_FILES в нормальную форму */
        $filesNorm = [];
        $names = $_FILES[$topName]['name'];
        $n = count($names);      // количество файлов
        for ($i = 0; $i < $n; $i++) {
            $fName = $_FILES[$topName]['name'][$i];
            $fType = $_FILES[$topName]['type'][$i];
            $fTmpName = $_FILES[$topName]['tmp_name'][$i];
            $fError = $_FILES[$topName]['error'][$i];
            $fSize = $_FILES[$topName]['size'][$i];
            $filesNorm[] = [
                'name' => $fName,
                'type' => $fType,
                'tmp_name' => $fTmpName,
                'error' => $fError,
                'size' => $fSize

            ];
        }
        return $filesNorm;
    }

    function doubleLoad($dirName,$fName) {     // повторная загрузка
        return (file_exists($dirName.'/'.$fName)) ;
    }

}