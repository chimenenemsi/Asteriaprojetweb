CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(150) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    secret_code VARCHAR(255) NULL,
    reset_token VARCHAR(255) NULL,
    reset_expires_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS product_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    status ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    sku VARCHAR(80) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock_quantity INT NOT NULL DEFAULT 0,
    status ENUM('ACTIVE', 'DRAFT', 'OUT_OF_STOCK') DEFAULT 'ACTIVE',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category_standalone
        FOREIGN KEY (product_category_id)
        REFERENCES product_categories(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    product_id INT DEFAULT NULL,
    product_name VARCHAR(150) NOT NULL,
    customer_name VARCHAR(150) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    status ENUM('PENDING', 'PAID', 'SHIPPED', 'CANCELLED') DEFAULT 'PENDING',
    order_date DATE NOT NULL,
    shipping_address TEXT,
    delivery_location_name VARCHAR(255),
    delivery_latitude DECIMAL(10,7),
    delivery_longitude DECIMAL(10,7),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_product_standalone
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_orders_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    goal_type VARCHAR(50),
    duration_weeks INT,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS exercises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_id INT,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    muscle_group VARCHAR(50),
    sets INT,
    reps INT,
    rest_seconds INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_exercises_program
        FOREIGN KEY (program_id)
        REFERENCES programs(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS progress_goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_progress_goal_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS progress_records (
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
