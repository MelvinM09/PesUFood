<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="refresh" content="0;url=http://localhost/PesUFood/">

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
            overflow: hidden; /* Prevent scrollbars */
            position: relative; /* For particle animation */
        }

        /* Background Gradient */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, #000000, #d4af37); /* Dark to gold gradient */
            z-index: -2; /* Ensure it's behind all elements */
        }

        /* Bright Light Source */
        body::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            background: radial-gradient(circle at 100% 50%, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 0) 70%); /* Bright light source */
            mix-blend-mode: screen; /* Blend mode for glowing effect */
            z-index: -1; /* Ensure it's behind particles but above gradient */
        }

        /* Maintenance Box */
        .maintenance-box {
            background: rgba(0, 0, 0, 0.9); /* Dark semi-transparent background */
            backdrop-filter: blur(10px); /* Frosted glass effect */
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 80%;
            max-width: 600px;
            text-align: center;
            animation: fadeInUp 1.5s ease-in-out;
            z-index: 10; /* Ensure the box is above the particles */
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Heading Styling */
        h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 15px;
            letter-spacing: 2px;
            color: rgba(255, 255, 255, 0.9); /* Bright white */
            text-shadow: 0 2px 5px rgba(255, 255, 255, 0.3); /* Subtle glow */
        }

        /* Paragraph Styling */
        p {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 25px;
            color: rgba(255, 255, 255, 0.7); /* Slightly transparent white */
        }

        /* Contact Link Styling */
        .contact {
            font-weight: bold;
            color: #ffeeba; /* Light orange */
            text-decoration: none;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .contact:hover {
            color: #fff8dc; /* Brighter yellow on hover */
            transform: scale(1.05); /* Slight zoom effect */
        }

        /* Thanks Note in Bottom-Right Corner */
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

        /* Particle Styling */
        .particle {
            position: absolute;
            background-color: white;
            border-radius: 50%;
            opacity: 0.5;
            pointer-events: none;
            z-index: 1; /* Ensure particles are behind the maintenance box */
        }
    </style>
</head>
<body>
    <!-- Background Text -->
    <!-- <div class="background-text">PesuFood</div> -->

    <!-- Maintenance Box -->
    <div class="maintenance-box">
        <!-- Icon -->
        <i class="icon">🛠️</i>
        <!-- Heading -->
        <h1>We're Under Maintenance</h1>
        <!-- Message -->
        <p>Thank you for your patience while we work on some improvements.<br>
        The site will be back shortly!</p>
        <!-- Contact -->
        <a href="mailto:admin@pesufood.com" class="contact">Contact: admin@pesufood.com</a>
    </div>

    <!-- Thanks Note -->
    <div class="thanks-note">
        Thanks - <a href="https://example.com">Melvin.M</a>
    </div>

    <!-- Script for Randomly Moving Dots -->
    <script>
        // Function to create particles
        function createParticles(count) {
            const body = document.body;

            for (let i = 0; i < count; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');

                // Random size
                const size = Math.random() * 4 + 2; // Between 2px and 6px
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;

                // Append to body
                body.appendChild(particle);

                // Animate particle
                animateParticle(particle);
            }
        }

        // Function to animate particles
        function animateParticle(particle) {
            const bodyRect = document.body.getBoundingClientRect();
            const maxWidth = bodyRect.width;
            const maxHeight = bodyRect.height;

            // Initial random position
            let left = Math.random() * maxWidth;
            let top = Math.random() * maxHeight;

            // Random base velocity
            let baseVelocityX = (Math.random() - 0.5) * 2; // Between -1 and 1
            let baseVelocityY = (Math.random() - 0.5) * 2; // Between -1 and 1

            // Current velocity (modified by mouse interaction)
            let velocityX = baseVelocityX;
            let velocityY = baseVelocityY;

            // Mouse interaction variables
            let mouseX = null;
            let mouseY = null;

            // Update mouse position
            document.addEventListener('mousemove', (e) => {
                mouseX = e.clientX;
                mouseY = e.clientY;
            });

            // Animation loop
            function updatePosition() {
                const rect = particle.getBoundingClientRect();

                // Reset velocity back to base velocity over time
                velocityX += (baseVelocityX - velocityX) * 0.05; // Gradual reset
                velocityY += (baseVelocityY - velocityY) * 0.05; // Gradual reset

                // Move away from mouse if close
                if (mouseX !== null && mouseY !== null) {
                    const distanceX = mouseX - rect.left;
                    const distanceY = mouseY - rect.top;
                    const distance = Math.sqrt(distanceX ** 2 + distanceY ** 2);

                    if (distance < 100) { // If mouse is within 100px of the particle
                        velocityX -= distanceX / 1000; // Push away horizontally
                        velocityY -= distanceY / 1000; // Push away vertically
                    }
                }

                // Update position
                left += velocityX;
                top += velocityY;

                // Bounce off edges
                if (left < 0 || left + particle.offsetWidth > maxWidth) {
                    velocityX *= -1;
                }
                if (top < 0 || top + particle.offsetHeight > maxHeight) {
                    velocityY *= -1;
                }

                particle.style.left = `${left}px`;
                particle.style.top = `${top}px`;

                requestAnimationFrame(updatePosition);
            }

            // Start animation
            updatePosition();
        }

        // Create 20 particles
        createParticles(40);
    </script>
</body>
</html>