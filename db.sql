<<<<<<< HEAD
-- db.sql
-- Create database and tables for club_voting system
DROP DATABASE IF EXISTS club_voting;
CREATE DATABASE club_voting CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE club_voting;

-- clubs
CREATE TABLE clubs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  description TEXT
) ENGINE=InnoDB;

-- users
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  university_email VARCHAR(255) NOT NULL UNIQUE,
  role ENUM('admin','voter') NOT NULL,
  password VARCHAR(255) NOT NULL,
  club_id INT DEFAULT NULL,           -- admins assigned to a club
  executive_role VARCHAR(100) DEFAULT NULL,
  nic_number VARCHAR(50) DEFAULT NULL,
  faculty VARCHAR(150) DEFAULT NULL,
  department VARCHAR(150) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- elections
CREATE TABLE elections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  club_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  start_datetime DATETIME NOT NULL,
  end_datetime DATETIME NOT NULL,
  is_active TINYINT(1) DEFAULT 0,
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
  INDEX (club_id),
  INDEX (start_datetime),
  INDEX (end_datetime)
) ENGINE=InnoDB;

-- candidates
CREATE TABLE candidates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  election_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  bio TEXT,
  photo VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (election_id) REFERENCES elections(id) ON DELETE CASCADE,
  INDEX (election_id)
) ENGINE=InnoDB;

-- votes
CREATE TABLE votes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  election_id INT NOT NULL,
  candidate_id INT NOT NULL,
  user_id INT NOT NULL,
  cast_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (election_id) REFERENCES elections(id) ON DELETE CASCADE,
  FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  -- prevent duplicate voting
  UNIQUE KEY unique_vote_per_user (election_id, user_id),
  INDEX (candidate_id),
  INDEX (user_id)
) ENGINE=InnoDB;

-- sample data
INSERT INTO clubs (name, description) VALUES
('IEEE Student Branch','IEEE club'), 
('Rotaract Club','Rotaract');

-- sample users: admin + 2 voters
-- password: use password_hash('AdminPass123', PASSWORD_DEFAULT) and similar; we will store pre-hashed values
-- Precomputed password hashes (these were generated using PHP password_hash):
-- AdminPass123 -> $2y$10$aYdY7cE3J8l/AEkG.0bK0.O3f9gqvY5rF6Y.W7N0bq2gG3m9vQ2QO
-- VoterPass1 -> $2y$10$Dq4fF8jvZ1k6T7gJ9R6F.uY3kO9Vf1cQ2xZ3H.4N5Yb8QkP1sT1uK
-- VoterPass2 -> $2y$10$hH8jK3mN9pQ7rT5xV6bC.oL2rN5yUj1W3eS6Qv2P9aZ8F4dL0kM2G
INSERT INTO users (name, university_email, role, password, club_id, executive_role, nic_number)
VALUES 
('Admin Example', 'admin@university.edu', 'admin', '$2y$10$aYdY7cE3J8l/AEkG.0bK0.O3f9gqvY5rF6Y.W7N0bq2gG3m9vQ2QO', 1, 'President', '123456789V');

INSERT INTO users (name, university_email, role, password, faculty, department)
VALUES 
('Voter One','voter1@university.edu','voter','$2y$10$Dq4fF8jvZ1k6T7gJ9R6F.uY3kO9Vf1cQ2xZ3H.4N5Yb8QkP1sT1uK','Engineering','Computer Science'),
('Voter Two','voter2@university.edu','voter','$2y$10$hH8jK3mN9pQ7rT5xV6bC.oL2rN5yUj1W3eS6Qv2P9aZ8F4dL0kM2G','Business','Marketing');

-- sample election (upcoming & active demo)
INSERT INTO elections (club_id, title, description, start_datetime, end_datetime, is_active, created_by)
VALUES
(1, 'IEEE President Election 2025', 'Election for IEEE Student President', DATE_ADD(NOW(), INTERVAL -1 HOUR), DATE_ADD(NOW(), INTERVAL +6 HOUR), 1, 1),
(1, 'IEEE Secretary Election 2025', 'Election for IEEE Secretary', DATE_ADD(NOW(), INTERVAL +1 DAY), DATE_ADD(NOW(), INTERVAL +2 DAY), 0, 1);

-- sample candidates for first election
INSERT INTO candidates (election_id, name, bio) VALUES
(1, 'Alice Johnson', 'Third year CS student.'),
(1, 'Bob Perera', 'Second year ECE.');

-- Done
=======
-- db.sql
-- Create database and tables for club_voting system
DROP DATABASE IF EXISTS club_voting;
CREATE DATABASE club_voting CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE club_voting;

-- clubs
CREATE TABLE clubs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  description TEXT
) ENGINE=InnoDB;

-- users
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  university_email VARCHAR(255) NOT NULL UNIQUE,
  role ENUM('admin','voter') NOT NULL,
  password VARCHAR(255) NOT NULL,
  club_id INT DEFAULT NULL,           -- admins assigned to a club
  executive_role VARCHAR(100) DEFAULT NULL,
  nic_number VARCHAR(50) DEFAULT NULL,
  faculty VARCHAR(150) DEFAULT NULL,
  department VARCHAR(150) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- elections
CREATE TABLE elections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  club_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  start_datetime DATETIME NOT NULL,
  end_datetime DATETIME NOT NULL,
  is_active TINYINT(1) DEFAULT 0,
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
  INDEX (club_id),
  INDEX (start_datetime),
  INDEX (end_datetime)
) ENGINE=InnoDB;

-- candidates
CREATE TABLE candidates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  election_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  bio TEXT,
  photo VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (election_id) REFERENCES elections(id) ON DELETE CASCADE,
  INDEX (election_id)
) ENGINE=InnoDB;

-- votes
CREATE TABLE votes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  election_id INT NOT NULL,
  candidate_id INT NOT NULL,
  user_id INT NOT NULL,
  cast_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (election_id) REFERENCES elections(id) ON DELETE CASCADE,
  FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  -- prevent duplicate voting
  UNIQUE KEY unique_vote_per_user (election_id, user_id),
  INDEX (candidate_id),
  INDEX (user_id)
) ENGINE=InnoDB;

-- sample data
INSERT INTO clubs (name, description) VALUES
('IEEE Student Branch','IEEE club'), 
('Rotaract Club','Rotaract');

-- sample users: admin + 2 voters
-- password: use password_hash('AdminPass123', PASSWORD_DEFAULT) and similar; we will store pre-hashed values
-- Precomputed password hashes (these were generated using PHP password_hash):
-- AdminPass123 -> $2y$10$aYdY7cE3J8l/AEkG.0bK0.O3f9gqvY5rF6Y.W7N0bq2gG3m9vQ2QO
-- VoterPass1 -> $2y$10$Dq4fF8jvZ1k6T7gJ9R6F.uY3kO9Vf1cQ2xZ3H.4N5Yb8QkP1sT1uK
-- VoterPass2 -> $2y$10$hH8jK3mN9pQ7rT5xV6bC.oL2rN5yUj1W3eS6Qv2P9aZ8F4dL0kM2G
INSERT INTO users (name, university_email, role, password, club_id, executive_role, nic_number)
VALUES 
('Admin Example', 'admin@university.edu', 'admin', '$2y$10$aYdY7cE3J8l/AEkG.0bK0.O3f9gqvY5rF6Y.W7N0bq2gG3m9vQ2QO', 1, 'President', '123456789V');

INSERT INTO users (name, university_email, role, password, faculty, department)
VALUES 
('Voter One','voter1@university.edu','voter','$2y$10$Dq4fF8jvZ1k6T7gJ9R6F.uY3kO9Vf1cQ2xZ3H.4N5Yb8QkP1sT1uK','Engineering','Computer Science'),
('Voter Two','voter2@university.edu','voter','$2y$10$hH8jK3mN9pQ7rT5xV6bC.oL2rN5yUj1W3eS6Qv2P9aZ8F4dL0kM2G','Business','Marketing');

-- sample election (upcoming & active demo)
INSERT INTO elections (club_id, title, description, start_datetime, end_datetime, is_active, created_by)
VALUES
(1, 'IEEE President Election 2025', 'Election for IEEE Student President', DATE_ADD(NOW(), INTERVAL -1 HOUR), DATE_ADD(NOW(), INTERVAL +6 HOUR), 1, 1),
(1, 'IEEE Secretary Election 2025', 'Election for IEEE Secretary', DATE_ADD(NOW(), INTERVAL +1 DAY), DATE_ADD(NOW(), INTERVAL +2 DAY), 0, 1);

-- sample candidates for first election
INSERT INTO candidates (election_id, name, bio) VALUES
(1, 'Alice Johnson', 'Third year CS student.'),
(1, 'Bob Perera', 'Second year ECE.');

-- Done
>>>>>>> 2ba3267 (Initial commit)
