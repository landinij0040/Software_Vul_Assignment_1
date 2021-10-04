<?php

class EvalSectionException extends Exception
{
    public function printException(string $m){
        echo PHP_EOL . "Parsing or execution Exception: " . $m . PHP_EOL."\n";
    }
}