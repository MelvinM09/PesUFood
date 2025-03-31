<?php
session_start();
include_once "connection/connect.php";

// Check maintenance mode status
$query = "SELECT setting_value FROM settings WHERE setting_key = 'maintenance_mode'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if (!$row || $row['setting_value'] !== '1') {
    // If maintenance mode is off, redirect back to the stored page or index.php
    $redirect_to = $_SESSION['return_to'] ?? 'index.php';
    unset($_SESSION['return_to']);
    header("Location: $redirect_to");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maintenance Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* General Styles */
        body {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* Background Gradient */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, #000000, #d4af37);
            z-index: -2;
        }

        /* Bright Light Source */
        body::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            background: radial-gradient(circle at 100% 50%, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 0) 70%);
            mix-blend-mode: screen;
            z-index: -1;
        }

        /* Maintenance Box */
        .maintenance-box {
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 80%;
            max-width: 600px;
            text-align: center;
            animation: fadeInUp 1.5s ease-in-out;
            z-index: 10;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 15px;
            letter-spacing: 2px;
            color: rgba(255, 255, 255, 0.9);
            text-shadow: 0 2px 5px rgba(255, 255, 255, 0.3);
        }

        p {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 25px;
            color: rgba(255, 255, 255, 0.7);
        }

        .contact {
            font-weight: bold;
            color: #ffeeba;
            text-decoration: none;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .contact:hover {
            color: #fff8dc;
            transform: scale(1.05);
        }

        .thanks-note {
            position: fixed;
            bottom: 10px;
            right: 10px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            font-style: italic;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        .thanks-note a {
            color: #ffeeba;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .thanks-note a:hover {
            color: #fff8dc;
        }

        .particle {
            position: absolute;
            background-color: white;
            border-radius: 50%;
            opacity: 0.5;
            pointer-events: none;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div class="maintenance-box">
        <i class="icon">🛠️</i>
        <h1>We're Under Maintenance</h1>
        <p>Thank you for your patience while we work on some improvements.<br>
        The site will be back shortly!</p>
        <a href="mailto:admin@pesufood.com" class="contact">Contact: admin@pesufood.com</a>
    </div>

    <div class="thanks-note">
        Thanks - <a href="https://example.com">Melvin.M</a>
    </div>

    <script>
        function createParticles(count) {
            const body = document.body;
            for (let i = 0; i < count; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                const size = Math.random() * 4 + 2;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                body.appendChild(particle);
                animateParticle(particle);
            }
        }

        function animateParticle(particle) {
            const bodyRect = document.body.getBoundingClientRect();
            const maxWidth = bodyRect.width;
            const maxHeight = bodyRect.height;
            let left = Math.random() * maxWidth;
            let top = Math.random() * maxHeight;
            let baseVelocityX = (Math.random() - 0.5) * 2;
            let baseVelocityY = (Math.random() - 0.5) * 2;
            let velocityX = baseVelocityX;
            let velocityY = baseVelocityY;
            let mouseX = null;
            let mouseY = null;

            document.addEventListener('mousemove', (e) => {
                mouseX = e.clientX;
                mouseY = e.clientY;
            });

            function updatePosition() {
                const rect = particle.getBoundingClientRect();
                velocityX += (baseVelocityX - velocityX) * 0.05;
                velocityY += (baseVelocityY - velocityY) * 0.05;

                if (mouseX !== null && mouseY !== null) {
                    const distanceX = mouseX - rect.left;
                    const distanceY = mouseY - rect.top;
                    const distance = Math.sqrt(distanceX ** 2 + distanceY ** 2);
                    if (distance < 100) {
                        velocityX -= distanceX / 1000;
                        velocityY -= distanceY / 1000;
                    }
                }

                left += velocityX;
                top += velocityY;
                if (left < 0 || left + particle.offsetWidth > maxWidth) velocityX *= -1;
                if (top < 0 || top + particle.offsetHeight > maxHeight) velocityY *= -1;

                particle.style.left = `${left}px`;
                particle.style.top = `${top}px`;
                requestAnimationFrame(updatePosition);
            }

            updatePosition();
        }

        createParticles(40);
    </script>
</body>
</html>