<html>
    <head><title>Calculator</title></head>
    <body>
        <h1>Calculator</h1>
        <p>Type an expression in the following box (e.g., 10.5+20*3/25).</p>
        <form method="GET">
            <input type="text" name="expr">
            <input type="submit" value="Calculate">
        </form>
        <ul>
                <li>Only numbers and +,-,* and / operators are allowed in the expression.</li>
                <li>The evaluation follows the standard operator precedence.</li>
                <li>The calculator does not support parentheses.</li>
                <li>The calculator handles invalid input "gracefully". It does not output PHP error messages.</li>
        </ul>
                    
        <?php       
            if($_GET["expr"]){
                    // Filter out any expressions with invalid characters
                    // ie. not [0-9,+,-,*,/,.,[space]]
                $invalid_char = "[^\d\.\*\/\-\+ ]";
                    // Filter out any expressions with invalid start/end
                    // ie. Start with [+,*,/]; End with [+,-,*,/,.]
                $invalid_start = "^[\+\*\/]";
                $invalid_end = "[\+\-\*\/\.]$";
                    // Filter out any expressions with invalid combination of chars
                $invalid_combo ="(" . "\* *\+" .    // invalid combo:  *+
                                "|" . "\+ *\+" .    // invalid combo:  ++
                                "|" . "\- *\+" .    // invalid combo:  -+
                                "|" . "\/ *\+" .    // invalid combo:  /+

                                "|" . "\* *\*" .    // invalid combo:  **
                                "|" . "\+ *\*" .    // invalid combo:  +*
                                "|" . "\- *\*" .    // invalid combo:  -*
                                "|" . "\/ *\*" .    // invalid combo:  /*

                                "|" . "\* *\/" .    // invalid combo:  */
                                "|" . "\+ *\/" .    // invalid combo:  +/
                                "|" . "\- *\/" .    // invalid combo:  -/
                                "|" . "\/ *\/" .    // invalid combo:  //

                                "|" . "\. *\+" .    // invalid combo:  .+
                                "|" . "\. *\-" .    // invalid combo:  .-
                                "|" . "\. *\*" .    // invalid combo:  .*
                                "|" . "\. *\/" .    // invalid combo:  ./
                                "|" . "\. *\." .    // invalid combo:  ..

                                "|" . "[0-9\.] +[0-9\.]" .      // invalid combo:  space in number
                                "|" . "[ \+\-\/\*]0+[0-9]" .    // invalid combo:  numbers starting with zero
                                "|" . "^0+[0-9]" .
                                "|" . "[ \+\-\/\*]0{2,}\." .    // invalid combo:  decimals starting with multiple zeroes
                                "|" . "^0{2,}\." .
                                "|" . "\/0$" .                  // invalid combo:  divide by zero  (at the end)
                                "|" . "\/0[ \+\-\/\*]" .        //                 divide by zero  (in the middle of expression)
                                "|" . "\- *\- *\-" .            // invalid combo: more than two -'s in a row
                                ")";
                $invalid = "/(".$invalid_char."|".$invalid_start."|".$invalid_end."|".$invalid_combo.")/";
                if (preg_match($invalid, $_GET["expr"]) == 1){
                    echo("<h2>Result:</h2> <p>Invalid Expression</p>");
                }
                // Expression is valid. Evaluate the result.
                else{
                    $expr_len = strlen($_GET["expr"]);
                    $expr_str = $_GET["expr"];
                    $expr_str = str_replace("--", "+", $expr_str);      // replaces double -'s to +
                    eval("\$result = $expr_str;");
                    echo("<h2>Result</h2> <p>".$_GET["expr"]." = ".$result."</p>");
                }
            }
        ?>
    </body>
</html>