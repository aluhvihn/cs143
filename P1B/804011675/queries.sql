###############################################
## TODO: FIX FIRST QUERY, CREATE THIRD QUERY ##
###############################################

# Give me the names of all the actors in the movie 'Die Another Day'. Please also make sure actor names are in this format:  <firstname> <lastname>   (seperated by single space).
#SELECT CONCAT_WS( ' ', first, last)     -- Select the first and last names of actors
SELECT first, last     -- Select the first and last names of actors
FROM Actor                              -- From the table "Actor"
WHERE id IN (                           -- Where their actor ids appear in
    SELECT aid                          -- Select actor ids of actors
    FROM MovieActor                     -- From the table "Movie Actor"
    WHERE mid = (                       -- Where the movie id is
        SELECT id                       -- Select the id of the movie
        FROM Movie                      -- From the table "Movie"
        WHERE title = 'Die Another Day'));      -- With the title "Die Another Day"

SELECT title, id
FROM Movie
WHERE title = 'Die Another Day';
-- Select the first and last names of the actors from table "Actor"
-- who appear in movies from table "MovieActor" with the movie id matching
-- movies from table "Movie" with the title "Die Another Day"

# Give me the count of all the actors who acted in multiple movies.
SELECT COUNT(*)
FROM (
    SELECT COUNT(mid)
    FROM MovieActor
    GROUP BY aid
    HAVING COUNT(mid) > 1) A;

# Additional Query