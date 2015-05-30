<?php
/**
 * Форма страничного навигатора
 * Date: 28.05.15
 * Time: 21:28
 */
?>
<div align="center" class="navigator">
<form method="POST"  action="<?php echo $urlNavigator ?>">
    <a href="<?php echo $urlNavigator.'&page=first' ?>" readonly="readonly">
            <img src="<?php echo $htmlDirTop ?>/images/go-first.png"
                     title="первая страница" alt="|<" >
    </a>
    <a href="<?php echo $urlNavigator.'&page=prev' ?>">
        <img src="<?php echo $htmlDirTop ?>/images/go-previous.png"
                     title="предыдущая страница" alt="<" >
    </a>
    <?php

    for ($i=$navPageMin; $i <= $navPageMax; $i++) {
        echo '<a href="'.$urlNavigator.'&page='.$i.'">' ;
        if ($currentPage == $i) {
            echo '<span  class="navPageCurrent">' . $i . '</span></a>' . TaskStore::LINE_END;
        }else {
            echo '<span  class="navPageNum">' . $i . '</span></a>' . TaskStore::LINE_END;
        }
    }

    ?>
    <a href="<?php echo $urlNavigator.'&page=next' ?>">
        <img src="<?php echo $htmlDirTop ?>/images/go-next.png"
                     title="следующая страница" alt=">" >
    </a>
    <a href="<?php echo $urlNavigator.'&page=last' ?>">
        <img src="<?php echo $htmlDirTop ?>/images/go-last.png"
                     title="последняя страница" alt=">|" >
    </a>

    <br><br>

    <select name="pictPerPage">
        <?php
        for ($i=1; $i<=5; $i++) {
            $selected = ($i == $pictPerPage) ? 'selected' :'' ;
            echo '<option value="'.$i.'"'.$selected.'>'.' картинок на странице-'.$i.'</option>' ;
        }
        for ($i=1; $i<=10; $i++) {
            $j = $i * 10 ;
            $selected = ($j == $pictPerPage) ? 'selected' :'' ;
            echo '<option value="'.$j.'"'.$selected.'>'.' картинок на странице-'.$j.'</option>' ;
        }
        ?>
</select>
<input type="submit" name="enter" value="Принять">

</form>
</div>
