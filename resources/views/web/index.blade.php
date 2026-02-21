<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>i-Vatan (iApp) - User Consent Agreement</title>
    
    <meta name="description" content="i-Vatan (iApp) User Consent Agreement. Mandatory before use.">
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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: hidden; background-color: white; }

        /* Scroll Progress */
        .scroll-progress {
            position: fixed; top: 0; left: 0; height: 4px; background: var(--primary-accent); z-index: 9999; width: 0%; transition: width 0.1s;
        }

        /* Navbar */
        .navbar { padding: 1rem 0; background: white; transition: all 0.3s; border-bottom: 1px solid var(--border-color); }
        .navbar.scrolled { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        
        /* Updated Navbar Brand Styles for Consistency */
        .navbar-brand { display: flex; align-items: center; gap: 10px; color: var(--text-dark) !important; font-weight: 600; font-size: 1.5rem; }
        .logo-icon { width: 45px; height: 45px; background: var(--primary-accent); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 700; color: white; font-style: italic; }
        .brand-name { display: flex; flex-direction: column; line-height: 1.2; }
        .brand-name span:first-child { font-size: 1.3rem; font-weight: 700; }
        .brand-name span:last-child { font-size: 0.75rem; color: var(--text-light); font-weight: 400; }

        .nav-link { color: var(--text-light) !important; margin: 0 0.8rem; font-weight: 400; transition: color 0.3s; }
        .nav-link:hover { color: var(--primary-accent) !important; }
        .btn-download { background: var(--primary-accent); color: white; padding: 0.5rem 1.2rem; border-radius: 20px; border: none; font-weight: 500; transition: all 0.3s; }
        .btn-download:hover { background: #333; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2); }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.8)), url('https://images.unsplash.com/photo-1519389950473-47ba0277781c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
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
        .feature-section { padding: 100px 0; }
        .feature-section:nth-child(even) { background: var(--light-bg); }
        .feature-content h2 { font-size: 2.5rem; font-weight: 300; color: var(--primary-accent); margin-bottom: 1.5rem; }
        .feature-content p { font-size: 1.1rem; color: var(--text-light); line-height: 1.7; margin-bottom: 1.5rem; }
        .feature-content ul { list-style: none; padding-left: 0; }
        .feature-content ul li { margin-bottom: 10px; color: var(--text-light); font-size: 1.05rem; }
        .feature-content ul li i { color: var(--primary-accent); margin-right: 10px; }
        
        .feature-link { color: var(--primary-accent); text-decoration: none; font-weight: 500; transition: all 0.3s; }
        .feature-link:hover { color: #666; transform: translateX(5px); display: inline-block; }
        
        .feature-mockup {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
            transition: transform 0.3s ease;
        }
        .feature-mockup:hover { transform: scale(1.02); }

        /* Dark Section */
        .dark-section { background: var(--dark-bg); color: white; padding: 100px 0; }
        .dark-section h2 { color: white; }
        .dark-section p { color: #AAAAAA; }
        .accent-text { font-weight: 600; color: white; border-bottom: 2px solid white; }

        /* Doc List Section */
        .doc-list-item { padding: 15px; border-bottom: 1px solid #eee; display: flex; align-items: center; }
        .doc-list-item:last-child { border-bottom: none; }
        .doc-icon { margin-right: 15px; font-size: 1.2rem; color: var(--primary-accent); }

        /* Checklist Section */
        .consent-box { background: #fff; padding: 30px; border-radius: 15px; border: 1px solid var(--border-color); margin-top: 20px; }
        .check-item { display: flex; margin-bottom: 15px; align-items: flex-start; }
        .check-icon { min-width: 25px; color: var(--primary-accent); margin-top: 4px; }
        
        /* ========= SLIDER CSS ========= */
        .update-section { padding: 100px 0; background: var(--light-bg); overflow: hidden; }
        
        .slider-wrapper {
            width: 100%;
            overflow: hidden; 
            position: relative;
            padding: 20px 0;
        }

        .slider-container {
            display: flex; 
            transition: transform 0.5s ease-in-out; 
            width: 100%;
        }

        .slide-item {
            flex: 0 0 50%; 
            max-width: 50%;
            padding: 0 15px; 
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
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transform: translateY(-5px);
        }

        .whatsapp-icon-badge {
            width: 60px; height: 60px; background: var(--primary-accent); border-radius: 50%;
            display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;
        }
        .whatsapp-icon-badge i { color: white; font-size: 1.8rem; }
        
        .card-btn-slider {
            background: transparent; border: 2px solid var(--primary-accent); padding: 0.6rem 1.5rem;
            border-radius: 25px; color: var(--primary-accent); font-weight: 500; transition: all 0.3s; cursor: pointer;
            margin-top: 20px; align-self: flex-start;
        }
        .card-btn-slider:hover { background: var(--primary-accent); color: white; }

        .slider-nav-btn {
            border: 2px solid var(--primary-accent); background: white; color: var(--primary-accent);
            width: 45px; height: 45px; border-radius: 50%; transition: all 0.3s; margin-left: 10px;
        }
        .slider-nav-btn:hover:not(:disabled) { background: var(--primary-accent); color: white; }
        .slider-nav-btn:disabled { opacity: 0.3; cursor: not-allowed; border-color: #ccc; color: #ccc; }

        /* Footer */
        .footer { background: var(--dark-bg); color: white; padding: 60px 0 30px; }
        .footer h6 { color: white; margin-bottom: 1.2rem; font-weight: 600; }
        .footer-links { list-style: none; padding: 0; }
        .footer-links li { margin-bottom: 0.8rem; }
        .footer-links a { color: #AAAAAA; text-decoration: none; transition: color 0.3s; }
        .footer-links a:hover { color: white; }
        .social-icons a { color: #AAAAAA; margin-left: 15px; font-size: 1.2rem; transition: color 0.3s; }
        .social-icons a:hover { color: white; }

        /* Animation Classes */
        .fade-up { opacity: 0; transform: translateY(30px); transition: opacity 0.8s ease, transform 0.8s ease; }
        .fade-up.active { opacity: 1; transform: translateY(0); }
        .delay-1 { transition-delay: 0.2s; }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-content h1 { font-size: 2.5rem; }
            .slide-item { flex: 0 0 100%; max-width: 100%; } 
            .hero-section { text-align: center; }
            .hero-content { margin-bottom: 40px; }
        }
    </style>
</head>
<body>

    <div class="scroll-progress" id="scrollProgress"></div>

    <nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white">
        <div class="container">
            <a class="navbar-brand" href="{{ route('web.index') }}">
                <div class="logo-icon">i</div>
                <div class="brand-name">
                    <span>i-Vatan</span>
                    <span>(iApp)</span>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                 <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="{{ route('web.privacy') }}">Privacy Policy</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('web.terms') }}">Terms and Conditions</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('web.trust') }}">Trust</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('web.quickhire') }}">QuickHire</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('web.market') }}">Mart</a></li>
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
                        <h1 style="color: white; font-weight: 300; margin-bottom: 20px;">i-Vatan (iApp) User Consent Agreement</h1>
                        <p style="color: rgba(255,255,255,0.9); font-size: 1.2rem; margin-bottom: 30px;">
                           Effective: 19 August 2025 | Mandatory Before Use<br><br>
                           By accessing, installing, or interacting with the i-Vatan (iApp) platform, you hereby agree to the following consent terms.
                        </p>
                        <button class="btn btn-download btn-lg me-3" style="border: 1px solid white;">
                            <i class="fas fa-check-circle"></i> I Agree & Register
                        </button>
                        <a href="#compliance" class="btn btn-outline-light btn-lg">
                            Read Full Terms
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="feature-section" id="compliance">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0 order-2 order-lg-1">
                    <div class="feature-image-wrapper fade-up">
                        <img src="https://images.unsplash.com/photo-1450101499163-c8848c66ca85?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Legal Agreement" class="feature-mockup">
                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2">
                    <div class="feature-content fade-up delay-1">
                        <h2 class="fw-bold">1. Voluntary Agreement & Data</h2>
                        <p><strong>Voluntary Agreement:</strong> You fully agree to abide by the latest Terms & Conditions and Privacy Policy as defined by Octroid Pvt. Ltd. These documents govern your entire interaction.</p>
                        <p><strong>Data Collection (Clause 2):</strong> You grant permission to i-Vatan to collect, analyze, and store your profile and activity data for:</p>
                        <ul>
                            <li><i class="fas fa-check"></i> Personalized content and job/product recommendations</li>
                            <li><i class="fas fa-check"></i> Service improvement, bug detection, and content moderation</li>
                            <li><i class="fas fa-check"></i> Market analysis, AI training, and business performance tracking</li>
                        </ul>
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
                        <div style="display:inline-block; background: rgba(255,255,255,0.15); padding: 5px 15px; border-radius: 20px; margin-bottom: 15px;">
                            <i class="fas fa-shield-alt"></i> Clause 3: Monitoring
                        </div>
                        <h2 class="fw-bold">3. Content Monitoring & Encryption</h2>
                        <p>You understand that all your activity (posting, sharing, liking, messaging, purchasing, job applying, etc.) are fully <span class="accent-text">5-layer encrypted</span>.</p>
                        <p>However, content may be monitored (gov. request only under IT act) or logged for quality control, legal compliance, and fraud detection purposes.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="feature-image-wrapper fade-up delay-1">
                        <img src="https://images.unsplash.com/photo-1563986768609-322da13575f3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Security and Compliance" class="feature-mockup">
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
                        <img src="https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=800&q=80" alt="Commercial Usage" class="feature-mockup">
                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2">
                    <div class="feature-content fade-up delay-1">
                        <h2 class="fw-bold">4. Commercial Usage</h2>
                        <p>You acknowledge and agree that certain user behavior, interaction data, and content may be used in anonymized or aggregate form to generate commercial value (including platform growth, AI-based personalization, ad systems).</p>
                        <p class="text-muted">Revenue derived from such platform-wide insights is the sole property of Octroid Pvt. Ltd., unless mutually agreed upon.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="feature-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-5">
                    <div class="feature-content fade-up">
                        <h3 class="fw-bold mb-3">5. User Responsibility</h3>
                        <p>You accept personal accountability for your content, profile behavior, and interactions. Any violation of platform rules may result in:</p>
                        <ul>
                            <li><i class="fas fa-exclamation-triangle"></i> Account warnings or permanent bans</li>
                            <li><i class="fas fa-gavel"></i> Legal actions under Indian cyber and IPR laws</li>
                            <li><i class="fas fa-rupee-sign"></i> Financial penalties in case of fraud, impersonation, misuse, or copyright breach</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="feature-content fade-up delay-1">
                        <h3 class="fw-bold mb-3">6. Endorsement of Principles</h3>
                        <p>You accept that i-Vatan operates on a <strong>trust-first model</strong>. You support its intent to build a secure, decentralized, India-first ecosystem.</p>
                        <p>Misuse of trust, spreading misinformation, or exploiting system loopholes will not be tolerated.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="feature-section" style="background-color: var(--light-bg);">
        <div class="container">
            <div class="text-center mb-5 fade-up">
                <h2 class="fw-bold">7. Acceptance of Legal Documents</h2>
                <p>By proceeding, you accept the following listed documents:</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card fade-up delay-1" style="border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                        <div class="card-body p-0">
                            <div class="doc-list-item"><i class="fas fa-file-contract doc-icon"></i> i-Vatan (iApp) Privacy Policy (Effective 19 Aug 25)</div>
                            <div class="doc-list-item"><i class="fas fa-file-alt doc-icon"></i> i-Vatan (iApp) Terms and Conditions T&C</div>
                            <div class="doc-list-item"><i class="fas fa-briefcase doc-icon"></i> i-Vatan (iApp) P&P T&C Use-Case Job-Portal i-QuickHire</div>
                            <div class="doc-list-item"><i class="fas fa-store doc-icon"></i> i-Vatan (iApp) P&P T&C Use-Case MarketPlace i-Mart</div>
                            <div class="doc-list-item"><i class="fas fa-handshake doc-icon"></i> i-Vatan (iApp) Process Facilitation & Ownership Declaration</div>
                            <div class="doc-list-item"><i class="fas fa-shield-alt doc-icon"></i> i-Vatan (iApp) P&P T&C Use-Case i-Trust TrustScore</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="update-section" id="updates">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-md-7">
                    <h2 class="fade-up mb-2 fw-bold" style="text-align: left;">Claims & Roadmap</h2>
                    <p class="fade-up" style="text-align: left; color: var(--text-light);">The Platform is being released in a phased manner.</p>
                </div>
                <div class="col-md-5 text-end">
                    <button class="slider-nav-btn prev-btn" id="prevBtn"><i class="fas fa-chevron-left"></i></button>
                    <button class="slider-nav-btn next-btn" id="nextBtn"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
            
            <div class="slider-wrapper">
                <div class="slider-container" id="sliderContainer">
                    
                    <div class="slide-item">
                        <div class="update-card-slider">
                            <div>
                                <div class="whatsapp-icon-badge"><i class="fas fa-cogs"></i></div>
                                <h5>Phased Rollout</h5>
                                <p style="font-size: 0.95rem; color: #666;">User expressly agrees that features may be introduced progressively, updated, or temporarily unavailable. Product Claims reflect aspirational roadmaps.</p>
                            </div>
                            <button class="card-btn-slider">Read Clause</button>
                        </div>
                    </div>
                    
                    <div class="slide-item">
                        <div class="update-card-slider">
                            <div>
                                <div class="whatsapp-icon-badge"><i class="fas fa-lightbulb"></i></div>
                                <h5>Aspirational Statements</h5>
                                <p style="font-size: 0.95rem; color: #666;">Future-use cases or upcoming capabilities constitute forward-looking statements and are not guaranteed deliverables.</p>
                            </div>
                            <button class="card-btn-slider">Read Clause</button>
                        </div>
                    </div>
                    
                    <div class="slide-item">
                        <div class="update-card-slider">
                            <div>
                                <div class="whatsapp-icon-badge"><i class="fas fa-gavel"></i></div>
                                <h5>Dispute Resolution</h5>
                                <p style="font-size: 0.95rem; color: #666;">In event of concern, User shall first seek clarification directly from the Company through official channels before initiating any dispute or derogatory action.</p>
                            </div>
                            <button class="card-btn-slider">Get Support</button>
                        </div>
                    </div>

                    <div class="slide-item">
                        <div class="update-card-slider">
                            <div>
                                <div class="whatsapp-icon-badge"><i class="fas fa-user-shield"></i></div>
                                <h5>Company Liability</h5>
                                <p style="font-size: 0.95rem; color: #666;">The Company shall not be held liable for temporary unavailability or delayed delivery. Occasional setbacks are inherent to large-scale deployment.</p>
                            </div>
                            <button class="card-btn-slider">Read Clause</button>
                        </div>
                    </div>
                    
                    <div class="slide-item">
                        <div class="update-card-slider">
                            <div>
                                <div class="whatsapp-icon-badge"><i class="fas fa-users"></i></div>
                                <h5>Collaborative Relationship</h5>
                                <p style="font-size: 0.95rem; color: #666;">User usage constitutes full acceptance of the phased launch system and the Company's right to modify functionality as required.</p>
                            </div>
                            <button class="card-btn-slider">Read Clause</button>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>

    <section class="feature-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h2 class="fw-bold text-center mb-4 fade-up">Consent Confirmation</h2>
                    <p class="text-center mb-4 fade-up">By continuing forward, you confirm:</p>
                    
                    <div class="consent-box fade-up delay-1">
                        <div class="check-item">
                            <i class="fas fa-check-square check-icon"></i>
                            <p class="mb-0 ms-3">I have read and understood i-Vatan's Terms & Conditions and Privacy Policy.</p>
                        </div>
                        <div class="check-item">
                            <i class="fas fa-check-square check-icon"></i>
                            <p class="mb-0 ms-3">I voluntarily agree to share, allow, and authorize data usage as per the platform's stated purpose.</p>
                        </div>
                        <div class="check-item">
                            <i class="fas fa-check-square check-icon"></i>
                            <p class="mb-0 ms-3">I acknowledge that i-Vatan is a service intermediary, and my conduct is legally binding.</p>
                        </div>
                        <div class="check-item">
                            <i class="fas fa-check-square check-icon"></i>
                            <p class="mb-0 ms-3">I agree that i-Vatan may use internal insights for business growth, trend mapping, or AI modeling.</p>
                        </div>
                        <div class="check-item">
                            <i class="fas fa-check-square check-icon"></i>
                            <p class="mb-0 ms-3">I understand that I may request data visibility or revoke access within lawful limits.</p>
                        </div>
                        <div class="check-item">
                            <i class="fas fa-check-square check-icon"></i>
                            <p class="mb-0 ms-3"><strong>Legal Updates:</strong> Due to newly platform, some legal documents may be updated without prior notice for user effect and privacy concerns. I allow such activity.</p>
                        </div>
                        
                        <hr class="my-4">
                        <div class="text-center">
                            <p class="fw-bold">Your presence, activity, and integrity power this ecosystem.</p>
                            <button class="btn btn-download btn-lg px-5 mt-2">I Confirm & Enter i-Vatan</button>
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
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Company</h6>
                    <ul class="footer-links">
                        <li><a href="#">About</a></li>
                        <li><a href="#">Privacy</a></li>
                        <li><a href="#">Terms</a></li>
                        <li><a href="{{ route('web.contact') }}">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Contact</h6>
                    <ul class="footer-links">
                        <li><a href="#">ivatan@octroid.in</a></li>
                        <li><a href="#">hello@ivatan.in</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Legal</h6>
                    <ul class="footer-links">
                        <li><a href="#">Octroid Pvt. Ltd.</a></li>
                        <li><a href="#">Dispute Resolution</a></li>
                    </ul>
                </div>
            </div>
            <hr style="border-color: #333; margin: 2rem 0;">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <p class="text-muted mb-0">&copy; 2025 Octroid Pvt. Ltd. | i-Vatan (iApp) | Effective 19 Aug 2025</p>
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