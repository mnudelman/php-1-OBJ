<?php
/**
 * Формирователь сообщений
 * User: mnudelman@yandex.ru
 * Date: 22.05.15
 */
class Message {
    private $messages = [] ;
    private $title ='' ;
    private $name = '' ;
    private $keys ;
    public function __construct($title='',$name='') {
        $this->title = $title ;
        $this->name = $name ;
        $this->messages = [] ;
    }
    public function addMessage($text) {
        if(is_array($text)){
            $this->keys = 'ARRAY:' ;
            $this->addListMessage($text) ;
        }else {
            $this->messages[] = $text ;
        }

    }
    private function addListMessage($textList) {
        foreach($textList as $k=>$m) {
            $this->keys .= 'key:'.$k ;
            if (is_array($m)){
                $this->addListMessage($m) ;
            }else {
                $this->messages[] = $this->keys.'-mean:'.$m ;
            }
        }
    }

    public function getMessages() {
        return $this->messages ;
    }
    public function getTitle(){
        return $this->title;
    }
    public function getName(){
        return $this->name;
    }
    public function clear() {
        $this->messages = [] ;
    }
}