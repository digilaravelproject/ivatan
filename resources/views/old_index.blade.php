<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp - Message Privately</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --whatsapp-green: #25D366;
            --dark-bg: #111B21;
            --light-cream: #FFF4E6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar {
            padding: 1rem 0;
            background: white;
            transition: all 0.3s;
        }

        .navbar.scrolled {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-bg) !important;
        }

        .navbar-brand .fa-whatsapp {
            color: var(--whatsapp-green);
            font-size: 2rem;
            margin-right: 5px;
        }

        .nav-link {
            color: #54656F !important;
            margin: 0 0.8rem;
            font-weight: 400;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: var(--dark-bg) !important;
        }

        .btn-download {
            background: var(--whatsapp-green);
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            border: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-download:hover {
            background: #22c55e;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e8f5e9 100%);
            padding: 120px 0 80px;
            position: relative;
            overflow: hidden;
            margin-top: 70px;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 300;
            color: var(--dark-bg);
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero-content p {
            font-size: 1.1rem;
            color: #54656F;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .hero-image-wrapper {
            position: relative;
            width: 100%;
            height: 500px;
            border-radius: 30px;
            overflow: visible;
        }

        .hero-mockup {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        .hero-floating-card {
            position: absolute;
            background: white;
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            animation: float 3s ease-in-out infinite;
            z-index: 10;
        }

        .hero-floating-card.card-1 {
            top: 5%;
            right: -5%;
            animation-delay: 0s;
        }

        .hero-floating-card.card-2 {
            top: 35%;
            right: -5%;
            animation-delay: 1s;
        }

        .hero-floating-card.card-3 {
            bottom: 15%;
            right: -5%;
            animation-delay: 0.5s;
        }

        .emoji-decoration {
            position: absolute;
            font-size: 3rem;
            z-index: 5;
        }

        .emoji-decoration.top-right {
            top: 10%;
            right: 8%;
        }

        /* Feature Sections */
        .feature-section {
            padding: 100px 0;
            position: relative;
        }

        .feature-section:nth-child(even) {
            background: var(--light-cream);
        }

        .feature-content h2 {
            font-size: 2.5rem;
            font-weight: 300;
            color: var(--dark-bg);
            margin-bottom: 1.5rem;
        }

        .feature-content p {
            font-size: 1.1rem;
            color: #54656F;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }

        .feature-link {
            color: var(--whatsapp-green);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .feature-link:hover {
            color: #22c55e;
            transform: translateX(5px);
        }

        .feature-image-wrapper {
            position: relative;
            max-width: 450px;
            margin: 0 auto;
        }

        .feature-mockup {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
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
            color: #8696A0;
        }

        .green-text {
            color: var(--whatsapp-green);
        }

        .encrypted-badge {
            display: inline-block;
            padding: 8px 15px;
            background: rgba(37, 211, 102, 0.1);
            border-radius: 20px;
            color: var(--whatsapp-green);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        /* Cards Section */
        .update-section {
            padding: 100px 0;
            background: linear-gradient(180deg, #fff 0%, #f8f9fa 100%);
        }

        .update-section h2 {
            font-size: 2.5rem;
            font-weight: 300;
            text-align: center;
            margin-bottom: 3rem;
        }

        .update-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            height: 100%;
            transition: all 0.3s;
            border: 1px solid #f0f0f0;
        }

        .update-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }

        .update-card h5 {
            font-size: 1.2rem;
            margin: 1rem 0;
            color: var(--dark-bg);
        }

        .update-card p {
            color: #54656F;
            line-height: 1.6;
        }

        .card-btn {
            background: #f0f0f0;
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            color: var(--dark-bg);
            font-weight: 500;
            transition: all 0.3s;
        }

        .card-btn:hover {
            background: var(--whatsapp-green);
            color: white;
        }

        /* Footer */
        .footer {
            background: var(--dark-bg);
            color: white;
            padding: 60px 0 30px;
        }

        .footer h6 {
            color: white;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: #8696A0;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--whatsapp-green);
        }

        .social-icons a {
            color: #8696A0;
            font-size: 1.2rem;
            margin: 0 10px;
            transition: color 0.3s;
        }

        .social-icons a:hover {
            color: var(--whatsapp-green);
        }

        /* Animations */
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-15px);
            }
        }

        /* Fade Bottom to Top Animation */
        .fade-up {
            opacity: 0;
            transform: translateY(50px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        .fade-up.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Stagger animation for multiple elements */
        .fade-up.delay-1 {
            transition-delay: 0.1s;
        }

        .fade-up.delay-2 {
            transition-delay: 0.2s;
        }

        .fade-up.delay-3 {
            transition-delay: 0.3s;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .feature-content h2 {
                font-size: 2rem;
            }

            .feature-section {
                padding: 60px 0;
            }

            .hero-floating-card {
                display: none;
            }

            .hero-image-wrapper {
                height: 400px;
            }
        }

        /* Scroll Progress Bar */
        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: var(--whatsapp-green);
            z-index: 9999;
            transition: width 0.1s;
        }
		/* Update Cards Section - Slider */
.update-section {
    padding: 100px 0;
    background: #E7F7DD;
}

.update-section h2 {
    font-size: 2.5rem;
    font-weight: 300;
    margin-bottom: 0.5rem;
}

.slider-wrapper {
    overflow: hidden;
    position: relative;
    width: 100%;
}

.slider-container {
    display: flex;
    transition: transform 0.5s ease;
    gap: 30px;
    width: max-content;
}

.slide-item {
    width: calc((100vw - 30px - 240px) / 2); /* 2 cards visible, 30px gap, 240px container padding */
    max-width: 600px;
    flex-shrink: 0;
}

.update-card-slider {
    background: white;
    border-radius: 20px;
    padding: 3rem;
    height: 100%;
    transition: all 0.3s;
    border: 1px solid #e0e0e0;
}

.whatsapp-icon-badge {
    width: 60px;
    height: 60px;
    background: var(--whatsapp-green);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
}

.whatsapp-icon-badge i {
    color: white;
    font-size: 2rem;
}

.update-card-slider h5 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: var(--dark-bg);
    line-height: 1.4;
}

.update-card-slider p {
    color: #54656F;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.card-btn-slider {
    background: transparent;
    border: 2px solid #e0e0e0;
    padding: 0.7rem 1.5rem;
    border-radius: 25px;
    color: var(--dark-bg);
    font-weight: 500;
    transition: all 0.3s;
    cursor: pointer;
}

.card-btn-slider:hover {
    background: var(--dark-bg);
    color: white;
    border-color: var(--dark-bg);
}

.slider-nav-btn {
    width: 50px;
    height: 50px;
    border: 2px solid var(--dark-bg);
    background: white;
    border-radius: 50%;
    color: var(--dark-bg);
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.3s;
    margin-left: 10px;
}

.slider-nav-btn:hover {
    background: var(--dark-bg);
    color: white;
}

.slider-nav-btn:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.slider-nav-btn:disabled:hover {
    background: white;
    color: var(--dark-bg);
}

/* Responsive */
@media (max-width: 768px) {
    .slide-item {
        width: 100%;
        max-width: 100%;
    }

    .slider-nav-btn {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
}
    </style>
</head>
<body>
    <!-- Scroll Progress Bar -->
    <div class="scroll-progress" id="scrollProgress"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#security">Privacy</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Help Center</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="#business">For Business</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Apps</a></li>
                    <li class="nav-item ms-3">
                        <button class="btn btn-download">Download</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

   <!-- Hero Section START -->
<section class="hero-section" style="position: relative; background: url('https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=1920&h=1080&fit=crop') center/cover; min-height: 650px; padding: 0; margin-top: 70px;">
    <!-- Dark Overlay -->
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.3); z-index: 1;"></div>
    
    <div class="container" style="position: relative; z-index: 2; height: 100%; min-height: 650px; display: flex; align-items: center;">
        <div class="row w-100 align-items-center">
            
            <!-- LEFT COLUMN - Text Content -->
            <div class="col-lg-5">
                <div class="hero-content fade-up" style="color: white;">
                    <h1 style="color: white; font-size: 3.5rem; font-weight: 300;">Message privately</h1>
                    <p style="color: rgba(255,255,255,0.9); font-size: 1.1rem;">Simple, reliable, private messaging and calling for free*, available all over the world.</p>
                    <button class="btn btn-download btn-lg me-3">
                        <i class="fas fa-download"></i> Download
                    </button>
                    <button class="btn btn-outline-light btn-lg">
                        Log in
                    </button>
                </div>
            </div>
            
            <!-- RIGHT COLUMN - Floating Cards -->
            <div class="col-lg-7 position-relative">
                <div class="fade-up delay-1" style="position: relative; min-height: 500px;">
                    
                    <!-- Floating Card 1 -->
                    <div class="hero-floating-card card-1" style="top: 10%; right: 10%;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 0.95rem; color: #111;">Good morning mom ☕🧇</span>
                            <span style="font-size: 0.75rem; color: #888;">11:54</span>
                        </div>
                        <div style="color: #FF3B30; font-size: 1.3rem; margin-top: 5px;">❤️</div>
                    </div>

                    <!-- Emoji Decoration -->
                    <div class="emoji-decoration" style="position: absolute; top: 8%; right: 5%; font-size: 3rem; z-index: 15;">
                        ☕❤️
                    </div>

                    <!-- Floating Card 2 - Voice Message -->
                    <div class="hero-floating-card card-2" style="top: 40%; right: 8%; padding: 12px; min-width: 300px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <button style="border: none; background: transparent; padding: 0;">
                                <i class="fas fa-play" style="color: var(--whatsapp-green); font-size: 1rem;"></i>
                            </button>
                            <div style="flex: 1; height: 4px; background: #E5E5EA; border-radius: 4px; position: relative;">
                                <div style="width: 8%; height: 100%; background: var(--whatsapp-green); border-radius: 4px;"></div>
                            </div>
                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=40&h=40&fit=crop" style="border-radius: 50%; width: 35px; height: 35px; object-fit: cover;">
                        </div>
                        <div style="font-size: 0.7rem; color: #888; text-align: center; margin-top: 5px;">0:03 / 11:57</div>
                    </div>

                    <!-- Floating Card 3 - Image -->
                    <div class="hero-floating-card card-3" style="bottom: 8%; right: 10%; padding: 0; overflow: hidden; width: 240px;">
                        <div style="position: relative;">
                            <img src="https://images.unsplash.com/photo-1476362555312-ab9e108a0b7e?w=250&h=180&fit=crop" style="width: 100%; height: 180px; object-fit: cover; display: block; border-radius: 15px;">
                            <div style="position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.6); color: white; padding: 3px 8px; border-radius: 5px; font-size: 0.75rem;">11:57</div>
                            <div style="position: absolute; bottom: 8px; left: 8px; background: rgba(255,255,255,0.95); padding: 4px 8px; border-radius: 50%; font-size: 1.1rem;">😊</div>
                        </div>
                    </div>
                    
                </div>
            </div>
            
        </div>
    </div>
</section> <!-- Hero Section END -->

    <!-- Feature Section 1: Chat on larger screen -->
    <section class="feature-section" id="features">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0 order-2 order-lg-1">
                    <div class="feature-image-wrapper fade-up">
                        <img src="https://images.unsplash.com/photo-1551650975-87deedd944c3?w=450&h=550&fit=crop" alt="Multiple devices" class="feature-mockup">
                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2">
                    <div class="feature-content fade-up delay-1">
                        <h2>Chat and call on a larger screen</h2>
                        <p>Sync WhatsApp on your phone with WhatsApp on your computer or tablet so that you can use the app on multiple devices at the same time.</p>
                        <a href="#" class="feature-link">Learn more <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Section 2: Voice and Video Calls -->
    <section class="feature-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="feature-content fade-up">
                        <h2>Never miss a moment with voice and video calls</h2>
                        <p>With voice calls, you can talk to your friends and family for free*, even if they're in another country. And with free* video calls, you can have face-to-face conversations when voice or text just isn't enough.</p>
                        <a href="#" class="feature-link">Learn more <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="feature-image-wrapper fade-up delay-1">
                        <img src="https://images.unsplash.com/photo-1516387938699-a93567ec168e?w=450&h=550&fit=crop" alt="Video calls" class="feature-mockup">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dark Section: Security -->
    <section class="dark-section" id="security">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="feature-content fade-up">
                        <div class="encrypted-badge">
                            <i class="fas fa-lock"></i> End-to-end encrypted
                        </div>
                        <h2>Speak <span class="green-text">freely</span></h2>
                        <p>With end-to-end encryption, your personal messages and calls are secured. Only you and the person you're talking to can read or listen to them, and nobody in between, not even WhatsApp.</p>
                        <a href="#" class="feature-link">Learn more <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="feature-image-wrapper fade-up delay-1">
                        <img src="https://images.unsplash.com/photo-1563986768609-322da13575f3?w=450&h=400&fit=crop" alt="Security" class="feature-mockup">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Section 3: Groups -->
    <section class="feature-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0 order-2 order-lg-1">
                    <div class="feature-image-wrapper fade-up">
                        <img src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=450&h=550&fit=crop" alt="Groups" class="feature-mockup">
                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2">
                    <div class="feature-content fade-up delay-1">
                        <h2>Keep in touch with your groups</h2>
                        <p>Whether it's planning an outing with friends or simply staying on top of your family group chat, group conversations should feel natural and seamless. That's why we support voice and video calls, photos, videos, and voice messages.</p>
                        <a href="#" class="feature-link">Learn more <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Section 4: Express Yourself -->
    <section class="feature-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="feature-content fade-up">
                        <h2>Say what you feel</h2>
                        <p>Express yourself without words. Use stickers and GIFs or share everyday moments on Status. Record a voice message for a quick hello or a longer story.</p>
                        <a href="#" class="feature-link">Learn more <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="feature-image-wrapper fade-up delay-1">
                        <img src="https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=450&h=550&fit=crop" alt="Express yourself" class="feature-mockup">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Section 5: Business -->
    <section class="feature-section" id="business">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0 order-2 order-lg-1">
                    <div class="feature-image-wrapper fade-up">
                        <img src="https://images.unsplash.com/photo-1556761175-b413da4baf72?w=450&h=550&fit=crop" alt="Business" class="feature-mockup">
                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2">
                    <div class="feature-content fade-up delay-1">
                        <h2>Transform your business</h2>
                        <p>WhatsApp Business Platform enables medium and large businesses to connect with customers at scale. You can start conversations in just a few clicks, send customer care notifications or purchase updates, offer your customers a level of personalized service, and provide support in the channel they prefer.</p>
                        <a href="#" class="feature-link">Learn more <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Update Cards Section with Slider -->
    <section class="update-section">
        <div class="container">
            <div class="row align-items-center mb-4">
                <div class="col-md-6">
                    <h2 class="fade-up mb-0" style="text-align: left;">Stay up to date</h2>
                    <p class="fade-up" style="text-align: left; color: #54656F; margin-top: 10px;">Get the latest from WhatsApp: news, useful tips, and our newest features to help you stay connected.</p>
                </div>
                <div class="col-md-6 text-end">
                    <button class="slider-nav-btn prev-btn" id="prevBtn">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="slider-nav-btn next-btn" id="nextBtn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            
            <div class="slider-wrapper">
                <div class="slider-container" id="sliderContainer">
                    <!-- Slide 1 -->
                    <div class="slide-item">
                        <div class="update-card-slider">
                            <div class="whatsapp-icon-badge">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <h5>New Feature Roundup: Live and Motion photos, creative Meta AI features, and more!</h5>
                            <p>Over the past few months, we've continued adding new features and updates to WhatsApp. Today's roundup includes the ability to send Live and Motion photos, new ways to get...</p>
                            <button class="card-btn-slider">Read More</button>
                        </div>
                    </div>
                    
                    <!-- Slide 2 -->
                    <div class="slide-item">
                        <div class="update-card-slider">
                            <div class="whatsapp-icon-badge">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <h5>Introducing Message Translations</h5>
                            <p>With more than 3 billion users in over 180 countries, we're always working to keep our global community connected, no matter where they are or what language they speak. We understand that...</p>
                            <button class="card-btn-slider">Read More</button>
                        </div>
                    </div>
                    
                    <!-- Slide 3 -->
                    <div class="slide-item">
                        <div class="update-card-slider">
                            <div class="whatsapp-icon-badge">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <h5>Get Fashion Inspiration Live</h5>
                            <p>Fashion Blogger, Creative Muse AI: Replicate, and more!</p>
                            <button class="card-btn-slider">Read More</button>
                        </div>
                    </div>
                    
                    <!-- Slide 4 -->
                    <div class="slide-item">
                        <div class="update-card-slider">
                            <div class="whatsapp-icon-badge">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <h5>Introducing Meta Verified</h5>
                            <p>Verification badge, account support, and impersonation protection.</p>
                            <button class="card-btn-slider">Read More</button>
                        </div>
                    </div>
                    
                    <!-- Slide 5 -->
                    <div class="slide-item">
                        <div class="update-card-slider">
                            <div class="whatsapp-icon-badge">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <h5>Screen Share on Video Call</h5>
                            <p>Share your screen during video calls with friends and family.</p>
                            <button class="card-btn-slider">Read More</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <h6>WhatsApp</h6>
                    <ul class="footer-links">
                        <li><a href="#">Features</a></li>
                        <li><a href="#">Security</a></li>
                        <li><a href="#">Download</a></li>
                        <li><a href="#">WhatsApp Web</a></li>
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
                        <li><a href="#">Mac/PC</a></li>
                        <li><a href="#">Android</a></li>
                        <li><a href="#">iPhone</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Help</h6>
                    <ul class="footer-links">
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">Coronavirus</a></li>
                        <li><a href="#">Security Advisories</a></li>
                    </ul>
                </div>
            </div>
            <hr style="border-color: #333; margin: 2rem 0;">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <p class="text-muted mb-0">&copy; 2024 WhatsApp LLC</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Scroll Progress Bar
            $(window).on('scroll', function() {
                var scrollTop = $(window).scrollTop();
                var docHeight = $(document).height();
                var winHeight = $(window).height();
                var scrollPercent = (scrollTop) / (docHeight - winHeight);
                var scrollPercentRounded = Math.round(scrollPercent * 100);
                $('#scrollProgress').css('width', scrollPercentRounded + '%');
            });

            // Navbar scroll effect
            $(window).scroll(function() {
                if ($(this).scrollTop() > 50) {
                    $('.navbar').addClass('scrolled');
                } else {
                    $('.navbar').removeClass('scrolled');
                }
            });

            // Fade up animation on scroll
            function checkFadeUp() {
                $('.fade-up').each(function() {
                    var elementTop = $(this).offset().top;
                    var elementBottom = elementTop + $(this).outerHeight();
                    var viewportTop = $(window).scrollTop();
                    var viewportBottom = viewportTop + $(window).height();
                    
                    // Trigger animation when element is 80% visible
                    if (elementTop < viewportBottom - 100) {
                        $(this).addClass('active');
                    }
                });
            }

            // Run on scroll
            $(window).on('scroll', function() {
                checkFadeUp();
            });

            // Initial check on page load
            checkFadeUp();

            // Smooth scroll for navigation links
            $('a[href^="#"]').on('click', function(e) {
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    e.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 80
                    }, 1000);
                }
            });

            // Parallax effect for hero image
            $(window).scroll(function() {
                var scrolled = $(window).scrollTop();
                $('.hero-image-wrapper').css('transform', 'translateY(' + (scrolled * 0.3) + 'px)');
            });
        });
    </script>

<script>
    $(document).ready(function() {
        // ========== SLIDER FUNCTIONALITY START ==========
        let currentSlide = 0;
        const slidesToShow = $(window).width() <= 768 ? 1 : 2;
        
        function updateSlider() {
            const slideItems = $('.slide-item');
            const totalSlides = slideItems.length;
            const maxSlide = totalSlides - slidesToShow;
            
            if (slideItems.length > 0) {
                const slideWidth = slideItems.eq(0).outerWidth(true); // Include margin/gap
                const offset = -(currentSlide * slideWidth);
                $('#sliderContainer').css('transform', `translateX(${offset}px)`);
                
                // Update button states
                $('#prevBtn').prop('disabled', currentSlide === 0);
                $('#nextBtn').prop('disabled', currentSlide >= maxSlide);
            }
        }

        // Next button click - Move by 1 slide
        $('#nextBtn').on('click', function() {
            const totalSlides = $('.slide-item').length;
            const maxSlide = totalSlides - slidesToShow;
            
            if (currentSlide < maxSlide) {
                currentSlide++;
                updateSlider();
            }
        });

        // Previous button click - Move by 1 slide
        $('#prevBtn').on('click', function() {
            if (currentSlide > 0) {
                currentSlide--;
                updateSlider();
            }
        });

        // Update on window resize
        let resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                currentSlide = 0;
                updateSlider();
            }, 250);
        });

        // Initialize slider
        setTimeout(function() {
            updateSlider();
        }, 100);
        // ========== SLIDER FUNCTIONALITY END ==========

        // Scroll Progress Bar
        $(window).on('scroll', function() {
            var scrollTop = $(window).scrollTop();
            var docHeight = $(document).height();
            var winHeight = $(window).height();
            var scrollPercent = (scrollTop) / (docHeight - winHeight);
            var scrollPercentRounded = Math.round(scrollPercent * 100);
            $('#scrollProgress').css('width', scrollPercentRounded + '%');
        });

        // Navbar scroll effect
        $(window).scroll(function() {
            if ($(this).scrollTop() > 50) {
                $('.navbar').addClass('scrolled');
            } else {
                $('.navbar').removeClass('scrolled');
            }
        });

        // Fade up animation on scroll
        function checkFadeUp() {
            $('.fade-up').each(function() {
                var elementTop = $(this).offset().top;
                var elementBottom = elementTop + $(this).outerHeight();
                var viewportTop = $(window).scrollTop();
                var viewportBottom = viewportTop + $(window).height();
                
                if (elementTop < viewportBottom - 100) {
                    $(this).addClass('active');
                }
            });
        }

        $(window).on('scroll', function() {
            checkFadeUp();
        });

        checkFadeUp();

        // Smooth scroll
        $('a[href^="#"]').on('click', function(e) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 80
                }, 1000);
            }
        });

        // Parallax effect
        $(window).scroll(function() {
            var scrolled = $(window).scrollTop();
            $('.hero-image-wrapper').css('transform', 'translateY(' + (scrolled * 0.3) + 'px)');
        });
    });
</script>
</body>
</html>