<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VibeReads Footer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        
        .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .social-icon:hover {
            transform: translateY(-4px) scale(1.1);
        }
        .facebook-icon {
            background-color: #e6f2ff;
            color: #1877f2;
        }
        .facebook-icon:hover {
            background-color: #1877f2;
            color: white !important;
        }
        .twitter-icon {
            background-color: #e6f3ff;
            color: #1da1f2;
        }
        .twitter-icon:hover {
            background-color: #1da1f2;
            color: white !important;
        }
        .instagram-icon {
            background-color: #fff0f5;
            color: #e1306c;
        }
        .instagram-icon:hover {
            background-color: #e1306c;
            color: white !important;
        }
        .linkedin-icon {
            background-color: #f0f7ff;
            color: #0a66c2;
        }
        .linkedin-icon:hover {
            background-color: #0a66c2;
            color: white !important;
        }

       
        .footer-links a {
            color: #495057;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .footer-links a:hover {
            color: #0d6efd;
        }
        
        @media (max-width: 768px) {
    .dropdown-menu {
        display: none;
        position: static;
        float: none;
        width: 100%;
        margin-top: 0.5rem;
        background-color: transparent;
        border: none;
        box-shadow: none;
    }
    .dropdown-menu.show {
        display: block;
    }
    .dropdown-item {
        padding: 0.25rem 0;
        color: #495057;
    }
}


@media (min-width: 769px) {
    .dropdown-menu {
        display: none;
    }
    .dropdown:hover .dropdown-menu {
        display: block;
    }
}
    </style>
</head>
<body>
<footer class="bg-light py-5">
    <div class="container">
        <div class="row g-4">
            <!-- About Company -->
            <div class="col-12 col-md-3">
                <div class="footer-section-header d-flex justify-content-between align-items-center">
                    <h2 class="h4 mb-3">About VibeReads</h2>
                    <i class="fas fa-chevron-down d-md-none toggle-icon"></i>
                </div>
                <div class="footer-section-content">
                    <p class="text-muted small">
                        VibeReads is your ultimate destination for discovering and enjoying 
                        digital books that resonate with your reading vibe.
                    </p>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-12 col-md-3">
                <div class="footer-section-header d-flex justify-content-between align-items-center">
                    <h2 class="h4 mb-3">Quick Links</h2>
                    <i class="fas fa-chevron-down d-md-none toggle-icon"></i>
                </div>
                <div class="footer-section-content footer-links">
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php">Home</a></li>
                        <li class="mb-2 dropdown">
    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Category</a>
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="index.php#Adventure">Adventure</a></li>
        <li><a class="dropdown-item" href="index.php#Magical">Magic</a></li>
        <li><a class="dropdown-item" href="index.php#Knowledge">Knowledge</a></li>
    </ul>
</li>
                        <li class="mb-2"><a href="about-us.php">About Us</a></li>
                    </ul>
                </div>
            </div>

            <!-- Newsletter Subscription -->
            <div class="col-12 col-md-3">
                <div class="footer-section-header d-flex justify-content-between align-items-center">
                    <h2 class="h4 mb-3">Stay Updated</h2>
                    <i class="fas fa-chevron-down d-md-none toggle-icon"></i>
                </div>
                <div class="footer-section-content">
                    <form id="newsletterForm">
                        <div class="mb-3">
                            <input 
                                type="email" 
                                class="form-control" 
                                placeholder="Enter your email" 
                                required
                            >
                        </div>
                        <button 
                            type="submit" 
                            class="btn btn-primary w-100"
                        >
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            <!-- Social Media Links -->
            <div class="col-12 col-md-3">
                <div class="footer-section-header d-flex justify-content-between align-items-center">
                    <h2 class="h4 mb-3">Connect With Us</h2>
                    <i class="fas fa-chevron-down d-md-none toggle-icon"></i>
                </div>
                <div class="footer-section-content">
                    <div class="d-flex gap-3">
                        <a href="https://web.facebook.com/ag.wijekoon" class="social-icon facebook-icon">
                            <i class="fab fa-facebook text-2xl"></i>
                        </a>
                        <a href="https://x.com/nethukzz" class="social-icon twitter-icon">
                            <i class="fab fa-twitter text-2xl"></i>
                        </a>
                        <a href="https://www.instagram.com/nethu_k_w/" class="social-icon instagram-icon">
                            <i class="fab fa-instagram text-2xl"></i>
                        </a>
                        <a href="https://www.linkedin.com/in/nethmi-wijekoon-b6460129a/" class="social-icon linkedin-icon">
                            <i class="fab fa-linkedin text-2xl"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Copyright Section -->
        <div class="mt-5 pt-3 border-top text-center">
            <p class="text-muted">
                Designed by Nethmi Wijekoon | Copyright &copy; 
                <span id="currentYear"></span> 
                VibeReads. All Rights Reserved.
            </p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<script>
    document.getElementById('currentYear').textContent = new Date().getFullYear();
    
    document.getElementById('newsletterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const email = this.querySelector('input').value;
        alert(`Thank you for subscribing with ${email}!`);
        this.reset();
    });
    
    // Mobile Section Toggle
    const footerSectionHeaders = document.querySelectorAll('.footer-section-header');
    footerSectionHeaders.forEach(header => {
        header.addEventListener('click', function() {
            // Only toggle on mobile
            if (window.innerWidth <= 768) {
                this.classList.toggle('active');
                
                // Hide other sections
                footerSectionHeaders.forEach(h => {
                    if (h !== this) {
                        h.classList.remove('active');
                    }
                });
            }
        });
    });
    
    // Category Dropdown Mobile Toggle
    const categoryDropdownToggle = document.querySelector('.dropdown-toggle');
    if (categoryDropdownToggle) {
        categoryDropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            if (window.innerWidth <= 768) {
                const dropdownMenu = this.nextElementSibling;
                dropdownMenu.classList.toggle('show');
            }
        });
    }
</script>
</body>
</html>