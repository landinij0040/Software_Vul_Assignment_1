<?php

/**
 * The Token Class
 */
class Token{
    /**
     * @var $type: Is an integer. Used to tell the type of the token
     */
    public $type;

    /**
     * @var $value: Is a String. Used to hold the value of the token
     */
    public $value;

    /**
     * @param $theType: A integer, used to determine the type of the token
     */
    public function __construct($theType){
        $this->type  = $theType;
        $this->value = "";
    }
    /**
     * @param $theType: A integer, used to determine the type of the token
     * @param $theValue: A string, used to store the value of the token
     */
    public function __constructor( $theType, $theValue){
        $this->type = $theType;
        $this->value = $theValue;
    }
}