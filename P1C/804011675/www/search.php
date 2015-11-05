<html>
    <head>
        <title>Search Page</title>
    </head>

    <body style="background-color:#AAE3FF;font-family:Arial;">
        <h1>Search BruinDB:</h1>
        <p>Search for an actor, actress, or movie.</p>
        
        <form method="GET">
            <!-- <textarea name="query" rows="8" cols="60"></textarea> -->
            <input type="text" name="input" size="50" />
            <input type="submit" value="Search"/>
        </form>

        <?php
            if($_GET["input"]){
                #establishing connection
                $db_connection = mysql_connect("localhost", "cs143", "");
                
                #basic error handling
                #if connection fails
                if(!$db_connection) {
                    $errmsg = mysql_error($db_connection);
                    echo "<h3>Connection failed: </h3> <p>$errmsg</p> <br/>";
                    exit(1);
                }

                #selecting database
                mysql_select_db( "CS143", $db_connection );

                #issuing query from input
                #separating input into keywords
                $keywords = explode(" ", $_GET["input"]);
                $num_keywords = count($keywords);

                $actor_query = "SELECT first, last, dob FROM Actor WHERE (first LIKE '%" . $keywords[0] . "%' OR last LIKE '%" . $keywords[0] . "%')";
                $movie_query = "SELECT title, year FROM Movie WHERE (title LIKE '%" . $keywords[0] . "%')";

                # If there are multiple keywords
                if ($num_keywords > 1) {
                    $i = 0;
                    for ($i=1; $i < $num_keywords; $i++) {
                        $actor_query = $actor_query . " AND (first LIKE '%" . $keywords[$i] . "%' OR last LIKE '%" . $keywords[$i] . "%')";
                        $movie_query = $movie_query . " AND (title LIKE '%" . $keywords[$i] . "%')";
                    }
                }
                $actor_result = mysql_query( $actor_query );
                $movie_result = mysql_query( $movie_query );

                echo "<h3>Actor Results:</h3>";
                #if no matching row (tuple) from database
                if (mysql_num_rows($actor_result) == 0) {
                    echo "No actors found";
                }
                else {
                    #retrieving results
                    #creating table for query result
                    echo "<table border=1><tr>";
                    $f = 0;
                    #get column information from query
                    #return as object (name: column name)
                    while ($f < mysql_num_fields($actor_result)) {
                        $meta = mysql_fetch_field($actor_result, $f);
                        echo "<td><strong>" . $meta->name . "</strong></td>";
                        $f = $f + 1;
                    }
                    echo "<tr>";

                    $r = 0;
                    #get row information from query
                    #returns numerical array of strings
                    while ($row = mysql_fetch_row($actor_result)) {
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
                }

                echo "<h3>Movie Results:</h3>";
                #if no matching row (tuple) from database
                if (mysql_num_rows($movie_result) == 0) {
                    echo "No movies found";
                    exit(1);
                }
                else {
                    #retrieving results
                    #creating table for query result
                    echo "<table border=1><tr>";
                    $f = 0;
                    #get column information from query
                    #return as object (name: column name)
                    while ($f < mysql_num_fields($movie_result)) {
                        $meta = mysql_fetch_field($movie_result, $f);
                        echo "<td><strong>" . $meta->name . "</strong></td>";
                        $f = $f + 1;
                    }
                    echo "<tr>";

                    $r = 0;
                    #get row information from query
                    #returns numerical array of strings
                    while ($row = mysql_fetch_row($movie_result)) {
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
                }

                #closing connection
                mysql_close($db_connection);
                echo '<br>
                <form method="GET">
                    <!-- <textarea name="query" rows="8" cols="60"></textarea> -->
                    <input type="text" name="input" size="50" />
                    <input type="submit" value="Search"/>
                </form>';
            }
        ?>

    </body>
</html>