<?php
require_once "EvalSectionException.php";


try{
    throwingException();
}catch (EvalSectionException $ex){
    $ex->printException($ex->getMessage());
    echo "Test done";
}
function throwingException(){

    throw new EvalSectionException("bruh work");
}

?>