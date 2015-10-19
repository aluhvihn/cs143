SELECT CONCAT_WS( ' ', first, last)     -- Select the first and last names of actors
FROM Actor                              -- From the table "Actor"
WHERE id IN (                           -- Where their actor ids appear in
    SELECT aid                          -- Select actor ids of actors
    FROM MovieActor                     -- From the table "Movie Actor"
    WHERE mid = (                       -- Where the movie id is
        SELECT id                       -- Select the id of the movie
        FROM Movie                      -- From the table "Movie"
        WHERE title = 'Die Another Day'));      -- With the title "Die Another Day"

-- Select the first and last names of the actors from table "Actor"
-- who appear in movies from table "MovieActor" with the movie id matching
-- movies from table "Movie" with the title "Die Another Day"

SELECT aid, COUNT(*)
FROM MovieActor
GROUP BY aid
HAVING COUNT(*) > 1