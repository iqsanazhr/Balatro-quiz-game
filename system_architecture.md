# System Architecture

## 1. System Flowchart

This flowchart illustrates the user journey through the application, from Login to the core Game Loop.

```mermaid
flowchart TD
    A[Start] --> B{Has Account?}
    B -- No --> C[Register]
    B -- Yes --> D[Login]
    C --> D
    D --> E[Home Dashboard]

    E --> F[Play Protocol (Quiz)]
    E --> G[Shop]
    E --> H[Collection]
    E --> I[Wiki / Profile]

    F --> J{Answer Question}
    J -- Correct --> K[Earn Chips]
    J -- Wrong --> L[Game Over]

    K --> M{Next Question?}
    M -- Yes --> J
    M -- No --> N[Win Round]

    N --> E
    L --> E

    G --> O{Buy Card?}
    O -- Yes --> P[Deduct Chips & Add to Inventory]
    P --> G

    H --> E
```

## 2. Sequence Diagram: Quiz & Score Submission

This diagram details the interaction between the Client (Browser), the Server (PHP API), and the Database when a user answers a question.

```mermaid
sequenceDiagram
    participant U as User
    participant JS as Client JS (quiz.js)
    participant API as API (submit_score.php)
    participant DB as Database (MySQL)

    U->>JS: Selects Answer
    JS->>JS: Validates Answer (Local Logic)

    alt Correct Answer
        JS->>API: POST /api/submit_score.php (score, chips)
        API->>DB: UPDATE users SET chips_balance += N
        DB-->>API: Success
        API-->>JS: JSON {success: true, new_balance: X}
        JS->>U: Show "Correct" Animation & Update HUD
    else Wrong Answer
        JS->>U: Trigger Game Over Effect
    end
```

## 3. Class Diagram (Conceptual)

Although the system uses procedural PHP, the logic mimics an Object-Oriented structure through separate modules.

```mermaid
classDiagram
    class User {
        +int id
        +string username
        +int chips_balance
        +login()
        +register()
    }

    class Card {
        +int id
        +string name
        +enum type (Joker/Tarot)
        +enum category (Skill/Collection)
        +int price
        +string effect_logic
    }

    class GameSession {
        +int score
        +int multiplier
        +int timer
        +startTimer()
        +validateAnswer()
        +submitScore()
    }

    class Shop {
        +buyCard(userId, cardId)
        +checkFunds()
    }

    User "1" --> "*" Card : Owns >
    GameSession --> "*" Card : Uses >
    Shop ..> User : Modifies Balance
```

## 4. Entity Relationship Diagram (ERD)

The database schema structure and relationships.

```mermaid
erDiagram
    USERS {
        int id PK
        string username
        string password
        int chips_balance
        timestamp created_at
    }

    CARDS {
        int id PK
        string name
        enum type
        enum category
        int price
        text effect_logic
        string image_url
    }

    USER_INVENTORY {
        int id PK
        int user_id FK
        int card_id FK
    }

    QUESTIONS {
        int id PK
        string question_text
        string option_a
        string option_b
        string option_c
        string option_d
        char correct_option
        int difficulty
        int base_chips
    }

    USERS ||--o{ USER_INVENTORY : has
    CARDS ||--o{ USER_INVENTORY : contains
```
