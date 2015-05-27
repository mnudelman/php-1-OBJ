<?php
/**
 * Загрузчик классов
 * имя файла совпадает с именем класса
 * User: michael
 * Date: 22.05.15
 * Time: 21:16
 */
function my_autoloader($class) {

    $classDirs = TaskStore::getClassDirs() ;  // список директорий для поиска класса
    $classFile = false ;
    foreach($classDirs as $dir) {
        $file = findFile($dir, $class);   // ищем во внутренних директориях
        if (false !== $file) {
            $classFile = $file;
            break;
        }
    }
    if (false !== $classFile){
        include $classFile ;
    }
}

function findFile($dir,$class) {
    $file = $dir.'/'.$class.'.php' ;
    if (file_exists($file)) {
        return $file ;
    }
    if ( $dh = opendir($dir)) {
        while ( false !== ($dr = readdir($dh)) ) {
            if ('.' == $dr || '..' == $dr) {
                continue ;
            }
            if (is_dir($dr)) {
                findFile($dr,$class) ;
            }
        }
    }
    return false ;

}
spl_autoload_register('my_autoloader');
