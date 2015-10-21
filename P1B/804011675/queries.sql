# Give me the names of all the actors in the movie 'Die Another Day'. Please also make sure actor names are in this format:  <firstname> <lastname>   (seperated by single space)

SELECT CONCAT(first, ' ', last) AS `Actors in "Die Another Day"`
FROM Actor A, MovieActor MA, Movie M
WHERE A.id = MA.aid AND M.id = MA.mid AND M.title = 'Die Another Day';

# Give me the count of all the actors who acted in multiple movies

SELECT COUNT(*)
FROM (
    SELECT COUNT(mid)
    FROM MovieActor
    GROUP BY aid
    HAVING COUNT(mid) > 1) A;

# Additional query
# Directors who has directed more than 5 Movies

SELECT CONCAT(first, ' ', last) AS `Directors with 5+ Movies`
FROM(
        SELECT first, last
        FROM Director D, MovieDirector MD, Movie M
        WHERE D.id = MD.did AND MD.mid = M.id
        GROUP BY did
        HAVING COUNT(mid)>5 ) AS Q3;
