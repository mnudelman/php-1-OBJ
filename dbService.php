<?php
/**
 * Created by PhpStorm.
 * User: mnudelman@yandex.ru
 * Date: 30.04.15
 * Time: 9:20
 */
function dbConnect() {

}

/**
 * sql оператор заканчивается ";"
 * комментарии // или --
 * @param $sqlScript - файл - sql операторы
 * @return $sqlOperators - список sql операторов
 */
function scriptParser($sqlScript) {
    $commentPrefix = ['//','--'] ;   // символы - начала комментарии
    $endSymb = ';' ;
    $sqlOperators = [] ;
    $handle = false ;
    if (!file_exists($sqlScript) ||
    !($handle = fopen($sqlScript,'r')) ) {
       echo 'Ошибка открытия файла:'.$sqlScript ;
        return false ;
    }
    $curOperator = '' ;
    while ($line = fgets($handle)) {
        $line = trim($line) ;
       /* $commentPos = false ;*/
        foreach ($commentPrefix as $c) {
            $commentPos = strpos($line,$c) ;
            if (gettype($commentPos) == 'integer') {
                $commentPos = strpos($line,$c) ;
                break ;
            }
        }
        if (gettype($commentPos) == 'integer') {
          if ( 0 == $commentPos) {
                continue ;
          }else {
              $line = rtrim( substr($line, 0, $commentPos) ) ;
          }
        }
        /**  конец оператора */
        if ($endPos = strpos($line,$endSymb)) {
            $line = substr($line,0,$endPos) ;
            $curOperator = $curOperator.' '.$line ;
            $name = sqlName($curOperator) ;
            $sqlOperators[] = ['text' => $curOperator,
                               'result' => '',
                               'error' => '',
                               'name' => $name,  // имяЗапроса
                               'count' => 0 ] ;
            $curOperator = '' ;
            continue ;
        }
        $curOperator = $curOperator.' '.$line ;

    }


    return $sqlOperators ;
}

/**
 * определяет имя sql-запроса
 * @param $sql - строка - текст sql-query
 */
function sqlName($sql) {
    $sql = trim($sql);
    $nArr = explode(' ',trim($sql)) ;
    return strtoupper($nArr[0]) ;
}