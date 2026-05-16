<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>i-Mart - In-built Marketplace - i-Vatan (iApp)</title>
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
                        <h1>i-Mart</h1>
                        <p class="subtitle">In-built Marketplace System</p>
                        <p>Buy, sell, and promote products or services within a secure, trust-driven ecosystem. A
                            one-window solution for community and commerce.</p>
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
                            <li><a href="#what-is" class="active">What is i-Mart?</a></li>
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
                            <i class="fas fa-store"></i>
                        </div>
                        <h2>What is i-Mart?</h2>

                        <p><strong>i-Mart</strong> is the in-app/in-built marketplace feature of <strong>i-Vatan
                                (iApp)</strong>, built by Octroid Pvt. Ltd. It allows users to buy, sell, and promote
                            products or services within a secure, trust-driven ecosystem.</p>
                        <p>Unlike standalone e-commerce apps, i-Mart is fully integrated with social media, jobs, and
                            creator tools, making it a one-window solution for both community engagement and commerce.
                        </p>

                        <div class="highlight-box">
                            <h5><i class="fas fa-info-circle"></i> Integrated Commerce</h5>
                            <p>Seamlessly connected with your social and community activities, providing a trusted
                                environment for transactions.</p>
                        </div>
                    </div>

                    <div class="terms-section fade-up delay-1" id="workflow">
                        <div class="icon-box">
                            <i class="fas fa-list-ol"></i>
                        </div>
                        <h2>Feature Workflow (Step-by-Step)</h2>

                        <div class="workflow-steps">
                            <div class="workflow-step">
                                <div class="step-number">1</div>
                                <div class="step-content">
                                    <h5>Product Posting</h5>
                                    <p>Seller selects 'Post Product' and enters details (title, description, price,
                                        images).</p>
                                </div>
                            </div>

                            <div class="workflow-step">
                                <div class="step-number">2</div>
                                <div class="step-content">
                                    <h5>Promotion (Optional)</h5>
                                    <p>If promotion/ads are required, seller pays Octroid via secure gateways.</p>
                                </div>
                            </div>

                            <div class="workflow-step">
                                <div class="step-number">3</div>
                                <div class="step-content">
                                    <h5>Go Live</h5>
                                    <p>Product listing goes live and becomes visible to buyers.</p>
                                </div>
                            </div>

                            <div class="workflow-step">
                                <div class="step-number">4</div>
                                <div class="step-content">
                                    <h5>Order & Payment</h5>
                                    <p>Buyer places an order and pays Octroid via UPI.</p>
                                </div>
                            </div>

                            <div class="workflow-step">
                                <div class="step-number">5</div>
                                <div class="step-content">
                                    <h5>Shipping</h5>
                                    <p>Seller prepares and ships product (self-managed in Phase 1 / integrated partner
                                        in future).</p>
                                </div>
                            </div>

                            <div class="workflow-step">
                                <div class="step-number">6</div>
                                <div class="step-content">
                                    <h5>Secure Escrow</h5>
                                    <p>Payment is held securely by Octroid until confirmation.</p>
                                </div>
                            </div>

                            <div class="workflow-step">
                                <div class="step-number">7</div>
                                <div class="step-content">
                                    <h5>Disbursement</h5>
                                    <p>Upon successful delivery confirmation, Octroid disburses payment to the seller
                                        within the weekly cycle.</p>
                                </div>
                            </div>

                            <div class="workflow-step">
                                <div class="step-number">8</div>
                                <div class="step-content">
                                    <h5>Dispute Resolution</h5>
                                    <p>If disputes occur, refunds are initiated within 5 business days after joint
                                        review.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="terms-section fade-up" id="terms">
                        <div class="icon-box">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <h2>Terms & Conditions (T&C)</h2>

                        <h3>1. Service Role</h3>
                        <p>i-Mart is a digital marketplace where users can buy, sell, and promote products or services.
                            Octroid Pvt. Ltd. acts solely as a service provider/intermediary and does not manufacture,
                            stock, or guarantee any products listed.</p>

                        <h3>2. Eligibility</h3>
                        <ul>
                            <li><strong>Buyers:</strong> Must be 15 years and above (parent/guardian consent required if
                                below 18).</li>
                            <li><strong>Sellers:</strong> Must be verified individuals, registered businesses, or
                                organizations with valid identification.</li>
                        </ul>

                        <h3>3. Product Listings</h3>
                        <p>Sellers must ensure all product descriptions, prices, and terms are truthful, lawful, and not
                            misleading. Prohibited items include illegal goods, counterfeit products, or anything
                            restricted under Indian law. Octroid reserves the right to remove listings without prior
                            notice.</p>

                        <h3>4. Purchases</h3>
                        <p>Buyers are responsible for reviewing product details before making purchases. Octroid Pvt.
                            Ltd. does not guarantee product quality, authenticity, or delivery unless supported by
                            future integrated logistics partners.</p>

                        <h3>5. Payments</h3>
                        <p>Payments are collected by Octroid Pvt. Ltd. through RBI-compliant gateways (UPI). Sellers
                            receive disbursement weekly, subject to successful transactions and no dispute. Refunds are
                            processed within 5 business days upon buyer/seller confirmation.</p>

                        <h3>6. Delivery</h3>
                        <p>In Phase 1, sellers manage delivery themselves. Octroid Pvt. Ltd. is not liable for shipping
                            delays, damages, or failure. In Phase 2, integrated logistics will be introduced.</p>

                        <h3>7. Prohibited Use</h3>
                        <p>Fake listings, spam promotions, scams, and counterfeit goods will lead to permanent
                            suspension and legal action.</p>

                        <h3>8. Termination</h3>
                        <p>Octroid Pvt. Ltd. may suspend or terminate any user account for fraudulent activity, law
                            violations, or misuse.</p>
                    </div>

                    <div class="terms-section fade-up delay-1" id="privacy">
                        <div class="icon-box">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h2>Privacy Policy</h2>

                        <h3>1. Data Collection</h3>
                        <p>We collect Buyer data (profile, order history, payment details processed via gateways),
                            Seller data (business identity, tax details, bank account), and Activity data (browsing,
                            clicks, complaints).</p>

                        <h3>2. Data Use</h3>
                        <p>Data is used to match buyers and sellers, prevent fraud, verify authenticity, and improve
                            marketplace recommendations. Data is never sold; it is shared only with trusted partners
                            (payment, legal, logistics).</p>

                        <h3>3. Confidentiality</h3>
                        <p>Private details (phone numbers, bank details) are never made public without consent.</p>

                        <h3>4. User Control</h3>
                        <p>Users can edit, delete, or request removal of listings/orders. Full deletion requests must be
                            emailed to support.</p>

                        <h3>5. Legal Compliance</h3>
                        <p>All policies align with Indian IT Act, Consumer Protection (E-Commerce) Rules 2020, and DPDP
                            Bill 2023 readiness.</p>
                    </div>

                    <div class="terms-section fade-up" id="in-use">
                        <div class="icon-box">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h2>In-Use Feature Policy</h2>

                        <h3>1. For Sellers</h3>
                        <ul>
                            <li>Must provide valid products, lawful pricing, and safe packaging.</li>
                            <li>Cannot demand extra payment outside i-Mart.</li>
                            <li>Refund policy must be honored within 5 working days if disputes arise.</li>
                        </ul>

                        <h3>2. For Buyers</h3>
                        <ul>
                            <li>Must purchase only after reviewing product descriptions.</li>
                            <li>False claims, chargebacks, or fraud will result in account suspension.</li>
                        </ul>

                        <h3>3. For Viewers</h3>
                        <ul>
                            <li>Browsing allowed. Misuse of data for third-party competitive advantage prohibited.</li>
                        </ul>
                    </div>

                    <div class="terms-section fade-up delay-1" id="indemnity">
                        <div class="icon-box">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <h2>Indemnity & Liability</h2>

                        <div class="highlight-box">
                            <h5><i class="fas fa-exclamation-triangle"></i> Risk & Liability</h5>
                            <p>Octroid Pvt. Ltd. / i-Mart is not responsible for:</p>
                            <ul style="margin-left: 1rem; margin-top: 0.5rem;">
                                <li>Fake goods, non-delivery, or misrepresentation by sellers.</li>
                                <li>Buyer fraud, false claims, or misuse of services.</li>
                                <li>Any disputes between buyer and seller.</li>
                            </ul>
                        </div>

                        <h3>Indemnity Clause</h3>
                        <p>All users indemnify Octroid Pvt. Ltd. against any disputes, damages, or claims from the use
                            of i-Mart.</p>
                    </div>

                    <div class="terms-section fade-up" id="acceptance">
                        <div class="icon-box">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h2>Acceptance</h2>

                        <p>By using i-Mart within i-Vatan (iApp):</p>
                        <ul>
                            <li>You acknowledge that you have read, understood, and agreed to these Terms, Privacy
                                Policy, and In-Use Rules.</li>
                            <li>You consent to your data being used for lawful platform purposes.</li>
                            <li>You accept that Octroid Pvt. Ltd. operates only as an intermediary and is not liable for
                                sell/buy, payment, or service outcomes.</li>
                        </ul>

                        <div
                            style="text-align: center; margin-top: 3rem; padding: 2.5rem; background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); border-radius: 20px; color: white;">
                            <h3 style="color: white; margin-bottom: 1rem; font-size: 2rem;">Shop with Trust</h3>
                            <p style="color: white; margin: 0; font-size: 1.1rem;">i-Mart by i-Vatan (iApp)</p>
                            <p style="color: white; margin-top: 0.5rem; font-size: 0.95rem;">Operated by Octroid Pvt.
                                Ltd.</p>
                            <p style="color: white; margin-top: 1.5rem; font-size: 1.3rem; font-weight: 600;">Secure.
                                Integrated. Reliable.</p>
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
