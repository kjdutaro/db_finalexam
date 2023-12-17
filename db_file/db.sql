CREATE DATABASE IF NOT EXISTS DocumentTracking;

USE DocumentTracking;

CREATE TABLE IF NOT EXISTS Document (
    document_id INT PRIMARY KEY AUTO_INCREMENT,
    file_path VARCHAR(255) NOT NULL,
    DateCreated DATETIME,
    status VARCHAR(1000),
    isAccomplished BOOLEAN DEFAULT FALSE
);

CREATE TABLE IF NOT EXISTS TrackDetails (
    track_id INT PRIMARY KEY AUTO_INCREMENT,
    document_id INT NOT NULL,
    origin_office VARCHAR(255) NOT NULL,
    FOREIGN KEY (document_id) REFERENCES Document(document_id)
);

CREATE TABLE IF NOT EXISTS Personnel (
   personnel_id INT PRIMARY KEY AUTO_INCREMENT, 
   name VARCHAR(255) NOT NULL, 
   password VARCHAR(255) NOT NULL, 
   email VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS Sender (
   sender_id INT PRIMARY KEY AUTO_INCREMENT, 
   track_id INT NOT NULL,
   personnel_id INT NOT NULL,
   FOREIGN KEY (track_id) REFERENCES TrackDetails(track_id), 
   FOREIGN KEY (personnel_id) REFERENCES Personnel(personnel_id)
);

CREATE TABLE IF NOT EXISTS Recipient (
  recipient_id INT PRIMARY KEY AUTO_INCREMENT, 
   track_id INT NOT NULL,
   personnel_id INT NOT NULL,
   FOREIGN KEY (track_id) REFERENCES TrackDetails(track_id), 
   FOREIGN KEY (personnel_id) REFERENCES Personnel(personnel_id)
);

INSERT INTO Personnel (name, password, email)
VALUES ('tester', 'test', 'test@mail.com'),
       ('Juan Dela Cruz', 'password123', 'juan@mail.com'),
       ('Maria Santos', 'password456', 'maria@mail.com'),
       ('Pedro Reyes', 'password789', 'pedro@mail.com'),
       ('Luisa Rodriguez', 'passwordabc', 'luisa@mail.com'),
       ('Miguel Garcia', 'passworddef', 'miguel@mail.com'),
       ('Sofia Hernandez', 'passwordghi', 'sofia@mail.com'),
       ('Diego Ramos', 'passwordjkl', 'diego@mail.com'),
       ('Isabella Reyes', 'passwordmno', 'isabella@mail.com'),
       ('Mateo Cruz', 'passwordpqr', 'mateo@mail.com'),
       ('Camila Torres', 'passwordstu', 'camila@mail.com');