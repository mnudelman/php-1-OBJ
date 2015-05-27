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
    public function __construct($title='',$name='') {
        $this->title = $title ;
        $this->name = $name ;
        $this->messages = [] ;
    }
    public function addMessage($text) {
        $this->messages[] = $text ;
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