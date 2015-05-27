<?php
/**
 * Форма задания login, password
 * Date: 23.05.15
 *
 */
?>
<div>
    ВХОД. Войдите под своим login,password или
    <a href="<?php echo $urlToProfile ?>"> пройдите регистрацию</a><br>
</div>
<form action="<?php echo  $urlToUser ?>" method="post">

    <label><span class="label"><strong>Имя:</strong></span>
        <input class="field" type="text" name="login"
               value ="<?php echo $login ?>"> </label> </br>

    <label><span class="label"> <strong>Пароль:</strong></span>
        <span> <input class="field" type="password" name="password"
               value ="<?php echo $password ?>" ></label> </br>


    <label>
        <input type="checkbox" name="savePassword" class="bt">Запомнить пароль </label><br>
    <button name="enter" class="bt">ВОЙТИ</button>
    <?php
    if ($profileIsPossible){
    ?>
    <button name="profile">ПРОФИЛЬ</button>
    <?php
    }
    ?>
    <button name="exit">НА ГЛАВНУЮ</button>

</form>
