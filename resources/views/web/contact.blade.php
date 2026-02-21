<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - i-Vatan (iApp)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #000000;
            --secondary-color: #1a1a1a;
            --light-bg: #f8f9fa;
            --text-dark: #000000;
            --text-light: #666666;
            --border-color: #e0e0e0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: hidden; background-color: white; }

        /* Navbar & Scroll Progress */
        .scroll-progress { position: fixed; top: 0; left: 0; height: 3px; background: var(--primary-color); z-index: 9999; width: 0%; transition: width 0.1s; }
        .navbar { padding: 1rem 0; background: white; transition: all 0.3s; border-bottom: 1px solid var(--border-color); }
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
            padding: 140px 0 80px;
            text-align: center;
        }
        .hero-content h1 { font-size: 3.5rem; font-weight: 700; margin-bottom: 1.5rem; }
        .hero-content p { font-size: 1.2rem; color: var(--text-light); max-width: 600px; margin: 0 auto; }

        /* Contact Cards */
        .contact-info-section { padding: 80px 0; }
        
        .contact-card {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            height: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-align: center; /* Centered text for better balance with 2 cards */
        }

        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
            border-color: var(--primary-color);
        }

        .icon-circle {
            width: 70px; height: 70px;
            background: var(--light-bg);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem; /* Centered icon */
            font-size: 1.8rem;
            color: var(--primary-color);
            transition: all 0.3s;
        }
        .contact-card:hover .icon-circle { background: var(--primary-color); color: white; }

        .contact-label { font-size: 0.9rem; font-weight: 600; text-transform: uppercase; color: #999; margin-bottom: 0.5rem; letter-spacing: 1px; }
        .contact-value { font-size: 1.25rem; font-weight: 600; color: var(--text-dark); margin-bottom: 0.5rem; display: block; text-decoration: none; }
        .contact-value:hover { text-decoration: underline; }

        /* Form Section */
        .form-section { background: var(--light-bg); padding: 80px 0; }
        .contact-form { background: white; padding: 3rem; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        
        .form-control, .form-select {
            padding: 0.8rem 1rem;
            border-radius: 10px;
            border: 1px solid #ddd;
            margin-bottom: 1.5rem;
            background: #fcfcfc;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: none;
            border-color: var(--primary-color);
            background: white;
        }
        .form-label { font-weight: 500; font-size: 0.95rem; margin-bottom: 0.5rem; }

        .btn-submit {
            background: var(--primary-color); color: white;
            padding: 1rem 2.5rem; border-radius: 30px; border: none;
            font-weight: 600; letter-spacing: 0.5px; width: 100%;
            transition: all 0.3s;
        }
        .btn-submit:hover { background: var(--secondary-color); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }

        /* Map Placeholder */
        .map-container {
            width: 100%; height: 400px; background: #eee;
            border-radius: 20px; overflow: hidden; position: relative;
        }
        .map-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: url('https://images.unsplash.com/photo-1577086664693-8945534a7a5d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;
            filter: grayscale(100%); opacity: 0.7;
            display: flex; align-items: center; justify-content: center;
        }
        .map-text { background: white; padding: 20px 40px; border-radius: 15px; font-weight: 600; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }

        /* Footer */
        .footer { background: var(--primary-color); color: white; padding: 60px 0 30px; }
        .footer h6 { color: white; margin-bottom: 1.2rem; font-weight: 600; }
        .footer-links { list-style: none; padding: 0; }
        .footer-links li { margin-bottom: 0.8rem; }
        .footer-links a { color: #999; text-decoration: none; transition: color 0.3s; }
        .footer-links a:hover { color: white; }
        .social-icons a { color: #999; margin: 0 10px; font-size: 1.2rem; transition: color 0.3s; }
        .social-icons a:hover { color: white; }

        /* Animations */
        .fade-up { opacity: 0; transform: translateY(30px); transition: opacity 0.8s ease, transform 0.8s ease; }
        .fade-up.active { opacity: 1; transform: translateY(0); }
        .delay-1 { transition-delay: 0.1s; }

        @media (max-width: 768px) {
            .hero-content h1 { font-size: 2.5rem; }
            .contact-form { padding: 1.5rem; }
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

    <section class="hero-section">
        <div class="container">
            <div class="hero-content fade-up">
                <h1>Get in Touch</h1>
                <p>Have questions about i-Vatan (iApp)? We are here to help you navigate our ecosystem, whether you are a user, job seeker, or business.</p>
            </div>
        </div>
    </section>

    <section class="contact-info-section">
        <div class="container">
            <div class="row g-4 justify-content-center">
                
                <div class="col-lg-5 col-md-6 fade-up">
                    <div class="contact-card">
                        <div class="icon-circle"><i class="fas fa-envelope"></i></div>
                        
                        <div class="mb-4">
                            <div class="contact-label">General Support</div>
                            <a href="mailto:hello@ivatan.in" class="contact-value">hello@ivatan.in</a>
                        </div>
                        
                        <div>
                            <div class="contact-label">Corporate Inquiries</div>
                            <a href="mailto:ivatan@octroid.in" class="contact-value">ivatan@octroid.in</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 col-md-6 fade-up delay-1">
                    <div class="contact-card">
                        <div class="icon-circle"><i class="fas fa-phone-alt"></i></div>
                        
                        <div class="mb-3">
                            <div class="contact-label">Helpline (Public)</div>
                            <a href="tel:+917743981585" class="contact-value" style="font-size: 1.5rem;">+91 7743981585</a>
                        </div>
                        
                        <p class="text-muted mt-3">Available Mon-Fri<br>10:00 AM - 6:00 PM IST</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="form-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="text-center mb-5 fade-up">
                        <h2 class="fw-bold">Send us a Message</h2>
                        <p class="text-muted">Fill out the form below and our team will get back to you within 24 hours.</p>
                    </div>
                    
                    <div class="contact-form fade-up delay-1">
                        <form action="#" method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" placeholder="John Doe">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" placeholder="name@example.com">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" placeholder="+91 00000 00000">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Subject</label>
                                    <select class="form-select">
                                        <option selected>General Inquiry</option>
                                        <option>Job Portal (QuickHire)</option>
                                        <option>Marketplace (i-Mart)</option>
                                        <option>Report a Bug</option>
                                        <option>Legal / Privacy</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Message</label>
                                <textarea class="form-control" rows="5" placeholder="How can we help you?"></textarea>
                            </div>

                            <button type="submit" class="btn-submit">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="pb-5 bg-light">
        <div class="container fade-up">
            <div class="map-container">
                <div class="map-overlay">
                    <div class="map-text">
                        <i class="fas fa-map-marker-alt text-danger me-2"></i> Octroid Pvt. Ltd. | Mumbai & Nashik, MH
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
                        <li><a href="{{ route('web.privacy') }}">Privacy</a></li>
                        <li><a href="{{ route('web.terms') }}">Terms</a></li>
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
                        <li><a href="{{ route('web.contact') }}">Contact Us</a></li>
                        <li><a href="#">Security Advisories</a></li>
                    </ul>
                </div>
            </div>
            <hr style="border-color: #333; margin: 2rem 0;">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <p class="text-muted mb-0">&copy; 2025 Octroid Pvt. Ltd. | i-Vatan (iApp)</p>
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">ivatan@octroid.in | hello@ivatan.in</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
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
            // Navbar Scroll
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
                    if (elementTop < viewportBottom - 50) {
                        $(this).addClass('active');
                    }
                });
            }
            $(window).on('scroll', checkFadeUp);
            checkFadeUp();
        });
    </script>
</body>
</html>