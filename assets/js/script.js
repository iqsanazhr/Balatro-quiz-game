document.addEventListener("DOMContentLoaded", () => {
  // --- INTRO PAGE LOGIC ---
  const introContainer = document.getElementById("intro-container");
  if (introContainer) {
    const handleStart = () => {
      // Slight delay/sound could be added here
      window.location.href = "login.php";
    };

    window.addEventListener("keydown", handleStart);
    window.addEventListener("click", handleStart);
  }

  // --- LOGIN PAGE LOGIC ---
  // MOVED TO login.php inline script to handle specific video duration and transitions.
  // const loginForm = document.getElementById("login-form");
  // if (loginForm) { ... }

  // Flash Animation Keyframes injected dynamically or use CSS class
  // We used CSS class 'glitch-active' in style.css
});
