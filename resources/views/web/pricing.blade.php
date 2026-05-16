<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Plans - i-Vatan (iApp)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #000000;
            --secondary-color: #1a1a1a;
            --text-dark: #000000;
            --text-light: #666666;
            --bg-light: #f5f5f5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            background-color: #ffffff;
        }

        /* Navbar Styles */
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
            color: white;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--bg-light) 0%, #e0e0e0 100%);
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

        /* Pricing Tables */
        .pricing-section {
            padding: 80px 0;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background-color: var(--primary-color);
            border-radius: 2px;
        }

        .table-custom {
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            border-radius: 15px;
            overflow: hidden;
            background: white;
        }

        .table-custom thead th {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            font-weight: 600;
            border: none;
            vertical-align: middle;
        }

        .table-custom thead th span.price {
            display: block;
            font-size: 1.2rem;
            font-weight: 700;
            margin-top: 5px;
            color: #e0e0e0;
        }

        .table-custom tbody td {
            padding: 18px 15px;
            vertical-align: middle;
            color: var(--text-light);
            border-color: #f0f0f0;
        }

        .table-custom tbody tr:hover {
            background-color: var(--bg-light);
        }

        .feature-name {
            font-weight: 600;
            color: var(--text-dark) !important;
            text-align: left;
        }

        .icon-yes {
            color: #198754; /* Bootstrap Success Green */
            font-size: 1.2rem;
        }

        .icon-no {
            color: #dc3545; /* Bootstrap Danger Red */
            font-size: 1.2rem;
        }

        /* Footer */
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

        /* Animations */
        .fade-up {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        .fade-up.active {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }
            .section-title {
                font-size: 2rem;
            }
            .table-custom tbody td {
                font-size: 0.9rem;
                padding: 12px 10px;
            }
        }
    </style>
</head>
<body>

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
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section text-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto">
                    <div class="hero-content fade-up">
                        <h1>Our Pricing Plans</h1>
                        <p>Choose the perfect plan to boost your visibility, access advanced AI tools, and grow your presence on i-Vatan. Tailored plans for everyday Users and professional Creators.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="pricing-section bg-white">
        <div class="container text-center">
            <h2 class="section-title fade-up">User Plans</h2>
            <p class="text-muted mb-5 fade-up">Explore our newly structured plans for everyday users.</p>
            
            <div class="table-responsive fade-up">
                <table class="table table-custom text-center align-middle">
                    <thead>
                        <tr>
                            <th class="text-start">Feature</th>
                            <th>Open<span class="price">Free</span></th>
                            <th>Plus<span class="price">₹119</span></th>
                            <th>Growth<span class="price">₹149</span></th>
                            <th>Pro+<span class="price">₹299</span></th>
                            <th>Prime<span class="price">₹549</span></th>
                            <th>Infinity<span class="price">₹999</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="feature-name">Visibility Multiplier</td>
                            <td>1.0x</td>
                            <td>1.2x</td>
                            <td>1.4x</td>
                            <td>1.8x</td>
                            <td>2.5x</td>
                            <td><strong>4.0x</strong></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Ads Frequency</td>
                            <td>High</td>
                            <td>Medium</td>
                            <td>Medium</td>
                            <td>Low</td>
                            <td>Low</td>
                            <td><strong>Major low</strong></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Job Priority</td>
                            <td>0</td>
                            <td>0</td>
                            <td>1</td>
                            <td>3</td>
                            <td>4</td>
                            <td>5</td>
                        </tr>
                        <tr>
                            <td class="feature-name">DM Recruiters</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Boost Credits</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>1</td>
                            <td>3</td>
                            <td>5</td>
                        </tr>
                        <tr>
                            <td class="feature-name">AI Tools</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Tipping (Collect)*</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Sell Services**</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Affiliate</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Ad Revenue</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-start mt-3 fade-up">
                <small class="text-muted">* Tipping: To give all users allowed but for collect only plan wise.</small><br>
                <small class="text-muted">** Sell Services: Only depends on profile change in service.</small>
            </div>
        </div>
    </section>

    <section class="pricing-section" style="background-color: var(--bg-light);">
        <div class="container text-center">
            <h2 class="section-title fade-up">Creator Plans</h2>
            <p class="text-muted mb-5 fade-up">Turn your passion into a business with our Creator models.</p>
            
            <div class="table-responsive fade-up">
                <table class="table table-custom text-center align-middle">
                    <thead>
                        <tr>
                            <th class="text-start">Feature</th>
                            <th>Free Creator<span class="price">Entry / Testing</span></th>
                            <th>Creator Start<span class="price">₹299 <small style="font-size: 0.8rem; font-weight: 400; display:block;">(Growth + Early Earning)</small></span></th>
                            <th>Creator Pro<span class="price">₹699 <small style="font-size: 0.8rem; font-weight: 400; display:block;">(Full Business Mode)</small></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="feature-name">Creator Badge</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                            <td><i class="fas fa-check icon-yes"></i> <span class="badge bg-dark ms-1">Highlighted</span></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Visibility Boost (Base)</td>
                            <td>1.1x</td>
                            <td>1.8x</td>
                            <td><strong>3.0x</strong></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Content Reach Priority</td>
                            <td>Low</td>
                            <td>Medium</td>
                            <td><strong>High</strong></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Monetization Access</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td>Limited</td>
                            <td><strong>Full</strong></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Tipping (i-ShoutPay™)</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Boost Credits</td>
                            <td>0</td>
                            <td>3 / month</td>
                            <td>15 / month</td>
                        </tr>
                        <tr>
                            <td class="feature-name">Creator Analytics</td>
                            <td>Low <small class="text-muted d-block">(Basic like of graph per bar table)</small></td>
                            <td>Basic</td>
                            <td>Advanced</td>
                        </tr>
                        <tr>
                            <td class="feature-name">Local Discovery Listing</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                            <td><strong>Priority</strong></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Creator Storefront</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Sell Services / Gigs</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td>Limited</td>
                            <td><strong>Full</strong></td>
                        </tr>
                        <tr>
                            <td class="feature-name">UPI Payments</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Affiliate Earnings</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Ad Revenue Share</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                        </tr>
                        <tr>
                            <td class="feature-name">AI Content Assistant</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Profile Customization</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td>Limited</td>
                            <td><strong>Full</strong></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Creator Score (Trust Rank)</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td><i class="fas fa-check icon-yes"></i></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Collab Access</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td>Basic</td>
                            <td><strong>Priority</strong></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Brand Deal Visibility</td>
                            <td><i class="fas fa-times icon-no"></i></td>
                            <td>Limited</td>
                            <td><strong>High</strong></td>
                        </tr>
                        <tr>
                            <td class="feature-name">Support Level</td>
                            <td>Basic</td>
                            <td>Standard</td>
                            <td><strong>Priority</strong></td>
                        </tr>
                    </tbody>
                </table>
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
            // Navbar scroll effect
            $(window).scroll(function() {
                if ($(this).scrollTop() > 50) {
                    $('.navbar').addClass('scrolled');
                } else {
                    $('.navbar').removeClass('scrolled');
                }
            });

            // Fade up animation logic
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
        });
    </script>
</body>
</html>