<?php
include('../Token.php');
include('../TokenType.php');

$newToken = new Token($TOKENTYPE['LBRACKET']);

echo $newToken->type;


