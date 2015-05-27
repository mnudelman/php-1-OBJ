<?php
/**
 * Форма вывода изображений
 * Date: 26.05.15
 */
?>
<?php
if (false === $imgFiles) {

}else {
    foreach ($imgFiles as $imgFile) {
        $file = $imgFile['file'];
        $comment = $imgFile['comment'];
        echo '<div class="imgBlock">' ."\n" ;
        echo '<img src="' . $dirPict . '/' . $file . '" class="imgGal" title="'.$file.'" alt="'.$file.'" >' ."\n";
        echo '<div >' . $comment . '</div>' . "\n";
        echo '</div>';
    }
}

