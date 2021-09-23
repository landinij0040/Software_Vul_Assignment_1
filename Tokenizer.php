<?php
require_once 'TokenType.php';
require_once 'Token.php';

class Tokenizer
{
    /**
     * @var: char array containing input file characters
     */
    private $e;

    /**
     * @var: Index of the current character
     */
    private $i;

    /**
     * @var: the actual current character
     */
    public $currentChar;

    /**
     * @param $s: In the input STRING file
     */
    function __construct($s){
        $this->e = str_split($s,1);
        $this->i = 0;
    }

    /**
     * To get the next token from the input string.
     * @return Token
     */
    function nextToken(){
        // Skip blanklike characters
        while ($this->i < count($this->e) && strpos(" \n\t\r", $this->e[$this->i])  !== FALSE  ){
            $this->i = $this->i + 1 ;
        }
        if ($this->i >= count($this->e) ) {
            return new Token(TokenType::$TOKENTYPE['EOF']);
        }
        // Check for INT
        $inputString = "";
        while ( $this->i < count($this->e) && strpos("0123456789",$this->e[$this->i]) !== FALSE ){
            $inputString = $inputString . $this->e[$this->i++];
        }
        if($inputString !== "")
        {
            return new Token(TokenType::$TOKENTYPE['INT'], $inputString);
        }

        // check for ID or reserved word
        while ($this->i < count($this->e) && strpos("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_", $this->e[$this->i]) !== FALSE) {
            $inputString = $inputString . $this->e[$this->i++];
        }
        if ($inputString !== ""){
            if($inputString === "if" ){
                return new Token(TokenType::$TOKENTYPE['IF']);
            }
            if($inputString === "else" ){
                return new Token(TokenType::$TOKENTYPE['ELSE']);
            }
            return new Token(TokenType::$TOKENTYPE['ID'], $inputString);
        }
        // We're left with strings or one character tokens
        switch($this->e[$this->i++])
        {
            case '{':
                return new Token(TokenType::$TOKENTYPE['LBRACKET'], "{");
            case '}':
                return new Token(TokenType::$TOKENTYPE['RBRACKET'], "}");
            case '[':
                return new Token(TokenType::$TOKENTYPE['LSQUAREBRACKET'],"[");
            case ']':
                return new Token(TokenType::$TOKENTYPE['RSQUAREBRACKET'],"]");
            case '<':
                return new Token(TokenType::$TOKENTYPE['LESS'],"<");
            case '>':
                return new Token(TokenType::$TOKENTYPE['GREATER'],">");
            case '=':
                return new Token(TokenType::$TOKENTYPE['EQUAL'],"=");
            case '"':
                $value = "";
                while($this->i < count($this->e) && $this->e[$this->i] !== '"'){
                    $c = $this->e[$this->i++];
                    if ($this->i >= count($this->e)){
                        return new Token(TokenType::$TOKENTYPE['OTHER']);
                    }
                    // check for escaped double quote
                    if($c==='\\' && $this->e[$this->i] === '"' ){
                        $c = '"';
                        $this->i++;
                    }
                    $value = $value . $c;
                }
                $this->i++;
                return new Token(TokenType::$TOKENTYPE['STRING'],$value);
            default:
                // Other Should result in exception
                return new Token(TokenType::$TOKENTYPE['OTHER']);
        }
    }
}