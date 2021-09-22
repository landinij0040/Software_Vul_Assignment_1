import java.net.URL;
import java.io.*;
import java.util.HashMap;
/**
 * @author Luc Longpre for Secure Web-Based Systems, Fall 2021
 *
 * This program parses and interprets a simple programming language that has
 * nested if or if-else, assignment and string statements.
 *
 * <input> ::= <section>*
 * <section> ::= [ <statement>* ]
 * <statement> ::= STRING | <assignment> | <conditional>
 * <assignment> ::= ID '=' INT
 * <conditional> ::= 'if' <condition> '{' <statement>* '}' [ 'else' '{'
 * <statement>* '}' ]
 * <condition> ::= ID ('<' | '>' | '=') INT
 */
public class Fall21JavaProg {
  
    static Token currentToken;
    static Tokenizer t;
    static HashMap<String, Integer> map;
    static String oneIndent = "   ";
    static String result; // string containing the result of execution
    static String EOL=System.lineSeparator(); // new line, depends on OS

    public static void main(String[] args) throws Exception {
        // open the URL into a buffered reader,
        // print the header,
        // parse each section, printing a formatted version
        //     followed by the result of the execution
        // print the footer.
        String inputSource;
        // inputSource = "http://localhost/4339_f21_assignment1/fall21Testing.txt"; was this
        inputSource = "file:///C:/Users/isaia/PhpstormProjects/Software_Vul_Assignment_1/The%20Java%20Program/fall21Testing.txt";
        URL inputUrl = new URL(inputSource);
        BufferedReader in = new BufferedReader(new InputStreamReader(inputUrl.openStream()));
        String header = "<html>"+EOL
                + "  <head>"+EOL
                + "    <title>CS 4339/5339 PHP assignment</title>"+EOL
                + "  </head>"+EOL
                + "  <body>"+EOL
                + "    <pre>";
        String footer = "    </pre>"+EOL
                + "  </body>"+EOL
                + "</html>";
        String inputLine;
        String inputFile = "";
        while ((inputLine = in.readLine()) != null) {
            inputFile += inputLine + EOL;
        }
        t = new Tokenizer(inputFile);
        System.out.println(header);
        currentToken = t.nextToken();
        int section = 0;
        
        // Loop through all sections, for each section printing result
        // If a section causes exception, catch and jump to next section
        while (currentToken.type != TokenType.EOF) {
            System.out.println("section " + ++section);
            try {
                evalSection();
                System.out.println("Section result:");
                System.out.println(result);
            } catch (EvalSectionException ex) {
                // skip to the end of section
                while (currentToken.type != TokenType.RSQUAREBRACKET
                        && currentToken.type != TokenType.EOF) {
                    currentToken = t.nextToken();
                }
                currentToken = t.nextToken();
            }
        }
        System.out.println(footer);
    }

    static void evalSection() throws EvalSectionException {
        // <section> ::= [ <statement>* ]
        map = new HashMap<>();
        result = "";
        if (currentToken.type != TokenType.LSQUAREBRACKET) {
            throw new EvalSectionException("A section must start with \"[\"");
        }
        System.out.println("[");
        currentToken = t.nextToken();
        while (currentToken.type != TokenType.RSQUAREBRACKET
                && currentToken.type != TokenType.EOF) {
            evalStatement(oneIndent, true);
        }
        System.out.println("]");
        currentToken = t.nextToken();
    }

    static void evalStatement(String indent, boolean exec) throws EvalSectionException {
        // exec it true if we are executing the statements in addition to parsing
        // <statement> ::= STRING | <assignment> | <conditional>
        switch (currentToken.type) {
            case ID:
                evalAssignment(indent, exec);
                break;
            case IF:
                evalConditional(indent, exec);
                break;
            case STRING:
                if (exec)
                    result += currentToken.value + EOL;
                System.out.println(indent + "\"" + currentToken.value + "\"");
                currentToken = t.nextToken();
                break;
            default:
                throw new EvalSectionException("invalid statement");
        }
    }

    static void evalAssignment(String indent, boolean exec) throws EvalSectionException {
        // <assignment> ::= ID '=' INT
        // we know currentToken is ID 
        String key = currentToken.value;
        System.out.print(indent + key);
        currentToken = t.nextToken();
        if (currentToken.type != TokenType.EQUAL) {
            throw new EvalSectionException("equal sign expected");
        }
        System.out.print("=");
        currentToken = t.nextToken();
        if (currentToken.type != TokenType.INT) {
            throw new EvalSectionException("integer expected");
        }
        int value = Integer.parseInt(currentToken.value);
        System.out.println(value);
        currentToken = t.nextToken();
        if (exec)
            map.put(key, value);
    }

    static void evalConditional(String indent, boolean exec) throws EvalSectionException {
        // <conditional> ::= 'if' <condition> '{' <statement>* '}' [ 'else' '{'
        // We know currentToken is "if"
        System.out.print(indent + "if ");
        currentToken = t.nextToken();
        boolean trueCondition = evalCondition(exec);

        if (currentToken.type != TokenType.LBRACKET) {
            throw new EvalSectionException("left bracket extected");
        }
        System.out.println(" {");
        currentToken = t.nextToken();
        while (currentToken.type != TokenType.RBRACKET
                && currentToken.type != TokenType.EOF) {
            if (trueCondition) {
                evalStatement(indent + oneIndent, exec);
            } else {
                evalStatement(indent + oneIndent, false);
            }
        }
        if (currentToken.type == TokenType.RBRACKET) {
            System.out.println(indent + "}");
            currentToken = t.nextToken();
        } else         
            throw new EvalSectionException("right bracket expected");
        if (currentToken.type == TokenType.ELSE) {
            System.out.print(indent + "else");
            currentToken = t.nextToken();
            if (currentToken.type != TokenType.LBRACKET) {
                throw new EvalSectionException("left bracket expected");
            }
            System.out.println(" {");
            currentToken = t.nextToken();
            while (currentToken.type != TokenType.RBRACKET
                    && currentToken.type != TokenType.EOF) {
                if (trueCondition) {
                    evalStatement(indent + oneIndent, false);
                } else {
                    evalStatement(indent + oneIndent, exec);
                }
            }
            if (currentToken.type == TokenType.RBRACKET) {
                System.out.println(indent + "}");
                currentToken = t.nextToken();
            } else 
                throw new EvalSectionException("right bracket expected");
        }
    }
    
    static boolean evalCondition(boolean exec) throws EvalSectionException { 
        // <condition> ::= ID ('<' | '>' | '=') INT
        Integer v1=null; // value associated with ID
        if (currentToken.type != TokenType.ID) {
            throw new EvalSectionException("identifier expected");
        }
        String key = currentToken.value;
        System.out.print(key);
        if (exec) {
            v1 = map.get(key);
            if (v1 == null) {
                throw new EvalSectionException("undefined variable");
            }
        } 
        currentToken = t.nextToken();
        TokenType operator = currentToken.type;
        if (currentToken.type != TokenType.EQUAL
                && currentToken.type != TokenType.LESS
                && currentToken.type != TokenType.GREATER) {
            throw new EvalSectionException("comparison operator expected");
        }
        System.out.print(currentToken.value);
        currentToken = t.nextToken();
        if (currentToken.type != TokenType.INT) {
            throw new EvalSectionException("integer expected");
        }
        int value = Integer.parseInt(currentToken.value);
        System.out.print(value + " ");
        currentToken = t.nextToken();        
        // compute return value
        if (!exec)
            return false;
        boolean trueResult = false;
        switch (operator) {
            case LESS:
                trueResult = v1 < value;
                break;
            case GREATER:
                trueResult = v1 > value;
                break;
            case EQUAL:
                trueResult = v1 == value;
        }
        return trueResult;
    }   
}