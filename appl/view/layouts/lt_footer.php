<?php
//session_start();
/**
 *   страница  с подвалом для вывода форм
 */
?>
<?php
ini_set('display_errors', 1);
//error_reporting(E_ALL) ;
error_reporting(E_ALL ^ E_NOTICE);
?>
<?php
$htmlDirTop = TaskStore::$htmlDirTop ;
$dirTop = TaskStore::$dirTop ;
?>

<html>
<head>
    <meta charset="utf-8">
    <title>php-1-MVC</title>
    <meta name="description" content="ШП-php-1-lesson_MVC-OBJ">
    <meta name="author" content="mnudelman@yandex.ru">

    <link rel="stylesheet" type="text/css" href="<?php echo $htmlDirTop?>/styles/task.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $htmlDirTop?>/styles/formStyle-1.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $htmlDirTop ?>/styles/galleryStyle-1.css">

</head>
<body>
<?php
include_once TaskStore::$dirView . '/topMenu.php';
?>
<div id="content">

    <?php
     include_once TaskStore::$dirView .'/messageForm.php' ;
    ?>
    <?php
    if (!empty($content)) {
        include_once $content;
    }
    ?>

</div>
<div id="footer">
    <?php
    if (!empty($footer)) {
        include_once $footer;
    }
    ?>

</div>
</body>
</html>