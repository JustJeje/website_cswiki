-- ==========================================
-- cs2wiki.sql — Database Export
-- CS2 Knife Wiki | UAS PHP
-- ==========================================

CREATE DATABASE IF NOT EXISTS cs2wiki;
USE cs2wiki;

-- ==========================================
-- TABLE 1: categories
-- ==========================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO categories (name, description) VALUES
('Curved Blade',    'Knives with curved blades, like the Karambit'),
('Folding Knife',   'Balisong and folding-style knives'),
('Fixed Blade',     'Military and fixed blade knives'),
('Tactical',        'Tactical and utility combat knives'),
('Collector',       'Rare collector-tier knives');

-- ==========================================
-- TABLE 2: knives
-- ==========================================
CREATE TABLE IF NOT EXISTS knives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    category_id INT,
    origin VARCHAR(100),
    rarity VARCHAR(50) DEFAULT 'Covert',
    rarity_color VARCHAR(10) DEFAULT '#e4432d',
    price_range VARCHAR(50),
    drop_chance VARCHAR(20) DEFAULT '~0.26%',
    inspect_anim VARCHAR(100),
    short_desc TEXT,
    description TEXT,
    fun_fact TEXT,
    image VARCHAR(200) DEFAULT 'images/placeholder.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

INSERT INTO knives (slug, name, category_id, origin, rarity, rarity_color, price_range, drop_chance, inspect_anim, short_desc, description, fun_fact, image) VALUES
('karambit', 'Karambit', 1, 'Southeast Asia', 'Covert', '#e4432d', '$200 — $3,000+', '~0.26%', 'Ring spin + retract',
 'The most iconic CS2 knife, inspired by the tiger\'s claw.',
 'The Karambit is a small curved knife originating from Southeast Asia. In CS2, it became the most coveted knife for its distinctive ring handle and mesmerizing inspect animation.',
 'The finger ring on the Karambit handle was traditionally used to keep the knife secure during combat.',
 'images/karambit.png'),

('butterfly', 'Butterfly Knife', 2, 'Philippines', 'Covert', '#8847ff', '$300 — $5,000+', '~0.26%', 'Full balisong flip sequence',
 'A Filipino balisong with hypnotic flipping animations.',
 'The Butterfly Knife, also known as a balisong, originated in the Philippines. In CS2 it is famous for one of the most satisfying inspect animations in the game.',
 'The Butterfly Knife inspect animation took longer to develop than other knives due to the complexity of flipping mechanics.',
 'images/butterfly.png'),

('m9bayonet', 'M9 Bayonet', 3, 'United States', 'Covert', '#4b96e5', '$100 — $1,500+', '~0.26%', 'Military grip flip + toss',
 'A military-grade combat bayonet with a clip-point blade.',
 'The M9 Bayonet is a military fighting knife used by the U.S. Army since the 1980s. It features a clip-point blade and distinctive serrated spine.',
 'The M9 Bayonet was designed to replace the M7 bayonet and can be attached to the barrel of an M16 or M4 rifle.',
 'images/m9bayonet.png'),

('huntsman', 'Huntsman Knife', 4, 'United States', 'Covert', '#fbbf24', '$80 — $1,200+', '~0.26%', 'Toss and catch',
 'A clip-point hunting knife with a wide blade and aggressive look.',
 'The Huntsman Knife features a wide clip-point blade inspired by classic hunting knives. Its toss-and-catch inspect animation makes it a fan favorite.',
 'The Huntsman Knife was one of the first new knives added to CS:GO after launch.',
 'images/placeholder.png'),

('falchion', 'Falchion Knife', 1, 'Europe', 'Covert', '#4ade80', '$60 — $800+', '~0.26%', 'Twirl + stab',
 'A medieval-inspired curved blade with a modern CS2 twist.',
 'The Falchion Knife draws inspiration from the medieval European falchion sword. Its wide curved blade gives it a unique silhouette among CS2 knives.',
 'The Falchion Knife was introduced in the Falchion Case, the first case to feature a knife named after its own case.',
 'images/placeholder.png');

-- ==========================================
-- TABLE 3: skins
-- ==========================================
CREATE TABLE IF NOT EXISTS skins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    knife_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    wear VARCHAR(50) DEFAULT 'Factory New',
    pattern_desc TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (knife_id) REFERENCES knives(id)
);

INSERT INTO skins (knife_id, name, wear, pattern_desc) VALUES
(1, 'Fade',         'Factory New', 'Gradient from yellow to pink to purple'),
(1, 'Doppler',      'Factory New', 'Marbled galaxy-like pattern, multiple phases'),
(1, 'Tiger Tooth',  'Factory New', 'Orange and black tiger stripe pattern'),
(2, 'Fade',         'Factory New', 'Full fade gradient, highly sought after'),
(2, 'Doppler',      'Factory New', 'Deep space swirl pattern'),
(2, 'Case Hardened','Field-Tested','Blue and gold case-hardened steel pattern'),
(3, 'Doppler',      'Factory New', 'Phase 2 doppler is most valued'),
(3, 'Crimson Web',  'Minimal Wear','Red web pattern on black blade'),
(3, 'Lore',         'Factory New', 'Gold and green ornate pattern'),
(4, 'Fade',         'Factory New', 'Classic fade from gold to pink'),
(5, 'Marble Fade',  'Factory New', 'Swirled marble candy cane pattern');

-- ==========================================
-- TABLE 4: users
-- ==========================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,   -- hashed dengan password_hash()
    email VARCHAR(100),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin account (password: admin123)
INSERT INTO users (username, password, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@cs2wiki.com', 'admin'),
('agent', '$2y$10$TKh8H1.PFbuSpgzLhpR4C.wkRxlO7t7mzI0qUbTEQQK.IyXfWMf7.', 'agent@cs2wiki.com', 'user');
-- admin password: password | agent password: password
-- (gunakan register.php untuk buat akun baru dengan password asli)

-- ==========================================
-- TABLE 5: contact_messages
-- ==========================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    topic VARCHAR(50),
    message TEXT NOT NULL,
    status ENUM('unread', 'read') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO contact_messages (name, email, topic, message, status) VALUES
('John Doe',   'john@example.com',  'knife_info', 'Hey, can you add more info about Karambit Fade patterns?', 'unread'),
('Jane Smith', 'jane@example.com',  'pricing',    'The price range for Butterfly Knife seems outdated.', 'read'),
('PlayerOne',  'player@example.com','general',    'Great wiki! Would love to see Gut Knife added.', 'unread');
