<html>
    <head>
        <title>Movie Information</title>
    </head>

    <body style="background-color:#AAE3FF;font-family:Arial;">

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

                $movie_query = "SELECT title, year, rating, company FROM Movie WHERE id=" . $_GET["mid"];
                $actor_list = "SELECT id, last, first, role FROM MovieActor M, Actor A WHERE M.mid=" . $_GET["mid"] . " AND M.aid=A.id ORDER BY last";
                $director_list = "SELECT last, first, id FROM MovieDirector M, Director D WHERE M.mid=" . $_GET["mid"] . " AND M.did=D.id ORDER BY last";
                $genre_list = "SELECT genre FROM MovieGenre WHERE mid=" . $_GET["mid"] . " ORDER BY genre";
                $review_list = "SELECT name, time, rating, comment FROM Review WHERE mid=" . $_GET["mid"] . " ORDER BY time";
                $avg_review = "SELECT AVG(rating) FROM Review WHERE mid=" . $_GET["mid"];
                $num_review = "SELECT COUNT(*) FROM Review WHERE mid=" . $_GET["mid"];

                $movie_result = mysql_query( $movie_query );
                $actor_result = mysql_query( $actor_list );
                $director_result = mysql_query( $director_list );
                $genre_result = mysql_query( $genre_list );
                $review_result = mysql_query( $review_list );
                $avg_review_result = mysql_query( $avg_review );
                $num_review_result = mysql_query( $num_review );

                # If no matching row (tuple) from database
                if (mysql_num_rows($movie_result) == 0) {
                    echo "No movie found";
                }
                else {
                    # Retrieving results
                    
                    # Get Movie information from movie_result
                    $m_row = mysql_fetch_row($movie_result);
                    echo "<h1><u>Movie Information</u></h1>";
                    echo "<h2>" . $m_row[0] . " (" . $m_row[1] . ")</h2>";
                    echo "<strong>Producer:</strong> " . $m_row[3] . "</br>";
                    echo "<strong>MPAA Rating:</strong> " . $m_row[2] . "</br>";
                    
                    # Get directors of the movie
                    echo "<strong>Director(s):</strong> ";
                    if (mysql_num_rows($director_result) == 0) {
                        echo "N/A";
                    }
                    else {
                        $d_row = mysql_fetch_row($director_result);
                        echo "<a href = './showActor.php?aid=" . $d_row[2] . "'>";
                        echo $d_row[1] . " " . $d_row[0] . "</a>";
                        while($d_row = mysql_fetch_row($director_result)) {
                            echo ", <a href = './showActor.php?aid=" . $d_row[2] . "'>";
                            echo "" . $d_row[1] . " " . $d_row[0] . "</a>";
                        }
                    }
                    # Link to add Director info
                    echo "<small>&nbsp;&nbsp;<a href = './addMovieDirector.php?id=" . $_GET["mid"] . "' style='text-decoration: none'>&lt; Add a director for this movie &gt;</a></small></br>";
                    
                    echo "<strong>Genre:</strong> ";
                    if (mysql_num_rows($genre_result) == 0) {
                        echo "N/A";
                    }
                    else {
                        $g_row = mysql_fetch_row($genre_result);
                        echo $g_row[1] . " " . $g_row[0];
                        while($g_row = mysql_fetch_row($genre_result)) {
                            echo ", " . $g_row[1] . " " . $g_row[0];
                        }
                    }

                    # Get actors who acted in this movie
                    echo "</br></br>";
                    echo "<strong>Cast of \"" . $m_row[0] . "\" (" . $m_row[1] . "):</strong>";
                    # Link to add Actor info
                    echo "&nbsp;&nbsp;<small><a href = './addMovieActor.php?id=" . $_GET["mid"] . "' style='text-decoration: none'>&lt; Add an actor for this movie &gt;</a></small></br>";
                    if (mysql_num_rows($actor_result) == 0) {
                        echo "No record of actors in this movie.</br>";
                    }
                    else {
                        echo "<div style=\"border:1px solid;width:800px;height:25%;overflow:auto;overflow-y:scroll;overflow-x:hidden;text-align:left;padding-left:2em;\" ><p>";
                        while($a_row = mysql_fetch_row($actor_result)) {
                            echo "<a href = './showActor.php?aid=" . $a_row[0] . "'>";
                            echo "" . $a_row[2] . " " . $a_row[1] . "</a>";
                            echo "&nbsp;&nbsp;As \"" . $a_row[3];
                            echo "\"</br>";
                        }
                        echo "</p></div>";
                    }

                    # Get reviews for this movie
                    echo "</br>";
                    echo "<strong>Reviews:</strong>";
                    # Link to add Review
                    echo "&nbsp;&nbsp;<small><a href = './addComments.php?id=" . $_GET["mid"] . "' style='text-decoration: none'>&lt; Add a comment for this movie &gt;</a></small></br>";
                    if (mysql_num_rows($review_result) == 0) {
                        echo "No reviews have been made for this movie yet.</br>";
                    }
                    else {
                        echo "<em>Average Score:</em> <strong>" . mysql_fetch_row($avg_review_result)[0] . "</strong>/5 (5.0 is best) by " . mysql_fetch_row($num_review_result)[0] . " comment(s).</br>";
                        echo "<div style=\"border:1px solid;width:800px;height:25%;overflow:auto;overflow-y:scroll;overflow-x:hidden;text-align:left;padding-left:2em;\" ><p>";
                        while($r_row = mysql_fetch_row($review_result)) {
                            # Date of review
                            echo "<p><font color=\"#993333\">" . date("F d, Y h:i:sa", strtotime($r_row[1]));
                            # Reviewer name
                            echo ", <font color=\"#003366\">" . $r_row[0];
                            # Reviewer rating
                            echo "</font> (Rating: <font color=\"#003366\">" . $r_row[2] . "</font>/5)</font></br>";
                            # Reviewer comment
                            echo "" . $r_row[3] . "</br></p>";
                        }
                        echo "</p></div>";
                    }
                }
                echo "</br><hr>";
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

                echo "<h2>Movies titles starting with the letter '" . $_GET["title"] . "'</h2>";
                $result = mysql_query($query, $db_connection);
                
                if ($result && mysql_num_rows($result)>0){
                    echo "<h3>Results: </h3><p>";
                    
                    echo "<div style=\"border:1px solid;width:800px;height:60%;overflow:auto;overflow-y:scroll;overflow-x:hidden;text-align:left;padding-left:2em;\" ><p>";

                    while($m_row = mysql_fetch_row($result)){
                        echo "<a href = './showMovie.php?mid=$m_row[1]'>";
                        echo "" . $m_row[0];
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