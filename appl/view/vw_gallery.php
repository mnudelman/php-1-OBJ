<?php
/**
 * Форма выбора текущей галереи
 * Date: 25.05.15
 */
?>
<form action="<?php echo $urlToGallery?>" method="post">
    <label>
        <span class="label">текущий режим:</span>
        <input type="text" readonly="readonly" name="galleryStatName" class="field"
               value="<?php echo $galleryStatName ?>">
    </label>&nbsp;&nbsp;
    <input type="hidden"  name="galleryEditStat" class="field"
           value="<?php echo $galleryEditStat ?>">


    <button name="changeStat" class="btGal">изменить режим</button>
    <br>
    <label>
        <span class="label">выбрать альбом:</span>
        <select name="currentGalleryId" class="field">
            <?php
            foreach($galleryList as $gallery) {
                $owner      = $gallery['owner'] ;
                $galleryid  = $gallery['galleryid'] ;
                $galleryName= $gallery['galleryname'] ;
                $text = $owner.':'.$galleryName ;
                $selected = ( $galleryid == $currentGalleryId ) ? 'selected' : '' ;
                echo '<option value="'.$galleryid.'"  '.$selected.' >'.$text.'</option>'.LINE_END ;
            }
            ?>
        </select>
    </label>&nbsp;&nbsp;
    <button name="goShow" class="bt btGal">Просмотр</button>&nbsp;&nbsp;
    <?php
    if ($editFlag) {
        ?>
        <button name="editGallery" class="btGal">Редактировать</button><br>
        <label>
            <span class="label">Новый альбом:</span>
            <input type="text" name="addGallery" class="field">
        </label>&nbsp;&nbsp;
        <button name="addGalleryExec" class="btGal">Добавить</button>
    <?php
    }
    ?>
    <br>
    <div style="margin-left:451px;">
        <button name="exit" class="btGal">Прервать</button
    </div>
</form
