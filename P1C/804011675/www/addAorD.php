<html>
    <head>
        <title>Add Actor/ Director</title>
    </head>

    <body bgcolor="#AAE3FF">
        <h1>Add Actor/ Director</h1>
        <br/>
        <form action="./addActorDirector.php" method="GET">
			Identity:	<input type="radio" name="identity" value="Actor" 
						<?php echo (htmlspecialchars($_GET['identity'])=='Actor')?'checked':''?> checked>Actor
						<input type="radio" name="identity" value="Director" 
						<?php echo (htmlspecialchars($_GET['identity'])=='Director')?'checked':''?>>Director
			<br/>
			First Name:	<input type="text" name="fname" maxlength="20" value="<?php echo htmlspecialchars($_GET['fname']);?>">
			<br/>
			Last Name:	<input type="text" name="lname" maxlength="20" value="<?php echo htmlspecialchars($_GET['lname']);?>">
			<br/>
			Sex:	<input type="radio" name="sex" value="Male" 
					<?php echo (htmlspecialchars($_GET['sex'])=='Male')?'checked':''?> checked>Male
					<input type="radio" name="sex" value="Female" 
					<?php echo (htmlspecialchars($_GET['sex'])=='Female')?'checked':''?>>Female
			<br/>
			Date of Birth:	<input type="text" name="dob" maxlength="10" value="<?php echo htmlspecialchars($_GET['dob']);?>"> (yyyy-mm-dd)
			<br/>
			Date of Death:	<input type="text" name="dod" maxlength="10" value="<?php echo htmlspecialchars($_GET['dod']);?>"> (Leave BLANK, if not applicable)<br/>
			<br/>
			<input type="submit" value="Add Person"/>
		</form>
		<hr/>

		<?php
			//establish connection with mySQL database
			$db_connection = mysql_connect( "localhost", "cs143", "" );

			//basic error handling
			if( !$db_connection )
			{
	            $errmsg = mysql_error( $db_connection );
	            echo "Connection failed: $errmsg <br />";
	            exit(1);
	        }
	
			//select database
			mysql_select_db( "CS143", $db_connection );

			//get different types of input
			$identity = trim( $_GET["identity"] );
			$fname = trim( $_GET["fname"] );
			$lname = trim( $_GET["lname"] );
			$sex = trim( $_GET["sex"] );
			$dob = trim( $_GET["dob"] );
			$dod = trim( $_GET["dod"] );
			$dobParsed = date_parse( $_GET["dob"] );
			$dodParsed = date_parse( $_GET["dod"] );

			/*
			$dbType=trim($_GET["type"]);
			$dbFirst=trim($_GET["first"]);
			$dbLast=trim($_GET["last"]);
			$dbSex=trim($_GET["sex"]);
			$dbDOB=trim($_GET["dob"]);
			$dbDOD=trim($_GET["dod"]);
			
			$dateDOB = date_parse($dbDOB);
			$dateDOD = date_parse($dbDOD);
			*/

			//query current max person ID & basic error handling
			$curMaxID = mysql_query( "SELECT id FROM MaxPersonID", $db_connection ) or die( mysql_error() );

			//calculate ID of person to be added
	        $newMaxID = $curMaxID + 1;


	        /*
			//determine current maximum person ID and calculate the next
			$maxIDrs = mysql_query("SELECT MAX(id) FROM MaxPersonID", $db_connection) or die(mysql_error());
			$maxIDArray = mysql_fetch_array($maxIDrs);

			$maxID = $maxIDArray[0];
			$newMaxID = $maxID + 1;
			*/
			
			//input validation before adding to database
			if( $identity=="" )
				echo "Must select an identity of person (Actor or Director) to add.";
			else if( $fname=="" )
				echo "Must enter a valid first name.";
			else if( $lname=="" )
				echo "Must enter a valid last name.";
			else if( preg_match('/[^A-Za-z\s\'-]/', $fname) || preg_match('/[^A-Za-z\s\'-]/', $lname) )
				echo "Invalid name: only letters, spaces, single quotes, and hyphens are allowed.";
			else if( $identity=='Actor' && $sex=="" )
				echo "Must select the Actor's sex.";
			else if( $dob=="" || !checkdate($dobParsed["month", $dobParsed["day"], $dobParsed["year"]) )
				echo "Must enter a valid Date of Birth.";
			else if( $dod=="" && !checkdate($dodParsed["month", $dodParsed["day"], $dodParsed["year"]) )
				echo "Must enter a valid Date of Death, if applicable.";
			else if( $identity=="" && $fname=="" && $lname=="" && $sex=="" && $dob=="" && $dod=="" )
				//do nothing
			else 	//all input validated; process the query
			{
				/*
				//escape single-quotes in the inputs to make sure it doesn't break the string up
				$dbLast = mysql_escape_string($dbLast);
				$dbFirst = mysql_escape_string($dbFirst);
				*/

				if( $identity=="Actor")
				{
					if( $dod != "")	//Actor who passed away
						$query = "INSERT INTO Actor VALUES ('$newMaxID', '$lname', '$fname', '$sex', '$dob', '$dod')";
					else 	//Actor who is alive
						$query = "INSERT INTO Actor VALUES ('$newMaxID', '$lname', '$fname', '$sex', '$dob', NULL)";
				}
				else 	//Director
				{
					if( $dod != "")	//Director who passed away
						$query = "INSERT INTO Director VALUES ('$newMaxID', '$lname', '$fname', '$dob', '$dod')";
					else 	//Director who is alive
						$query = "INSERT INTO Director VALUES ('$newMaxID', '$lname', '$fname', '$dob', NULL)";
				}

				//update Actor or Director & basic error handling
				mysql_query( $query, $db_connection ) or die( mysql_error() );

				//update max person ID & basic error handling
				mysql_query( "UPDATE MaxPersonID SET id=$newMaxID WHERE id=$curMaxID", $db_connection ) or die( mysql_error() );

				//success message
				echo "Add Sucess!"
			}

			mysql_close( $db_connection );
		?>
    </body>
</html>