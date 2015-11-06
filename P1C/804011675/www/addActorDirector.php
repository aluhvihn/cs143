<html>
    <head>
        <title>Add Actor/Director</title>
    </head>

  	<body style="background-color:#AAE3FF;font-family:Arial;">
		<h1>Add Actor/Director</h1>
        <form action="./addActorDirector.php" method="GET">
        	<table>
        		<tr>
					<td width="90px">Identity: </td>
					<td><input type="radio" name="identity" value="Actor" 
						<?php echo (htmlspecialchars($_GET['identity'])=='Actor')?'checked':''?>>Actor
						<input type="radio" name="identity" value="Director" 
						<?php echo (htmlspecialchars($_GET['identity'])=='Director')?'checked':''?>>Director
					</td>
				</tr>
				<tr>
					<td>First Name:	</td>
					<td><input type="text" name="fname" maxlength="20" value="<?php echo htmlspecialchars($_GET['fname']);?>"></td>
				</tr>
				<tr>
					<td>Last Name:	</td>
					<td><input type="text" name="lname" maxlength="20" value="<?php echo htmlspecialchars($_GET['lname']);?>"></td>
				</tr>
				<tr>
					<td>Sex: </td>
					<td><input type="radio" name="sex" value="Male" 
						<?php echo (htmlspecialchars($_GET['sex'])=='Male')?'checked':''?>>Male
						<input type="radio" name="sex" value="Female" 
						<?php echo (htmlspecialchars($_GET['sex'])=='Female')?'checked':''?>>Female
					</td>
				</tr>
				<tr>
					<td>Date of Birth: </td>
					<td><input type="text" name="dob" maxlength="10" value="<?php echo htmlspecialchars($_GET['dob']);?>"> (yyyy-mm-dd)</td>
				</tr>
				<tr>
					<td>Date of Death: </td>
					<td><input type="text" name="dod" maxlength="10" value="<?php echo htmlspecialchars($_GET['dod']);?>"> (Leave BLANK, if not applicable)</td>
				</tr>
			</table>
			<input type="submit" value="Add Person"/>
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
		$identity = trim( $_GET["identity"] );
		$fname = trim( $_GET["fname"] );
		$lname = trim( $_GET["lname"] );
		$sex = trim( $_GET["sex"] );
		$dob = trim( $_GET["dob"] );
		$dod = trim( $_GET["dod"] );
		$dobParsed = date_parse( $_GET["dob"] );
		$dodParsed = date_parse( $_GET["dod"] );
	
		//query current max person ID & basic error handling
		$maxID = mysql_query( "SELECT id FROM MaxPersonID", $db_connection ) or die( mysql_error() );
		$maxIDArr = mysql_fetch_array( $maxID );
		$curMaxID = $maxIDArr[0];

		//calculate ID of person to be added
        $newMaxID = $curMaxID + 1;
		
		//pass in user inputs
	  if( $identity=="" && $fname=="" && $lname=="" && $sex=="" && $dob=="" && $dod=="" )	// ALL FIELDS EMPTY
		{
			//do nothing; page loaded or no insert attempt made
		}
		else if( $identity=="" )
		{
			echo "<strong>Must select an identity of person (Actor or Director) to add.</strong>";
		}
		else if( $fname=="" )
		{
			echo "<strong>Must enter a first name.</strong>";
		}
		else if( $lname=="" )
		{
			echo "<strong>Must enter a last name.</strong>";
		}
		else if(preg_match('/[^A-Za-z\s\'-]/', $fname) || preg_match('/[^A-Za-z\s\'-]/', $lname))
		{
			echo "<strong>Invalid name format: only letters, spaces, single quotes, and hyphens are allowed.</strong>";
		}
		else if( $identity=='Actor' && $sex=="" )
		{
			echo "<strong>Must select the Actor's sex.</strong>";
		}
		else if( $dob=="" || !checkdate($dobParsed["month"], $dobParsed["day"], $dobParsed["year"]) )
		{
			echo "<strong>Must enter a valid Date of Birth.</strong>";
		}
		else if( $dod!="" && !checkdate($dodParsed["month"], $dodParsed["day"], $dodParsed["year"]) )
		{
			echo "<strong>Must enter a valid Date of Death, if applicable.</strong>";
		}
		else 	//all input validated; process the query
		{
			//escape single quotes
			$fnameParsed = mysql_escape_string($fname);
			$lnameParsed = mysql_escape_string($lname);

			if( $identity=="Actor")
			{
				if( $dod != "")	//Actor who passed away
					$query = "INSERT INTO Actor VALUES ('$newMaxID', '$lnameParsed', '$fnameParsed', '$sex', '$dob', '$dod')";
				else 	//Actor who is alive
					$query = "INSERT INTO Actor VALUES ('$newMaxID', '$lnameParsed', '$fnameParsed', '$sex', '$dob', NULL)";
			}
			else 	//Director
			{
				if( $dod != "")	//Director who passed away
					$query = "INSERT INTO Director VALUES ('$newMaxID', '$lnameParsed', '$fnameParsed', '$dob', '$dod')";
				else 	//Director who is alive
					$query = "INSERT INTO Director VALUES ('$newMaxID', '$lnameParsed', '$fnameParsed', '$dob', NULL)";
			}
			
			//update Actor or Director & basic error handling
			mysql_query( $query, $db_connection ) or die( mysql_error() );

			//update max person ID & basic error handling
			mysql_query( "UPDATE MaxPersonID SET id=$newMaxID WHERE id=$curMaxID", $db_connection ) or die( mysql_error() );

			//success message
			echo "Add Success!";
		}
	
		//close database connection
		mysql_close($db_connection);
	?>

	</body>
</html>