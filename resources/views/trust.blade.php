<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>i-Trust - TrustScore System - i-Vatan (iApp)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #000000;
            --secondary-color: #1a1a1a;
            --text-dark: #000000;
            --text-light: #666666;
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

        .navbar {
            padding: 1rem 0;
            background: white;
            transition: all 0.3s;
        }

        .navbar.scrolled {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-dark) !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background: var(--primary-color);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
            font-style: italic;
        }

        .brand-name {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .brand-name span:first-child {
            font-size: 1.3rem;
            font-weight: 700;
        }

        .brand-name span:last-child {
            font-size: 0.75rem;
            color: var(--text-light);
            font-weight: 400;
        }

        .nav-link {
            color: var(--text-light) !important;
            margin: 0 0.8rem;
            font-weight: 400;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: var(--text-dark) !important;
        }

        .btn-download {
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            border: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-download:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .hero-section {
            background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
            padding: 120px 0 80px;
            margin-top: 70px;
            border-bottom: 3px solid var(--primary-color);
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
        }

        .hero-content .subtitle {
            font-size: 1.5rem;
            color: var(--text-dark);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .hero-content p {
            font-size: 1.1rem;
            color: var(--text-light);
            margin-bottom: 2rem;
        }

        .terms-content {
            padding: 80px 0;
            background: white;
        }

        .terms-sidebar {
            position: sticky;
            top: 100px;
            background: #f5f5f5;
            border-radius: 20px;
            padding: 2rem;
            border: 2px solid var(--primary-color);
        }

        .terms-sidebar h5 {
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
        }

        .sidebar-nav li {
            margin-bottom: 0.5rem;
        }

        .sidebar-nav a {
            color: var(--text-light);
            text-decoration: none;
            display: block;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            color: white;
            background: var(--primary-color);
        }

        .terms-section {
            margin-bottom: 4rem;
            scroll-margin-top: 100px;
        }

        .terms-section h2 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid var(--primary-color);
        }

        .terms-section h3 {
            font-size: 1.4rem;
            font-weight: 500;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }

        .terms-section p {
            line-height: 1.8;
            color: var(--text-light);
            margin-bottom: 1.2rem;
            font-size: 1.05rem;
        }

        .terms-section ul {
            margin-left: 2rem;
            margin-bottom: 1.5rem;
        }

        .terms-section ul li {
            line-height: 1.8;
            color: var(--text-light);
            margin-bottom: 0.8rem;
        }

        .highlight-box {
            background: #f5f5f5;
            border-left: 4px solid var(--primary-color);
            padding: 2rem;
            border-radius: 15px;
            margin: 2rem 0;
        }

        .highlight-box h5 {
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .highlight-box p {
            color: var(--text-light);
            margin: 0;
        }

        .date-badge {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 0.7rem 1.5rem;
            border-radius: 25px;
            font-size: 0.9rem;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .icon-box {
            width: 60px;
            height: 60px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .icon-box i {
            color: white;
            font-size: 1.8rem;
        }

        .footer {
            background: var(--primary-color);
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
            color: #999;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: white;
        }

        .social-icons a {
            color: #999;
            font-size: 1.2rem;
            margin: 0 10px;
            transition: color 0.3s;
        }

        .social-icons a:hover {
            color: white;
        }

        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: var(--primary-color);
            z-index: 9999;
            transition: width 0.1s;
        }

        .fade-up {
            opacity: 0;
            transform: translateY(50px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        .fade-up.active {
            opacity: 1;
            transform: translateY(0);
        }

        .fade-up.delay-1 {
            transition-delay: 0.1s;
        }

        .workflow-steps {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .workflow-step {
            display: flex;
            align-items: start;
            gap: 1.5rem;
            padding: 1.5rem;
            background: #f9f9f9;
            border-radius: 15px;
            border-left: 4px solid var(--primary-color);
        }

        .step-number {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
        }

        .step-content h5 {
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .step-content p {
            margin: 0;
            color: var(--text-light);
        }

        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }

            .hero-content .subtitle {
                font-size: 1.2rem;
            }

            .terms-section h2 {
                font-size: 1.6rem;
            }

            .terms-sidebar {
                position: relative;
                top: 0;
                margin-bottom: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="scroll-progress" id="scrollProgress"></div>

    <nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
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

    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <div class="hero-content fade-up">
                        <h1>i-Trust</h1>
                        <p class="subtitle">TrustScore System</p>
                        <p>Your Digital Identity – Defined by Your Environment and Internet</p>
                        <div class="date-badge">
                            <i class="far fa-calendar-alt"></i> Effective Date: August 19, 2025 | Version 1.0
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="terms-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="terms-sidebar fade-up">
                        <h5>Quick Navigation</h5>
                        <ul class="sidebar-nav">
                            <li><a href="#what-is" class="active">What is i-Trust?</a></li>
                            <li><a href="#workflow">Feature Workflow</a></li>
                            <li><a href="#terms">Terms & Conditions</a></li>
                            <li><a href="#privacy">Privacy Policy</a></li>
                            <li><a href="#in-use">In-Use Feature Policy</a></li>
                            <li><a href="#indemnity">Indemnity & Liability</a></li>
                            <li><a href="#acceptance">Acceptance</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="terms-section fade-up" id="what-is">
                        <div class="icon-box">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h2>What is i-Trust?</h2>

                        <p><strong>i-Trust</strong> is the in-app TrustScore system inside <strong>i-Vatan
                                (iApp)</strong>. It measures and displays a user's credibility, reliability, and
                            authenticity across all platform activities. It's an <strong>AUTO-INDEPENDENT
                                system</strong> that works on review and environment inputs, including social, jobs,
                            marketplace, and creator tools.</p>

                        <p>It works like a digital trust identity, empowering users and creating a transparent,
                            fraud-free environment.</p>

                        <div class="highlight-box">
                            <h5><i class="fas fa-info-circle"></i> Key Features</h5>
                            <p>i-Trust continuously monitors and evaluates user behavior, transactions, and community
                                interactions to provide a real-time trust rating that helps create a safer digital
                                ecosystem.</p>
                        </div>
                    </div>

                    <div class="terms-section fade-up delay-1" id="workflow">
                        <div class="icon-box">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h2>Feature Workflow (Step-by-Step)</h2>

                        <div class="workflow-steps">
                            <div class="workflow-step">
                                <div class="step-number">1</div>
                                <div class="step-content">
                                    <h5>Registration & Verification</h5>
                                    <p>User registers and verifies their identity (mobile + optional KYC/DigiLocker).
                                    </p>
                                </div>
                            </div>

                            <div class="workflow-step">
                                <div class="step-number">2</div>
                                <div class="step-content">
                                    <h5>TrustScore Generation</h5>
                                    <p>TrustScore is automatically generated based on:</p>
                                    <ul style="margin-left: 1rem; margin-top: 0.5rem;">
                                        <li>Profile authenticity</li>
                                        <li>Behavior on platform (posts, interactions, complaints)</li>
                                        <li>Job/Marketplace activity (completion rate, disputes)</li>
                                        <li>Peer and community ratings</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="workflow-step">
                                <div class="step-number">3</div>
                                <div class="step-content">
                                    <h5>Dynamic Updates</h5>
                                    <p>TrustScore updates dynamically with every positive or negative activity.</p>
                                </div>
                            </div>

                            <div class="workflow-step">
                                <div class="step-number">4</div>
                                <div class="step-content">
                                    <h5>Unlock Opportunities</h5>
                                    <p>Higher scores unlock better visibility, opportunities, and rewards.</p>
                                </div>
                            </div>

                            <div class="workflow-step">
                                <div class="step-number">5</div>
                                <div class="step-content">
                                    <h5>Penalties & Restrictions</h5>
                                    <p>Scores below the threshold may lead to restricted access, penalties, or
                                        suspension.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="terms-section fade-up" id="terms">
                        <div class="icon-box">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <h2>Terms & Conditions (T&C)</h2>

                        <h3>1. Service Role</h3>
                        <ul>
                            <li>i-Trust is an assessment and scoring mechanism, not a legal certification.</li>
                            <li>Octroid Pvt. Ltd. does not guarantee employment, sales, or outcomes based solely on
                                TrustScore. It's an <strong>AUTO-INDEPENDENT system</strong> that works on review and
                                environment inputs.</li>
                        </ul>

                        <h3>2. Eligibility</h3>
                        <ul>
                            <li>Available to all users age 15+ with verified accounts.</li>
                            <li>Fake, duplicate, or bot accounts are not eligible.</li>
                        </ul>

                        <h3>3. Use of Score</h3>
                        <ul>
                            <li>TrustScore can be viewed by other users (Environment Members, employers, buyers,
                                creators, business, etc.) as an indicator of credibility.</li>
                            <li>Users cannot manipulate or demand artificial boosts.</li>
                        </ul>

                        <h3>4. Penalties</h3>
                        <ul>
                            <li>Fraud, abuse, or misuse leads to score deduction, suspension, or termination.</li>
                            <li>Octroid Pvt. Ltd. reserves full rights to revise or reset TrustScores.</li>
                        </ul>
                    </div>

                    <div class="terms-section fade-up delay-1" id="privacy">
                        <div class="icon-box">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h2>Privacy Policy</h2>

                        <h3>1. Data Collection</h3>
                        <p>User profile, activity, engagement metrics, transaction history, and peer ratings.</p>

                        <h3>2. Data Use</h3>
                        <ul>
                            <li>To calculate TrustScore, detect fraud, and ensure a safe environment.</li>
                            <li>Not sold to third parties. Shared only when legally required.</li>
                        </ul>

                        <h3>3. Confidentiality</h3>
                        <p>Raw activity data remains private. Only final TrustScore and badges are visible to others.
                        </p>

                        <h3>4. User Control</h3>
                        <ul>
                            <li>Users may request clarification or review of their score.</li>
                            <li>Deleting the account removes associated TrustScore permanently.</li>
                        </ul>

                        <h3>5. Legal Compliance</h3>
                        <p>Complies with Indian IT Act 2000, Consumer Protection Rules 2020, and DPDP Bill 2023
                            readiness.</p>

                        <div class="highlight-box">
                            <h5><i class="fas fa-eye-slash"></i> Hide TrustScore Option</h5>
                            <p>All users can request to hide their TrustScore from their profile by contacting the
                                platform team with a strong reason.</p>
                        </div>
                    </div>

                    <div class="terms-section fade-up" id="in-use">
                        <div class="icon-box">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h2>In-Use Feature Policy</h2>

                        <h3>1. For Employers/Buyers/Creators/Business/Environment Members</h3>
                        <ul>
                            <li>Use TrustScore as a guideline, not the sole factor in decisions.</li>
                            <li>Octroid not liable for relying solely on TrustScore outcomes.</li>
                        </ul>

                        <h3>2. For Users</h3>
                        <ul>
                            <li>Maintain honesty, professionalism, and lawful behavior to build score.</li>
                            <li>Repeated violations lower TrustScore and may lock features.</li>
                        </ul>

                        <h3>3. For Viewers</h3>
                        <ul>
                            <li>Viewing TrustScore is for internal platform use only.</li>
                            <li>External use (copying, publishing) requires Octroid's consent.</li>
                        </ul>
                    </div>

                    <div class="terms-section fade-up delay-1" id="indemnity">
                        <div class="icon-box">
                            <i class="fas fa-gavel"></i>
                        </div>
                        <h2>Indemnity & Liability</h2>

                        <div class="highlight-box">
                            <h5><i class="fas fa-exclamation-triangle"></i> Liability Disclaimer</h5>
                            <ul style="margin-left: 0; padding-left: 1.5rem; margin-top: 0.5rem;">
                                <li>Octroid Pvt. Ltd. is not liable for damages, losses, or disputes arising from
                                    reliance on TrustScore.</li>
                                <li>All users indemnify Octroid against claims arising from misuse, fraud, or
                                    misrepresentation of their TrustScore.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="terms-section fade-up" id="acceptance">
                        <div class="icon-box">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <h2>Acceptance</h2>

                        <p>By using i-Trust within i-Vatan (iApp):</p>
                        <ul>
                            <li>You acknowledge that your activities contribute to your TrustScore.</li>
                            <li>You consent to the collection and use of your data for lawful platform purposes.</li>
                            <li>You accept that Octroid Pvt. Ltd. has the final authority on TrustScore assignment,
                                revision, and suspension.</li>
                        </ul>

                        <div
                            style="text-align: center; margin-top: 3rem; padding: 2.5rem; background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); border-radius: 20px; color: white;">
                            <h3 style="color: white; margin-bottom: 1rem; font-size: 2rem;">Build Your Trust</h3>
                            <p style="color: white; margin: 0; font-size: 1.1rem;">i-Trust by i-Vatan (iApp)</p>
                            <p style="color: white; margin-top: 0.5rem; font-size: 0.95rem;">Operated by Octroid Pvt.
                                Ltd.</p>
                            <p style="color: white; margin-top: 1.5rem; font-size: 1.3rem; font-weight: 600;">Your
                                Digital Identity Matters!</p>
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
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Contact: ivatan@octroid.in | hello@ivatan.in
                    </p>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">www.ivatan.in | www.octroid.in</p>
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
            $(window).on('scroll', function() {
                var scrollTop = $(window).scrollTop();
                var docHeight = $(document).height();
                var winHeight = $(window).height();
                var scrollPercent = (scrollTop) / (docHeight - winHeight);
                var scrollPercentRounded = Math.round(scrollPercent * 100);
                $('#scrollProgress').css('width', scrollPercentRounded + '%');
            });

            $(window).scroll(function() {
                if ($(this).scrollTop() > 50) {
                    $('.navbar').addClass('scrolled');
                } else {
                    $('.navbar').removeClass('scrolled');
                }
            });

            function checkFadeUp() {
                $('.fade-up').each(function() {
                    var elementTop = $(this).offset().top;
                    var viewportBottom = $(window).scrollTop() + $(window).height();

                    if (elementTop < viewportBottom - 100) {
                        $(this).addClass('active');
                    }
                });
            }

            $(window).on('scroll', function() {
                checkFadeUp();
            });

            checkFadeUp();

            $('.sidebar-nav a').on('click', function(e) {
                e.preventDefault();
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);

                    $('.sidebar-nav a').removeClass('active');
                    $(this).addClass('active');
                }
            });

            $(window).on('scroll', function() {
                var scrollPos = $(window).scrollTop() + 150;

                $('.terms-section').each(function() {
                    var currLink = $(this);
                    var refElement = currLink;

                    if (refElement.position().top <= scrollPos && refElement.position().top +
                        refElement.height() > scrollPos) {
                        $('.sidebar-nav a').removeClass('active');
                        $('.sidebar-nav a[href="#' + currLink.attr('id') + '"]').addClass('active');
                    }
                });
            });
        });
    </script>
</body>

</html>
