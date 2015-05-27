<?php
/**
 *
 */
session_start() ;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>php-1-lesson-7</title>

    <meta name="description" content="ШП-php-1-lesson_7(MVC) ">
    <meta name="description" content="ШП-php-1-lesson_7 ">
    <meta name="author" content="mnudelman@yandex.ru">
    <link rel="stylesheet" type="text/css" href="./styles/task.css">
    <style>
        .programText{
            background-color:beige;
            color:blue
        }
    </style>
</head>
<body>


<div class="comment">
    <p><strong>Систематизация изображени</strong></p>

    <p>
        Имеются следующие виды объектов: пользователь ->> альбом(галерея) ->> картинки.
    </p>
    <p>
        Каждый пользователь после регистрации может создавать произвольное число альбомов.
        В альбом помещаются картинки. Все альбомы доступны для просмотра любым пользователем.
        Но редактировать альбом (помещать новые картинки, удалять надоевшие, преименовывать, ...)
        может только владелец, создавший альбом.
    </p>
    <p>
        Добавленные на сайт картинки хранятся в одной директории ./pictureHeap . Логическое
        разбиение на альбомы только в БД .
    </p>


    <h3>Реализация  MVC</h3>
    <p><strong>Иерархия контроллеров:</strong>  <i>index.php -> (cnt_user,cnt_gallery)</i> <br>
        <i>cnt_gallery</i>( ведение спискаАльбомов) -> <i>cnt_picture</i>(ведение спискаИзображений) <br>
        <i>cnt_user</i>(регистрация пользователей) ->  <i>cnt_profile</i>(редактирование профиляПользователя)<br>
        Диспетчерезацию контроллеров выполняет Router.<br>
        Контроллер получает данные из свой формы,
        обрабатывает их и формирует новый массив данных.
        Router, получив массив данных от контроллера,
        передает их в <i>ViewDriver</i> - управление представлениями.

    </p>
    <p>
       <strong> Модели -</strong> это два класса - набора функций, обеспечивающих взаимодействие с БД. <br>
        - <i>mod_gallery</i> - модель, обслуживающая  cnt_gallery,cnt_picture <br>
        - <i>mod_user</i>    - модель, обслуживающая  cnt_user,cnt_profile <br>
    </p>
    <p>
        <strong>Представления</strong> состоят из двух частей: <br>
         - шаблоны расположения (layouts) - это html файлы с префиксом lt_, которые подгружают формы контроллеров.<br>
         - формы контроллеров .<br>
        Диспетчеризацию представлений выполняет объект класса <i>ViewDriver</i>.<br>
        По имени котроллера определяет соответствующие форму, шаблон расположения и
        выводит в окно браузера.
    </p>
    <p>
        Статический класс <i>TaskStore</i> используется для хранения общих параметров задачи.

    </p>

        <h3> Схема БД</h3>
    <p >

        <pre class="programText">
        -- Создание схемы БД gallery
-- --------------------------------------
CREATE DATABASE IF NOT EXISTS gallery;
-- --------------------------------------
-- users - список пользователей
CREATE TABLE IF NOT EXISTS users (
  userId   INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
  login    VARCHAR(20) UNIQUE,
  password CHAR(32)
);
-- --------------------------------------
-- Профиль пользователя
CREATE TABLE IF NOT EXISTS userprofile (
  id         INTEGER NOT NULL  AUTO_INCREMENT PRIMARY KEY,
  userid     INTEGER
    REFERENCES users (userid)
      ON DELETE CASCADE,
  firstname  VARCHAR(40),
  middlename VARCHAR(40),
  lastname   VARCHAR(40),
  fileFoto   VARCHAR(100), -- файл с фотографией
  tel        VARCHAR(15),
  email      VARCHAR(40),
  sex        CHAR(1)           DEFAULT 'm',
  birthday   DATE,
  CHECK (sex IN ('m', 'w'))
);
-- --------------------------------------
-- Список галерей
CREATE TABLE IF NOT EXISTS gallery (
  galleryid INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
  userid    INTEGER            -- владелец альбома
    REFERENCES users (userid)
      ON DELETE CASCADE,
  themeName VARCHAR(40), -- тема галереи (имяальбома)
  comment   VARCHAR(100),
  UNIQUE (userid, themeName)   -- у владельца только одна галерея с именем  themename
);
DROP TABLE galleryContent;
-- --------------------------------------
-- galleryContent -Содержание галереи (список файлов-изображений
CREATE TABLE IF NOT EXISTS galleryContent (
  contentid INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
  galleryId INTEGER
    REFERENCES gallery (galleryid)
      ON DELETE CASCADE,
  fileImg   VARCHAR(100), -- файл изображение
  comment   VARCHAR(100), -- комментарий(подпись под картинкой)
  UNIQUE (galleryid, fileImg)    -- в гелерее только один файл с именем fileImg
);
-- --------------------------------------
-- строка в userprofile появляется вместе с users
CREATE TRIGGER insert_user AFTER INSERT ON users
FOR EACH ROW
  INSERT INTO userprofile (userid) VALUES (new.userId);
-- --------------------------------------
-- --------------------------------------
    </pre>
    </p>


    <h3>Тексты, которые могут оказаться полезными</h3>

   <ul>
        <li>
            Функция, приводящая  структуру $_FILES в "человеческий вид". Работать с $_FILES
            непосредственно, особенно если загружаются сразу несколько файлов, мягко говоря
            не удобно. Следующий тест трансформирует $_FILES  к нормальному виду
            <p>
                <pre class="programText">
          /**
 * преобразует  $_FILES в нормальную форму
 * @param $topName - этот атрибут name = "..." из input type="file" name="topname[]".....
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

            </pre>
            </p>
        </li>
    <li>
        <p>
        Простой интерпритатор sql запросов, записанных в текстовый файл.
            Можно использовать для отладки sql-запросов.
            Как работает можно посмотреть <a href="./sqlExecute.php">здесь.</a>
        </p>
    </li>
    </ul>

</div>



