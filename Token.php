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
     * Constructor that delegates to two other constructors.
     */
    public function __construct(){
        $arguments = func_get_args();
        $numberOfArguments = func_num_args();
        if(method_exists($this, $function = '__construct'.$numberOfArguments)){
            call_user_func_array(array($this,$function), $arguments);
        }
    }

    /**
     * @param $theType: A integer, used to determine the type of the token
     */
    public function __construct1(int $theType){
        $this->type  = $theType;
        $this->value = "";
    }
    /**
     * @param $theType: A integer, used to determine the type of the token
     * @param $theValue: A string, used to store the value of the token
     */
    public function __construct2( int $theType, string $theValue){
        $this->type = $theType;
        $this->value = $theValue;
    }
}