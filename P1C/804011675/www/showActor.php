<html>
    <head>
        <title>Actor Information</title>
    </head>

    <body style="background-color:#AAE3FF;font-family:Arial;">
        <h1>Movie Information:</h1>

        <?php
            if($_GET["aid"]){
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

                $actor_query = "SELECT * FROM Actor WHERE id=" . $_GET["aid"];

                $actor_result = mysql_query( $actor_query );

                #if no matching row (tuple) from database
                if (mysql_num_rows($actor_result) == 0) {
                    echo "No actor found";
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

                #closing connection
                mysql_close($db_connection);
            }
        ?>

        <?php
            if ($_GET["first"]) {
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

                $query = "SELECT first, last, id FROM Actor 
                    WHERE first LIKE '" . $_GET["first"] . "%' ORDER BY first";

                echo "<h2>Actors starting with letter <u>" . $_GET["first"] . "</u></h2>";
                $result = mysql_query($query, $db_connection);
                
                if ($result && mysql_num_rows($result)>0){
                    echo "<h3>Results: </h3><p>";
                    
                    // bordered box to display results (so it doesn't get ugly)
                    echo "<div style=\"border:1px solid #8D6932;width:500px;height:60%;overflow:auto;overflow-y:scroll;overflow-x:hidden;text-align:left\" ><p>";

                    while($row = mysql_fetch_row($result)){
                        echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";

                        echo "<a href = './showActor.php?aid=$row[2]'>";
                        echo "" . $row[0] . " " . $row[1];
                        echo "</a><br/>";
                    }
                    echo "</p></div>";
                } else { echo "<b>No actors found. </b>";}

                // close database
                mysql_close($db);
            }
        ?>

        <?php
            if ($_GET["last"]) {
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

                $query = "SELECT first, last, id FROM Actor 
                    WHERE last LIKE '" . $_GET["last"] . "%' ORDER BY last";

                echo "<h2>Actors starting with letter <u>" . $_GET["last"] . "</u></h2>";
                $result = mysql_query($query, $db_connection);
                
                if ($result && mysql_num_rows($result)>0){
                    echo "<h3>Results: </h3><p>";
                    
                    // bordered box to display results (so it doesn't get ugly)
                    echo "<div style=\"border:1px solid #8D6932;width:500px;height:60%;overflow:auto;overflow-y:scroll;overflow-x:hidden;text-align:left\" ><p>";

                    while($row = mysql_fetch_row($result)){
                        echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";

                        echo "<a href = './showActor.php?aid=$row[2]'>";
                        echo "" . $row[1] . ", " . $row[0];
                        echo "</a><br/>";
                    }
                    echo "</p></div>";
                } else { echo "<b>No actors found. </b>";}

                // close database
                mysql_close($db);
            }
        ?>

        <?php
            // PHP to display letters at the bottom
            echo "<h3>Select Actor (by First Name): </h3>";
            $some = array(A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, P, Q, R, S, T, U, V, W, X, Y, Z);
            foreach ($some as $page){
                echo " <a href=./showActor.php?first=$page>$page</a> ";
                if($page != Z)
                    echo "|";
            }

            // PHP to display letters at the bottom
            echo "<h3>Select Actor (by Last Name): </h3>";
            $some = array(A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, P, Q, R, S, T, U, V, W, X, Y, Z);
            foreach ($some as $page){
                echo " <a href=./showActor.php?last=$page>$page</a> ";
                if($page != Z)
                    echo "|";
            }
        ?>

    </body>
</html>