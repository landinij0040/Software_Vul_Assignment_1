<?php
require_once "Token.php";
require_once "Tokenizer.php";
require_once "EvalSectionException.php";
require_once "TokenType.php";

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

/**
 * @Token $currentToken: null
 */
static $currentToken=null;
/**
 * @Tokenizer: $t
 */
static $t = null;
/**
 * @var $map
 */
static $map = [];
/**
 * @var $oneIndent
 */
static $oneIndent = "   ";
/**
 * @var $result
 */
static $result = "";

/**
 * @var EOl
 */
static $EOL = PHP_EOL;

/**
 * @var $inputSource
 */

//$inputSource = fopen("The Java Program/fall21Testing.txt",'r');
//$test = file_get_contents($inputSource);

$header = <<<_END
<html>
    <head>
        <title>CS 4339/5339 PHP assignment</title>
    </head>
    <body>
        <pre>
_END;

$footer = <<<_END
        </pre>
    </body>
</html>
_END;

//$inputFile = file_get_contents("The Java Program/fall21Testing.txt"); For my local computer
$inputFile = file_get_contents("fall21Testing.txt");



$t = new Tokenizer($inputFile);
echo $header."\n";
$currentToken = $t->nextToken();
$section = 0;

while ($currentToken->type != 12){
    echo "section " . ++$section ."\n";

    try{
        evalSection();
        echo "Section result:\n";
        echo $result."\n";

    }catch(EvalSectionException $ex){
//        $result = "";
        $ex->printException($ex->getMessage());

        // skip to the end of section
        while($currentToken->type != 3 && $currentToken->type != 12){
            $currentToken = $t->nextToken();
        }
        $currentToken = $t->nextToken();

    }
}

echo $footer."\n";

/**
 * @throws
 */
function evalSection(){
    global $currentToken;
    global $t;
    global $map;
    global $oneIndent;
    global $result;
    $map = [];
    $result = "";
    if($currentToken->type != 2){
        throw new EvalSectionException("A section must start with \"[\"");
    }
    echo "[\n";
    $currentToken = $t->nextToken();
    while($currentToken->type != TokenType::$TOKENTYPE['RSQUAREBRACKET'] && $currentToken->type != TokenType::$TOKENTYPE['EOF']){
        evalStatement($oneIndent, true);
    }
    echo "]\n";
    $currentToken = $t->nextToken();

}

/**
 * @throws EvalSectionException
 */
function evalStatement(string $indent, bool $exec){
    // exec it true if we are executing the statements in addition to parsing
    // <statement> ::= STRING | <assignement> | conditional
    global $currentToken;
    global $result;
    global $t;

    switch ($currentToken->type) {
        case TokenType::$TOKENTYPE['ID']:
            evalAssignment($indent, $exec);
            break;
        case TokenType::$TOKENTYPE['IF']:
            evalConditional($indent, $exec);
            break;
        case TokenType::$TOKENTYPE['STRING']:
            if ($exec) {
                $result = $result . $currentToken->value . PHP_EOL;
            }
            echo $indent . "\"" . $currentToken->value . "\"\n";
            $currentToken = $t->nextToken();
            break;
        default:
            throw new EvalSectionException("invalid statment");

    }

}

function evalAssignment(string $indent, bool $exec){
    // <assignment ::= ID '=' INT>
    // we know currentToken is ID
    global $currentToken;
    global $t;
    global $map;
    $key = $currentToken->value;
    echo $indent . $key;
    $currentToken = $t->nextToken();
    if($currentToken->type !=  TokenType::$TOKENTYPE['EQUAL']){
        throw new EvalSectionException("equal sign expected");
    }
    echo "=";
    $currentToken = $t->nextToken();
    if ($currentToken->type != TokenType::$TOKENTYPE['INT']){
        throw new EvalSectionException("integer expected");
    }
    $value = (int) $currentToken->value;
    /**
     * @var the first instance of $value
     */
    echo $value."\n";
    $currentToken = $t->nextToken();
    if ($exec){
        $map += [$key => $value];
    }
}

/**
 * @throws EvalSectionException
 */
function evalConditional($indent, $exec){
    // <conditional> ::= 'if' <condition> '{' <statement>* '}' [ 'else' '{
    // We know currentToken is "if"
    global $currentToken;
    global $t;
    global $oneIndent;
    echo $indent . "if ";
    $currentToken = $t->nextToken();
    $trueCondition = evalCondtion($exec);

    if($currentToken->type != TokenType::$TOKENTYPE['LBRACKET']){
        throw new EvalSectionException("left bracket extected");
    }
    echo " {"."\n";
    $currentToken = $t->nextToken();
    while($currentToken->type != TokenType::$TOKENTYPE['RBRACKET'] && $currentToken->type != TokenType::$TOKENTYPE['EOF']){
        if ($trueCondition){
            evalStatement($indent.$oneIndent, $exec);
        }else{
            evalStatement($indent.$oneIndent, false);
        }
    }
    if ($currentToken->type == TokenType::$TOKENTYPE['RBRACKET']) {
        echo $indent . "}\n";
        $currentToken = $t->nextToken();
    } else {
        throw new EvalSectionException("right bracket expected");
    }
    if($currentToken->type == TokenType::$TOKENTYPE['ELSE']){
        echo $indent."else";
        $currentToken = $t->nextToken();
        if ($currentToken->type != TokenType::$TOKENTYPE['LBRACKET']){
            throw new EvalSectionException("left bracket expected");
        }
        echo " {\n";
        $currentToken = $t->nextToken();
        while ($currentToken->type != TokenType::$TOKENTYPE['RBRACKET'] && $currentToken->type != Tokentype::$TOKENTYPE['EOF']){
            if ($trueCondition){
                evalStatement($indent.$oneIndent, false);
            }else{
                evalStatement($indent.$oneIndent, $exec);
            }
        }
        if($currentToken->type == TokenType::$TOKENTYPE['RBRACKET']){
            echo $indent."}\n";
            $currentToken = $t->nextToken();
        }else{
            throw new EvalSectionException("right bracket expected");
        }
    }

}

/**
 * @throws EvalSectionException
 */
function evalCondtion($exec){
    // <condition> ::= ID ('<' | '>' | '=') INT
    global $currentToken;
    global $map;
    global $t;
    $v1 = null; // value associated with ID
    if ($currentToken->type != TokenType::$TOKENTYPE['ID']) {
        throw new EvalSectionException("identifier expected");
    }
    $key = $currentToken->value;
    echo $key;
    if($exec){
        $v1 = $map[$key];
        if($v1 == null){
            throw new EvalSectionException("undefined variable");
        }
    }
    $currentToken = $t->nextToken();
    $operator = $currentToken->type;
    if($currentToken->type != TokenType::$TOKENTYPE['EQUAL'] && $currentToken->type != TokenType::$TOKENTYPE['LESS'] && $currentToken->type != TokenType::$TOKENTYPE['GREATER']){
        throw new EvalSectionException("comparison operator expected");
    }
    echo $currentToken->value;
    $currentToken = $t->nextToken();
    if ($currentToken->type != TokenType::$TOKENTYPE['INT']){
        throw new EvalSectionException("integer expected");
    }
    $value = (int) $currentToken->value;
    echo $value." ";
    $currentToken = $t->nextToken();
    // compute return value
    if(!$exec){
        return false;
    }
    $trueResult = false;
    switch ($operator){
        case TokenType::$TOKENTYPE['LESS']:
            $trueResult = $v1 < $value;
            break;
        case TokenType::$TOKENTYPE['GREATER']:
            $trueResult = $v1 > $value;
            break;
        case TokenType::$TOKENTYPE['EQUAL']:
            $trueResult = $v1 == $value;
    }
    return $trueResult;
}

?>