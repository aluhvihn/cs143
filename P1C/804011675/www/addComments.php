<html>
    <head>
        <title>Add Movie Comments</title>
    </head>

    <body style="background-color:#AAE3FF;font-family:Arial;">
    <h1>Add Movie Comments</h1>
        <form action="./addComments.php" method="GET">  

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

    // first (default) option is blank
    $selectedMovie.="<option value=\"\"></option>";
    
    //fetch the array from query
    while( $r = mysql_fetch_array( $allMovies ) )
    {
      $id = $r["id"];
      $title = $r["title"];
      $year = $r["year"];
      
      $selectedMovie.="<option value=\"$id\">".$title." (".$year.")</option>";
    }
  ?>    
      <table>
        <tr>
          <td>Reviewer Name: </td>
          <td><input type="text" name="name" maxlength="20" value="<?php echo htmlspecialchars($_GET['name']);?>" maxlength="20">
          </td>
        </tr>
        <tr>
          <td>Movie Title: </td>
          <td><select name="id"><?=$selectedMovie?>
            </select>
          </td>
        </tr>
        <tr>
          <td>Rating: </td>
          <td><select name="rating">
              <option value="" selected></option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
            </select> Out of 5
          </td>
        </tr>
        <tr>
          <td>Comment: </td>
          <td><textarea name="comment" maxlength="500" value=><?php echo htmlspecialchars($_GET['comment']);?></textarea>
          </td>
        </tr>
      </table>
      <input type="submit" value="Add Comment"/>
    </form>
    <hr/>

  <?php
    //get different types of input
    $name = trim( $_GET["name"] );
    $movie = $_GET["id"];
    $rating = $_GET["rating"];
    $comment = trim( $_GET["comment"] );
    
    //pass in user inputs
    if( $name=="" && $movie=="" && $rating=="" && $comment=="" )  // ALL FIELDS EMPTY
    {
      //do nothing; page loaded or no insert attempt made
    }
    else if( $name=="" )
    {
      echo "You must enter your name.";
    }
    else if( $movie=="" )
    {
      echo "You must select a movie from the list.";
    }
    else if ( $rating=="" )
    {
      echo "You must select a rating.";
    }
    else  //all input validated; process the query
    {
      //escape single quotes
      $nameParsed = mysql_escape_string($name);
      
      if( $comment != "" )
      {
        $commentParsed = mysql_escape_string($comment);
        $query = "INSERT INTO Review VALUES ( '$nameParsed', now(), '$movie', '$rating', '$commentParsed' )";
      }
      else
      {
        $query = "INSERT INTO Review VALUES ( '$nameParsed', now(), '$movie', '$rating', NULL )";
      }

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