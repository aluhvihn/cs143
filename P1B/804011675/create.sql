CREATE TABLE Movie(
    id INT NOT NULL,                -- Movie ID
    title VARCHAR(100) NOT NULL,    -- Movie title
    year INT NOT NULL,              -- Release year
    rating VARCHAR(10),             -- MPAA rating
    company VARCHAR(50) NOT NULL,   -- Production company
    
    PRIMARY KEY (id),                           -- Every movie has a unique id
    CHECK (id > 0 AND id <= MaxMovieID.id)     -- Make sure movie ID is valid
);

CREATE TABLE Actor(
    id INT NOT NULL,                -- Actor ID
    last VARCHAR(20) NOT NULL,      -- Last name
    first VARCHAR(20) NOT NULL,     -- First name
    sex VARCHAR(6) NOT NULL,        -- Sex of the actor
    dob DATE NOT NULL,              -- Date of birth
    dod DATE,                       -- Date of death
    
    PRIMARY KEY (id),                           -- Every actor has a unique id
    CHECK (id > 0 AND id <= MaxPersonID.id)     -- Make sure actor ID is valid
);

CREATE TABLE Director(
    id INT NOT NULL,                -- Director ID
    last VARCHAR(20) NOT NULL,      -- Last name
    first VARCHAR(20) NOT NULL,     -- First name
    dob DATE NOT NULL,              -- Date of birth
    dod DATE,                       -- Date of death
    
    PRIMARY KEY (id),                           -- Every director has a unique id
    CHECK (id > 0 AND id <= MaxPersonID.id)     -- Make sure director ID is valid
);

CREATE TABLE MovieGenre(
    mid INT,                        -- Movie ID
    genre VARCHAR(20),              -- Movie genre
    
    FOREIGN KEY (mid) REFERENCES Movie(id)      -- Every movie id must exist in the Movie table
) ENGINE=INNODB;

CREATE TABLE MovieDirector(
    mid INT,                        -- Movie ID
    did INT,                        -- Director ID
    
    FOREIGN KEY (mid) REFERENCES Movie(id),     -- Every movie id must exist in the Movie table
    FOREIGN KEY (did) REFERENCES Director(id)   -- Every director id must exist in the Director table
) ENGINE=INNODB;

CREATE TABLE MovieActor(
    mid INT,                        -- Movie ID
    aid INT,                        -- Actor ID
    role VARCHAR(50),               -- Actor role in movie
    
    FOREIGN KEY (mid) REFERENCES Movie(id),     -- Every movie id must exist in the Movie table
    FOREIGN KEY (aid) REFERENCES Actor(id)      -- Every actor id must exist in the Actor table
) ENGINE=INNODB;

CREATE TABLE Review(
    name VARCHAR(20) NOT NULL,      -- Reviewer name
    time TIMESTAMP NOT NULL,        -- Review time
    mid INT NOT NULL,               -- Movie ID
    rating INT NOT NULL,            -- Review rating
    comment VARCHAR(500),           -- Reviewer comment

    FOREIGN KEY (mid) REFERENCES Movie(id),     -- Every movie id must exist in the Movie table
    CHECK (rating >=0 AND rating <= 5)          -- Make sure rating entered is valid
) ENGINE=INNODB;

CREATE TABLE MaxPersonID(
    id INT                          -- Max ID assigned to all persons
);

CREATE TABLE MaxMovieID(
    id INT                          -- Max ID assigned to all movies
);