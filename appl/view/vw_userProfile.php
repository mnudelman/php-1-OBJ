<?php
/**
 * Форма редактирования профиля
 * Date: 23.05.15
 */
?>
<?php
//$successfulRegistration = (!empty($_SESSION['successfulRegistration'])) ;
//$profile = $_SESSION['profile'] ;
//$profileEditFlag = isset($_SESSION['tmp_profileEditFlag']) ;
if ($successfulSave && !$profileEditFlag) {
    ?>
    <a href="<?php echo $urlToDefault ; ?>"><strong>Регистрация завершена успешно</strong></a>
<?php
} else {   // выводить форму
    ?>
    <?php
    if (!empty($profileEditFlag)) {
        echo '<h3>Ваш профиль</h3>' . LINE_END;
    } else {
        echo '<h3>Заполните карту регистрации</h3>' . LINE_END;
    }
    ?>
    <form action="<?php echo $urlToProfile ;?>" method="post" class="formColor">

        <label> <span class="label"><strong>Ваша фамилия:</strong></span>
            <input class="field" type="text" name="lastname"
            <?php echo 'value="' . $profile['lastname'] . '"> ' ?>
        </label> <br>

        <label> <span class="label"> <strong>имя:</strong> </span>
            <input class="field" type="text" name="firstname"
            <?php echo 'value="' . $profile['firstname'] . '"> ' ?>
        </label> <br>

        <label> <span class="label"> <strong>Отчество:</strong></span>
            <input class="field" type="text" name="middlename"
            <?php echo 'value="' . $profile['middlename'] . '"> ' ?>
        </label> <br>


        <label> <span class="label"><strong>Эл.почта:</strong>  </span>
            <input class="field1" type="email" name="email"
            <?php echo 'value="' . $profile['email'] . '"> ' ?>
        </label> <br>
        <?php
        if ( false === $profileEditFlag ) { // при проосмотре профиля login,passw убираю
            // для простоты
            ?>
            <label> <span class="label"><strong>login*:</strong></span>
                <input class="field1" type="text" name="login"
                <?php echo 'value="' . $profile['login'] . '"> ' ?>
            </label> <br>
            </label>    <span class="label"><strong>пароль*:</strong></span>
            <input class="field1" type="password" name="password"
            <?php echo 'value="' . $profile['password'] . '"> ' ?>
            </label> <br>
            <?php
        }
        ?>
        <p><span class="label"><strong>пол:</strong></span>

        <div class="group_item">
            <label>
                <input type="radio" name="sex" value="m"
                    <?php echo ("m" == $profile['sex']) ? "checked" : ''; ?>
                    >
                мужской </label>
            <label>
                <input type="radio" name="sex" value="w"
                    <?php echo ("w" == $profile['sex']) ? "checked" : ''; ?>
                    >
                женский</label></br>
        </div>
        </p>
        <p><span class="label"><strong>Дата рождения:</strong></span>

        <div class="group_item">
            &nbsp;&nbsp;год:
            <select name="birthday_year">
                <?php
                for ($i = 1920; $i <= 2010; $i++) {
                    $selected = ($i == $profile['birthday_year']) ? "selected" : '';
                    echo '<option value="' . $i . '"  ' . $selected . '>' . $i . '</option>';
                }
                ?>
            </select>
            &nbsp;&nbsp;месяц:
            <select name="birthday_month">
                <?php
                $monthList = ['январь', 'февраль', 'март', 'апрель', 'май', 'июнь',
                    'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'];
                foreach ($monthList as $i => $month) {
                    $selected = ($i == $profile['birthday_month']) ? "selected" : '';
                    echo '<option value="' . $i . '"  ' . $selected . '>' . $month . '</option>';
                }
                ?>
            </select>
            &nbsp;&nbsp;день:
            <select name="birthday_day">
                <?php
                for ($i = 1; $i <= 31; $i++) {
                    $selected = ($i == $profile['birthday_day']) ? "selected" : '';
                    echo '<option value="' . $i . '"  ' . $selected . '>' . $i . '</option>';
                }
                ?>

            </select><br>
        </div>
        <p>
            <strong>Добавьте произвольную информацию о себе:</strong> <br>
        <textarea width=“1000px” height=“150px” name="info" value="дополнительная информация">
            <?php echo $profile['info'] ?>
          </textarea>

        </p>
        <?php
        if (!$profileError) {   // при ошибке кнопки не выводятся
            ?>

            <p><input type="submit" name="save" value="Сохранить">
                <input type="reset" value="Сбросить">
                <button name="exit">Прервать</button>
            </p>
        <?php
        }
        ?>

    </form>
<?php
}
?>

