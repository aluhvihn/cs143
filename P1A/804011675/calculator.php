<html>
    <head><title>Calculator</title></head>
    <body>
        <h1>Calculator</h1><br/>
        <form method="GET">
            <input type="text" name="expr">
            <input type="submit" value="Calculate">
        </form>

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
                                ")";
                $invalid = "/(".$invalid_char."|".$invalid_start."|".$invalid_end."|".$invalid_combo.")/";
                if (preg_match($invalid, $_GET["expr"]) == 1){
                    echo("<h2>Result:</h2> <p>Invalid Expression</p>");
                }

                // Expression is valid. Evaluate the result.
                else{
                    $expr_len = strlen($_GET["expr"]);
                    $expr_str = $_GET["expr"];
                    
                    eval("\$result = $expr_str;");

                    echo("<h2>Result:</h2> <p>".$_GET["expr"]." = ".$result."</p>");
                }
            }
        ?>
    </body>
</html>