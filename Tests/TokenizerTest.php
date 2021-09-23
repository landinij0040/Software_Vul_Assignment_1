<?php
include("../Tokenizer.php");

// Testing to see if the Tokenizer Class Works
//$test = new Tokenizer("yeahboi");
//echo  var_dump($test);
//
//// Testing the nextToken()
//// Testing the skip blank characters
//$test2 = new Tokenizer(" ");
//$test2->nextToken();
//
//$test2 = new Tokenizer("  1234");
//$test2->nextToken();
//
//$test2 = new Tokenizer("\t\t\n\t     1234");
//$test2->nextToken();

// Testing when i is bigger than the length of the array
// Must make i public to test
//$test2 = new Tokenizer("\t\t\n\t     1234");
//$test2->i = 70;
//echo $test2->nextToken();


// Testing the number input
//$test2 = new Tokenizer("\t\t\n\t     1234");
//$tokenmaybe = $test2->nextToken();
//echo var_dump($tokenmaybe);

// Testing the LETTERS AND IDS
// Testing IF
/*
$test3 = new Tokenizer("  if");
echo var_dump($test3->nextToken());
*/

/*
$test3 = new Tokenizer("  else");
echo var_dump($test3->nextToken());
*/

//$test3 = new Tokenizer("  test");
//echo var_dump($test3->nextToken());

// Testing the strings or one character tokens
//$test4 = new Tokenizer("{");
//echo var_dump($test4->nextToken());

//$test4 = new Tokenizer("}");
//echo var_dump($test4->nextToken());

//$test4 = new Tokenizer("[");
//echo var_dump($test4->nextToken());

//$test4 = new Tokenizer("]");
//echo var_dump($test4->nextToken());

//$test4 = new Tokenizer("<");
//echo var_dump($test4->nextToken());

//$test4 = new Tokenizer(">");
//echo var_dump($test4->nextToken());

//$test4 = new Tokenizer("=");
//echo var_dump($test4->nextToken());
//
//$test4 = new Tokenizer('"');
//echo var_dump($test4->nextToken());

//$test4 = new Tokenizer("\"begun\"");
//echo var_dump($test4->nextToken());

//$test4 = new Tokenizer("\"begun");
//echo var_dump($test4->nextToken());
//echo '"' === "\"";
//
//echo "a" === "a";