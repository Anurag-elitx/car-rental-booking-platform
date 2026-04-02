<?php
    require_once 'includes/db_connection.php';
    require_once 'includes/auth_functions.php';

    // Fetch available cars (Prioritize user requested top 3)
    $stmt = $pdo->query("SELECT * FROM cars 
                        ORDER BY 
                            CASE 
                                WHEN model = 'Test Audi R8' THEN 1
                                WHEN model = 'Volt Hyperion Mk1' THEN 2
                                WHEN model = 'Rosso Corsa Spyder' THEN 3
                                ELSE 4
                            END ASC, 
                            created_at DESC");
    $cars = $stmt->fetchAll();

    // Handle Booking Submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rent_car'])) {
        if (!isLoggedIn()) {
            header("Location: login.php");
            exit();
        }
        if (isAgency()) {
            $booking_error = "Agencies cannot book cars.";
        } else {
            $car_id = $_POST['car_id'];
            $start_date = $_POST['start_date'];
            $days = $_POST['days'];
            $rent_per_day = $_POST['rent_per_day'];
            $total_price = $rent_per_day * $days;
            $customer_id = $_SESSION['user_id'];

            $insert = $pdo->prepare("INSERT INTO bookings (customer_id, car_id, start_date, duration_days, total_price) VALUES (?, ?, ?, ?, ?)");
            $insert->execute([$customer_id, $car_id, $start_date, $days, $total_price]);
            $booking_success = "Car booked successfully!";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obsidian Automotive | Elite Automotive Experiences</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Flatpickr for Elite Date Picking -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Feather Icons for the fleet UI -->
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">Obsidian Automotive</a>
        
        <div class="mx-auto d-none d-lg-flex">
            <a href="#home" class="nav-link">Home</a>
            <a href="#about" class="nav-link">About Us</a>
            <a href="#fleet" class="nav-link">Our Fleet</a>
            <a href="#how-it-works" class="nav-link">How It Works</a>
            <a href="#faq" class="nav-link">FAQ</a>
            <a href="#contact" class="nav-link">Contact Us</a>
        </div>

        <div class="d-flex align-items-center gap-3">
            <?php if(isLoggedIn()): ?>
                <span class="text-secondary d-none d-md-inline">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <?php if(isAgency()): ?>
                    <a href="agency/dashboard.php" class="btn-pill btn-ghost">Dashboard</a>
                <?php endif; ?>
                <a href="login.php?logout=1" class="btn-pill btn-solid-white">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-pill btn-ghost">Log In</a>
                <a href="register.php" class="btn-pill btn-solid-white">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section id="home" class="hero-section">
    <div class="container">
        <h1 class="hero-headline text-white tracking-wide">Elite Automotive<br>Experiences, Redefined</h1>
        <p class="hero-subtitle text-gray">Experience elegance, power, and style with our handpicked luxury fleet.</p>
        
        <a href="#fleet" class="btn-pill btn-solid-white mb-5 d-inline-flex">Pick Your Perfect Drive</a>

        <div class="d-flex justify-content-center gap-2 mt-5" style="padding-top: 250px;">
            <div style="width:30px; height:4px; background:#fff; border-radius:2px;"></div>
            <div style="width:30px; height:4px; background:#444; border-radius:2px;"></div>
            <div style="width:30px; height:4px; background:#444; border-radius:2px;"></div>
        </div>

        <div class="brand-strip">
            <span>ARCFOX</span>
            <span>ATALANTA</span>
            <span>DRAKO</span>
            <span>ARRIVAL</span>
            <span>LYNK & CO</span>
        </div>
    </div>
</section>

<!-- About Us Section -->
<section id="about" class="section-spacing">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <img src="assets/images/about.png" class="about-image" alt="Rear view of luxury sports car">
            </div>
            <div class="col-lg-6 ps-lg-5">
                <div class="section-label">About Us</div>
                <h2 class="display-6 fw-bold mb-4">Elite Car Rentals with Refined Service & Unmatched Class</h2>
                <p class="mb-4">At Obsidian Automotive, we offer more than just cars – we deliver sophistication on wheels. Every ride is a blend of premium quality, precision performance, and white-glove service. Our commitment ensures your journey is not only stylish but seamless.</p>
                <p class="mb-5">We provide more than transport – we curate refined driving experiences with world-class vehicles, flawless service, and unmatched attention to detail. From first contact to final mile, we ensure luxury meets convenience at every turn.</p>
                <a href="#about" class="btn-pill btn-ghost">About Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Our Fleet -->
<section id="fleet" class="section-spacing">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <div class="section-label">Our Fleet</div>
                <h2 class="display-6 fw-bold mb-2">Browse Our Elite Fleet</h2>
                <p class="text-gray mb-0">From high-performance sports cars to refined executive sedans, our fleet suits every journey and lifestyle.</p>
            </div>
            <div class="d-flex gap-2">
                <button id="fleetPrev" class="carousel-btn position-relative transform-none"><i data-feather="chevron-left"></i></button>
                <button id="fleetNext" class="carousel-btn position-relative transform-none"><i data-feather="chevron-right"></i></button>
            </div>
        </div>

        <?php if(isset($booking_success)): ?>
            <div class="alert alert-success bg-dark text-success border-success"><?= $booking_success ?></div>
        <?php endif; ?>
        <?php if(isset($booking_error)): ?>
            <div class="alert alert-danger bg-dark text-danger border-danger"><?= $booking_error ?></div>
        <?php endif; ?>

        <div class="row mb-4 align-items-center">
            <div class="col-md-9 mb-3 mb-md-0">
                <div class="d-flex align-items-center gap-3">
                    <label class="text-white small fw-bold" style="letter-spacing: 1px;">CATEGORY</label>
                    <select id="categoryFilters" class="form-select bg-dark text-white border-secondary w-auto" style="font-size:13px; padding: 0.6rem 2.5rem 0.6rem 1rem; cursor: pointer;">
                        <option value="All">All Vehicles</option>
                        <?php 
                        $cats = ['SUV', 'Sedan', 'Hatchback', 'Coupe', 'MPV', 'Convertible', 'Wagon', 'Pickup Truck', 'Crossover'];
                        foreach($cats as $c): 
                        ?>
                        <option value="<?= $c ?>"><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <input type="text" id="fleetSearch" class="form-control" placeholder="Search vehicle models..." style="font-size:13px; padding: 0.6rem 1rem;">
            </div>
        </div>

        <div class="fleet-slider-container" id="fleetGrid">
            <?php 
            $i = 0;
            foreach($cars as $car):
                // Use DB image or fallback to default
                $img = !empty($car['image_filename']) ? $car['image_filename'] : 'fleet1.png';
                $cat = !empty($car['category']) ? $car['category'] : 'Sedan';
                $i++;
            ?>
                <div class="col-md-6 col-lg-4 fleet-item-container" data-model="<?= htmlspecialchars($car['model']) ?>" data-category="<?= htmlspecialchars($cat) ?>">
                    <div class="fleet-card">
                        <img src="assets/images/<?= htmlspecialchars($img) ?>" class="fleet-img" alt="Car">
                        <div class="fleet-title mb-1"><?= htmlspecialchars($car['model']) ?></div>
                        <div class="text-muted small mb-3">
                            <span class="badge border border-secondary text-gray bg-transparent fw-normal me-2"><?= htmlspecialchars($car['vehicle_number']) ?></span>
                            ₹<?= number_format($car['rent_per_day'], 2) ?> / Day
                        </div>
                        
                        <div class="fleet-icons">
                            <div><i data-feather="box" width="16"></i><span>Luxury</span></div>
                            <div><i data-feather="users" width="16"></i><span><?= $car['seating_capacity'] ?> Seats</span></div>
                            <div><i data-feather="grid" width="16"></i><span>4 Door</span></div>
                            <div><i data-feather="settings" width="16"></i><span>Auto</span></div>
                        </div>
                        
                        <!-- Booking Mechanism inside a Modal or Dropdown, simplified for UI -->
                        <form method="POST" class="mt-auto pt-3 border-top border-dark">
                            <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                            <input type="hidden" name="rent_per_day" value="<?= $car['rent_per_day'] ?>">
                            
                            <?php if(isLoggedIn()): ?>
                                <?php if(isCustomer()): ?>
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <label class="text-muted small mb-1" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Start Date</label>
                                            <input type="text" name="start_date" class="form-control datepicker" placeholder="Select Date" required style="font-size: 14px; padding: 0.6rem 0.75rem;">
                                        </div>
                                        <div class="col-6">
                                            <label class="text-muted small mb-1" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Duration</label>
                                            <select name="days" class="form-select days-dropdown" required style="font-size: 14px; padding: 0.6rem 0.75rem;" onchange="toggleCustomDays(this)">
                                                <option value="1">1 Day</option>
                                                <option value="2">2 Days</option>
                                                <option value="3">3 Days</option>
                                                <option value="7">7 Days</option>
                                                <option value="14">14 Days</option>
                                                <option value="custom">Custom...</option>
                                            </select>
                                            <input type="number" class="form-control custom-days-input" style="font-size: 14px; padding: 0.6rem 0.75rem; display: none;" placeholder="No. of days" min="1">
                                        </div>
                                    </div>
                                    <button type="submit" name="rent_car" class="btn-pill btn-fleet w-100 mt-2">Rent Vehicle</button>
                                <?php else: ?>
                                    <button disabled class="btn-pill btn-fleet w-100 opacity-50">Agency Cannot Book</button>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="login.php" class="btn-pill btn-fleet w-100 text-center">Rent Vehicle</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if(empty($cars)): ?>
                <!-- Hardcoded placeholders if DB is empty so the UI still looks like Obsidian Automotive -->
                <div class="col-md-6 col-lg-4">
                    <div class="fleet-card">
                        <img src="assets/images/fleet1.png" class="fleet-img" alt="Porsche">
                        <div class="fleet-title mb-3">Porsche 911 GT3 - 2025</div>
                        <div class="fleet-icons">
                            <div><i data-feather="box" width="16"></i><span>Sports Car</span></div>
                            <div><i data-feather="users" width="16"></i><span>4 Seater</span></div>
                            <div><i data-feather="grid" width="16"></i><span>2 Door</span></div>
                            <div><i data-feather="settings" width="16"></i><span>Auto</span></div>
                        </div>
                        <button class="btn-pill btn-solid-white w-100">View Details</button>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="fleet-card">
                        <img src="assets/images/fleet2.png" class="fleet-img" alt="BMW">
                        <div class="fleet-title mb-3">BMW 7 Series - 2025</div>
                        <div class="fleet-icons">
                            <div><i data-feather="box" width="16"></i><span>Sports Car</span></div>
                            <div><i data-feather="users" width="16"></i><span>5 Seater</span></div>
                            <div><i data-feather="grid" width="16"></i><span>4 Door</span></div>
                            <div><i data-feather="settings" width="16"></i><span>Auto</span></div>
                        </div>
                        <button class="btn-pill btn-ghost w-100">View Details</button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- How It Works Section (Image 1) -->
<section id="how-it-works" class="section-spacing">
    <div class="container">
        <div class="mb-5">
            <div class="section-label">How It Works</div>
            <h2 class="display-6 fw-bold mb-4">Luxury Rentals Made<br>Effortless</h2>
            <p class="text-gray mb-0" style="max-width: 450px;">From high-performance sports cars to refined executive sedans, our fleet suits every journey and lifestyle.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="how-it-works-card">
                    <h4>Select Your<br>Dream Car</h4>
                    <p class="text-muted small mt-auto opacity-75">Browse our premium collection and select a vehicle that suits your style and needs.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="how-it-works-card">
                    <h4>Book Instantly<br>Online</h4>
                    <p class="text-muted small mt-auto opacity-75">Complete your reservation through our website with secure and quick payment options.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="how-it-works-card">
                    <h4>Pick Up or Get<br>Delivery</h4>
                    <p class="text-muted small mt-auto opacity-75">Enjoy the convenience of car delivery anywhere in the UAE or collect it from our location.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="how-it-works-card">
                    <h4>Enjoy Every<br>Mile</h4>
                    <p class="text-muted small mt-auto opacity-75">Experience effortless luxury and outstanding performance on the road.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section (Image 2) -->
<section id="faq" class="section-spacing">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <div class="section-label">FAQ</div>
                <h2 class="display-6 fw-bold mb-4">Got questions? We've<br>got answers!</h2>
                <p class="text-gray mb-0">Here are some of the most common questions about our car rental service, with clear answers to ensure a smooth experience</p>
            </div>
            <div class="col-lg-7 ps-lg-5">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q1">
                                Can I get the car delivered to a different location?
                            </button>
                        </h2>
                        <div id="q1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Absolutely. We offer a "Bespoke Delivery" service. You can have your obsidian vehicle delivered directly to your doorstep in Dubai, your hotel in Abu Dhabi, or even meet us at the airport arrival gate.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q2">
                                How can I book a car?
                            </button>
                        </h2>
                        <div id="q2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                The process is streamlined for elite users: 1. Browse our fleet above. 2. Select your prestige vehicle. 3. Choose your dates and duration. 4. Confirm your booking instantly through your personalized dashboard.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q3">
                                What is the minimum rental period?
                            </button>
                        </h2>
                        <div id="q3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                To ensure the highest standard of service, our minimum rental period is 24 hours (1 full day). For extended stays, we offer sophisticated long-term leasing packages.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q4">
                                Does the price include insurance?
                            </button>
                        </h2>
                        <div id="q4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Every rental at Obsidian Automotive comes with comprehensive premium insurance coverage as standard. We prioritize your total peace of mind during every mile of your journey.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q5">
                                What documents do I need to rent a car?
                            </button>
                        </h2>
                        <div id="q5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Residents require a valid UAE driving license and Emirates ID. International travelers require a valid passport, visa entry stamp, and a driving license from their home country (with an IDP where applicable).
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer (Image 2 Redesign) -->
<footer id="contact" class="footer">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <h3 class="navbar-brand mb-4 d-block">Obsidian Automotive</h3>
                <p class="text-muted small mb-4 pe-lg-5">Obsidian Automotive – Your trusted partner for premium car rentals since 2020. Making your journey comfortable and memorable.</p>
                <div class="d-flex gap-2">
                    <a href="#" class="social-icon"><i data-feather="facebook"></i></a>
                    <a href="#" class="social-icon"><i data-feather="twitter"></i></a>
                    <a href="#" class="social-icon"><i data-feather="linkedin"></i></a>
                </div>
            </div>
            <div class="col-lg-2">
                <h6 class="text-white fw-bold mb-4">Quick Links</h6>
                <div class="footer-links">
                    <a href="#home">Home</a>
                    <a href="#about">About Us</a>
                    <a href="#fleet">Our Fleet</a>
                    <a href="#how-it-works">How It Works</a>
                    <a href="#faq">FAQ</a>
                </div>
            </div>
            <div class="col-lg-3">
                <h6 class="text-white fw-bold mb-4">Contact</h6>
                <div class="footer-links">
                    <span class="text-muted small d-block mb-2">123 Rental Street,<br>Dubai, Abu Dhabi</span>
                    <span class="text-muted small d-block mb-3">+1 (555) 123-4567</span>
                    <span class="text-muted small d-block">info@obsidianauto.com</span>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="newsletter-block">
                    <h6 class="text-white fw-bold mb-4">Newsletter</h6>
                    <input type="email" class="newsletter-input" placeholder="Enter Your Email Address">
                    <button class="btn-subscribe">Subscribe</button>
                </div>
            </div>
        </div>
        <div class="border-top border-dark mt-5 pt-4 d-flex justify-content-between align-items-center">
            <p class="text-muted small m-0">&copy; 2026 Obsidian Automotive. All rights reserved.</p>
            <div class="d-flex gap-4">
                <a href="#" class="text-muted small text-decoration-none">Privacy Policy</a>
                <a href="#" class="text-muted small text-decoration-none">Terms & Conditions</a>
            </div>
        </div>
        <div class="footer-watermark">OBSIDIAN</div>
    </div>
</footer>

<!-- Bootstrap JS Bundle (Required for Navbar & Accordions) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    feather.replace();

    // Custom Days Input Toggle
    function toggleCustomDays(selectElement) {
        if (selectElement.value === 'custom') {
            let input = selectElement.nextElementSibling;
            selectElement.style.display = 'none';
            selectElement.removeAttribute('name');
            input.style.display = 'block';
            input.setAttribute('name', 'days');
            input.setAttribute('required', 'true');
            input.focus();
        }
    }

    // Fleet Real-Time Search & Category Filter
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize Flatpickr on all date inputs
        flatpickr(".datepicker", {
            minDate: "today",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
        });

        const searchInput = document.getElementById('fleetSearch');
        const filterSelect = document.getElementById('categoryFilters');
        const fleetCards = document.querySelectorAll('.fleet-item-container');

        function filterFleet() {
            const searchTerm = searchInput.value.toLowerCase();
            const activeCategory = filterSelect.value;

            fleetCards.forEach(card => {
                const title = card.getAttribute('data-model').toLowerCase();
                const category = card.getAttribute('data-category');
                
                const matchesSearch = title.includes(searchTerm);
                const matchesCategory = (activeCategory === 'All' || category === activeCategory);

                if (matchesSearch && matchesCategory) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        if(searchInput) searchInput.addEventListener('input', filterFleet);
        if(filterSelect) filterSelect.addEventListener('change', filterFleet);

        // Slider Navigation Logic
        const fleetGrid = document.getElementById('fleetGrid');
        const nextBtn = document.getElementById('fleetNext');
        const prevBtn = document.getElementById('fleetPrev');

        if(nextBtn && prevBtn && fleetGrid) {
            nextBtn.addEventListener('click', () => {
                const cardWidth = document.querySelector('.fleet-item-container').offsetWidth + 24; // Width + gap
                fleetGrid.scrollBy({ left: cardWidth, behavior: 'smooth' });
            });
            prevBtn.addEventListener('click', () => {
                const cardWidth = document.querySelector('.fleet-item-container').offsetWidth + 24; // Width + gap
                fleetGrid.scrollBy({ left: -cardWidth, behavior: 'smooth' });
            });
        }
    });

    // Simple Navbar Background transition
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            document.querySelector('.navbar').style.background = 'rgba(10, 10, 10, 0.98)';
            document.querySelector('.navbar').style.borderBottom = '1px solid var(--border-color)';
        } else {
            document.querySelector('.navbar').style.background = 'transparent';
            document.querySelector('.navbar').style.borderBottom = '1px solid transparent';
        }
    });

    // Smooth scrolling anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            if(this.getAttribute('href') !== '#') {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
</script>
</body>
</html>
<?php
// LOGOUT LOGIC
if (isset($_GET['logout'])) {
    logoutUser();
}
?>
