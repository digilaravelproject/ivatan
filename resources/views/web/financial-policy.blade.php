<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial & Compliance Policy - i-Vatan (iApp)</title>
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
                        <a href="https://play.google.com/store/apps/details?id=com.octroid.ivatan" class="btn btn-download">Download</a>
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
                        <h1>Financial & Compliance Policy</h1>
                        <p>Ultra Comprehensive Financial, Payment, Refund, Escrow, Compliance & Liability Policy.</p>
                        <div class="date-badge">
                            <i class="far fa-calendar-alt"></i> Effective Date: Current | Version 1.0
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
                            <li><a href="#legal-governance" class="active">Legal & Governance</a></li>
                            <li><a href="#escrow-settlement">Escrow & Settlement</a></li>
                            <li><a href="#payments-refunds">Payments & Refunds</a></li>
                            <li><a href="#fraud-compliance">Fraud & Compliance</a></li>
                            <li><a href="#tax-crossborder">Tax & Cross-Border</a></li>
                            <li><a href="#liability-security">Liability & Security</a></li>
                            <li><a href="#disputes-agreement">Disputes & Agreement</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="policy-section fade-up" id="legal-governance">
                        <div class="icon-box">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <h2>1. Legal Nature & Governance</h2>
                        
                        <h3>1. Foundational Legal Nature</h3>
                        <p>i-Vatan operates strictly as a digital facilitation and technology infrastructure enabling interactions between independent users. The platform does not function as a bank, escrow institution, NBFC, employer, contractor, or financial advisor.</p>
                        <p>All transactions are user-initiated and executed at user discretion. The Company provides only system-level support for transaction processing, validation, and communication. Users acknowledge that participation on the platform does not create any legal partnership, fiduciary relationship, or agency obligation.</p>

                        <h3>2. Absolute Discretion & Governance Authority</h3>
                        <p>The Company retains full, irrevocable authority over all transactions, accounts, and activities. This includes the right to suspend, block, reverse, delay, or cancel any financial activity without prior notice where risk, fraud suspicion, compliance triggers, or behavioral anomalies are detected.</p>
                        <p>Such decisions are based on automated systems, internal review, or regulatory obligations. Users expressly agree that such actions shall not constitute breach, liability, or grounds for dispute.</p>
                    </div>

                    <div class="policy-section fade-up delay-1" id="escrow-settlement">
                        <div class="icon-box">
                            <i class="fas fa-university"></i>
                        </div>
                        <h2>2. Escrow & Settlement Architecture</h2>
                        
                        <h3>3. Escrow & Controlled Settlement Architecture</h3>
                        <p>Funds processed through the platform are held in a controlled system for verification, validation, and compliance purposes. During this phase, operational control of funds may rest with the platform strictly for transaction completion.</p>
                        <p>Release of funds is conditional upon system validation, mutual confirmation, or dispute resolution outcomes. The Company reserves the right to extend holding periods, split settlements, or partially release funds based on risk assessment.</p>

                        <h3>5. Settlement Flow & Transaction Logic</h3>
                        <p>Standard transaction flow includes: <strong>Initiation &rarr; System Hold &rarr; Validation &rarr; Completion &rarr; Settlement.</strong></p>
                        <p>The platform may implement automated or manual checks at any stage. Any dispute raised pauses the settlement process until resolution is achieved. Settlement timelines may vary based on transaction type, user history, and system evaluation.</p>
                    </div>

                    <div class="policy-section fade-up" id="payments-refunds">
                        <div class="icon-box">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h2>3. Payments, Refunds & Chargebacks</h2>
                        <P>The refunds are processed with the 5 to 8 business days from date of refund confirmation</P>
                        
                        <h3>4. Payment Processing & Infrastructure</h3>
                        <p>All payments are executed through secure, RBI-compliant third-party payment gateways. The platform does not store or retain sensitive payment credentials. Users acknowledge that third-party failures, downtime, or delays are outside platform control, and the Company shall not be liable for such occurrences.</p>

                        <h3>6. Refund Framework</h3>
                        <p>Refunds are strictly limited to cases of failed transactions, duplicate payments, or verified system errors. Requests must be submitted within specified timelines and supported by relevant proof.</p>
                        <p>Refunds may be processed through the original payment method or as internal wallet credit, at the Company's discretion. Refund approval is subject to validation and final decision by the Company.</p>

                        <h3>7. Non-Refundable Transactions</h3>
                        <div class="highlight-box">
                            <h5><i class="fas fa-exclamation-triangle"></i> Strictly Non-Refundable</h5>
                            <p>All completed transactions, including but not limited to advertisement services, subscription plans, service fees, marketplace purchases, and voluntary transfers, are non-refundable.</p>
                            <p style="margin-top: 10px;">Usage-based charges are deemed consumed and non-reversible. User negligence, incorrect inputs, or delay in action shall not qualify for refund consideration.</p>
                        </div>

                        <h3>8. Chargeback Policy</h3>
                        <p>Users are required to resolve disputes through platform mechanisms before initiating chargebacks. Unauthorized chargebacks may result in account suspension, financial recovery actions, and legal proceedings. The Company reserves the right to recover losses arising from chargebacks through any lawful means.</p>
                    </div>

                    <div class="policy-section fade-up delay-1" id="fraud-compliance">
                        <div class="icon-box">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h2>4. Fraud Monitoring & Compliance</h2>
                        
                        <h3>9. Fraud Monitoring & Enforcement</h3>
                        <p>The platform employs advanced monitoring systems including behavioral analytics and AI-driven detection. Suspicious activities may result in account suspension, fund freezing, or reporting to authorities.</p>
                        <p>The Company may share user data with law enforcement agencies without prior consent where required by law or risk mitigation.</p>

                        <h3>10. AML, KYC & Compliance</h3>
                        <p>Users may be required to complete identity verification processes. Non-compliance may lead to restricted access, transaction blocking, or account suspension. The platform adheres to anti-money laundering regulations and may monitor transactions accordingly.</p>
                    </div>

                    <div class="policy-section fade-up" id="tax-crossborder">
                        <div class="icon-box">
                            <i class="fas fa-globe"></i>
                        </div>
                        <h2>5. Taxation & Cross-Border</h2>
                        
                        <h3>11. Cross-Border Transactions</h3>
                        <p>Users engaging in international transactions must comply with FEMA and applicable jurisdictional laws. Currency conversion risks, exchange losses, and international charges are borne by the user. The Company holds no liability for cross-border financial discrepancies.</p>

                        <h3>12. Taxation & Financial Obligations</h3>
                        <p>Users are solely responsible for compliance with applicable tax laws, including GST, income tax, and reporting obligations. The platform may generate transaction records or invoices for compliance purposes.</p>
                    </div>

                    <div class="policy-section fade-up delay-1" id="liability-security">
                        <div class="icon-box">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h2>6. Liability, Indemnity & Security</h2>
                        
                        <h3>13. Limitation of Liability</h3>
                        <p>The Company's liability is strictly limited to the value of the transaction in question. Under no circumstances shall the Company be liable for indirect, incidental, consequential, or reputational damages arising from platform usage.</p>

                        <h3>14. Indemnity Clause</h3>
                        <p>Users agree to indemnify and hold harmless the Company, its promoters, directors, and employees from any claims, damages, liabilities, or legal actions arising from misuse, violation, or unlawful activity conducted through the platform.</p>

                        <h3>15. Force Majeure</h3>
                        <p>The Company shall not be liable for delays or failures resulting from events beyond control, including cyber attacks, system outages, natural disasters, or regulatory actions.</p>

                        <h3>16. Data Security Disclaimer</h3>
                        <p>While industry-standard security measures are implemented, the Company does not guarantee absolute protection against breaches or unauthorized access.</p>
                    </div>

                    <div class="policy-section fade-up" id="disputes-agreement">
                        <div class="icon-box">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h2>7. Disputes & Final Agreement</h2>
                        
                        <h3>17. Dispute Resolution</h3>
                        <p>All disputes must be raised within specified timelines. Resolution shall first be attempted internally, failing which arbitration under Indian law shall apply. Jurisdiction shall remain Maharashtra, India.</p>

                        <h3>18. Policy Interpretation</h3>
                        <p>In the event of ambiguity, the Company's interpretation shall prevail. Users waive rights to challenge interpretation unless mandated by law.</p>

                        <h3>19. Policy Updates</h3>
                        <p>The Company reserves the right to modify this policy at any time without prior notice. Continued use constitutes acceptance of updated terms.</p>

                        <h3>20. Final Binding Agreement</h3>
                        <div class="highlight-box">
                            <h5><i class="fas fa-check-circle"></i> Acknowledgment & Acceptance</h5>
                            <p>By using i-Vatan and engaging in transactions, users acknowledge full understanding and acceptance of all terms, waiving any conflicting claims or interpretations.</p>
                        </div>
                        
                        <div style="text-align: center; margin-top: 3rem; padding: 2.5rem; background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); border-radius: 20px; color: white;">
                            <h3 style="color: white; margin-bottom: 1rem; font-size: 2rem;">WELCOME!</h3>
                            <p style="color: white; margin: 0; font-size: 1.1rem;">To Onboard the i-Vatan (iApp) platform.</p>
                            <p style="color: white; margin-top: 1.5rem; font-size: 1.3rem; font-weight: 600;">THANK YOU!</p>
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
                       
                   
                    <li><a href="{{ route('web.pricing-plan') }}">Pricing Plan</a></li>
                    <li><a href="{{ route('web.quickhire') }}">QuickHire</a></li>
                    <li><a href="{{ route('web.market') }}">Mart</a></li>
                        
                        <!--<li><a href="#">Features</a></li>-->
                        <!--<li><a href="#">Security</a></li>-->
                        <!--<li><a href="#">Download</a></li>-->
                        <!--<li><a href="#">Web App</a></li>-->
                        <!--<li><a href="#">Business</a></li>-->
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Company</h6>
                    <ul class="footer-links">
                        <!--<li><a href="#">About</a></li>-->
                        <!--<li><a href="#">Careers</a></li>-->
                        <!--<li><a href="#">Brand Center</a></li>-->
                        <li><a href="{{ route('web.trust') }}">Trust</a></li>
                         <li><a href="{{ route('web.privacy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('web.terms') }}">Terms and Conditions</a></li>
                        <li><a href="{{ route('web.financial-policy') }}">Payment and Refund Policy</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Download</h6>
                    <ul class="footer-links">
                        <li><a href="https://play.google.com/store/apps/details?id=com.octroid.ivatan"><i class="fab fa-android"></i> Android</a></li>
                        <!--<li><a href="#">iPhone</a></li>-->
                        <!--<li><a href="#">Mac/PC</a></li>-->
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Help</h6>
                    <ul class="footer-links">
                        <!--<li><a href="#">Help Center</a></li>-->
                        <li><a href="{{ route('web.contact') }}">Contact Us</a></li>
                        <!--<li><a href="#">Security Advisories</a></li>-->
                    </ul>
                </div>
            </div>
            <hr style="border-color: #333; margin: 2rem 0;">
            <div class="row align-items-center">
              <div class="col-md-6 mb-3 mb-md-0">
    <p class="text-white mb-0">&copy; 2024 Octroid Pvt. Ltd. | i-Vatan (iApp)</p>
</div>
                <div class="col-md-6 text-md-end">
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                       <a href="#" style="text-decoration: none; line-height: 0;">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle;">
        <path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/>
    </svg>
</a>
</a>
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

            $(window).on('scroll', checkFadeUp);
            checkFadeUp();

            $('.sidebar-nav a').on('click', function(e) {
                e.preventDefault();
                var target = $($(this).attr('href'));
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
                    
                    if (refElement.position().top <= scrollPos && refElement.position().top + refElement.height() > scrollPos) {
                        $('.sidebar-nav a').removeClass('active');
                        $('.sidebar-nav a[href="#' + currLink.attr('id') + '"]').addClass('active');
                    }
                });
            });
        });
    </script>
</body>
</html>