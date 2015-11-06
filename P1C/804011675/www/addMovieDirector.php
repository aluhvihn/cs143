<html>
    <head>
        <title>Add Director/Movie Relation</title>
    </head>

    <body style="background-color:#AAE3FF;font-family:Arial;">
        <h1>Add Director/Movie Relation</h1>

        <form action="./addMovieDirector.php" method="GET">    
            <?php
                //establish database connection
                $db_connection = mysql_connect("localhost", "cs143", "");

                //basic error handling
                if( !$db_connection )
                {
                    $errmsg = mysql_error( $db_connection );
                    echo "Connection failed: $errmsg <br />";
                    exit(1);
                }
                
                //select database
                mysql_select_db( "CS143", $db_connection );

                //select ALL movie IDs, titles, years --> dropdown option
                $allMovies = mysql_query( "SELECT id, title, year FROM Movie ORDER BY title", $db_connection ) or die( mysql_error() );
                
                //first (default) option is blank
                $selectedMovie.="<option value=\"\"></option>";
                
                //fetch the array from query
                while( $r = mysql_fetch_array( $allMovies ) )
                {
                    $id = $r["id"];
                    $title = $r["title"];
                    $year = $r["year"];
                    
                    $selectedMovie.="<option value=\"$id\">".$title." (".$year.")</option>";
                }

                //select ALL actor IDs and names (first, last), dob --> dropdown option
                $allDirectors = mysql_query( "SELECT id, last, first, dob FROM Director ORDER BY first", $db_connection ) or die( mysql_error() );

                //first (default) option is blank
                $selectedDirector.="<option value=\"\"></option>";

                //fetch the array from query
                while( $r = mysql_fetch_array( $allDirectors ) )
                {
                    $id = $r["id"];
                    $last = $r["last"];
                    $first = $r["first"];
                    $dob = $r["dob"];
                    
                    $selectedDirector.="<option value=\"$id\">".$first." ".$last." (".$dob.")</option>";
                }
            ?>      
            <table>
                <tr>
                    <td>Movie Title: </td>
                    <td><select name="mid"><?=$selectedMovie?></select>
                    </td>
                </tr>
                <tr>
                    <td>Director Name: </td>
                    <td><select name="aid"><?=$selectedDirector?></select>
                    </td>
                </tr>
            </table>
            <input type="submit" value="Add Relation"/>
        </form>
        
        <hr/>

    <?php
        //get different types of input
        $movie = $_GET["mid"];
        $director = $_GET["aid"];
        
        //pass in user inputs
        if( $movie=="" && $director=="" )   // ALL FIELDS EMPTY
        {
            //do nothing; page loaded or no insert attempt made
        }
        else if( $movie=="" )
        {
            echo "You must select a movie from the list.";
        }
        else if( $director=="" )
        {
            echo "You must select a director from the list.";
        }
        else    //all input validated; process the query
        {           
            $query = "INSERT INTO MovieDirector VALUES ( '$movie', '$director' )";

            //update Movie & basic error handling
            mysql_query( $query, $db_connection ) or die( mysql_error() );
            
            //success message
            echo "Add Success!";
        }
        
        //close the database connection
        mysql_close($db_connection);
    ?>

    </body>
</html>