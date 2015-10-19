<html>
    <head><title>SQL Query</title></head>
    <body>
        <h1>SQL Query</h1>
        <p>Type an SQL query in the following box:</p>
        <p>Example: "<kbd>SELECT * FROM Actor WHERE id=10;</kbd>"</p>
        <form method="GET">
            <textarea name="query" rows="8" cols="60"></textarea>
            <br>
            <input type="submit" value="submit">
        </form>
        <p><small>Note: tables and fields are case sensitive. All tables in Project 1B are availale.</small></p>

        <?php
            if($_GET["query"]){
                $db_connection = mysql_connect("localhost:1438", "cs143", "");
                if (!$db_connection) {
                    die("Couldn't connect: ".mysql_error());
                }
                mysql_select_db("TEST", $db_connection);
                $query = mysql_real_escape_string($_GET["query"]);
                echo "$query";
                $result = msql_query($query, $db_connection);
                while ($row = mysql_fetch_row($result)) {
                    # Based on SELECT
                    $sid = $row[0];
                    $name = $row[1];
                    $email = $row[2];
                    echo "$sid, $name, $email";
                }
                mysql_close($db_connection);
            }
        ?>
    </body>
</html>