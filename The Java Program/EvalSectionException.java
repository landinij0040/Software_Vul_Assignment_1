import java.io.PrintWriter;

public class EvalSectionException extends Exception{
    
    public EvalSectionException(String m) {
        System.out.println(Fall21JavaProg.EOL+"Parsing or execution Exception: "+m+Fall21JavaProg.EOL);
    }   
}
