// assets/js/quiz.js

let questions = [];
let currentQuestionIndex = 0;
let score = 0;
let currentMult = 1.0;
let targetScore = 500; // Example target
let timerInterval;
let timeLeft = 30;
let isPaused = false;
let chicotActive = false; // Joker effect

// DOM Elements
const els = {
    questionText: document.getElementById('question-text'),
    rewardText: document.getElementById('q-reward'),
    optionsContainer: document.getElementById('options-container'),
    score: document.getElementById('current-score'),
    mult: document.getElementById('current-mult'),
    target: document.getElementById('target-score'),
    timerBar: document.getElementById('timer-bar'),
    overlay: document.getElementById('game-overlay'),
    overlayTitle: document.getElementById('overlay-title'),
    overlayScore: document.getElementById('overlay-score')
};

// Initialize
document.addEventListener('DOMContentLoaded', startGame);

async function startGame() {
    try {
        const res = await fetch('api/get_questions.php');
        const data = await res.json();
        
        if (data.error) throw new Error(data.error);
        if (data.length === 0) throw new Error("No questions found");

        questions = data;
        resetGameState();
        loadQuestion();
        startTimer();

    } catch (e) {
        console.error(e);
        alert("Failed to load deck: " + e.message);
        // window.location.href = 'home.php'; // DISABLED FOR DEBUGGING
    }
}

function resetGameState() {
    currentQuestionIndex = 0;
    score = 0;
    currentMult = 1.0;
    timeLeft = 30;
    isPaused = false;
    chicotActive = checkJokerEffect('protect_streak'); 
    renderInventory();
    updateHUD();
}

function checkJokerEffect(effect) {
    if (!window.userInventory) return false;
    return window.userInventory.some(c => c.type === 'Joker' && c.effect_logic === effect);
}

function renderInventory() {
    // const jokerContainer = document.getElementById('joker-slots'); // Removed
    const consumableContainer = document.getElementById('consumable-slots');
    
    // jokerContainer.innerHTML = '';
    consumableContainer.innerHTML = '';

    if (!window.userInventory || window.userInventory.length === 0) {
        // Optional: Show message if empty?
    }

    window.userInventory.forEach(card => {
        const div = document.createElement('div');
        div.className = (card.type === 'Joker') ? 'joker-card' : 'consumable-card';
        div.style.backgroundImage = `url('${card.image_url}')`; 
        div.style.backgroundSize = 'contain';
        div.style.backgroundRepeat = 'no-repeat';
        div.style.backgroundPosition = 'center';
        
        // Removed inner text to show full art. Tooltip could be added.
        div.title = `${card.name} (${card.type === 'Joker' ? 'Passive' : 'Click'})`;
        
        // Quantity Badge (if stacked)
        if (card.quantity > 1) {
            const badge = document.createElement('span');
            badge.innerText = `x${card.quantity}`;
            badge.style.position = 'absolute';
            badge.style.top = '-5px';
            badge.style.right = '-5px';
            badge.style.background = 'gold';
            badge.style.color = 'black';
            badge.style.borderRadius = '50%';
            badge.style.padding = '2px 6px';
            badge.style.fontSize = '0.8rem';
            div.appendChild(badge);
        }

        // Render ALL cards in the Consumable container (User request: "hapus box card joker", "chicot join consumable")
        
        // Logic: Chicot is passive, others are active.
        if (card.type === 'Tarot' || card.effect_logic === 'retrigger_last') {
            div.onclick = () => usePowerup(card.effect_logic);
            div.style.cursor = 'pointer';
        } else {
             // Passive (Chicot)
             div.style.cursor = 'default';
             div.title += " (Passive Effect Active)";
        }
        
        consumableContainer.appendChild(div);
    });
}

function updateHUD() {
    els.score.textContent = Math.floor(score);
    els.mult.textContent = "X" + currentMult.toFixed(1);
    els.target.textContent = targetScore;
}

function loadQuestion() {
    if (currentQuestionIndex >= questions.length) {
        endGame(true);
        return;
    }

    const q = questions[currentQuestionIndex];
    els.questionText.textContent = q.question_text;
    els.rewardText.textContent = q.base_chips;

    // Render Options
    els.optionsContainer.innerHTML = '';
    const opts = [
        { key: 'A', text: q.option_a },
        { key: 'B', text: q.option_b },
        { key: 'C', text: q.option_c },
        { key: 'D', text: q.option_d }
    ];

    opts.forEach(opt => {
        const btn = document.createElement('div');
        btn.className = 'option-card';
        btn.textContent = opt.text; // Just text, or styled
        btn.onclick = () => handleAnswer(opt.key, btn);
        btn.dataset.key = opt.key;
        els.optionsContainer.appendChild(btn);
    });
}

function startTimer() {
    clearInterval(timerInterval);
    timeLeft = 30;
    isPaused = false;
    
    timerInterval = setInterval(() => {
        if (!isPaused) {
            timeLeft -= 0.1;
            els.timerBar.style.height = (timeLeft / 30 * 100) + '%';
            
            if (timeLeft <= 0) {
                // Time Over = Wrong Answer
                handleAnswer(null, null); 
            }
        }
    }, 100);
}

function handleAnswer(selectedKey, btnElement) {
    clearInterval(timerInterval);

    const q = questions[currentQuestionIndex];
    const isCorrect = (selectedKey === q.correct_option);

    // Visual Feedback
    if (btnElement) {
        btnElement.classList.add(isCorrect ? 'option-correct' : 'option-wrong');
    }
    
    // Reveal correct if wrong
    if (!isCorrect && selectedKey !== null) {
        const correctBtn = Array.from(els.optionsContainer.children).find(b => b.dataset.key === q.correct_option);
        if (correctBtn) correctBtn.classList.add('option-correct');
    }

    // Scoring Logic
    if (isCorrect) {
        const points = q.base_chips * currentMult;
        score += points;
        currentMult += 0.5; // Streak Bonus
        animateScore(points);
    } else {
        if (!chicotActive) {
            currentMult = 1.0; // Reset Streak
        } else {
            // Chicot prevents reset
            // Maybe visual cue?
        }
        document.body.classList.add('shake');
        setTimeout(() => document.body.classList.remove('shake'), 500);
    }

    updateHUD();

    // Next Question Delay
    setTimeout(() => {
        currentQuestionIndex++;
        loadQuestion();
        startTimer();
    }, 1500);
}

function animateScore(added) {
    // Simple popup or something (Future)
}

// State for Blueprint
let lastUsedEffect = null;

async function usePowerup(effect) {
    if (isPaused && effect === 'freeze_timer') return; // Prevent double freeze

    // Handle Blueprint (copy last effect)
    if (effect === 'retrigger_last') {
        if (!lastUsedEffect) {
            alert("BLUEPRINT: No Tarot used yet!");
            return;
        }
        // Use the last effect, but don't consume Blueprint (Wait, user says "Card skill that has been purchased... disappears"). 
        // Logic: Blueprint itself is a card. Consuming it triggers the last effect.
        // Recursive call with the LAST effect.
        // IMPORTANT: We need to consume Blueprint card, NOT the last card again.
        // The recursive call would try to consume the last card again if we just passed the effect to `usePowerup`.
        // So we need to separate "consume card" from "execute effect".
        
        // Let's refactor slightly or handle it here.
        // Simplest way: duplicate logic or separate "execute" function.
        // Let's allow `usePowerup` to take a `skipConsume` flag?
        // No, `Blueprint` is the card being consumed.
        
        // We will proceed to consume 'retrigger_last' (Blueprint) normally via API.
        // Then we execute the logic of `lastUsedEffect`.
        effect = lastUsedEffect; // Swap effect for execution, but we still consumed 'retrigger_last' in API?
        // Wait, the API call below uses `effect`. If I swap it here, it will try to consume the `lastUsedEffect` card.
        // So I must consume 'retrigger_last' first, THEN swap effect for Logic.
    }

    // Capture original effect for API call
    let effectToConsume = (effect === lastUsedEffect && lastUsedEffect !== null) ? 'retrigger_last' : effect; 
    // Actually, simpler: The button onclick for Blueprint passes 'retrigger_last'.
    // We call API with 'retrigger_last'.
    // Then if success, we look up `lastUsedEffect` and execute THAT logic.

    try {
        const payloadEffect = (effect === 'retrigger_last') ? 'retrigger_last' : effect;

        const res = await fetch('api/use_card.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ effect: payloadEffect })
        });
        const data = await res.json();
        
        if (data.error) {
            alert("Failed to use card: " + data.error);
            return;
        }

        // Success: Update Local Inventory
        const cardIndex = window.userInventory.findIndex(c => c.effect_logic === payloadEffect);
        if (cardIndex !== -1) {
            window.userInventory[cardIndex].quantity--;
            if (window.userInventory[cardIndex].quantity <= 0) {
                window.userInventory.splice(cardIndex, 1);
            }
            renderInventory(); 
        }

        // --- EXECUTE EFFECT LOGIC ---
        let logicEffect = effect;
        if (effect === 'retrigger_last') {
            if (!lastUsedEffect) {
                 alert("Blueprint wasted! No previous Tarot used.");
                 return;
            }
            logicEffect = lastUsedEffect;
            alert(`BLUEPRINT copies ${logicEffect.toUpperCase()}!`);
        } else {
            // Update last used (only if not Blueprint itself, to prevent Blueprint copying Blueprint?)
            // Balatro: Blueprint copies the Joker to the right. 
            // HERE: User said "Re-triggers the last used Tarot card".
            // So if I use World, then Blueprint -> Blueprint copies World.
            // If I use World, then Hanged Man, then Blueprint -> Blueprint copies Hanged Man.
            lastUsedEffect = effect;
        }

        const q = questions[currentQuestionIndex];

        if (logicEffect === 'freeze_timer') { // The World
            isPaused = true;
            els.timerBar.style.backgroundColor = '#00f'; 
            setTimeout(() => {
                isPaused = false;
                els.timerBar.style.backgroundColor = 'var(--red)';
            }, 10000); 
            // Alert handled above or generic
            if (effect !== 'retrigger_last') alert("THE WORLD: Time Frozen for 10s!");
        }
        else if (logicEffect === 'hide_wrong') { // Hanged Man
            const wrongKeys = ['A','B','C','D'].filter(k => k !== q.correct_option);
            for (let i = wrongKeys.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [wrongKeys[i], wrongKeys[j]] = [wrongKeys[j], wrongKeys[i]];
            }
            const toHide = wrongKeys.slice(0, 2);
            
            Array.from(els.optionsContainer.children).forEach(btn => {
                if (toHide.includes(btn.dataset.key)) {
                    btn.style.opacity = '0.2';
                    btn.style.pointerEvents = 'none';
                }
            });
        }
        else if (logicEffect === 'double_mult') { // Hierophant
            currentMult *= 2;
            updateHUD();
        }

    } catch (e) {
        console.error(e);
    }
}

async function endGame(completed) {
    clearInterval(timerInterval);
    els.optionsContainer.innerHTML = '';
    els.questionText.textContent = "FINISH";
    
    // Submit Score
    try {
        await fetch('api/submit_score.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ score: Math.floor(score) })
        });
    } catch (e) {
        console.error("Score submit failed", e);
    }

    // Show Overlay
    els.overlayTitle.textContent = completed ? "BLIND DEFEATED" : "GAME OVER";
    els.overlayScore.textContent = "FINAL SCORE: " + Math.floor(score);
    els.overlay.style.display = 'flex';
}
