<?php
//session_start();
/**
 *  Меню - шапка страницы
 */
?>
<?php
 $htmlDirTop = TaskStore::$htmlDirTop ;
 $dirTop = TaskStore::$dirTop ;
?>
<div id="topMenu">
    <strong>ШП. PHP-1.Занятие -8+(MVC-OBJ)</strong> <br>

    <a href="<?php echo $htmlDirTop.'/index.php?cnt=cnt_gallery' ?>" class="menu">
        <img src="<?php echo $htmlDirTop ?>/images/folder-image.png" title="Альбом(владелец : имя)" alt="Альбом">
        <?php
        $gName = TaskStore::getParam('galleryName') ;
        $owner = TaskStore::getParam('galleryOwner') ;
        echo  ( empty($gName)) ? 'альбом не выбран' : $owner,':'.$gName ;
        ?>
    </a>&nbsp;&nbsp;

    <a href="<?php echo $htmlDirTop.'/index.php?cnt=cnt_user' ?>" class="menu">
        <img src="<?php echo $htmlDirTop ?>/images/people.png"
             title="пользователь" alt="пользователь">
        <?php

           echo TaskStore::getParam('userName') ;
        ?>
    </a> &nbsp;&nbsp;
    <a href="<?php echo  $htmlDirTop ?>/about.php" class="menu">
     <img src="<?php echo  $htmlDirTop ?>/images/help-about.png" title="about" alt="about"></a>

</div>
&nbsp;&nbsp;
