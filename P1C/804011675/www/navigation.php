<html>
    <head>
        <title>Navigation</title>
    </head>
    
    <!-- <body style="background-color:#FFCCCC"> -->
    <!-- <body style="background-color:#FF9D9D"> -->
    <body style="background-color:#FFFFCC;font-family:Arial;">
        <p style="font-size:20px"><strong>Search:</strong></p>
        <ul style="font-size:15px">
            <li>
                <a href=./search.php target=main>Search for an Actor, Actress, or Movie</a>
            </li>
        </ul>

        <hr>

        <p style="font-size:20px"><strong>Browse:</strong></p>
        <ul style="font-size:15px">
            <li>
                <a href=./showActor.php target=main>Show Actor Information</a>
            </li>
            <li>
                <a href=./showMovie.php target=main>Show Movie Information</a>
            </li>
        </ul>

        <hr>

        <p style="font-size:20px"><strong>Add:</strong></p>
        <ul style="font-size:15px">
            <li>
                <a href=./addActorDirector.php target=main>Add Actor/Director</a>
            </li>
            <li>
                <a href=./addMovieInfo.php target=main>Add Movie Information</a>
            </li>
            <li>
                <a href=./addMovieActor.php target=main>Add an Actor to a Movie</a>
            </li>
            <li>
                <a href=./addMovieDirector.php target=main>Add a Director to a Movie</a>
            </li>
        </ul>

        <hr>

        <form action="./search.php" method="GET" target=main>
            <input type="text" name="input">
            <br><br>
            <input type="submit" value="Search Movie/Actor">
        </form>
    </body>
</html>