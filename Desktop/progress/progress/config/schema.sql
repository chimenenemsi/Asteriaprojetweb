CREATE TABLE progress_goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    metric VARCHAR(100) NOT NULL,
    start_value DECIMAL(10,2) NOT NULL,
    target_value DECIMAL(10,2) NOT NULL,
    unit VARCHAR(30) NOT NULL,
    start_date DATE NOT NULL,
    target_date DATE NOT NULL,
    goal_type ENUM('WEIGHT_LOSS', 'FITNESS', 'CAREER', 'FINANCE', 'LEARNING', 'HEALTH', 'PRODUCTIVITY', 'OTHER') DEFAULT 'OTHER',
    status ENUM('ACTIVE', 'COMPLETED', 'ON_HOLD') DEFAULT 'ACTIVE',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE progress_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    progress_goal_id INT NOT NULL,
    record_date DATE NOT NULL,
    recorded_value DECIMAL(10,2) NOT NULL,
    adherence_score INT DEFAULT NULL,
    mood ENUM('LOW', 'STEADY', 'HIGH') DEFAULT 'STEADY',
    record_type ENUM('CHECKPOINT', 'MILESTONE', 'MEASUREMENT', 'NOTE', 'REPORT') DEFAULT 'CHECKPOINT',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_progress_record_goal
        FOREIGN KEY (progress_goal_id)
        REFERENCES progress_goals(id)
        ON DELETE CASCADE
);
