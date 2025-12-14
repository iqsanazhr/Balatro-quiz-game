# WEB BALATRO

A fan-made web implementation of the popular game "Balatro" by LocalThunk. This project adapts the core mechanic of solving "Blinds" via a quiz system, integrated with a functional Shop and Deck Building economy.

## 🃏 Features

- **The Blind (Quiz Mode)**: Answer poker-themed questions against a timer to earn Chips.
- **Shop System**: Buy Jokers and Tarot cards using earned Chips.
- **Deck Building**: Manage your collection of cards.
- **Powerups**: Use Tarot cards in-game to freeze time, hide wrong answers, or boost multipliers.
- **Economy**: Persistent Chip balance and inventory system.
- **CRT Aesthetic**: Retro visual style with screen burn-in and scanline effects.

## 🛠️ Tech Stack

- **Frontend**: HTML5, CSS3, Vanilla JavaScript.
- **Backend**: Native PHP (8.0+).
- **Database**: MySQL / MariaDB.
- **Architecture**: MVC-lite (Separation of Logic, View, and Data).

## 🚀 Installation & Setup

1.  **Clone the Repository**

    ```bash
    git clone https://github.com/username/webbalatro.git
    cd webbalatro
    ```

2.  **Database Setup**

    - Create a database named `balatro_web`.
    - Import `database.sql` to initialize tables and seed data.
    - Configure `includes/db.php` if your credentials differ from default (root/empty).

3.  **Run the Server**

    - Using XAMPP/WAMP: Move folder to `htdocs`.
    - Using PHP Built-in Server:
      ```bash
      php -S localhost:8001
      ```

4.  **Login Credentials**
    - **Admin**: `admin` / `admin`
    - Or Register a new account on the login screen.

## 📂 Project Structure

```
webbalatro/
├── api/                # AJAX Endpoints (Score submission, Card usage)
├── assets/             # Images, CSS, JS, Fonts
├── includes/           # Reusable PHP components (Header, Navbar, Footer)
├── database.sql        # Database schema and seeds
├── home.php            # Main Dashboard
├── quiz.php            # Game Loop
├── shop.php            # Card Store
├── collection.php      # User Inventory
└── style.css           # Global Styles
```

## ⚠️ Notes

This is an educational project and is not affiliated with the official Balatro game. All assets are used for demonstration purposes.
