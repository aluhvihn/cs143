------------------------------
-- PRIMARY KEY constraints: --
------------------------------

-- 1    Movie:      id is PRIMARY KEY
INSERT INTO Movie VALUES (4734, 'mTitle', 2015, 'PG', 'mCompany');
        -- ERROR 1062 (23000): Duplicate entry '4734' for key 'PRIMARY'

-- 2    Actor:      id is PRIMARY KEY
INSERT INTO Actor VALUES (68618, 'aFirst', 'aLast', 'Male', '2015-10-20', '2015-10-20');
        -- ERROR 1062 (23000): Duplicate entry '68618' for key 'PRIMARY'

-- 3    Director:   id is PRIMARY KEY
INSERT INTO Director VALUES (68626, 'dFirst', 'dLast', '2015-10-20', '2015-10-20');
        -- ERROR 1062 (23000): Duplicate entry '68626' for key 'PRIMARY'

-- These PRIMARY KEY violations occur because id's are duplicates of existing people's id's

--------------------------------
-- REFERENCE KEY constraints: --
--------------------------------

--1 MovieGenre : mid refers to Movie id

--2 MovieDirector : mid refers to Movie id

--3                 did refers to Director id

--4 MovieActor : mid refers to Movie id

--5              aid refers to Actor id

--6 Review : mid revers to Movie id

--1
DELETE FROM Movie WHERE id >= 120 OR id <= 150;
--ERROR 1451 (23000) at line 26: Cannot delete or update a parent row: a foreign key constraint fails (`CS143/MovieGenre`, CONSTRAINT `MovieGenre_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))
--2
UPDATE MovieDirector SET mid = mid + 1;
--ERROR 1452 (23000) at line 29: Cannot add or update a child row: a foreign key constraint fails (`CS143/MovieDirector`, CONSTRAINT `MovieDirector_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))
--3
UPDATE MovieDirector SET did = did + 1;
--ERROR 1452 (23000) at line 32: Cannot add or update a child row: a foreign key constraint fails (`CS143/MovieDirector`, CONSTRAINT `MovieDirector_ibfk_2` FOREIGN KEY (`did`) REFERENCES `Director` (`id`))
--4
UPDATE MovieActor SET mid = mid + 1;
--ERROR 1452 (23000) at line 35: Cannot add or update a child row: a foreign key constraint fails (`CS143/MovieActor`, CONSTRAINT `MovieActor_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))
--5
UPDATE MovieActor SET aid = aid + 1;
--ERROR 1452 (23000) at line 38: Cannot add or update a child row: a foreign key constraint fails (`CS143/MovieActor`, CONSTRAINT `MovieActor_ibfk_2` FOREIGN KEY (`aid`) REFERENCES `Actor` (`id`))
--6
UPDATE Review SET mid = mid + 1;
--ERROR 1451 (23000) at line 42: Cannot delete or update a parent row: a foreign key constraint fails (`CS143/MovieGenre`, CONSTRAINT `MovieGenre_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))

-- All of the UPDATES update their corresponding mid/aids, but they dont update Actor id or Movie id.
-- All of the DELETES would delete the movie id being referenced, so that would be an error.

--------------------------------
-- CHECK constraints: --
--------------------------------

-- 1    Movie :     CHECK (id > 0 AND id <= MaxMovieID.id)
-- INSERT INTO Movie VALUES (1000000, 'mTitle', 2015, 'PG', 'mCompany');

-- 2    Actor :     CHECK (id > 0 AND id <= MaxPersonID.id)
-- INSERT INTO Actor VALUES (-1, 'aFirst', 'aLast', 'Male', '2015-10-20', '2015-10-20');

-- 3    Director :  CHECK (id > 0 AND id <= MaxPersonID.id)
-- INSERT INTO Director VALUES (0, 'dFirst', 'dLast', '2015-10-20', '2015-10-20');

-- 4    Review :    CHECK (rating >= 0 & <= 5)
-- INSERT INTO Review VALUES ('rName', CURRENT_TIMESTAMP, 4733, 100, 'rComment');

-- These are violations because the values they insert are out of bounds for the CHECK range
-- (id's for Movie, Actor, and Director; review for Review)