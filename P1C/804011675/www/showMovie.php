<html>
    <head>
        <title>Movie Information</title>
    </head>

    <body style="background-color:#AAE3FF;font-family:Arial;">
        <h1>Movie Information:</h1>

        <?php
            if($_GET["mid"]){
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

                $movie_query = "SELECT * FROM Movie WHERE id=" . $_GET["mid"];

                $movie_result = mysql_query( $movie_query );

                #if no matching row (tuple) from database
                if (mysql_num_rows($movie_result) == 0) {
                    echo "No movie found";
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
            }
        ?>

        <?php
            if ($_GET["title"]) {
                # establish connection
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

                $query = "SELECT title, id FROM Movie WHERE title LIKE '" . $_GET["title"] . "%' ORDER BY title";

                echo "<h2>Movies starting with letter <u>" . $_GET["title"] . "</u></h2>";
                $result = mysql_query($query, $db_connection);
                
                if ($result && mysql_num_rows($result)>0){
                    echo "<h3>Results: </h3><p>";
                    
                    // bordered box to display results (so it doesn't get ugly)
                    echo "<div style=\"border:1px solid #8D6932;width:500px;height:60%;overflow:auto;overflow-y:scroll;overflow-x:hidden;text-align:left\" ><p>";

                    while($row = mysql_fetch_row($result)){
                        echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";

                        echo "<a href = './showMovie.php?mid=$row[1]'>";
                        echo "" . $row[0];
                        echo "</a><br/>";
                    }
                    echo "</p></div>";
                } else { echo "<b>No movies found. </b>";}

                // close database
                mysql_close($db);
            }
        ?>

        <?php
            // PHP to display letters at the bottom
            echo "<h3>Select Movie (by Title): </h3>";
            $some = array(A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, P, Q, R, S, T, U, V, W, X, Y, Z);
            foreach ($some as $page){
                echo " <a href=./showMovie.php?title=$page>$page</a> ";
                if($page != Z)
                    echo "|";
            }
        ?>

    </body>
</html>