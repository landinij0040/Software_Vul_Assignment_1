public class Token {
    TokenType type;
    String value;
    
    Token(TokenType theType){
        type = theType;
        value = "";
    }
    Token (TokenType theType, String theValue){
        type = theType;
        value = theValue;
    }
//  The printString function was used for debugging, 
//  It is currently not used.
    String printToken(){
        switch (type) {
            case LBRACKET:
                return "LBRACKET";
            case RBRACKET:
                return "RBRACKET";
            case LSQUAREBRACKET:
                return "LSQUAREBRACKET";
            case RSQUAREBRACKET:
                return "RSQUAREBRACKET";                
            case LESS:
                return "LESS";
            case GREATER:
                return "REATER";
            case EQUAL:
                return "EQUAL";
            case ID:
                return "ID "+value;
            case INT:
                return "INT "+value;
            case IF:
                return "IF";
            case ELSE:
                return "ELSE";
            case STRING:
                return "STRING "+value;
            default:
                return "OTHER";
        }
    } 
}
