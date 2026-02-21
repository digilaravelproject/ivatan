<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - i-Vatan (iApp)</title>
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

        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
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
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <div class="hero-content fade-up">
                        <h1>Terms and Conditions</h1>
                        <p>Please read these terms carefully before using i-Vatan. Your use of the platform constitutes acceptance of these terms.</p>
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
                            <li><a href="#introduction" class="active">Introduction</a></li>
                            <li><a href="#in-app-use">In-App Use</a></li>
                            <li><a href="#app-activity">App Activity</a></li>
                            <li><a href="#trust-ecosystem">Trust Ecosystem</a></li>
                            <li><a href="#data-usability">Data Usability</a></li>
                            <li><a href="#connectivity">User Connectivity</a></li>
                            <li><a href="#privacy-control">Privacy & Control</a></li>
                            <li><a href="#vulnerability">Data Vulnerability</a></li>
                            <li><a href="#legal-framework">Legal Framework</a></li>
                            <li><a href="#acceptance">Acceptance</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="terms-section fade-up" id="introduction">
                        <div class="icon-box">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <h2>1. General Introduction</h2>
                        
                        <p>Welcome to <strong>i-Vatan (iApp)</strong>, a social media + digital service platform operated by <strong>Octroid Pvt. Ltd.</strong>, registered in India under applicable laws. These Terms and Conditions ("T&C") govern your use of the i-Vatan application ("iApp"), website, and all associated services.</p>

                        <p>By downloading, accessing, or using the platform, you agree to be bound by these T&C.</p>

                        <div class="highlight-box">
                            <h5><i class="fas fa-info-circle"></i> Important Notice</h5>
                            <p>i-Vatan (iApp) is an intermediary service provider that facilitates content sharing, job discovery, marketplace access, user communication, and creator monetization. We do not hold any liability or responsibility for user-generated actions or content and operate solely as a bridge between users and their interactive digital environment.</p>
                        </div>
                    </div>

                    <div class="terms-section fade-up delay-1" id="in-app-use">
                        <div class="icon-box">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h2>2. In-App Use</h2>
                        
                        <p>The i-Vatan platform, in its first phase launch, includes the following features:</p>
                        
                        <ul>
                            <li>User profile creation and content posting (video, photo, text)</li>
                            <li>Creator channel and monetization access</li>
                            <li>i-Marketplace for user-led buying/selling</li>
                            <li>i-QuickHire Job hiring and applying services</li>
                            <li>AI-powered content feed</li>
                            <li>Community forum and alerts</li>
                            <li>Personalization and analytics for better experience</li>
                            <li>Trust Score System for authorized and behavior monitoring</li>
                        </ul>

                        <h3>Data Usage Policy</h3>
                        <p>User activity is monitored only for the purpose of enhancing app performance, detecting spam/fraud, and recommending personalized content.</p>

                        <p>The collected data will be stored within Indian jurisdiction and used only when both user and company gain value from it (example: ads, job alerts).</p>

                        <div class="highlight-box">
                            <h5><i class="fas fa-exclamation-triangle"></i> Prohibited Activities</h5>
                            <p>No part of i-Vatan is to be used to promote or perform illegal, harmful, or unauthorized activities. Breach will result in permanent suspension and legal liability under IT Act, 2000.</p>
                        </div>
                    </div>

                    <div class="terms-section fade-up" id="app-activity">
                        <div class="icon-box">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h2>3. App Activity</h2>
                        
                        <h3>Permitted Activities</h3>
                        <p>Users may engage in the following activities:</p>
                        <ul>
                            <li>Posting content: video, audio, text, jobs, ads, products</li>
                            <li>Messaging with other users or service providers</li>
                            <li>Liking, commenting, saving, following content or users</li>
                            <li>Applying for jobs or publishing hiring requests</li>
                            <li>Selling products/services in the i-Marketplace</li>
                        </ul>

                        <h3>Activity Tracking</h3>
                        <p>All such activity is tracked and stored for the following purposes:</p>
                        <ul>
                            <li>Personalized content recommendations</li>
                            <li>Content moderation and reporting</li>
                            <li>Community health and trend analysis</li>
                            <li>Technical and UX improvement</li>
                        </ul>

                        <div class="highlight-box">
                            <h5><i class="fas fa-shield-alt"></i> Data Disclosure</h5>
                            <p>i-Vatan will not disclose personal activity data to any third party unless:</p>
                            <ul style="margin-left: 0; padding-left: 1.5rem; margin-top: 0.5rem;">
                                <li>Required under law</li>
                                <li>Mutual consent is obtained from the user</li>
                            </ul>
                        </div>
                    </div>

                    <div class="terms-section fade-up delay-1" id="trust-ecosystem">
                        <div class="icon-box">
                            <i class="fas fa-users"></i>
                        </div>
                        <h2>4. User Control and Trust Ecosystem</h2>
                        
                        <p>i-Vatan is built on trust, accountability, and ethical interaction. Any user involved in fraud, impersonation, harassment, or fake content shall be penalized under relevant sections of the IT Act, 2000 and Indian Penal Code.</p>

                        <h3>User Control Rights</h3>
                        <p>Users are granted full control to:</p>
                        <ul>
                            <li>View their historical activities (on request)</li>
                            <li>Edit or delete personal content</li>
                            <li>Request account deactivation</li>
                            <li>Receive notifications and alerts</li>
                        </ul>

                        <h3>Consequences of Trust Violations</h3>
                        <p>Any attempts to manipulate trust - such as misleading claims, fake jobs/products, or abuse of features, shall result in:</p>
                        <ul>
                            <li>Immediate account suspension</li>
                            <li>Legal action</li>
                            <li>Blacklisting from all i-Vatan features</li>
                        </ul>
                    </div>

                    <div class="terms-section fade-up" id="data-usability">
                        <div class="icon-box">
                            <i class="fas fa-database"></i>
                        </div>
                        <h2>5. Data Usability and Ethical Tracking</h2>
                        
                        <p>All usage data is collected only to:</p>
                        <ul>
                            <li>Enhance user experience</li>
                            <li>Improve algorithmic recommendations</li>
                            <li>Protect community integrity</li>
                            <li>Inform platform updates</li>
                        </ul>

                        <div class="highlight-box">
                            <h5><i class="fas fa-lock"></i> Data Protection Guarantee</h5>
                            <p>Data is not sold or transferred to external advertisers or platforms.</p>
                        </div>

                        <h3>Strict Prohibitions</h3>
                        <p>Downloading, duplicating, or exporting user data from i-Vatan for external platform use without written consent of the company is prohibited and punishable under Indian law.</p>

                        <p>i-Vatan reserves the right to collect anonymized trend data for research and development.</p>

                        <p><strong>Warning:</strong> Any misuse of stored user data will lead to permanent ban, FIR registration, and potential legal action.</p>
                    </div>

                    <div class="terms-section fade-up delay-1" id="connectivity">
                        <div class="icon-box">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h2>6. User Connectivity</h2>
                        
                        <p>Users are permitted to connect with others within the i-Vatan ecosystem, either with context (mutual interest, posts, jobs, marketplace) or without it.</p>

                        <h3>Connection Guidelines</h3>
                        <p>Users may connect with others for:</p>
                        <ul>
                            <li>Communication</li>
                            <li>Transaction</li>
                            <li>Collaboration</li>
                            <li>Discovery</li>
                        </ul>

                        <p>Connections may occur with or without prior context, but users are expected to behave responsibly.</p>

                        <h3>Prohibited Behavior</h3>
                        <p>Any misuse of this feature, including harassment, abuse, spam, unsolicited promotional messages, or harmful behavior, will result in:</p>
                        <ul>
                            <li>Temporary or permanent suspension of account</li>
                            <li>Restriction of messaging and connection features</li>
                            <li>Disclosure of offending account data to authorities (if required)</li>
                            <li>Complete feature lockdown until further review</li>
                        </ul>

                        <div class="highlight-box">
                            <h5><i class="fas fa-handshake"></i> Expected Conduct</h5>
                            <p>All users are expected to maintain a civil, respectful, and disciplined tone while engaging with others, as per community guidelines and Indian cyber laws. Users must represent a respectful environment on i-Vatan.</p>
                        </div>
                    </div>

                    <div class="terms-section fade-up" id="privacy-control">
                        <div class="icon-box">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h2>7. User Privacy and Control</h2>
                        
                        <p>i-Vatan ensures user identity and profile settings are managed with full privacy control.</p>

                        <h3>Privacy Features</h3>
                        <ul>
                            <li>Users can select anonymous, public, or private visibility modes</li>
                            <li>All shared data is limited to what is needed for service functionality</li>
                            <li>Users can request to view, modify, or delete their profile data</li>
                        </ul>

                        <h3>User Rights</h3>
                        <p>The application architecture is built for consent-based access, where all users can:</p>
                        <ul>
                            <li>Download their data</li>
                            <li>View their engagement activity</li>
                            <li>Modify their visibility status</li>
                        </ul>

                        <div class="highlight-box">
                            <h5><i class="fas fa-check-circle"></i> Consent Mechanisms</h5>
                            <p>Consent mechanisms are clearly displayed for every data-sharing instance, and no hidden tracking occurs. All collected data is encrypted, stored securely, and accessible only with user permission.</p>
                        </div>

                        <p>Trust and understanding are expected between platform and user. Misrepresentation or breach of mutual consent is punishable as per IT Act and platform policies.</p>
                    </div>

                    <div class="terms-section fade-up delay-1" id="vulnerability">
                        <div class="icon-box">
                            <i class="fas fa-shield-virus"></i>
                        </div>
                        <h2>8. Data and Activity Vulnerability</h2>
                        
                        <p>i-Vatan acknowledges that all digital platforms are susceptible to data-related risks.</p>

                        <h3>Security Measures</h3>
                        <p>To minimize vulnerabilities:</p>
                        <ul>
                            <li>We employ encryption, internal audits, firewall protections, and secure login mechanisms</li>
                            <li>Third-party integrations (e.g., payment, delivery) follow strict security compliance</li>
                            <li>Users are prohibited from sharing app content, screenshots, or internal data on external channels for malicious intent</li>
                        </ul>

                        <h3>User Responsibility</h3>
                        <p>Users are advised not to share sensitive information (e.g., OTPs, payment details) via chats or comments.</p>

                        <p>Users are encouraged to report suspicious activity and use multi-step verification wherever possible.</p>

                        <div class="highlight-box">
                            <h5><i class="fas fa-exclamation-triangle"></i> Liability Disclaimer</h5>
                            <p>The app shall not be held responsible for:</p>
                            <ul style="margin-left: 0; padding-left: 1.5rem; margin-top: 0.5rem;">
                                <li>User-side device vulnerabilities</li>
                                <li>Third-party hacking incidents beyond platform control</li>
                                <li>Breach due to user negligence or unethical conduct</li>
                            </ul>
                        </div>

                        <p><strong>Warning:</strong> Any attempt to tamper with system data or export information to other platforms will be tracked and legally pursued.</p>
                    </div>

                    <div class="terms-section fade-up" id="legal-framework">
                        <div class="icon-box">
                            <i class="fas fa-gavel"></i>
                        </div>
                        <h2>9. Legal Framework and Indemnity</h2>
                        
                        <h3>Compliance</h3>
                        <p>These T&C comply with:</p>
                        <ul>
                            <li>Information Technology Act, 2000 (as amended)</li>
                            <li>Consumer Protection (E-Commerce) Rules, 2020</li>
                            <li>Indian Contract Act, 1872</li>
                            <li>Personal Data Protection guidelines (Draft DPDP 2023)</li>
                        </ul>

                        <h3>Intermediary Status</h3>
                        <p>i-Vatan functions only as a digital intermediary.</p>

                        <h3>User Indemnification</h3>
                        <p>Users agree to indemnify and hold Octroid Pvt. Ltd. harmless from any disputes, claims, or legal proceedings arising from their activities on the platform.</p>

                        <div class="highlight-box">
                            <h5><i class="fas fa-map-marker-alt"></i> Jurisdiction</h5>
                            <p>Jurisdiction for all matters shall rest solely with the courts located in Mumbai, Nashik, Maharashtra, INDIA. This may change according to requirement and INDIAN LAW.</p>
                        </div>
                    </div>

                    <div class="terms-section fade-up delay-1" id="acceptance">
                        <div class="icon-box">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <h2>10. Acceptance and Consent Clause</h2>
                        
                        <p>By creating an account or continuing to use the i-Vatan (iApp) platform, users acknowledge that they have:</p>
                        <ul>
                            <li>Read, understood, and agreed to these Terms and Conditions</li>
                            <li>Consented to the collection and use of data as outlined</li>
                            <li>Agreed to behave ethically and lawfully</li>
                        </ul>

                        <div class="highlight-box">
                            <h5><i class="fas fa-signature"></i> Consent Confirmation</h5>
                            <p>By ticking the box during signup/login, I confirm that I have read and accept the i-Vatan (iApp) Terms and Conditions, Privacy Policy, and agree to comply with all applicable laws and platform rules.</p>
                        </div>

                        <h3>Consequences of Non-Compliance</h3>
                        <p>Failure to adhere to any clause may result in partial or complete termination of account privileges without prior notice.</p>

                        <h3>Policy Updates</h3>
                        <p>i-Vatan and Octroid Pvt. Ltd. reserve the right to update, amend, or change these terms at any time, with or without user notice. All users are advised to revisit the terms periodically.</p>

                        <div style="text-align: center; margin-top: 3rem; padding: 2.5rem; background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); border-radius: 20px; color: white;">
                            <h3 style="color: white; margin-bottom: 1rem; font-size: 2rem;">WELCOME!</h3>
                            <p style="color: white; margin: 0; font-size: 1.1rem;">To i-Vatan app by OCTROID PVT. LTD.</p>
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
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Contact: ivatan@octroid.in | hello@ivatan.in</p>
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
            // Scroll progress bar
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

            // Fade up animation
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

            // Smooth scroll for sidebar links
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

            // Active section highlighting
            $(window).on('scroll', function() {
                var scrollPos = $(window).scrollTop() + 150;
                
                $('.terms-section').each(function() {
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