<?php
include 'config.php';

try {
    // Create users table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    
    echo "Users table created successfully.<br>";
    
    // Check if email column exists, if not add it
    try {
        $result = $pdo->query("SHOW COLUMNS FROM users LIKE 'email'");
        if ($result->rowCount() == 0) {
            echo "Adding 'email' column to users table...<br>";
            // First add as nullable to avoid constraint issues with existing data
            $pdo->exec("ALTER TABLE users ADD COLUMN email VARCHAR(100) NULL AFTER username");
            
            // Update existing rows with unique email addresses
            $pdo->exec("UPDATE users SET email = CONCAT(username, '@example.com') WHERE email IS NULL OR email = ''");
            
            // Now make it unique and not null
            $pdo->exec("ALTER TABLE users MODIFY email VARCHAR(100) UNIQUE NOT NULL");
            echo "Email column added successfully.<br>";
        }
    } catch (Exception $e) {
        echo "Note: " . $e->getMessage() . "<br>";
    }
    
    // Check if role column exists, if not add it
    try {
        $result = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'");
        if ($result->rowCount() == 0) {
            echo "Adding 'role' column to users table...<br>";
            $pdo->exec("ALTER TABLE users ADD COLUMN role ENUM('admin', 'user') DEFAULT 'user' AFTER password");
            echo "Role column added successfully.<br>";
        }
    } catch (Exception $e) {
        echo "Note: " . $e->getMessage() . "<br>";
    }
    
    // Check if admin user exists, if not create it
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute(['amani kennedy']);
        
        if (!$stmt->fetch()) {
            $hashed_password = password_hash('amani1', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute(['amani kennedy', 'amani.kennedy@example.com', $hashed_password, 'admin']);
            echo "Admin user 'amani kennedy' created successfully.<br>";
        } else {
            echo "Admin user 'amani kennedy' already exists.<br>";
            // Update existing user to ensure they have admin role
            $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE username = ?");
            $stmt->execute(['amani kennedy']);
            echo "Admin role confirmed for 'amani kennedy'.<br>";
        }
    } catch (Exception $e) {
        echo "Admin user note: " . $e->getMessage() . "<br>";
    }
    
    // Create genres table if it doesn't exist
    try {
        $sql = "CREATE TABLE IF NOT EXISTS genres (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) UNIQUE NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $pdo->exec($sql);
        echo "Genres table created successfully.<br>";
        
        // Check if description column exists, if not add it
        try {
            $result = $pdo->query("SHOW COLUMNS FROM genres LIKE 'description'");
            if ($result->rowCount() == 0) {
                echo "Adding 'description' column to genres table...<br>";
                $pdo->exec("ALTER TABLE genres ADD COLUMN description TEXT AFTER name");
                echo "Description column added successfully.<br>";
            }
        } catch (Exception $e) {
            echo "Note: " . $e->getMessage() . "<br>";
        }
        
        // Insert default genres if table is empty
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM genres");
        $count = $stmt->fetch()['count'];
        
        if ($count == 0) {
            echo "Adding default genres...<br>";
            $default_genres = [
                ['Action', 'High-energy films with physical stunts, chases, and battles'],
                ['Comedy', 'Films designed to make the audience laugh'],
                ['Drama', 'Serious, plot-driven films focusing on realistic characters'],
                ['Horror', 'Films designed to frighten and invoke fear'],
                ['Sci-Fi', 'Science fiction films with futuristic concepts'],
                ['Thriller', 'Suspenseful films that keep viewers on edge'],
                ['Romance', 'Films focused on love stories and relationships'],
                ['Animation', 'Films created using animation techniques'],
                ['Adventure', 'Exciting stories with journeys and quests'],
                ['Crime', 'Films centered around criminal activities'],
                ['Fantasy', 'Films with magical and supernatural elements'],
                ['Mystery', 'Films involving puzzle-solving and investigations']
            ];
            
            $stmt = $pdo->prepare("INSERT INTO genres (name, description) VALUES (?, ?)");
            foreach ($default_genres as $genre) {
                $stmt->execute($genre);
            }
            echo "Default genres added successfully.<br>";
        }
    } catch (Exception $e) {
        echo "Genres table note: " . $e->getMessage() . "<br>";
    }
    
    // Create movies table if it doesn't exist
    try {
        $sql = "CREATE TABLE IF NOT EXISTS movies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            release_year INT,
            duration INT,
            poster_url VARCHAR(500),
            video_url VARCHAR(500),
            genre_id INT,
            featured BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE SET NULL
        )";
        
        $pdo->exec($sql);
        echo "Movies table created successfully.<br>";
    } catch (Exception $e) {
        echo "Movies table note: " . $e->getMessage() . "<br>";
    }
    
    // Create slides table if it doesn't exist
    try {
        $sql = "CREATE TABLE IF NOT EXISTS slides (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            image_url VARCHAR(500),
            video_url VARCHAR(500),
            active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $pdo->exec($sql);
        echo "Slides table created successfully.<br>";
    } catch (Exception $e) {
        echo "Slides table note: " . $e->getMessage() . "<br>";
    }
    
    // Create views table if it doesn't exist
    try {
        $sql = "CREATE TABLE IF NOT EXISTS views (
            id INT AUTO_INCREMENT PRIMARY KEY,
            movie_id INT,
            ip_address VARCHAR(50),
            view_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
        )";
        
        $pdo->exec($sql);
        echo "Views table created successfully.<br>";
    } catch (Exception $e) {
        echo "Views table note: " . $e->getMessage() . "<br>";
    }
    
    echo "<br><strong>Database setup completed successfully!</strong><br>";
    echo "<br><a href='admin/login.php'>Go to Admin Login</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>