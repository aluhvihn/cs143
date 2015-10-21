<html>
    <head><title>SQL Query</title></head>
    <body>
        <h1>SQL Query</h1>
        <p>Type an SQL query in the following box:</p>
        <p>Example: <kbd>SELECT * FROM Actor WHERE id=10;</kbd></p>
        <form method="GET">
            <textarea name="query" rows="8" cols="60"></textarea>
            <br>
            <input type="submit" value="submit"/>
        </form>
        <p><small>Note: tables and fields are case sensitive. All tables in Project 1B are availale.</small></p>
        
        <?php
            if($_GET["query"]){
                    #establishing connection
                    $db_connection = mysql_connect("localhost", "cs143", "");
                    
                    #basic error handling
                    #if connection fails
                    if(!$db_connection) {
                            $errmsg = mysql_error($db_connection);
                            echo "Connection failed: $errmsg <br />";
                            exit(1);
                    }

                    #selecting database
                    mysql_select_db( "CS143", $db_connection );

                    #issuing query from input
                    $result = mysql_query( $_GET["query"] );

                    #if no matching row (tuple) from database
                    if (mysql_num_rows($result) == 0) {
                            echo "No result found";
                            exit(1);
                    }

                    echo "<h3>Results from MySQL:</h3>";
                    #retrieving results
                    #creating table for query result
                    echo "<table border=1><tr>";
                    $f = 0;
                    #get column information from query
                    #return as object (name: column name)
                    while ($f < mysql_num_fields($result)) {
                            $meta = mysql_fetch_field($result, $f);
                            echo "<td><strong>" . $meta->name . "</strong></td>";
                            $f = $f + 1;
                    }
                    echo "<tr>";

                    $r = 0;
                    #get row information from query
                    #returns numerical array of strings
                    while ($row = mysql_fetch_row($result)) {
                            for ($r = 0; $r < $f; $r++) {
                                    #if column is NULL, write N/A
                                    if ($row[$r] == NULL) {
                                            echo "<td>N/A</td>";
                                    }
                                    #otherwise, write fetched value of column from the row
                                    else {
                                            echo "<td>" . $row[$r] . "</td>";
                                    }
                            }
                            echo "</td><tr>";
                    }
                    echo "</tr></table>";

                    #closing connection
                    mysql_close($db_connection);
            }
        ?>
    </body>
</html>
