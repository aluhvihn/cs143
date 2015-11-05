<html>
    <head>
        <title>Actor Information</title>
    </head>

    <body style="background-color:#AAE3FF;font-family:Arial;">

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

                $actor_query = "SELECT last, first, sex, dob, dod FROM Actor WHERE id=" . $_GET["aid"];
                $acting_background = "SELECT mid, role, title, year FROM MovieActor A, Movie M WHERE A.aid=" . $_GET["aid"] . " AND A.mid = M.id ORDER BY year";
                $directing_background = "SELECT mid, title, year FROM MovieDirector D, Movie M WHERE D.did=" . $_GET["aid"] . " AND D.mid = M.id ORDER BY year";

                $actor_result = mysql_query( $actor_query );
                $movie_result = mysql_query( $acting_background );
                $directing_result = mysql_query( $directing_background );

                # If no matching row (tuple) from database
                if (mysql_num_rows($actor_result) == 0) {
                    echo "No actor found";
                }
                else {
                    # Retrieving results
                    
                    # Get actor information from actor_result
                    $a_row = mysql_fetch_row($actor_result);
                    echo "<h1><u>Actor Information</u></h1>";
                    echo "<h2>" . $a_row[1] . " " . $a_row[0] . "</h2>";
                    echo "<strong>Sex:</strong> " . $a_row[2] . "</br>";
                    echo "<strong>Date of Birth:</strong> " . date("F d, Y", strtotime($a_row[3])) . "</br>";
                    echo "<strong>Date of Death:</strong> ";
                    if ($a_row[4]) {  //If there is a "Date of Death"
                        echo "" . date("F d, Y", strtotime($a_row[4]));
                    }
                    else {
                        echo "N/A (Still Alive)";
                    }

                    # Get movies actor was in from movie_result
                    echo "</br></br>";
                    echo "<strong>Movies starring " . $a_row[1] . " " . $a_row[0] . ":</strong>";
                    # Link to add movie roles
                    echo "&nbsp;&nbsp;<small><a href = './addMovieActor.php' style='text-decoration: none'>&lt; Add a movie role for this actor &gt;</a></small></br>";

                    if (mysql_num_rows($movie_result) == 0) {
                        echo "No record of movie roles for this actor.</br>";
                    }
                    else {
                        echo "<div style=\"border:1px solid;width:800px;height:20%;overflow:auto;overflow-y:scroll;overflow-x:hidden;text-align:left;padding-left:2em;\" ><p>";
                        while($m_row = mysql_fetch_row($movie_result)) {
                            echo "<a href = './showMovie.php?mid=" . $m_row[0] . "'>";
                            echo "" . $m_row[2] . " (" . $m_row[3] . ")</a>";
                            echo "&nbsp;&nbsp;<em>Role:</em> \"" . $m_row[1];
                            echo "\"</br>";
                        }
                        echo "</p></div>";
                    }

                    # Get movies person directed
                    echo "</br></br>";
                    echo "<strong>Movies directed by " . $a_row[1] . " " . $a_row[0] . ":</strong>";
                    # Link to add movie roles
                    echo "&nbsp;&nbsp;<small><a href = './addMovieDirector.php' style='text-decoration: none'>&lt; Add a movie directed by this person &gt;</a></small></br>";

                    if (mysql_num_rows($directing_result) == 0) {
                        echo "No record of movies directed by this person.</br>";
                    }
                    else {
                        echo "<div style=\"border:1px solid;width:800px;height:20%;overflow:auto;overflow-y:scroll;overflow-x:hidden;text-align:left;padding-left:2em;\" ><p>";
                        while($d_row = mysql_fetch_row($directing_result)) {
                            echo "<a href = './showMovie.php?mid=" . $d_row[0] . "'>";
                            echo "" . $d_row[1] . " (" . $d_row[2] . ")</a></br>";
                            echo "";
                        }
                        echo "</p></div>";
                    }
                }
                echo "</br><hr>";
                # Closing connection
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

                echo "<h2>Actors with first name starting with the letter '" . $_GET["first"] . "'</h2>";
                $result = mysql_query($query, $db_connection);
                
                if ($result && mysql_num_rows($result)>0){
                    echo "<h3>Results: </h3><p>";
                    
                    echo "<div style=\"border:1px solid;width:800px;height:60%;overflow:auto;overflow-y:scroll;overflow-x:hidden;text-align:left;padding-left:2em;\" ><p>";

                    while($row = mysql_fetch_row($result)){
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

                echo "<h2>Actors with last name starting with the letter '" . $_GET["last"] . "'</h2>";
                $result = mysql_query($query, $db_connection);
                
                if ($result && mysql_num_rows($result)>0){
                    echo "<h3>Results: </h3><p>";
                    
                    // bordered box to display results (so it doesn't get ugly)
                    echo "<div style=\"border:1px solid;width:800px;height:60%;overflow:auto;overflow-y:scroll;overflow-x:hidden;text-align:left;padding-left:2em;\" ><p>";

                    while($row = mysql_fetch_row($result)){
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