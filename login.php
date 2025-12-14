<?php
session_start();
include 'includes/db.php';

// Simple Login Logic
$error = '';
if (!$db_connected) {
    $error = "DATABASE DISCONNECTED: " . $db_error_msg;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $db_connected) {
    // Check if this is an AJAX request
    $is_ajax = isset($_POST['ajax']) && $_POST['ajax'] === '1';

    $action = $_POST['action'] ?? 'login';
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Response array for AJAX
    $response = ['status' => 'error', 'message' => 'An unknown error occurred'];

    if ($action === 'register') {
        // --- REGISTER LOGIC ---
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "USERNAME TAKEN";
        } else {
            // Insert new user
            $stmt = $pdo->prepare("INSERT INTO users (username, password, chips_balance) VALUES (?, ?, 50)");
            if ($stmt->execute([$username, $password])) {
                // Auto login after register
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $username;

                if ($is_ajax) {
                    echo json_encode(['status' => 'success']);
                    exit;
                }

                header("Location: home.php");
                exit;
            } else {
                $error = "REGISTRATION FAILED";
            }
        }

    } else {
        // --- LOGIN LOGIC ---
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u");
        $stmt->execute(['u' => $username]);
        $user = $stmt->fetch();

        if ($user) {
            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                if ($is_ajax) {
                    echo json_encode(['status' => 'success']);
                    exit;
                }

                header("Location: home.php");
                exit;
            } else {
                $error = "INVALID HAND (WRONG PASSWORD)";
            }
        } else {
            $error = "PLAYER NOT FOUND";
        }
    }

    // If we are here, there was an error
    if ($is_ajax && $error) {
        echo json_encode(['status' => 'error', 'message' => $error]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BALATRO - LOGIN</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="login-bg-active">
    <div class="crt-overlay"></div>
    <div id="screen-burn"></div>

    <!-- Container for 3D Switch -->
    <div class="login-container">

        <!-- CARD 1: SIGN IN (Active by default) -->
        <div class="login-box-3d signIn">
            <!-- Logo Removed -->
            <h2 style="color: var(--blue);">LOG IN</h2>

            <?php if ($error): ?>
                <p style="color: var(--red); font-weight: bold;"><?php echo $error; ?></p>
            <?php endif; ?>

            <form class="login-form-submit" method="POST" action="">
                <input type="hidden" name="action" value="login">
                <input type="text" name="username" placeholder="USERNAME" required autocomplete="off">
                <input type="password" name="password" placeholder="PASSWORD" required>

                <button type="submit" class="form-btn btn-submit">PLAY</button>
                <button type="button" class="form-btn btn-switch to-signup">CREATE ACCOUNT</button>
            </form>
        </div>

        <!-- CARD 2: SIGN UP (Inactive/Back by default) -->
        <div class="login-box-3d signUp">
            <h2 style="color: var(--red);">NEW RUN</h2>
            <p>ENTER YOUR CREDENTIALS<br>TO JOIN THE TABLE</p>

            <form class="login-form-submit" method="POST" action="">
                <input type="hidden" name="action" value="register">
                <input type="text" name="username" placeholder="NEW USERNAME" required autocomplete="off">
                <input type="password" name="password" placeholder="PASSWORD" required>
                <!-- <input type="password" name="confirm_password" placeholder="CONFIRM" required> -->

                <button type="submit" class="form-btn btn-submit" style="background: var(--blue);">REGISTER</button>
                <button type="button" class="form-btn btn-switch to-signin">BACK TO LOGIN</button>
            </form>
        </div>

    </div>

    <!-- Loading Animation Video overlay -->
    <div id="loading-overlay"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:9000; background:white;">
        <video id="loading-video" width="100%" height="100%" style="object-fit:cover;">
            <source src="assets/loading-animation.mp4" type="video/mp4">
        </video>
    </div>

    <!-- Transition Overlay -->
    <div id="page-transition"></div>

    <script src="assets/js/script.js"></script>
    <script>
        // --- 3D SWITCH LOGIC (Vanilla JS) ---
        const signInCard = document.querySelector('.signIn');
        const signUpCard = document.querySelector('.signUp');
        const toSignUpBtn = document.querySelector('.to-signup');
        const toSignInBtn = document.querySelector('.to-signin');

        toSignUpBtn.addEventListener('click', () => {
            signInCard.classList.remove('active-dx', 'inactive-dx');
            signInCard.classList.add('inactive-dx');

            signUpCard.classList.remove('active-sx', 'inactive-sx');
            signUpCard.classList.add('active-sx');
        });

        toSignInBtn.addEventListener('click', () => {
            signUpCard.classList.remove('active-sx', 'inactive-sx');
            signUpCard.classList.add('inactive-sx');

            signInCard.classList.remove('active-dx', 'inactive-dx');
            signInCard.classList.add('active-dx');
        });

        // --- FORM SUBMISSION & VIDEO LOGIC ---
        const forms = document.querySelectorAll('.login-form-submit');
        const errorMessageDisplay = document.querySelector('.error-message-display'); // Placeholder selector

        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                if (form.dataset.submitting) return;

                // Create FormData
                const formData = new FormData(form);
                formData.append('ajax', '1');

                // Clear previous errors (if any, though we mostly rely on PHP reload previously)
                // We need a place to show errors if we aren't reloading. 
                // Let's assume we update the specific form's error area.
                // Since layout has a single error area at top of SingIn card, let's target that or alert.

                fetch('login.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // SUCCESS: Play Video then Redirect
                            form.dataset.submitting = true;

                            const vidOverlay = document.getElementById('loading-overlay');
                            const vid = document.getElementById('loading-video');
                            vidOverlay.style.display = 'block';

                            // Enable Sound
                            vid.muted = false;
                            vid.volume = 1.0;

                            vid.play().catch(e => {
                                console.error("Video play failed:", e);
                                window.location.href = 'home.php';
                            });

                            setTimeout(() => {
                                const transition = document.getElementById('page-transition');
                                if (transition) transition.classList.add('fade-out-active');
                                setTimeout(() => {
                                    window.location.href = 'home.php';
                                }, 1000);
                            }, 10200); // 10.2s video duration

                        } else {
                            // ERROR: Show error message immediately
                            // Check where to display error
                            let errorContainer = form.closest('.login-box-3d').querySelector('.error-banner');

                            if (!errorContainer) {
                                // Create one if it doesn't exist
                                errorContainer = document.createElement('p');
                                errorContainer.className = 'error-banner';
                                errorContainer.style.color = 'var(--red)';
                                errorContainer.style.fontWeight = 'bold';
                                errorContainer.style.marginBottom = '10px';
                                form.closest('.login-box-3d').insertBefore(errorContainer, form);
                            }

                            errorContainer.innerText = data.message;

                            // Shake effect for feedback
                            form.closest('.login-box-3d').animate([
                                { transform: 'translateX(0)' },
                                { transform: 'translateX(-10px)' },
                                { transform: 'translateX(10px)' },
                                { transform: 'translateX(0)' }
                            ], { duration: 300 });
                        }
                    })
                    .catch(err => {
                        console.error('Fetch error:', err);
                        alert("A connection error occurred.");
                    });
            });
        });
    </script>
</body>

</html>