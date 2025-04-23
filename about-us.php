<?php
include 'index_header.php'; // Include the header
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - VibeReads Bookstore</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    
    <style>

        /* Style the hero section of the page */
        .mood-section {
            min-height: 100vh;
            background: linear-gradient(45deg, #f3f4f6, #ffffff);
            position: relative;
            overflow: hidden;
        }

        /* Style the floating book emojis */
        .floating-book {
            position: absolute;
            animation: float 6s ease-in-out infinite;
            opacity: 0.1;
            font-size: 4rem;
        }

        /* Define the animation for the floating book emojis */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        /* Style the cards that contain information about the bookstore */
        .mood-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .mood-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .hero-text {
            font-size: 3.5rem;
            background: linear-gradient(45deg, #2c3e50, #3498db);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .mood-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="mood-section">
        <!-- Container for floating book emojis -->
        <div id="floating-books"></div>

        <div class="container py-5">
            <!-- Hero section -->
            <div class="text-center mb-5 animate__animated animate__fadeIn">
                <h1 class="hero-text mb-4">Where Books Match Your Mood</h1>
                <p class="lead">Discover stories that resonate with your emotional journey</p>
            </div>

            <div class="row g-4">
                <!-- Section about the bookstore -->
                <div class="col-md-6 animate__animated animate__fadeInLeft">
                    <div class="mood-card p-4 h-100">
                        <h2 class="mb-4">Our Story 📚</h2>
                        <p>At Mood Matcher, we believe that every reader deserves to find the perfect book that matches their current emotional state. Our innovative approach combines the art of storytelling with the science of emotions to create a uniquely personal reading experience.</p>
                        <p>Founded by book lovers for book lovers, we've created a space where literature and emotions intertwine, helping you discover stories that truly resonate with your heart.</p>
                    </div>
                </div>

                <div class="col-md-6 animate__animated animate__fadeInRight">
                    <div class="mood-card p-4 h-100">
                        <h2 class="mb-4">How We Match 🎯</h2>
                        <p>Our sophisticated mood-matching algorithm considers:</p>
                        <ul class="list-unstyled">
                            <li class="mb-3">🌟 Your current emotional state</li>
                            <li class="mb-3">📊 Reading preferences and history</li>
                            <li class="mb-3">💫 Genre affinities</li>
                            <li>🤝 Community recommendations</li>
                        </ul>
                    </div>
                </div>

                <!-- Statistics section -->
                <div class="col-12 mt-5">
                    <div class="row g-4 text-center">
                        <div class="col-md-4 animate__animated animate__fadeInUp">
                            <div class="mood-card p-4">
                                <div class="mood-icon">📚</div>
                                <h3 class="counter" data-target="10000">0</h3>
                                <p>Books in Collection</p>
                            </div>
                        </div>
                        <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                            <div class="mood-card p-4">
                                <div class="mood-icon">😊</div>
                                <h3 class="counter" data-target="5000">0</h3>
                                <p>Happy Readers</p>
                            </div>
                        </div>
                        <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.4s">
                            <div class="mood-card p-4">
                                <div class="mood-icon">🎯</div>
                                <h3 class="counter" data-target="95">0</h3>
                                <p>Match Accuracy %</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to create floating book emojis
        function createFloatingBooks() {
            const bookEmojis = ['📚', '📖', '📕', '📗', '📘', '📙'];
            const container = document.getElementById('floating-books');
            
            for (let i = 0; i < 20; i++) {
                const book = document.createElement('div');
                book.className = 'floating-book';
                book.textContent = bookEmojis[Math.floor(Math.random() * bookEmojis.length)];
                book.style.left = `${Math.random() * 100}vw`;
                book.style.top = `${Math.random() * 100}vh`;
                book.style.animationDelay = `${Math.random() * 5}s`;
                container.appendChild(book);
            }
        }

        // Function to animate the counter numbers
        function animateCounter(counter) {
            const target = parseInt(counter.getAttribute('data-target'));
            const increment = target / 100;
            let current = 0;

            const updateCounter = () => {
                if (current < target) {
                    current += increment;
                    counter.textContent = Math.ceil(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            };

            updateCounter();
        }

        // Event listener for DOMContentLoaded
        document.addEventListener('DOMContentLoaded', () => {
            createFloatingBooks();
            
            // Intersection Observer to animate counters when they come into view
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            });

            // Observe each counter element
            document.querySelectorAll('.counter').forEach(counter => {
                observer.observe(counter);
            });
        });
    </script>
</body>
</html>

<?php
include 'index_footer.php'; // Include the footer
?>
