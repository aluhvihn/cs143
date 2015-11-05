<html>
    <head>
        <title>Navigation</title>
    </head>
    
    <body style="background-color:#FFFFCC;font-family:Arial;">
        <h1>BruinDB</h1>
        <hr align="center" noshade="noshade" size="2" width="100%" color="#AAE3FF">

        <p style="font-size:20px"><strong>Search:</strong></p>
        <ul style="font-size:15px">
            <li>
                <a href=./search.php target=main>Actor/Director/Movie</a>
            </li>
        </ul>

        <hr align="center" noshade="noshade" size="2" width="80%" color="#AAE3FF">

        <p style="font-size:20px"><strong>Browse:</strong></p>
        <ul style="font-size:15px">
            <li>
                <a href=./showActor.php target=main>Actor Information</a>
            </li>
            <li>
                <a href=./showMovie.php target=main>Movie Information</a>
            </li>
        </ul>

        <hr align="center" noshade="noshade" size="2" width="80%" color="#AAE3FF">

        <p style="font-size:20px"><strong>Add:</strong></p>
        <ul style="font-size:15px">
            <li>
                <a href=./addActorDirector.php target=main>Actor/Director</a>
            </li>
            <li>
                <a href=./addMovieInfo.php target=main>Movie Information</a>
            </li>
            <li>
                <a href=./addComments.php target=main>Movie Comments</a>
            </li>
            <li>
                <a href=./addMovieActor.php target=main>Movie/Actor Relation</a>
            </li>
            <li>
                <a href=./addMovieDirector.php target=main>Movie/Director Relation</a>
            </li>
        </ul>

        <hr align="center" noshade="noshade" size="2" width="100%" color="#AAE3FF">

        <form action="./search.php" method="GET" target=main>
            <input type="text" name="input">
            <br><br>
            <input type="submit" value="Search Movie/Actor">
        </form>
    </body>
</html>