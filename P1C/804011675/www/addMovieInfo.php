<html>
    <head>
        <title>Add Movie</title>
    </head>

  	<body style="background-color:#AAE3FF;font-family:Arial;">
		<h1>Add Movie</h1>
        <form action="./addMovieInfo.php" method="GET">
        	<table>
        		<tr>
        			<td width="150px">Movie Title: </td>
        			<td><input type="text" name="title" maxlength="100" value="<?php echo htmlspecialchars($_GET['title']);?>"></td>
				</tr>
				<tr>
					<td>Release Year: </td>
					<td><input type="text" name="year" maxlength="4" value="<?php echo htmlspecialchars($_GET['year']);?>"></td>
				</tr>
				<tr>
					<td>MPAA Rating: </td>
					<td><select name="rating">
							<option value="" selected></option>
							<option value="G" <?php echo (htmlspecialchars($_GET['rating'])=='G')?'selected':''?>>G</option>
							<option value="NC-17" <?php echo (htmlspecialchars($_GET['rating'])=='NC-17')?'selected':''?>>NC-17</option>
							<option value="PG" <?php echo (htmlspecialchars($_GET['rating'])=='PG')?'selected':''?>>PG</option>
							<option value="PG-13" <?php echo (htmlspecialchars($_GET['rating'])=='PG-13')?'selected':''?>>PG-13</option>
							<option value="R" <?php echo (htmlspecialchars($_GET['rating'])=='R')?'selected':''?>>R</option>
							<option value="surrendere" <?php echo (htmlspecialchars($_GET['rating'])=='surrendere')?'selected':''?>>surrendere</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Production Company:	</td>
					<td><input type="text" name="company" maxlength="50" value="<?php echo htmlspecialchars($_GET['company']);?>"></td>
				</tr>
				<tr>
					<td>Genre: </td>
					<td><input type="checkbox" name="genre[]" value="Action">Action</input>
						<input type="checkbox" name="genre[]" value="Adult">Adult</input>
						<input type="checkbox" name="genre[]" value="Adventure">Adventure</input>
						<input type="checkbox" name="genre[]" value="Animation">Animation</input>
						<input type="checkbox" name="genre[]" value="Comedy">Comedy</input>
						<input type="checkbox" name="genre[]" value="Crime">Crime</input>
						<input type="checkbox" name="genre[]" value="Documentary">Documentary</input>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input type="checkbox" name="genre[]" value="Drama">Drama</input>
						<input type="checkbox" name="genre[]" value="Family">Family</input>
						<input type="checkbox" name="genre[]" value="Fantasy">Fantasy</input>
						<input type="checkbox" name="genre[]" value="Horror">Horror</input>
						<input type="checkbox" name="genre[]" value="Musical">Musical</input>
						<input type="checkbox" name="genre[]" value="Mystery">Mystery</input>
						<input type="checkbox" name="genre[]" value="Romance">Romance</input>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><input type="checkbox" name="genre[]" value="Sci-Fi">Sci-Fi</input>
						<input type="checkbox" name="genre[]" value="Short">Short</input>
						<input type="checkbox" name="genre[]" value="Thriller">Thriller</input>
						<input type="checkbox" name="genre[]" value="War">War</input>
						<input type="checkbox" name="genre[]" value="Western">Western</input>
					</td>
				</tr>
			</table>
			<input type="submit" value="Add Movie"/>
		</form>
		<hr/>

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
		mysql_select_db("CS143", $db_connection);

		//get different types of input
		$title = trim( $_GET["title"] );
		$year = trim( $_GET["year"] );
		$rating = $_GET["rating"];
		$company = trim( $_GET["company"] );
		$genre = $_GET["genre"];

		//query current max movie ID & basic error handling
		$maxID = mysql_query( "SELECT id FROM MaxMovieID", $db_connection ) or die( mysql_error() );
		$maxIDArr = mysql_fetch_array( $maxID );
		$curMaxID = $maxIDArr[0];

		//calculate ID of movie to be added
        $newMaxID = $curMaxID + 1;

        //pass in user inputs
		if( $title=="" && $year=="" && $rating=="" && $company=="" && $genre=="" )	// ALL FIELDS EMPTY
		{
			//do nothing; page loaded or no insert attempt made
		}
		else if( $title=="" )
		{
			echo "Must enter a movie title.";
		}
		else if( $year=="" || !is_numeric($year) )
		{
			echo "Must enter a valid release year.";
		}
		else if( $company=="" )
		{
			echo "Must enter a production company.";
		}
		else 	//all input validated; process the query
		{
			//escape single quotes
			$titleParsed = mysql_escape_string($title);
			$companyParsed = mysql_escape_string($company);

			if( $rating != "" )
			{
				$query = "INSERT INTO Movie VALUES ( '$newMaxID', '$titleParsed', '$year', '$rating', '$companyParsed' )";
			}
			else
			{
				$query = "INSERT INTO Movie VALUES ( '$newMaxID', '$titleParsed', '$year', NULL, '$companyParsed' )";
			}

			//update Movie & basic error handling
			mysql_query( $query, $db_connection ) or die( mysql_error() );

			//update MovieGenre, if applicable & basic error handling
			for( $i=0; $i < count($genre); $i++ )
			{
				$gQuery = "INSERT INTO MovieGenre VALUES ( '$newMaxID', '$genre[$i]' )";
				mysql_query( $gQuery, $db_connection ) or die( mysql_error() );
			}

			//update max movie ID & basic error handling
			mysql_query( "UPDATE MaxMovieID SET id=$newMaxID WHERE id=$curMaxID", $db_connection ) or die( mysql_error() );

			//success message
			echo "Add Success!";
		}
		
		//close database connection
		mysql_close($db_connection);
	?>

	</body>
</html>