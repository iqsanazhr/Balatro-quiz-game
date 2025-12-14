<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BALATRO WEB - INSERT COIN</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <div class="crt-overlay"></div>

    <div id="intro-container">
        <!-- Muted looped background trailer (Start muted for autoplay) -->
        <video autoplay muted loop id="bg-video">
            <source src="assets/trailer-balatro.mp4" type="video/mp4">
            Your browser does not support HTML5 video.
        </video>

        <div id="intro-text" style="z-index: 10; text-align: center;">
            <h1 class="blink-text press-any-key" style="cursor:pointer;">CLICK TO START</h1>
        </div>
    </div>

    <!-- Removed generic script.js to control this specific flow -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const video = document.getElementById("bg-video");
            const text = document.querySelector(".press-any-key");
            
            let soundEnabled = false;

            const handleInteraction = () => {
                if (!soundEnabled) {
                    console.log("Attempting to unmute...");
                    // First interaction: Enable Sound
                    video.muted = false;
                    video.volume = 1.0;
                    video.currentTime = 0; // Restart for impact
                    
                    const playPromise = video.play();
                    
                    if (playPromise !== undefined) {
                        playPromise.then(() => {
                            console.log("Video playing with sound (muted: " + video.muted + ")");
                            soundEnabled = true;
                            text.textContent = "PRESS TO LOGIN"; // Change prompt
                        }).catch(err => {
                            console.error("Audio allow failed:", err);
                            // Fallback if autoplay/unmute fails completely
                            window.location.href = "login.php";
                        });
                    }
                } else {
                    // Second interaction: Go to Login
                    window.location.href = "login.php";
                }
            };

            // Listeners
            window.addEventListener("click", handleInteraction);
            window.addEventListener("keydown", handleInteraction);
        });
    </script>
</body>

</html>