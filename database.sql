CREATE DATABASE IF NOT EXISTS balatro_web;
USE balatro_web;

DROP TABLE IF EXISTS user_inventory;
DROP TABLE IF EXISTS user_collection;
DROP TABLE IF EXISTS questions;
DROP TABLE IF EXISTS cards;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    chips_balance INT DEFAULT 1000, -- Increased starting balance for testing
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('Joker', 'Tarot') NOT NULL,
    category ENUM('Skill', 'Collection') DEFAULT 'Collection', -- New Column
    effect_logic TEXT,
    price INT NOT NULL,
    description VARCHAR(255),
    image_url VARCHAR(255)
);

CREATE TABLE user_inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    card_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (card_id) REFERENCES cards(id) ON DELETE CASCADE
);

CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option CHAR(1) NOT NULL,
    difficulty INT DEFAULT 1,
    base_chips INT DEFAULT 50
);

-- Seed Questions
INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_option, difficulty, base_chips) VALUES
-- Level 1 (50 Chips)
('Apa nama tangan Poker yang terdiri dari 5 kartu dengan jenis sama?', 'Straight', 'Flush', 'Full House', 'Two Pair', 'B', 1, 50),
('Warna apa yang merepresentasikan "Mult" dalam skor Balatro?', 'Biru', 'Hijau', 'Merah', 'Kuning', 'C', 1, 50),
('Kartu apa yang berfungsi memberikan multiplier pasif?', 'Tarot', 'Planet', 'Joker', 'Spectral', 'C', 1, 50),
('Warna apa yang merepresentasikan "Chips"?', 'Biru', 'Merah', 'Ungu', 'Putih', 'A', 1, 50),
('Jumlah kartu dalam satu deck standar?', '50', '52', '54', '60', 'B', 1, 50),
('Apa sebutan musuh di setiap stage?', 'The Wall', 'The Needle', 'Blind', 'The Hook', 'C', 1, 50),
('Max interest per ronde ($5) didapat jika punya uang?', '$15', '$20', '$25', '$50', 'C', 1, 50),
('Kegunaan kartu Planet?', 'Uang', 'Level Poker Hand', 'Hancurkan kartu', 'Joker Gratis', 'B', 1, 50),
('Joker berbentuk pisang?', 'Gros Michel', 'Cavendish', 'Ramen', 'Popcorn', 'A', 1, 50),
('Tombol membuang kartu?', 'Play', 'Discard', 'Sort', 'Sell', 'B', 1, 50),
-- Level 2 (100 Chips)
('Efek Tarot "The Fool"?', 'Uang', 'Spawn last Tarot/Planet', 'Gold Card', 'Destroy 2', 'B', 2, 100),
('Kemampuan Joker "Blueprint"?', 'X2 Mult', 'Copy right Joker', 'Add Hand', 'Polychrome', 'B', 2, 100),
('Syarat unlock "Cavendish"?', 'Win Ante 8', 'Gros Michel extinct', 'Have $100', 'No Discard', 'B', 2, 100),
('Planet untuk "High Card"?', 'Earth', 'Mars', 'Pluto', 'Uranus', 'C', 2, 100),
('Efek "Red Seal"?', '+50 Chips', 'Retrigger 1 time', '$3 on discard', '1.5x Mult', 'B', 2, 100),
('Efek samping Spectral "Immolate"?', '-Level', 'Destroy 5 cards', '-Joker Slot', 'Game Over', 'B', 2, 100),
('Kemampuan Boss "The Wall"?', 'Extra Large Blind', '1 Hand Only', 'Face Down', 'No Discard', 'A', 2, 100),
('Edition kartu efek 1.5x Mult?', 'Foil', 'Holographic', 'Polychrome', 'Negative', 'C', 2, 100),
('Tarot pengubah Gold Card?', 'Magician', 'Devil', 'Empress', 'Lovers', 'B', 2, 100),
('Abstract Joker beri +3 Mult per...?', 'Card in hand', 'Joker owned', 'Tarot owned', '$10 owned', 'B', 2, 100),
-- Level 3 (150 Chips)
('Boss "The Needle" memaksa?', 'No Discard', 'Play Only 1 Hand', '0 Discard', 'Sell Joker', 'B', 3, 150),
('Efek Spectral "Hex"?', 'Polychrome random Joker, destroy others', 'Double Money', 'All Spades', '+Joker Slot', 'A', 3, 150),
('Joker "DNA" aktif jika mainkan?', 'Flush', '1 Card (High Card)', 'Royal Flush', '5 Cards', 'B', 3, 150),
('Probabilitas "Glass Card" pecah?', '1 in 2', '1 in 4', '1 in 6', '1 in 10', 'B', 3, 150),
('Legendary Joker X2 Mult per King/Queen?', 'Yorick', 'Perkeo', 'Triboulet', 'Canio', 'C', 3, 150),
('Voucher "Antimatter" beri?', '+1 Hand Size', '+1 Joker Slot', '+1 Consumable', 'Discount', 'B', 3, 150),
('Boss "The Psychic" memaksa?', 'Random', 'Play 5 cards', 'Face Down', 'Blind X2', 'B', 3, 150),
('Penalti Voucher "Hieroglyph"?', '-1 Hand', '-1 Discard', 'Price Up', '-Joker Slot', 'A', 3, 150),
('Nilai chips "Stone Card"?', '30', '50', '100', '0', 'B', 3, 150),
('Unlock "Yellow Deck"?', 'Win Red', '50 different Jokers', 'Have $50', 'Play 1000 cards', 'B', 3, 150),
-- Level 4 (300 Chips)
('Urutan skor benar?', 'Mult->Chips', 'Chips->Mult->Hand->Joker', 'Joker->Card', 'Simultan', 'B', 4, 300),
('5 kartu A,K,Q,J,10 sama jenis?', 'Royal Flush', 'Straight Flush', '5 of a Kind', 'Flush House', 'A', 4, 300),
('Syarat "Five of a Kind"?', 'Joker', 'Deck Mod (Duplicate)', 'Spectral', 'Boss', 'B', 4, 300),
('Efek "Gold Seal"?', '$3 played', '$3 held in hand', 'Retrigger', '1.5x Mult', 'B', 4, 300),
('Joker "Mime" retrigger?', 'Held in Hand', 'On Score', 'On Discard', 'Joker ability', 'A', 4, 300),
('Skor Ante 8 White Stake?', '50k', '100k', '300k', '1M', 'B', 4, 300),
('Penalty Joker "Stuntman"?', '-1 Joker', '-2 Hand Size', '-2 Discard', 'Money 0', 'B', 4, 300),
('Boss Debuff Face Cards?', 'The Plant', 'The Goad', 'The Window', 'The Head', 'A', 4, 300),
('Joker "Rocket" nambah uang saat?', 'Discard', 'Beat Boss Blind', 'Buy Tarot', 'Play Flush', 'B', 4, 300),
('Spectral "Soul" creates?', 'Rare Joker', 'Legendary Joker', 'Negative', 'Black Hole', 'B', 4, 300);

-- Seed Cards
-- SKILL CARDS (For Game)
INSERT INTO cards (name, type, category, effect_logic, price, description, image_url) VALUES 
('The World', 'Tarot', 'Skill', 'freeze_timer', 500, 'Freeze time for 10s', 'assets/card/w-card.webp'),
('The Hanged Man', 'Tarot', 'Skill', 'hide_wrong', 500, 'Hide 2 wrong answers', 'assets/card/hm-card.webp'),
('The Hierophant', 'Tarot', 'Skill', 'double_mult', 800, 'Double your Multiplier', 'assets/card/h-card.webp'),
('Chicot', 'Joker', 'Skill', 'protect_streak', 1500, 'Prevent Streak Reset', 'assets/card/Chicot-card.webp'),
('Blueprint', 'Joker', 'Skill', 'retrigger_last', 2000, 'Copies last Tarot used', 'assets/card/Blueprint-card.webp');

-- COLLECTION CARDS (Vanity / High Price)
INSERT INTO cards (name, type, category, effect_logic, price, description, image_url) VALUES 
('Stuntman (Rare)', 'Joker', 'Collection', NULL, 3000, '+300 Chips, -2 Hand Size.', 'assets/card/Stuntman.webp'),
('Perkeo (Holo)', 'Joker', 'Collection', NULL, 5000, 'Replicates your consumables.', 'assets/card/Perkeo.webp'),
('Yorick (Foil)', 'Joker', 'Collection', NULL, 10000, 'A tragic companion.', 'assets/card/Yorick.webp'),
('Canio (Legendary)', 'Joker', 'Collection', NULL, 20000, 'Gains X1 Mult when a face card is destroyed.', 'assets/card/Canio.webp'),
('Triboulet (Legendary)', 'Joker', 'Collection', NULL, 20000, 'Played Kings and Queens give X2 Mult.', 'assets/card/Triboulet.webp'),
('Baron (Rare)', 'Joker', 'Collection', NULL, 8000, 'Each King held in hand gives X1.5 Mult.', 'assets/card/Baron.webp'),
('Brainstorm (Rare)', 'Joker', 'Collection', NULL, 8000, 'Copies the ability of leftmost Joker.', 'assets/card/Brainstorm.webp'),
('DNA (Rare)', 'Joker', 'Collection', NULL, 6000, 'If first hand is 1 card, add a permanent copy to deck.', 'assets/card/DNA.webp'),
('Hit the Road (Rare)', 'Joker', 'Collection', NULL, 5000, 'Gains X0.5 Mult for every Jack discarded this round.', 'assets/card/Hit_the_Road.webp'),
('Obelisk (Rare)', 'Joker', 'Collection', NULL, 5000, 'X0.2 Mult per consecutive hand played without repeating hand type.', 'assets/card/Obelisk.webp'),
('Invisible Joker (Rare)', 'Joker', 'Collection', NULL, 4000, 'After 3 rounds, sell to duplicate a random Joker.', 'assets/card/Invisible_Joker.webp'),
('Matador (Uncommon)', 'Joker', 'Collection', NULL, 2500, 'Earn $8 if played hand triggers Boss Blind ability.', 'assets/card/Matador.webp'),
('Mime (Uncommon)', 'Joker', 'Collection', NULL, 2500, 'Retrigger all card held in hand abilities.', 'assets/card/Mime.webp'),
('The Idol (Uncommon)', 'Joker', 'Collection', NULL, 2500, 'X2 Mult for each [Rank] of [Suit] when scored.', 'assets/card/The_Idol.webp'),
('Pareidolia (Uncommon)', 'Joker', 'Collection', NULL, 2500, 'All cards are considered Face cards.', 'assets/card/Pareidolia.webp'),
('Showman (Uncommon)', 'Joker', 'Collection', NULL, 2500, 'Joker, Tarot, Planet, and Spectral cards may appear multiple times.', 'assets/card/Showman.webp'),
('Dusk (Uncommon)', 'Joker', 'Collection', NULL, 2500, 'Retrigger all played cards in final hand of round.', 'assets/card/Dusk.webp'),
('Sock and Buskin (Uncommon)', 'Joker', 'Collection', NULL, 2500, 'Retrigger all played Face cards.', 'assets/card/Sock_and_Buskin.webp'),
('Madness (Uncommon)', 'Joker', 'Collection', NULL, 2500, 'X0.5 Mult when Blind selected. Destroys random Joker.', 'assets/card/Madness.webp');

INSERT INTO users (username, password, chips_balance) VALUES ('admin', 'admin', 100000);
