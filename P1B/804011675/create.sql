CREATE TABLE Movie(
    id INT,                 # Movie ID
    title VARCHAR(100),     # Movie title
    year INT,               # Release year
    rating VARCHAR(10),     # MPAA rating
    company VARCHAR(50)     # Production company
);

CREATE TABLE Actor(
    id INT,                 # Actor ID
    last VARCHAR(20),       # Last name
    first VARCHAR(20),      # First name
    sex VARCHAR(6),         # Sex of the actor
    dob DATE,               # Date of birth
    dod DATE                # Date of death
);

CREATE TABLE Director(
    id INT,                 # Director ID
    last VARCHAR(20),       # Last name
    first VARCHAR(20),      # First name
    dob DATE,               # Date of birth
    dod DATE                # Date of death
);

CREATE TABLE MovieGenre(
    mid INT,                # Movie ID
    genre VARCHAR(20)       # Movie genre
);

CREATE TABLE MovieDirector(
    mid INT,                # Movie ID
    did INT                 # Director ID
);

CREATE TABLE MovieActor(
    mid INT,                # Movie ID
    aid INT,                # Actor ID
    role VARCHAR(50)        # Actor role in movie
);

CREATE TABLE Review(
    name VARCHAR(20),       # Reviewer name
    time TIMESTAMP,         # Review time
    mid INT,                # Movie ID
    rating INT,             # Review rating
    comment VARCHAR(500)    # Reviewer comment
);

CREATE TABLE MaxPersonID(
    id INT                  # Max ID assigned to all persons
);

CREATE TABLE MaxMovieID(
    id INT                  # Max ID assigned to all movies
);