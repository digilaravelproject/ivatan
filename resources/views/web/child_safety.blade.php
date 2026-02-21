<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Safety Policy - i-Vatan (iApp)</title>
    <meta name="description" content="i-Vatan Child Safety Policy. Our commitment to protecting minors and ensuring a safe social media environment.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #000000;
            --secondary-color: #1a1a1a;
            --text-dark: #000000;
            --text-light: #666666;
            --alert-color: #dc3545; /* Red for warnings */
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: hidden; }

        /* Navbar */
        .navbar { padding: 1rem 0; background: white; transition: all 0.3s; border-bottom: 1px solid #e0e0e0; }
        .navbar.scrolled { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar-brand { font-size: 1.5rem; font-weight: 600; color: var(--text-dark) !important; display: flex; align-items: center; gap: 10px; }
        .logo-icon { width: 45px; height: 45px; background: var(--primary-color); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 700; color: white; font-style: italic; }
        .brand-name { display: flex; flex-direction: column; line-height: 1.2; }
        .brand-name span:first-child { font-size: 1.3rem; font-weight: 700; }
        .brand-name span:last-child { font-size: 0.75rem; color: var(--text-light); font-weight: 400; }
        .nav-link { color: var(--text-light) !important; margin: 0 0.8rem; font-weight: 400; transition: color 0.3s; }
        .nav-link:hover { color: var(--text-dark) !important; }
        .btn-download { background: var(--primary-color); color: white; padding: 0.5rem 1.2rem; border-radius: 20px; border: none; font-weight: 500; transition: all 0.3s; }
        .btn-download:hover { background: var(--secondary-color); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
            padding: 120px 0 80px;
            margin-top: 70px;
            border-bottom: 3px solid var(--primary-color);
        }
        .hero-content h1 { font-size: 3.5rem; font-weight: 700; color: var(--text-dark); margin-bottom: 1.5rem; }
        .hero-content p { font-size: 1.1rem; color: var(--text-light); margin-bottom: 2rem; }

        /* Content Structure */
        .policy-content { padding: 80px 0; background: white; }
        
        /* Sidebar */
        .policy-sidebar {
            position: sticky; top: 100px; background: #f5f5f5;
            border-radius: 20px; padding: 2rem; border: 2px solid var(--primary-color);
        }
        .policy-sidebar h5 { font-weight: 600; margin-bottom: 1.5rem; }
        .sidebar-nav { list-style: none; padding: 0; }
        .sidebar-nav li { margin-bottom: 0.5rem; }
        .sidebar-nav a {
            color: var(--text-light); text-decoration: none; display: block;
            padding: 0.5rem 1rem; border-radius: 10px; font-size: 0.95rem; transition: all 0.3s;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active { color: white; background: var(--primary-color); }

        /* Sections */
        .policy-section { margin-bottom: 4rem; scroll-margin-top: 120px; }
        .policy-section h2 { font-size: 2rem; font-weight: 600; color: var(--text-dark); margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 3px solid var(--primary-color); }
        .policy-section h3 { font-size: 1.4rem; font-weight: 500; margin-top: 2rem; margin-bottom: 1rem; }
        .policy-section p, .policy-section li { line-height: 1.8; color: var(--text-light); font-size: 1.05rem; margin-bottom: 1rem; }
        
        /* Specific Child Safety Styles */
        .highlight-box { background: #f5f5f5; border-left: 4px solid var(--primary-color); padding: 2rem; border-radius: 15px; margin: 2rem 0; }
        .highlight-box.danger { border-left-color: var(--alert-color); background: #fff5f5; }
        .highlight-box h5 { font-weight: 600; margin-bottom: 0.5rem; }
        .highlight-box.danger h5 { color: var(--alert-color); }
        
        .icon-box {
            width: 60px; height: 60px; background: var(--primary-color);
            border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;
        }
        .icon-box i { color: white; font-size: 1.8rem; }
        
        .date-badge { display: inline-block; background: var(--primary-color); color: white; padding: 0.7rem 1.5rem; border-radius: 25px; font-size: 0.9rem; margin-bottom: 2rem; font-weight: 500; }

        /* Footer */
        .footer { background: var(--primary-color); color: white; padding: 60px 0 30px; }
        .footer h6 { color: white; margin-bottom: 1.2rem; font-weight: 600; }
        .footer-links { list-style: none; padding: 0; }
        .footer-links li { margin-bottom: 0.8rem; }
        .footer-links a { color: #999; text-decoration: none; transition: color 0.3s; }
        .footer-links a:hover { color: white; }
        .social-icons a { color: #999; margin: 0 10px; font-size: 1.2rem; transition: color 0.3s; }
        .social-icons a:hover { color: white; }

        /* Scroll Progress & Animation */
        .scroll-progress { position: fixed; top: 0; left: 0; height: 3px; background: var(--primary-color); z-index: 9999; transition: width 0.1s; }
        .fade-up { opacity: 0; transform: translateY(50px); transition: opacity 0.8s ease, transform 0.8s ease; }
        .fade-up.active { opacity: 1; transform: translateY(0); }
        .fade-up.delay-1 { transition-delay: 0.1s; }

        @media (max-width: 768px) {
            .hero-content h1 { font-size: 2.5rem; }
            .policy-sidebar { position: relative; top: 0; margin-bottom: 2rem; }
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
                    <li class="nav-item"><a class="nav-link" href="{{ route('web.index') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('web.privacy') }}">Privacy</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('web.terms') }}">Terms</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('web.contact') }}">Contact</a></li>
                    <li class="nav-item ms-3">
                        <button class="btn btn-download">Download</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <div class="hero-content fade-up">
                        <h1>Child Safety Policy</h1>
                        <p>i-Vatan is committed to creating a safe digital environment. We have strict policies to protect minors from exploitation, abuse, and inappropriate content.</p>
                        <div class="date-badge">
                            <i class="fas fa-shield-alt"></i> Effective Date: August 19, 2025
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="policy-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="policy-sidebar fade-up">
                        <h5>Policy Sections</h5>
                        <ul class="sidebar-nav">
                            <li><a href="#commitment" class="active">Our Commitment</a></li>
                            <li><a href="#age-limit">Age Restrictions</a></li>
                            <li><a href="#zero-tolerance">Zero Tolerance (CSAM)</a></li>
                            <li><a href="#grooming">Anti-Grooming</a></li>
                            <li><a href="#data-protection">Minors' Data</a></li>
                            <li><a href="#reporting">Reporting Mechanism</a></li>
                            <li><a href="#enforcement">Enforcement</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-9">
                    
                    <div class="policy-section fade-up" id="commitment">
                        <div class="icon-box"><i class="fas fa-hand-holding-heart"></i></div>
                        <h2>1. Our Commitment to Safety</h2>
                        <p>At i-Vatan (iApp), the safety of children and minors is our highest priority. We recognize the vulnerability of younger users in the digital space and are dedicated to providing a platform that is free from exploitation.</p>
                        <p>This policy outlines our strict standards regarding Age verification, Content moderation, and Cooperation with law enforcement agencies to protect minors.</p>
                    </div>

                    <div class="policy-section fade-up delay-1" id="age-limit">
                        <div class="icon-box"><i class="fas fa-user-clock"></i></div>
                        <h2>2. Age Restrictions</h2>
                        <p>i-Vatan is not intended for use by children under the age of 13.</p>
                        <ul>
                            <li><strong>Minimum Age:</strong> You must be at least 13 years old to create an account.</li>
                            <li><strong>Parental Consent:</strong> If you are between 13 and 18 years old, you represent that you have the permission of a parent or legal guardian to use the app.</li>
                            <li><strong>Verification:</strong> We use age-gating mechanisms during sign-up. If we discover an account belongs to a user under 13, it will be deleted immediately.</li>
                        </ul>
                    </div>

                    <div class="policy-section fade-up" id="zero-tolerance">
                        <div class="icon-box" style="background: #dc3545;"><i class="fas fa-ban"></i></div>
                        <h2>3. Zero Tolerance for CSAM</h2>
                        <p>We maintain a <strong>Zero Tolerance Policy</strong> towards Child Sexual Abuse Material (CSAM). This includes the creation, solicitation, sharing, or hosting of any content that sexualizes minors.</p>
                        
                        <div class="highlight-box danger">
                            <h5><i class="fas fa-exclamation-circle"></i> Immediate Action</h5>
                            <p>If CSAM is detected on i-Vatan:</p>
                            <ul>
                                <li>The account will be permanently banned immediately.</li>
                                <li>The content will be removed and preserved as evidence.</li>
                                <li>We will report the user and content to the <strong>National Center for Missing & Exploited Children (NCMEC)</strong> and Indian Law Enforcement Agencies (Cyber Crime Cell).</li>
                            </ul>
                        </div>
                    </div>

                    <div class="policy-section fade-up delay-1" id="grooming">
                        <div class="icon-box"><i class="fas fa-user-shield"></i></div>
                        <h2>4. Anti-Grooming and Harassment</h2>
                        <p>We strictly prohibit any predatory behavior towards minors. This includes:</p>
                        <ul>
                            <li><strong>Grooming:</strong> Building an emotional connection with a minor to lower their inhibitions for sexual activity.</li>
                            <li><strong>Sextortion:</strong> Threatening to release sensitive images of a minor to coerce them.</li>
                            <li><strong>Cyberbullying:</strong> Harassing or intimidating minors through messages or posts.</li>
                            <li><strong>Solicitation:</strong> Asking minors for personal information, physical meetings, or inappropriate images.</li>
                        </ul>
                        <p>Our AI monitoring systems and human moderators actively scan for such behavioral patterns in public spaces and reported private chats.</p>
                    </div>

                    <div class="policy-section fade-up" id="data-protection">
                        <div class="icon-box"><i class="fas fa-lock"></i></div>
                        <h2>5. Data Protection for Minors</h2>
                        <p>We handle the data of users aged 13-18 with extra care, complying with the Digital Personal Data Protection Act (India) and global standards.</p>
                        <ul>
                            <li><strong>No Sale of Data:</strong> We do not sell the personal data of minors to third-party data brokers.</li>
                            <li><strong>Restricted Targeting:</strong> Ads targeted specifically at minors based on sensitive behavior are restricted.</li>
                            <li><strong>Default Privacy:</strong> Profiles of minors may default to higher privacy settings upon creation.</li>
                        </ul>
                    </div>

                    <div class="policy-section fade-up delay-1" id="reporting">
                        <div class="icon-box"><i class="fas fa-bullhorn"></i></div>
                        <h2>6. How to Report Violations</h2>
                        <p>If you see a child in danger or encounter inappropriate content involving a minor, you must report it immediately.</p>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="p-4 border rounded bg-light h-100">
                                    <h5><i class="fas fa-flag"></i> In-App Reporting</h5>
                                    <p class="mb-0 text-muted">Use the "Report" button on any post, profile, or message. Select <strong>"Child Safety"</strong> or <strong>"Inappropriate Content"</strong> as the reason.</p>
                                </div>
                            </div>
                            <div class="col-md-6 mt-3 mt-md-0">
                                <div class="p-4 border rounded bg-light h-100">
                                    <h5><i class="fas fa-envelope"></i> Direct Escalation</h5>
                                    <p class="mb-0 text-muted">Email our Trust & Safety team directly at:<br><strong>safety@ivatan.in</strong><br>Subject: "Child Safety Alert"</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="policy-section fade-up" id="enforcement">
                        <div class="icon-box"><i class="fas fa-gavel"></i></div>
                        <h2>7. Legal Enforcement</h2>
                        <p>i-Vatan cooperates fully with law enforcement authorities in India and internationally.</p>
                        <p>In cases involving imminent harm to a child, we may disclose user information to authorities without a subpoena, as permitted by law, to prevent physical harm or abuse.</p>
                    </div>
                    
                    <div style="text-align: center; margin-top: 3rem; padding: 2.5rem; background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 20px;">
                        <h4 class="fw-bold">Resources for Parents</h4>
                        <p class="text-muted">If you are a parent, we encourage you to talk to your children about online safety.</p>
                        <a href="https://cybercrime.gov.in/" target="_blank" class="btn btn-outline-dark mt-2">Visit Indian Cyber Crime Portal</a>
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
                        <li><a href="{{ route('web.privacy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('web.terms') }}">Terms & Conditions</a></li>
                        <li><a href="#">Child Safety</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Contact</h6>
                    <ul class="footer-links">
                        <li><a href="mailto:safety@ivatan.in">safety@ivatan.in</a></li>
                        <li><a href="{{ route('web.contact') }}">Contact Us</a></li>
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
                    <p class="text-muted mb-0">&copy; 2025 Octroid Pvt. Ltd. | i-Vatan (iApp)</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Navbar Scroll Effect
            $(window).scroll(function() {
                if ($(this).scrollTop() > 50) {
                    $('.navbar').addClass('scrolled');
                } else {
                    $('.navbar').removeClass('scrolled');
                }
            });

            // Scroll Progress
            $(window).on('scroll', function() {
                var scrollTop = $(window).scrollTop();
                var docHeight = $(document).height();
                var winHeight = $(window).height();
                var scrollPercent = (scrollTop) / (docHeight - winHeight);
                $('#scrollProgress').css('width', Math.round(scrollPercent * 100) + '%');
            });

            // Fade Up Animation
            function checkFadeUp() {
                $('.fade-up').each(function() {
                    var elementTop = $(this).offset().top;
                    var viewportBottom = $(window).scrollTop() + $(window).height();
                    if (elementTop < viewportBottom - 100) {
                        $(this).addClass('active');
                    }
                });
            }
            $(window).on('scroll', checkFadeUp);
            checkFadeUp();

            // Sidebar Active Link logic
            $(window).on('scroll', function() {
                var scrollPos = $(window).scrollTop() + 150;
                $('.policy-section').each(function() {
                    var currLink = $(this);
                    if (currLink.position().top <= scrollPos && currLink.position().top + currLink.height() > scrollPos) {
                        $('.sidebar-nav a').removeClass('active');
                        $('.sidebar-nav a[href="#' + currLink.attr('id') + '"]').addClass('active');
                    }
                });
            });

            // Smooth Scroll
            $('.sidebar-nav a').on('click', function(e) {
                e.preventDefault();
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 120
                    }, 800);
                }
            });
        });
    </script>
</body>
</html>