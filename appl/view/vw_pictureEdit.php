<?php
/**
 * Форма редактирования альбома
 * Date: 26.05.15
 */
?>
<?php
?>
<form action="<?php echo $urlPictEdit;?>" method="post"
      enctype="multipart/form-data">

    <table border="4"
           cellspacing="1"
           cellpadding=“1” class="galFformEdit">

        <tr>
            <th>Изображение</th>
            <th>Комментарий</th>
            <th>отметка</th>
        </tr>
        <?php
        if (!empty($imgFiles)) {
            foreach ($imgFiles as $imgFile) {
                $file = $imgFile['file'];
                $comment = $imgFile['comment'];
                echo '<tr>'  ;
                echo '<td>' ;
                echo '<img src=" ' . $dirPict . '/' . $file . '" class="imgGal" name="file-' . $file . '">';
                echo '</td>' ."\n" ;
                echo '<td class="comment">'  ;
                echo '<input type="text" class="commentGal"
                      name="comment-' . $file . '" value="' . $comment . '"">';
                echo '</td>' ."\n" ;
                echo '<td>'  ;
                echo '<input type="checkbox" class="checkGal" name="check-' . $file . '">';
                echo '</td>' ."\n" ;
                echo '</tr>'  ;
            }
        }
        ?>

    </table>
    <br>
    <label>

        Выбор изображения
        <input type="file" name="pictures[]" accept="image/jpeg,image/png" multiple>
    </label>
        <span style="margin-left:52px">
        <button class="btGalEdit" name="add">Добавить в альбом</button>
        </span>
    <?php

    if ($isNotEmptyBuffer) {
        echo '<button class="btGalEdit" name="addFrom">Добавить из буфера</button>' ;
    }
    ?>
    <br>

    <button class="btGalEdit" name="save">Сохранить</button>
    <button class="btGalEdit" name="del">Удалить отмеченные</button>
    <button class="btGalEdit" name="copyTo">Копировать в буфер</button>

    <button class="btGalEdit" name="show">В просмотр</button>
</form>
