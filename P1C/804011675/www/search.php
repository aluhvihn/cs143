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

                $actor_query = "SELECT id, first, last, dob FROM Actor WHERE (first LIKE '%" . $keywords[0] . "%' OR last LIKE '%" . $keywords[0] . "%')";
                $movie_query = "SELECT id, title, year FROM Movie WHERE (title LIKE '%" . $keywords[0] . "%')";

                # If there are multiple keywords
                if ($num_keywords > 1) {
                    $i = 0;
                    for ($i=1; $i < $num_keywords; $i++) {
                        $actor_query = $actor_query . " AND (first LIKE '%" . $keywords[$i] . "%' OR last LIKE '%" . $keywords[$i] . "%')";
                        $movie_query = $movie_query . " AND (title LIKE '%" . $keywords[$i] . "%')";
                    }
                }

                # sort results
                $actor_query = $actor_query . " ORDER BY last";
                $movie_query = $movie_query . " ORDER BY title";

                $actor_result = mysql_query( $actor_query );
                $movie_result = mysql_query( $movie_query );

                echo "<h3>Actor Results (by Last Name):</h3>";
                #if no matching row (tuple) from database
                if (mysql_num_rows($actor_result) == 0) {
                    echo "No actors found";
                }
                else {
                    #retrieving results
                    echo "<div style=\"border:1px solid;width:800px;height:30%;overflow:auto;overflow-y:scroll;overflow-x:hidden;text-align:left;padding-left:2em;\" ><p>";
                    while ($row = mysql_fetch_row($actor_result)) {
                        echo "<a href = './showActor.php?aid=$row[0]'>";
                        echo "" . $row[2] . ", " . $row[1];
                        echo "</a><br/>";
                    }
                    echo "</p></div>";
                }

                echo "<h3>Movie Results (by Title):</h3>";
                #if no matching row (tuple) from database
                if (mysql_num_rows($movie_result) == 0) {
                    echo "No movies found<br/>";
                }
                else {
                    #retrieving results
                    echo "<div style=\"border:1px solid;width:800px;height:30%;overflow:auto;overflow-y:scroll;overflow-x:hidden;text-align:left;padding-left:2em;\" ><p>";
                    while ($row = mysql_fetch_row($movie_result)) {
                        echo "<a href = './showMovie.php?mid=$row[0]'>";
                        echo "" . $row[1] . " (" . $row[2] . ")";
                        echo "</a><br/>";
                    }
                    echo "</p></div>";
                }

                #closing connection
                mysql_close($db_connection);
                echo '<hr><br/>
                <form method="GET">
                    <!-- <textarea name="query" rows="8" cols="60"></textarea> -->
                    <input type="text" name="input" size="50" />
                    <input type="submit" value="Search"/>
                </form>';
            }
        ?>

    </body>
</html>