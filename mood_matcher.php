<?php
// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'vibeReadsdb') or die('connection failed');
session_start();

if (isset($_POST['add_to_cart'])) {
    $response = array();
    
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $book_id = mysqli_real_escape_string($conn, $_POST['book_id']);
        $book_name = mysqli_real_escape_string($conn, $_POST['book_name']);
        $book_price = mysqli_real_escape_string($conn, $_POST['book_price']);
        $book_image = mysqli_real_escape_string($conn, $_POST['book_image']);
        $book_quantity = 1;

        $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE book_id = '$book_id' AND user_id = '$user_id'");
        if (!$check_cart_numbers) {
            $response = array('status' => 'error', 'message' => 'Query failed: ' . mysqli_error($conn));
            echo json_encode($response);
            exit;
        }

        if (mysqli_num_rows($check_cart_numbers) > 0) {
            $response = array('status' => 'error', 'message' => 'Already added to cart!');
        } else {
            $insert_query = "INSERT INTO `cart` (user_id, book_id, name, price, quantity, image) 
                 VALUES ('$user_id', '$book_id', '$book_name', '$book_price', '$book_quantity', '$book_image')";

            if (mysqli_query($conn, $insert_query)) {
                $response = array('status' => 'success', 'message' => 'Book added to cart!');
            } else {
                $response = array('status' => 'error', 'message' => 'Insert query failed: ' . mysqli_error($conn));
            }
        }
    } else {
        $response = array('status' => 'redirect', 'message' => 'Please login first!', 'redirect' => 'login.php');
    }
    
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        // If AJAX request, return JSON response
        echo json_encode($response);
        exit;
    } else {
        // If regular form submission, set message array
        $message[] = $response['message'];
        if ($response['status'] == 'redirect') {
            header('Location: login.php');
            exit;
        }
    }
}

 
 
// Process mood matching form submission
if(isset($_POST['match_mood'])) {
    $mood = mysqli_real_escape_string($conn, $_POST['primary_mood']);
    $genre = mysqli_real_escape_string($conn, $_POST['genre_preference']);
    
    // Query to find matching books
    $query = "SELECT * FROM book_info WHERE mood LIKE '%$mood%' AND category LIKE '%$genre%'";
    $result = mysqli_query($conn, $query);
    
    // If no exact matches, try finding books just by mood
    if(mysqli_num_rows($result) == 0) {
        $query = "SELECT * FROM book_info WHERE mood LIKE '%$mood%'";
        $result = mysqli_query($conn, $query);
    }
    
    // Display results section
    if(mysqli_num_rows($result) > 0) {
        // Display messages if any
        if(isset($message)){
            foreach($message as $message){
                echo '<div class="message" onclick="this.remove();">'.$message.'</div>';
            }
        }

        echo '<section class="py-16 bg-gray-50"><div class="container mx-auto px-4">';
        echo '<h2 class="text-3xl font-bold mb-8 text-center">Books That Match Your Mood</h2>';
        echo '<div class="grid grid-cols-1 md:grid-cols-3 gap-8">';
        
        while($book = mysqli_fetch_assoc($result)) {
            ?>
            <div class="book-card rounded-xl overflow-hidden shadow-lg bg-white">
                <div class="relative">
                    <?php if(!empty($book['image'])): ?>
                        <img src="added_books/<?php echo htmlspecialchars($book['image']); ?>" 
                             alt="<?php echo htmlspecialchars($book['title']); ?>"
                             class="w-full h-64 object-cover"
                             onerror="this.src='images/default-book.jpg'">
                    <?php else: ?>
                        <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                            <span class="text-4xl">📚</span>
                        </div>
                    <?php endif; ?>
                    <div class="absolute top-0 right-0 bg-indigo-500 text-white px-3 py-1 m-2 rounded-full">
                        <?php echo htmlspecialchars($book['category']); ?>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($book['name']); ?></p>
                    <div class="flex justify-between items-center">
                        <span class="text-indigo-600 font-bold">RS<?php echo htmlspecialchars($book['price']); ?></span>
                        <div class="flex space-x-2">
                           <!-- <a href="book_details.php?bid=<?php echo $book['bid']; ?>" 
                               class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600 transition">
                                View Details
                            </a>-->
                            <!-- Update the form button in the book display section to include JavaScript handling -->
<form action="" method="post" class="inline" onsubmit="return handleAddToCart(event, <?php echo $book['bid']; ?>)">
    <input type="hidden" name="book_id" value="<?php echo $book['bid']; ?>">
    <input type="hidden" name="book_name" value="<?php echo $book['title']; ?>">
    <input type="hidden" name="book_price" value="<?php echo $book['price']; ?>">
    <input type="hidden" name="book_image" value="<?php echo $book['image']; ?>">
    <button type="submit" name="add_to_cart" 
            class="bg-green-500 text-white px-3 py-2 rounded-lg hover:bg-green-600 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
    </button>
</form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        
        echo '</div></div></section>';
    } else {
        echo '<div class="text-center py-16">
                <p class="text-xl text-gray-600">No books found matching your current mood and preferences. 
                Try adjusting your selections!</p>
              </div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mood Matcher - VibeReads</title>
    <link rel="stylesheet" href="css/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .hero-section {
            background: linear-gradient(135deg, #6366F1 0%, #A855F7 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.1;
        }

        .mood-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            background: white;
            
        }

        .mood-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: #6366F1;
        }

        .mood-card.selected {
            background: #EEF2FF;
            border-color: #6366F1;
        }

        .intensity-slider {
            -webkit-appearance: none;
            height: 8px;
            border-radius: 4px;
            background: #E5E7EB;
            outline: none;
        }

        .intensity-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #6366F1;
            cursor: pointer;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .intensity-slider::-webkit-slider-thumb:hover {
            transform: scale(1.1);
        }

        .custom-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236366F1'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1.5em;
        }

        .book-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
        }

        .book-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .submit-button {
            background: linear-gradient(135deg, #6366F1 0%, #A855F7 100%);
            transition: all 0.3s ease;
        }

        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }

        .mood-emoji {
            font-size: 2.5rem;
            transition: transform 0.3s ease;
        }

        .mood-card:hover .mood-emoji {
            transform: scale(1.2);
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .floating-emoji {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="min-h-screen">
    <?php include 'index_header.php'; ?>
    
    <!-- Hero Section -->
    <section class="hero-section relative text-white py-24">
        <div class="container mx-auto px-4 text-center relative z-10">
            <h1 class="text-6xl font-bold mb-6 leading-tight">Find Your Perfect Read</h1>
            <p class="text-xl mb-8 max-w-2xl mx-auto opacity-90">Let your mood guide you to your next literary adventure. We'll match you with books that resonate with your current state of mind.</p>
            <div class="flex justify-center space-x-8">
                <span class="floating-emoji text-4xl" style="animation-delay: 0s">📚</span>
                <span class="floating-emoji text-4xl" style="animation-delay: 0.5s">💭</span>
                <span class="floating-emoji text-4xl" style="animation-delay: 1s">✨</span>
            </div>
        </div>
    </section>

    <!-- Mood Matching Form -->
    <section class="py-20">
        <div class="container mx-auto px-4 max-w-4xl">
            <form method="POST" action="" class="bg-white rounded-2xl shadow-xl p-8 transform -mt-20">
                <!-- Mood Selection -->
                <div class="mb-12">
                    <h3 class="text-2xl font-semibold mb-6">How are you feeling today?</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <?php
                        $moods = [
                            'Happy' => '😊',
                            'Reflective' => '🤔',
                            'Adventurous' => '🏃‍♂️',
                            'Romantic' => '❤️',
                            'Peaceful' => '😌',
                            'Excited' => '🤩',
                            'Curious' => '🧐',
                            'Inspired' => '✨'
                        ];
                        
                        foreach ($moods as $mood => $emoji) {
                            echo "
                            <label class='mood-card cursor-pointer rounded-xl p-6 text-center'>
                                <input type='radio' name='primary_mood' value='$mood' class='hidden' required>
                                <span class='mood-emoji block mb-4'>$emoji</span>
                                <span class='block font-medium text-gray-800'>$mood</span>
                            </label>";
                        }
                        ?>
                    </div>
                </div>

                <!-- Genre -->
                <div class="mb-12">
                    <h3 class="text-2xl font-semibold mb-6">What kind of story interests you right now?</h3>
                    <select name="genre_preference" class="custom-select w-full p-4 border bg-white border-blue-200 rounded-xl text-blue-700 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-all" required>
                        <option value="">Select a genre...</option>
                        <option value="Fiction">Fiction</option>
                        <option value="Mystery">Mystery</option>
                        <option value="Romance">Romance</option>
                        <option value="Adventure">Adventure</option>
                        <option value="Fantasy">Fantasy</option>
                        <option value="Science Fiction">Science Fiction</option>
                        <option value="Non-Fiction">Non-Fiction</option>
                        <option value="Self-Help">Self-Help</option>
                    </select>
                </div>


                <button type="submit" name="match_mood" class="submit-button w-full py-4 px-6 rounded-xl text-white text-lg font-semibold relative overflow-hidden">
                    Find My Perfect Books
                </button>
            </form>
        </div>
    </section>
    

   

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mood card selection
        const moodCards = document.querySelectorAll('.mood-card');
        moodCards.forEach(card => {
            const input = card.querySelector('input[type="radio"]');
            
            card.addEventListener('click', function() {
                // Remove selected class from all cards
                moodCards.forEach(c => c.classList.remove('selected'));
                
                // Add selected class to clicked card
                if (input.checked) {
                    card.classList.add('selected');
                }
            });
        });

        // Intensity slider visual feedback
        const intensitySlider = document.querySelector('.intensity-slider');
        const updateSliderBackground = (slider) => {
            const value = (slider.value - slider.min) / (slider.max - slider.min) * 100;
            slider.style.background = `linear-gradient(to right, 
                #6366F1 0%, 
                #A855F7 ${value}%, 
                #E5E7EB ${value}%, 
                #E5E7EB 100%)`;
        };

        if (intensitySlider) {
            updateSliderBackground(intensitySlider); 
            intensitySlider.addEventListener('input', function() {
                updateSliderBackground(this);
            });
        }

        // Smooth scroll to results
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            
            setTimeout(() => {
                const results = document.querySelector('.matched-books');
                if (results) {
                    results.scrollIntoView({ behavior: 'smooth' });
                }
            }, 500);
        });

        // Add loading state to submit button
        const submitButton = document.querySelector('button[name="match_mood"]');
        submitButton.addEventListener('click', function() {
            this.innerHTML = `
                <span class="inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Finding your perfect books...
                </span>
            `;
        });
    });
    function addToCart(bookId) {
            // Add AJAX call to add item to cart
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    book_id: bookId
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Update cart count
                    const cartCount = document.querySelector('.badge');
                    cartCount.textContent = parseInt(cartCount.textContent) + 1;
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>

    <?php include 'index_footer.php'; ?>
</body>
</html>