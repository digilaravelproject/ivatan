<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - i-Vatan (iApp)</title>
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

        .hero-content p {
            font-size: 1.1rem;
            color: var(--text-light);
            margin-bottom: 2rem;
        }

        .privacy-content {
            padding: 80px 0;
            background: white;
        }

        .privacy-sidebar {
            position: sticky;
            top: 100px;
            background: #f5f5f5;
            border-radius: 20px;
            padding: 2rem;
            border: 2px solid var(--primary-color);
        }

        .privacy-sidebar h5 {
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

        .policy-section {
            margin-bottom: 4rem;
            scroll-margin-top: 100px;
        }

        .policy-section h2 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid var(--primary-color);
        }

        .policy-section h3 {
            font-size: 1.4rem;
            font-weight: 500;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }

        .policy-section p {
            line-height: 1.8;
            color: var(--text-light);
            margin-bottom: 1.2rem;
            font-size: 1.05rem;
        }

        .policy-section ul {
            margin-left: 2rem;
            margin-bottom: 1.5rem;
        }

        .policy-section ul li {
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

        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }

            .policy-section h2 {
                font-size: 1.6rem;
            }

            .privacy-sidebar {
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
                        <h1>Privacy Policy</h1>
                        <p>Your privacy is important to us. Learn how i-Vatan protects your data and respects your
                            rights.</p>
                        <div class="date-badge">
                            <i class="far fa-calendar-alt"></i> Effective Date: August 19, 2025 | Version 1.0
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="privacy-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="privacy-sidebar fade-up">
                        <h5>Quick Navigation</h5>
                        <ul class="sidebar-nav">
                            <li><a href="#ownership" class="active">Content Ownership</a></li>
                            <li><a href="#revenue">Revenue Sharing</a></li>
                            <li><a href="#profile">Profile & Account</a></li>
                            <li><a href="#data">Data Collection</a></li>
                            <li><a href="#security">Security</a></li>
                            <li><a href="#messaging">Messaging Privacy</a></li>
                            <li><a href="#liability">Liability</a></li>
                            <li><a href="#modifications">Modifications</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="policy-section fade-up" id="ownership">
                        <div class="icon-box">
                            <i class="fas fa-copyright"></i>
                        </div>
                        <h2>Content Ownership & Restrictions</h2>

                        <p>All content (text, images, videos, products, job postings, etc.) uploaded, created, or shared
                            on i-Vatan ("Platform") by any user (including individual users, creators, marketplace
                            vendors, organizations, and corporate profiles) remains the intellectual property of its
                            original creator or owner.</p>

                        <h3>User Restrictions</h3>
                        <p>Users are strictly prohibited from:</p>
                        <ul>
                            <li>Copying, reproducing, or distributing any data or content from i-Vatan on external
                                platforms or services without explicit written consent from the content owner and/or
                                Octroid Pvt. Ltd.</li>
                            <li>Using i-Vatan content for commercial purposes without proper authorization</li>
                            <li>Violating intellectual property rights of other users</li>
                        </ul>

                        <div class="highlight-box">
                            <h5><i class="fas fa-exclamation-triangle"></i> Important Warning</h5>
                            <p>Any violation of this clause will result in immediate account suspension, legal
                                consequences, and forfeiture of rights to access or earnings from the platform.</p>
                        </div>
                    </div>

                    <div class="policy-section fade-up delay-1" id="revenue">
                        <div class="icon-box">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h2>Revenue Sharing and Credit Attribution</h2>

                        <h3>Monetization Rules</h3>
                        <p>If i-Vatan data or user-generated content is used or monetized (directly or indirectly) on
                            any other platform also on i-Vatan platform, the revenue generated must be shared fairly
                            with the original content owner(s) as per mutual agreement or as directed by Octroid Pvt.
                            Ltd.</p>

                        <h3>Attribution Requirements</h3>
                        <p>When sharing, referencing, or republishing i-Vatan content outside the Platform, proper
                            attribution to both the original creator and i-Vatan is strictly mandatory.</p>

                        <p><strong>Consequences of Non-Compliance:</strong> Failure to comply will lead to removal of
                            the content, account penalties, legal proceedings, and recovery of damages as per Indian IPR
                            and IT laws.</p>
                    </div>

                    <div class="policy-section fade-up" id="profile">
                        <div class="icon-box">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <h2>Profile and Account</h2>

                        <p>Your profile data (including personal details, preferences, tags, and connected accounts) are
                            collected and processed with your explicit consent, granted during registration or through
                            subsequent profile updates.</p>

                        <h3>How We Use Your Profile Data</h3>
                        <p>This data is utilized strictly for:</p>
                        <ul>
                            <li>Platform functionality and secure identity verification (including Digilocker API
                                integration)</li>
                            <li>Personalizing user experience</li>
                            <li>Ensuring compliance with national data security regulations</li>
                        </ul>

                        <div class="highlight-box">
                            <h5><i class="fas fa-shield-alt"></i> Your Privacy Matters</h5>
                            <p>We only collect data that's necessary to provide you with a secure and personalized
                                experience on i-Vatan.</p>
                        </div>
                    </div>

                    <div class="policy-section fade-up delay-1" id="data">
                        <div class="icon-box">
                            <i class="fas fa-database"></i>
                        </div>
                        <h2>Data and Activity Collection</h2>

                        <h3>Categories of Data We Collect</h3>
                        <ul>
                            <li><strong>User-input data:</strong> e.g., sign-up forms, content uploads, job posts</li>
                            <li><strong>Platform activity data:</strong> e.g., browsing behavior, likes, shares,
                                purchases, search history</li>
                        </ul>

                        <h3>Purpose of Collection</h3>
                        <p>We collect this data to:</p>
                        <ul>
                            <li>Provide personalized content and recommendations</li>
                            <li>Ensure platform security and detect suspicious or fraudulent activities</li>
                            <li>Analyze user interaction for performance improvement</li>
                        </ul>

                        <div class="highlight-box">
                            <h5><i class="fas fa-lock"></i> Data Protection Guarantee</h5>
                            <p>Your data will never be sold or shared with unauthorized third parties. Access to data is
                                provided only:</p>
                            <ul style="margin-left: 0; padding-left: 1.5rem; margin-top: 0.5rem;">
                                <li>With user consent</li>
                                <li>When legally mandated by courts, law enforcement, or government agencies</li>
                            </ul>
                        </div>
                    </div>

                    <div class="policy-section fade-up" id="security">
                        <div class="icon-box">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h2>User Usability & Activity Monitoring</h2>

                        <h3>Core Features</h3>
                        <p>Our core features are built to enhance user experience while preserving user rights. These
                            include:</p>
                        <ul>
                            <li>AI-based recommendations</li>
                            <li>Localized job/product alerts</li>
                            <li>Community safety suggestions</li>
                        </ul>

                        <p>Usability tracking is implemented to detect bugs, prevent misuse, and make continuous updates
                            without compromising data security or exposing identities.</p>

                        <h3>Activity Tracing and Monitoring</h3>
                        <p>Every interaction on the platform (post, comment, follow, like, application, listing) is
                            logged for internal quality and compliance.</p>

                        <p>Activity logs help us:</p>
                        <ul>
                            <li>Respond to user disputes</li>
                            <li>Investigate misuse</li>
                            <li>Maintain app hygiene and fair exposure</li>
                        </ul>

                        <p>Such data is only accessible by authorized staff for moderation and not exposed to external
                            parties.</p>

                        <h3>End-to-End Encryption</h3>
                        <div class="highlight-box">
                            <h5><i class="fas fa-lock"></i> Maximum Security</h5>
                            <p>Most of the i-Vatan features are End-To-End encrypted, that not allow outsider as well as
                                i-vatan platform "us" to see, share and track the data.</p>
                            <p style="margin-top: 0.5rem;"><strong>Protected Data:</strong> Messages, transactions,
                                internal activity, performance activity, user inputs</p>
                        </div>

                        <h3>Account Connection and Environment</h3>
                        <p>Users can link their i-Vatan accounts to external services like Digilocker or third-party
                            business tools. Each integration requires the user's permission and follows encrypted
                            connection protocols.</p>

                        <p>We collect limited device/environmental data (browser type, IP, crash logs) to maintain
                            system integrity and troubleshoot technical errors efficiently.</p>
                    </div>

                    <div class="policy-section fade-up delay-1" id="messaging">
                        <div class="icon-box">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h2>Messaging and Sharing</h2>

                        <p>All direct messages, shared posts, and internal conversations are governed by our content
                            guidelines. (May be access and need the Legal request under IT act)</p>

                        <h3>Message Privacy</h3>
                        <p>Private messages remain confidential unless:</p>
                        <ul>
                            <li>A legal order requires their review only government of respected country</li>
                            <li>The message is reported by users and flagged for moderation</li>
                        </ul>

                        <h3>Content Sharing Rules</h3>
                        <p>Publicly shared content must follow our attribution rules. Reuse or external sharing without
                            credit is prohibited and liable under intellectual property norms.</p>
                    </div>

                    <div class="policy-section fade-up" id="liability">
                        <div class="icon-box">
                            <i class="fas fa-gavel"></i>
                        </div>
                        <h2>Liability and Enforcement</h2>

                        <p>Breach of any privacy, content, or attribution rule is subject to investigation, temporary or
                            permanent account restrictions, and possible legal enforcement.</p>

                        <p>Octroid Pvt. Ltd. reserves full right to act on behalf of content creators or platform
                            integrity in all legal forums.</p>

                        <h3>Complaint Resolution</h3>
                        <p>Users can raise complaints or data access requests through our in-app support channel or
                            official email address.</p>

                        <div class="highlight-box">
                            <h5><i class="fas fa-envelope"></i> Contact Information</h5>
                            <p>Email: <strong>ivatan@octroid.in</strong> or <strong>hello@ivatan.in</strong></p>
                            <p style="margin-top: 0.5rem;">Website: <strong>www.ivatan.in</strong> |
                                <strong>www.octroid.in</strong>
                            </p>
                        </div>
                    </div>

                    <div class="policy-section fade-up delay-1" id="modifications">
                        <div class="icon-box">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <h2>Modifications and User Acceptance</h2>

                        <h3>Policy Updates</h3>
                        <p>This privacy policy is subject to periodic updates in line with evolving technology, laws,
                            and platform enhancements.</p>

                        <p>All users will be or may be notified within the app or website prior to significant changes.
                        </p>

                        <h3>Your Acceptance</h3>
                        <p>By creating an account and continuing to use i-Vatan, users confirm:</p>
                        <ul>
                            <li>They have read, understood, and accepted the Privacy Policy</li>
                            <li>They consent to data collection, processing, and use as outlined</li>
                            <li>They agree to comply with all rules, rights, and responsibilities stated above</li>
                            <li>AGREE to share there all "stated rules and points and T&C in doc." and use for the
                                company beneficial and user to maintain the safe and trust environment as per platform
                                motive</li>
                        </ul>

                        <div class="highlight-box">
                            <h5><i class="fas fa-check-circle"></i> Consent Confirmation</h5>
                            <p>By logging in and ticking the consent checkbox, you confirm that:</p>
                            <ul style="margin-left: 0; padding-left: 1.5rem; margin-top: 0.5rem;">
                                <li>☑ You agree to all i-Vatan Terms & Conditions and Privacy Policy</li>
                                <li>☑ You understand that i-Vatan acts only as an intermediary platform</li>
                                <li>☑ You acknowledge that your actions are accountable under Indian law</li>
                                <li>☑ You accept that violation of terms may result in suspension or legal action</li>
                            </ul>
                        </div>

                        <div
                            style="text-align: center; margin-top: 3rem; padding: 2.5rem; background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); border-radius: 20px; color: white;">
                            <h3 style="color: white; margin-bottom: 1rem; font-size: 2rem;">WELCOME!</h3>
                            <p style="color: white; margin: 0; font-size: 1.1rem;">To Onboard the i-Vatan (iApp)
                                platform.</p>
                            <p style="color: white; margin-top: 1.5rem; font-size: 1.3rem; font-weight: 600;">THANK
                                YOU!</p>
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

                $('.policy-section').each(function() {
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
