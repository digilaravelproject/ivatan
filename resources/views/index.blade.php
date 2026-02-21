<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>i-Vatan (iApp) - India’s Own Social Future & Digital Space</title>

    <meta name="description"
        content="i-Vatan (iApp) is India’s own secure, decentralized, and trust-first social future platform.">
    <link rel="canonical" href="https://www.ivatan.in/">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* New Black & White Color Palette */
        :root {
            --primary-accent: #000000;
            --dark-bg: #1A1A1A;
            --light-bg: #F8F8F8;
            --text-dark: #333333;
            --text-light: #666666;
            --border-color: #E0E0E0;
        }

        /* Base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            background-color: white;
        }

        /* Scroll Progress */
        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 4px;
            background: var(--primary-accent);
            z-index: 9999;
            width: 0%;
            transition: width 0.1s;
        }

        /* Navbar */
        .navbar {
            padding: 1rem 0;
            background: white;
            transition: all 0.3s;
            border-bottom: 1px solid var(--border-color);
        }

        .navbar.scrolled {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-accent) !important;
        }

        .nav-link {
            color: var(--text-light) !important;
            margin: 0 0.8rem;
            font-weight: 400;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: var(--primary-accent) !important;
        }

        .btn-download {
            background: var(--primary-accent);
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            border: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-download:hover {
            background: #333;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.8)), url('https://images.unsplash.com/photo-1519389950473-47ba0277781c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            padding: 120px 0 80px;
            position: relative;
            min-height: 650px;
            display: flex;
            align-items: center;
            margin-top: 60px;
        }

        /* Feature Sections */
        .feature-section {
            padding: 100px 0;
        }

        .feature-section:nth-child(even) {
            background: var(--light-bg);
        }

        .feature-content h2 {
            font-size: 2.5rem;
            font-weight: 300;
            color: var(--primary-accent);
            margin-bottom: 1.5rem;
        }

        .feature-content p {
            font-size: 1.1rem;
            color: var(--text-light);
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }

        .feature-link {
            color: var(--primary-accent);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .feature-link:hover {
            color: #666;
            transform: translateX(5px);
            display: inline-block;
        }

        .feature-mockup {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
        }

        .feature-mockup:hover {
            transform: scale(1.02);
        }

        /* Dark Section */
        .dark-section {
            background: var(--dark-bg);
            color: white;
            padding: 100px 0;
        }

        .dark-section h2 {
            color: white;
        }

        .dark-section p {
            color: #AAAAAA;
        }

        .accent-text {
            font-weight: 600;
            color: white;
            border-bottom: 2px solid white;
        }

        /* ========= SLIDER CSS (FIXED) ========= */
        .update-section {
            padding: 100px 0;
            background: var(--light-bg);
            overflow: hidden;
        }

        .slider-wrapper {
            width: 100%;
            overflow: hidden;
            /* Hides the cards that are off-screen */
            position: relative;
            padding: 20px 0;
        }

        .slider-container {
            display: flex;
            /* Lines items up horizontally */
            transition: transform 0.5s ease-in-out;
            /* Smooth sliding animation */
            width: 100%;
        }

        .slide-item {
            flex: 0 0 50%;
            /* Desktop: Show 2 items (50% width each) */
            max-width: 50%;
            padding: 0 15px;
            /* Spacing between cards */
        }

        .update-card-slider {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            height: 100%;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .update-card-slider:hover {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transform: translateY(-5px);
        }

        .whatsapp-icon-badge {
            width: 60px;
            height: 60px;
            background: var(--primary-accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .whatsapp-icon-badge i {
            color: white;
            font-size: 1.8rem;
        }

        .card-btn-slider {
            background: transparent;
            border: 2px solid var(--primary-accent);
            padding: 0.6rem 1.5rem;
            border-radius: 25px;
            color: var(--primary-accent);
            font-weight: 500;
            transition: all 0.3s;
            cursor: pointer;
            margin-top: 20px;
            align-self: flex-start;
        }

        .card-btn-slider:hover {
            background: var(--primary-accent);
            color: white;
        }

        .slider-nav-btn {
            border: 2px solid var(--primary-accent);
            background: white;
            color: var(--primary-accent);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            transition: all 0.3s;
            margin-left: 10px;
        }

        .slider-nav-btn:hover:not(:disabled) {
            background: var(--primary-accent);
            color: white;
        }

        .slider-nav-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
            border-color: #ccc;
            color: #ccc;
        }

        /* Footer */
        .footer {
            background: var(--dark-bg);
            color: white;
            padding: 60px 0 30px;
        }

        .footer h6 {
            color: white;
            margin-bottom: 1.2rem;
            font-weight: 600;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: #AAAAAA;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: white;
        }

        .social-icons a {
            color: #AAAAAA;
            margin-left: 15px;
            font-size: 1.2rem;
            transition: color 0.3s;
        }

        .social-icons a:hover {
            color: white;
        }

        /* Animation Classes */
        .fade-up {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        .fade-up.active {
            opacity: 1;
            transform: translateY(0);
        }

        .delay-1 {
            transition-delay: 0.2s;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }

            .slide-item {
                flex: 0 0 100%;
                max-width: 100%;
            }

            /* Mobile: Show 1 item */
            .hero-section {
                text-align: center;
            }

            .hero-content {
                margin-bottom: 40px;
            }
        }
    </style>
</head>

<body>

    <div class="scroll-progress" id="scrollProgress"></div>

    <nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">

                <i class="fas fa-seedling"></i> i-Vatan
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <!-- <li class="nav-item"><a class="nav-link" href="#">Features</a></li> -->
                    <li class="nav-item"><a class="nav-link" href="{{ route('privacy') }}">Privacy Policy</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('terms') }}">Terms and Conditions</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('trust') }}">Trust</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('quickhire') }}">QuickHire</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('market') }}">Mart</a></li>

                    <li class="nav-item ms-3">
                        <button class="btn btn-download">Download</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section" id="hero">
        <div class="container" style="position: relative; z-index: 2;">
            <div class="row w-100 align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <div class="hero-content fade-up">
                        <h1 style="color: white; font-weight: 300; margin-bottom: 20px;">India’s Own Social Future and
                            Digital Space</h1>
                        <p style="color: rgba(255,255,255,0.9); font-size: 1.2rem; margin-bottom: 30px;">
                            Your presence, activity, and integrity power this ecosystem. i-Vatan (iApp) is a secure,
                            decentralized, India-first platform built on a trust-first model.
                        </p>
                        <button class="btn btn-download btn-lg me-3" style="border: 1px solid white;">
                            <i class="fas fa-sign-in-alt"></i> Register Now
                        </button>
                        <a href="#compliance" class="btn btn-outline-light btn-lg">
                            Read Consent Terms
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="feature-section" id="ecosystem">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0 order-2 order-lg-1">
                    <div class="feature-image-wrapper fade-up">
                        <img src="https://images.unsplash.com/photo-1450101499163-c8848c66ca85?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                            alt="Legal Agreement" class="feature-mockup">
                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2">
                    <div class="feature-content fade-up delay-1">
                        <h2 class="fw-bold">Your Voluntary Agreement</h2>
                        <p>By accessing, installing, or interacting with i-Vatan (iApp), you fully agree to abide by the
                            latest Terms & Conditions and Privacy Policy as defined by Octroid Pvt. Ltd.. These
                            documents govern your entire interaction and may be updated periodically.</p>
                        <p class="text-muted small">The Application, Name, UI/UX Design, Software and Platform is under
                            the trademark authority.</p>
                        <a href="#compliance" class="feature-link">Review Full T&C <i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="feature-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="feature-content fade-up">
                        <h2 class="fw-bold">Data for Personalization & AI</h2>
                        <p>You grant permission to i-Vatan to collect, analyze, and store your profile and activity
                            data. This data is used for personalized content, **job/product recommendations, service
                            improvement, **AI training, and business performance tracking.</p>
                        <a href="#compliance" class="feature-link">Learn about Data Use <i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="feature-image-wrapper fade-up delay-1">
                        <img src="https://images.unsplash.com/photo-1677442136019-21780ecad995?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                            alt="Data Analytics and AI" class="feature-mockup">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="dark-section" id="trust">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="feature-content fade-up">
                        <div
                            style="display:inline-block; background: rgba(255,255,255,0.15); padding: 5px 15px; border-radius: 20px; margin-bottom: 15px;">
                            <i class="fas fa-shield-alt"></i> Trust-First Model
                        </div>
                        <h2 class="fw-bold">Secure, <span class="accent-text">Encrypted</span> Activity</h2>
                        <p>You understand that all your activity (posting, messaging, purchasing, job applying) are
                            fully 5-layer encrypted. Content may be monitored for quality control, legal compliance, and
                            fraud detection purposes.</p>
                        <a href="#compliance" class="feature-link" style="color: white;">TrustScore System <i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="feature-image-wrapper fade-up delay-1">
                        <img src="https://images.unsplash.com/photo-1563986768609-322da13575f3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                            alt="Security and Compliance" class="feature-mockup">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="feature-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0 order-2 order-lg-1">
                    <div class="feature-image-wrapper fade-up">
                        <img src="https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=800&q=80"
                            alt="Security Image" class="feature-mockup">

                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2">
                    <div class="feature-content fade-up delay-1">
                        <h2 class="fw-bold">Marketplace & Growth</h2>
                        <p>Certain user behavior, interaction data, and content may be used to generate commercial value
                            (e.g., AI-based personalization, marketplace trends). Revenue derived from such
                            platform-wide insights is the sole property of Octroid Pvt. Ltd..</p>
                        <a href="#compliance" class="feature-link">View Commercial Terms <i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="update-section" id="updates">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-md-7">
                    <h2 class="fade-up mb-2 fw-bold" style="text-align: left;">Development Roadmap</h2>
                    <p class="fade-up" style="text-align: left; color: var(--text-light);">The platform is being
                        released in a phased manner. Explore our core modules.</p>
                </div>
                <div class="col-md-5 text-end">
                    <button class="slider-nav-btn prev-btn" id="prevBtn"><i
                            class="fas fa-chevron-left"></i></button>
                    <button class="slider-nav-btn next-btn" id="nextBtn"><i
                            class="fas fa-chevron-right"></i></button>
                </div>
            </div>

            <div class="slider-wrapper">
                <div class="slider-container" id="sliderContainer">

                    <div class="slide-item">
                        <div class="update-card-slider">
                            <div>
                                <div class="whatsapp-icon-badge"><i class="fas fa-briefcase"></i></div>
                                <h5>i-QuickHire Job Portal</h5>
                                <p style="font-size: 0.95rem; color: #666;">A dedicated use-case for job seekers and
                                    employers. Interactions are governed by i-QuickHire specific policies.</p>
                            </div>
                            <button class="card-btn-slider">Explore QuickHire</button>
                        </div>
                    </div>

                    <div class="slide-item">
                        <div class="update-card-slider">
                            <div>
                                <div class="whatsapp-icon-badge"><i class="fas fa-store"></i></div>
                                <h5>i-Mart Marketplace</h5>
                                <p style="font-size: 0.95rem; color: #666;">Seamlessly access the i-Mart marketplace
                                    for product purchasing. Activity covered under i-Mart T&C.</p>
                            </div>
                            <button class="card-btn-slider">Shop on i-Mart</button>
                        </div>
                    </div>

                    <div class="slide-item">
                        <div class="update-card-slider">
                            <div>
                                <div class="whatsapp-icon-badge"><i class="fas fa-handshake"></i></div>
                                <h5>i-Trust TrustScore</h5>
                                <p style="font-size: 0.95rem; color: #666;">Operating on a trust-first model. Misuse of
                                    trust is not tolerated and affects your score globally.</p>
                            </div>
                            <button class="card-btn-slider">Read about Trust</button>
                        </div>
                    </div>

                    <div class="slide-item">
                        <div class="update-card-slider">
                            <div>
                                <div class="whatsapp-icon-badge"><i class="fas fa-cogs"></i></div>
                                <h5>Phased Rollout</h5>
                                <p style="font-size: 0.95rem; color: #666;">Features reflect aspirational statements.
                                    They are not guaranteed deliverables and may experience delays.</p>
                            </div>
                            <button class="card-btn-slider">View Roadmap</button>
                        </div>
                    </div>

                    <div class="slide-item">
                        <div class="update-card-slider">
                            <div>
                                <div class="whatsapp-icon-badge"><i class="fas fa-gavel"></i></div>
                                <h5>Dispute Resolution</h5>
                                <p style="font-size: 0.95rem; color: #666;">In case of concerns, users must first seek
                                    clarification from the Company through official channels.</p>
                            </div>
                            <button class="card-btn-slider">Get Support</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <h6>i-Vatan</h6>
                    <ul class="footer-links">
                        <li><a href="#">Features</a></li>
                        <li><a href="#">Security</a></li>
                        <li><a href="#">Download</a></li>
                        <li><a href="#">Web App</a></li>
                        <li><a href="#">Business</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Company</h6>
                    <ul class="footer-links">
                        <li><a href="#">About</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Brand Center</a></li>
                        <li><a href="#">Privacy</a></li>
                        <li><a href="#">Terms</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Download</h6>
                    <ul class="footer-links">
                        <li><a href="#">Android</a></li>
                        <li><a href="#">iPhone</a></li>
                        <li><a href="#">Mac/PC</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Help</h6>
                    <ul class="footer-links">
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">Security Advisories</a></li>
                    </ul>
                </div>
            </div>
            <hr style="border-color: #333; margin: 2rem 0;">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <p class="text-muted mb-0">&copy; 2024 Octroid Pvt. Ltd. | i-Vatan (iApp)</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {

            // ========== IMPROVED SLIDER LOGIC ==========
            let currentSlide = 0;
            const $sliderContainer = $('#sliderContainer');
            const $slideItems = $('.slide-item');
            const totalSlides = $slideItems.length;

            function updateSlider() {
                const viewportWidth = $(window).width();
                const isMobile = viewportWidth <= 768;

                // Desktop: show 2 slides, Mobile: show 1 slide
                const slidesVisible = isMobile ? 1 : 2;

                // Calculate max index so we don't scroll past empty space
                const maxIndex = totalSlides - slidesVisible;

                // Ensure currentSlide is within bounds
                if (currentSlide > maxIndex) currentSlide = maxIndex;
                if (currentSlide < 0) currentSlide = 0;

                // Get width of one slide (including padding/margin defined in CSS)
                const slideWidth = $slideItems.outerWidth(true);

                // Calculate translate offset
                const offset = -(currentSlide * slideWidth);

                // Apply transform
                $sliderContainer.css('transform', 'translateX(' + offset + 'px)');

                // Button states
                $('#prevBtn').prop('disabled', currentSlide === 0);
                $('#nextBtn').prop('disabled', currentSlide >= maxIndex);
            }

            // Event Listeners
            $('#nextBtn').click(function() {
                currentSlide++;
                updateSlider();
            });

            $('#prevBtn').click(function() {
                currentSlide--;
                updateSlider();
            });

            // Debounce resize event
            let resizeTimer;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(updateSlider, 200);
            });

            // Initialize
            updateSlider();
            // ========== END SLIDER ==========

            // Scroll Progress Bar
            $(window).on('scroll', function() {
                var scrollTop = $(window).scrollTop();
                var docHeight = $(document).height();
                var winHeight = $(window).height();
                var scrollPercent = (scrollTop) / (docHeight - winHeight);
                $('#scrollProgress').css('width', Math.round(scrollPercent * 100) + '%');
            });

            // Navbar shadow
            $(window).scroll(function() {
                if ($(this).scrollTop() > 50) {
                    $('.navbar').addClass('scrolled');
                } else {
                    $('.navbar').removeClass('scrolled');
                }
            });

            // Fade Up Animation
            function checkFadeUp() {
                $('.fade-up').each(function() {
                    var elementTop = $(this).offset().top;
                    var viewportBottom = $(window).scrollTop() + $(window).height();
                    if (elementTop < viewportBottom - 50) {
                        $(this).addClass('active');
                    }
                });
            }
            $(window).on('scroll', checkFadeUp);
            checkFadeUp(); // Trigger on load

            // Smooth Scroll
            $('a[href^="#"]').on('click', function(e) {
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    e.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 80
                    }, 800);
                }
            });
        });
    </script>
</body>

</html>
