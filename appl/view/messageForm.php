<?php
/**
 *  Вывод сообщений полученных от контроллера или модели
 */
function messageShow($messages,$title)
{
    if (!empty($messages)) {
        ?>
        <form>
            <h3><?php echo $title?></h3>
            <textarea name="sqlText" readonly="readonly"
                      style="width:620px;height:200px;font-size:15px ;background-color: #d6e356; color:blue">
        <?php
        echo chr(10);

        foreach ($messages as $erTxt) {
            echo $erTxt . CHR(10);
            echo '' . CHR(10);
        }

        ?>
    </textarea><br>
        </form> <br>
    <?php
    }
}
?>
<?php
// вывести сообщения модели
$msg = TaskStore::getParam('message') ;
$msgText = $msg->getMessages() ;
$title = '' ;
messageShow($msgText,$title) ;
$msg->clear() ;
