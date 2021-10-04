<?php
static $test =" Before the tests";


echo $test;
test1();
echo $test;
test2();
echo $test;
test3();



function test1(){
    global $test;
    $test = "Within the test1";
}

function test2(){
    global $test;
    $test = "Within the test2";
}

function test3(){
    global $test;
    $test = $test . "+ test3";
    echo $test;
}