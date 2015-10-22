------------------------------
-- PRIMARY KEY constraints: --
------------------------------

--1 Movie : id is PRIMARY KEY
INSERT INTO Movie VALUES (4734, 'mTitle', 2015, 'PG', 'mCompany');
        -- ERROR 1062 (23000): Duplicate entry '4734' for key 'PRIMARY'

--2 Actor : id is PRIMARY KEY
INSERT INTO Actor VALUES (68618, 'aFirst', 'aLast', 'Male', '2015-10-20', '2015-10-20');
        -- ERROR 1062 (23000): Duplicate entry '68618' for key 'PRIMARY'

--3 Director : id is PRIMARY KEY
INSERT INTO Director VALUES (68626, 'dFirst', 'dLast', '2015-10-20', '2015-10-20');
        -- ERROR 1062 (23000): Duplicate entry '68626' for key 'PRIMARY'

--These PRIMARY KEY violations occur because id's are duplicates of existing people's ids

--------------------------------
-- REFERENCE KEY constraints: --
--------------------------------

--1 MovieGenre:     FOREIGN KEY (mid) REFERENCES Movie(id)
DROP TABLE Movie;
        -- ERROR 1217 (23000): Cannot delete or update a parent row: a foreign key constraint fails

--2 MovieDirector:  FOREIGN KEY (mid) REFERENCES Movie(id)
UPDATE MovieDirector SET mid = 4731;
        -- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieDirector`, CONSTRAINT `MovieDirector_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))

--3 MovieDirector:  FOREIGN KEY (did) REFERENCES Director(id)
DELETE FROM Movie WHERE id > 1000;
        -- ERROR 1451 (23000): Cannot delete or update a parent row: a foreign key constraint fails (`CS143`.`MovieGenre`, CONSTRAINT `MovieGenre_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))

--4 MovieActor:     FOREIGN KEY (mid) REFERENCES Movie(id)
INSERT INTO MovieActor VALUES (4731, 68618, 'aRole');
        -- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieActor`, CONSTRAINT `MovieActor_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))

--5 MovieActor:     FOREIGN KEY (mid) REFERENCES Actor(id)
UPDATE MovieActor SET aid = aid + 1;
        -- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieActor`, CONSTRAINT `MovieActor_ibfk_2` FOREIGN KEY (`aid`) REFERENCES `Actor` (`id`))

--6 Review:         FOREIGN KEY (mid) REFERENCES Movie(id)
INSERT INTO Review VALUES ('rName', CURRENT_TIMESTAMP, 4731, 5, 'rComment');
        -- ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`Review`, CONSTRAINT `Review_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))

-- #1 is a violation because we can't drop the table Movie when MovieGenre references the movie id
-- #2 is a violation because there is no movie with the id 4731
-- #3 is a violation because we cant delete rows with values referenced by MovieDirector
-- #4 is a violation because there is no movie with the id 4731
-- #5 is a violation because we can't update all of these actor id's without updating their ids in the Actor table
-- #6 is a violation because there is no movie with the id 4731

------------------------
-- CHECK constraints: --
------------------------

--1 Movie : CHECK (id > 0 AND id <= MaxMovieID.id)
-- INSERT INTO Movie VALUES (1000000, 'mTitle', 2015, 'PG', 'mCompany');

--2 Actor : CHECK (id > 0 AND id <= MaxPersonID.id)
-- INSERT INTO Actor VALUES (-1, 'aFirst', 'aLast', 'Male', '2015-10-20', '2015-10-20');

--3 Director : CHECK (id > 0 AND id <= MaxPersonID.id)
-- INSERT INTO Director VALUES (0, 'dFirst', 'dLast', '2015-10-20', '2015-10-20');

--4 Review : CHECK (rating >= 0 & <= 5)
-- INSERT INTO Review VALUES ('rName', CURRENT_TIMESTAMP, 4733, 100, 'rComment');

--These are violations because the values they insert are out of bounds for the CHECK range
--(id's for Movie, Actor, and Director; review for Review)
