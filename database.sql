============================================

CREATE DATABASE IF NOT EXISTS rakt_db;
USE rakt_db;

-- ── DONORS TABLE ──
CREATE TABLE IF NOT EXISTS donors (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    full_name  VARCHAR(100) NOT NULL,
    email      VARCHAR(100) NOT NULL UNIQUE,
    phone      VARCHAR(15)  NOT NULL,
    age        INT          NOT NULL,
    blood_type VARCHAR(5)   NOT NULL,
    city       VARCHAR(50)  NOT NULL,
    available  TINYINT(1)   DEFAULT 1,
    created_at DATETIME     DEFAULT CURRENT_TIMESTAMP
);

-- ── EMERGENCY REQUESTS TABLE ──
CREATE TABLE IF NOT EXISTS emergency_requests (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    blood_type     VARCHAR(5)   NOT NULL,
    units_needed   INT          NOT NULL DEFAULT 1,
    hospital       VARCHAR(100) NOT NULL,
    city           VARCHAR(50)  NOT NULL,
    urgency        ENUM('Critical','Urgent','Moderate') DEFAULT 'Moderate',
    contact_phone  VARCHAR(15)  NOT NULL,
    patient_name   VARCHAR(100) DEFAULT NULL,
    notes          TEXT         DEFAULT NULL,
    created_at     DATETIME     DEFAULT CURRENT_TIMESTAMP
);

-- ── SAMPLE DONORS DATA ──
INSERT INTO donors (full_name, email, phone, age, blood_type, city, available) VALUES
('Aarav Sharma',  'aarav@example.com',  '+91 9876543210', 24, 'O+',  'Mumbai',     1),
('Priya Patel',   'priya@example.com',  '+91 9812345678', 29, 'A+',  'Delhi',      1),
('Rohit Verma',   'rohit@example.com',  '+91 9934567890', 32, 'B+',  'Bangalore',  0),
('Sneha Gupta',   'sneha@example.com',  '+91 9765432100', 27, 'AB+', 'Pune',       1),
('Karan Singh',   'karan@example.com',  '+91 9887654321', 35, 'O-',  'Chennai',    1),
('Meera Nair',    'meera@example.com',  '+91 9923456789', 22, 'A-',  'Hyderabad',  1),
('Vikram Joshi',  'vikram@example.com', '+91 9811223344', 41, 'B-',  'Kolkata',    0),
('Ananya Reddy',  'ananya@example.com', '+91 9799887766', 26, 'AB-', 'Ahmedabad',  1),
('Amit Kumar',    'amit@example.com',   '+91 9843219876', 30, 'O+',  'Jaipur',     1),
('Pooja Agarwal', 'pooja@example.com',  '+91 9867890123', 23, 'O-',  'Nagpur',     1);

-- ── SAMPLE EMERGENCY REQUESTS ──
INSERT INTO emergency_requests (blood_type, units_needed, hospital, city, urgency, contact_phone, patient_name) VALUES
('O-',  2, 'Apollo Hospital',  'Mumbai',     'Critical', '+91 9876543210', 'Ramesh Kumar'),
('AB+', 1, 'AIIMS',           'Delhi',      'Urgent',   '+91 9812345678', 'Sunita Devi'),
('B+',  3, 'Fortis Hospital',  'Bangalore',  'Moderate', '+91 9934567890', 'Ajay Singh'),
('A-',  1, 'Max Hospital',    'Pune',       'Critical', '+91 9416289390', 'Kavita Sharma'),
('A+',  1, 'Max Hospital',    'Kurukshetra','Critical', '+91 9034710495', 'Deepak Yadav'),
('O+',  4, 'Medanta Hospital', 'Gurgaon',   'Urgent',   '+91 9998887776', 'Ritu Mehta');