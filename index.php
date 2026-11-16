<?php
require_once __DIR__ . "/includes/auth.php";
$isLoggedIn = isset($_SESSION['user']);
$isAdmin = $isLoggedIn && (($_SESSION['role'] ?? '') === 'admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Youth Education Support connects students with scholarships, tutoring, study resources and educational opportunities.">
    <title>Youth Education Support | Opportunities for Every Student</title>
    <link rel="icon" type="image/svg+xml" href="assets/yes-logo.svg">
    <link rel="stylesheet" href="style.css?v=8">
</head>
<body<?= $isLoggedIn ? ' class="has-app-sidebar"' : '' ?>>
<?php require_once __DIR__ . "/includes/sidebar.php"; ?>

<header class="site-header">
    <div class="nav-shell">
        <a class="brand" href="index.php" aria-label="Youth Education Support home">
            <img src="assets/yes-logo.svg" alt="Youth Education Support logo">
            <span>Youth Education <strong>Support</strong></span>
        </a>
        <div class="header-actions">
            <button class="theme-toggle" type="button" onclick="toggleDarkMode()" aria-label="Toggle dark mode"><span class="theme-toggle-icon">◐</span> <span class="theme-toggle-label">Dark mode</span></button>
            <button class="menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false" onclick="toggleSiteMenu()">☰</button>
        </div>
        <nav class="main-nav" id="mainNav">
            <a class="active" href="index.php">Home</a>
            <?php if ($isLoggedIn): ?>
                <?php if ($isAdmin): ?><a href="admin_dashboard.php">Admin</a><?php else: ?><a href="dashboard.php">Dashboard</a><?php endif; ?>
                <a href="scholarships.php">Scholarships</a>
                <a href="tutors.php">Tutoring</a>
                <a href="materials.php">Resources</a>
                <a href="events.php">Events</a>
                <a href="volunteer.php">Volunteer</a>
                <a href="contact.php">Contact</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="#opportunities">Our services</a>
                <a href="#about">About</a>
                <a href="scholarships.php">Scholarships</a>
                <a href="tutors.php">Tutoring</a>
                <a href="contact.php">Contact</a>
                <a class="nav-login" href="login.php">Log in</a>
                <a class="nav-donate" href="donations.php">Donate</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main>
    <section class="hero-section">
        <div class="hero-pattern"></div>
        <div class="hero-inner">
            <div class="hero-copy">
                <span class="eyebrow"><span class="eyebrow-dot"></span> YOUR FUTURE STARTS HERE</span>
                <h1>Opening doors to a <em>brighter future.</em></h1>
                <p class="hero-lead">Education creates possibilities. Find the support, resources and opportunities you need to take your next step with confidence.</p>
                <div class="hero-actions">
                    <?php if ($isLoggedIn): ?>
                        <a class="btn btn-orange" href="<?= $isAdmin ? 'admin_dashboard.php' : 'dashboard.php' ?>">Go to dashboard <span>→</span></a>
                    <?php else: ?>
                        <a class="btn btn-orange" href="register.php">Get started <span>→</span></a>
                    <?php endif; ?>
                    <a class="btn btn-outline" href="#opportunities">Explore opportunities</a>
                </div>
                <div class="hero-proof">
                    <div class="proof-icon">✦</div>
                    <div><strong>One place. More possibilities.</strong><small>Scholarships, tutoring and learning resources.</small></div>
                </div>
            </div>
            <div class="hero-visual">
                <img class="hero-photo" src="https://psu-gatsby-files-prod.s3.amazonaws.com/s3fs-public/styles/4_3_1500w/public/2024/10/penn-state-learning-2023.jpg?h=427191ee&itok=82NBoRSZ" alt="Illustration of students learning and working together">
                <div class="photo-label"><span class="label-mark">✦</span><span><strong>Learn. Grow. Succeed.</strong><small>Building brighter futures together</small></span></div>
                <div class="floating-note"><span class="note-icon">🎓</span><span><strong>Your next chapter</strong><small>Starts with an opportunity</small></span></div>
            </div>
        </div>
        <div class="hero-bottom-shape"></div>
    </section>

    <section class="trust-strip" aria-label="Our focus areas">
        <div><span class="trust-symbol">◎</span><span><strong>Student-centred</strong><small>Support that starts with you</small></span></div>
        <div><span class="trust-symbol">✧</span><span><strong>Opportunity-led</strong><small>Resources for your next step</small></span></div>
        <div><span class="trust-symbol">↗</span><span><strong>Future-focused</strong><small>Skills for lifelong growth</small></span></div>
    </section>

    <section class="section opportunities" id="opportunities">
        <div class="section-heading">
            <span class="section-kicker">WHAT WE OFFER</span>
            <h2>Find the support <span>that moves you forward.</span></h2>
            <p>Explore services designed to help you learn, develop your skills and make the most of your education.</p>
        </div>
        <div class="service-grid">
            <a class="service-card service-scholarship" href="scholarships.php">
                <div class="service-icon">🎓</div><span class="card-tag">FUND YOUR STUDIES</span>
                <h3>Scholarships</h3><p>Explore funding opportunities that can help you continue your education.</p><span class="card-link">Explore scholarships <b>→</b></span>
            </a>
            <a class="service-card service-tutoring" href="tutors.php">
                <div class="service-icon">✎</div><span class="card-tag">GET ACADEMIC SUPPORT</span>
                <h3>Tutoring</h3><p>Connect with tutoring support and build confidence in your studies.</p><span class="card-link">Find tutoring <b>→</b></span>
            </a>
            <a class="service-card service-materials" href="materials.php">
                <div class="service-icon">▤</div><span class="card-tag">KEEP LEARNING</span>
                <h3>Study materials</h3><p>Access learning resources to support your academic journey.</p><span class="card-link">View resources <b>→</b></span>
            </a>
            <a class="service-card service-events" href="events.php">
                <div class="service-icon">▦</div><span class="card-tag">GET INVOLVED</span>
                <h3>Events & workshops</h3><p>Stay up to date with educational events, activities and announcements.</p><span class="card-link">See what's on <b>→</b></span>
            </a>
            <a class="service-card service-volunteer" href="volunteer.php">
                <div class="service-icon">♡</div><span class="card-tag">MAKE A DIFFERENCE</span>
                <h3>Volunteer</h3><p>Share your time and skills to help create opportunities for others.</p><span class="card-link">Get involved <b>→</b></span>
            </a>
            <a class="service-card service-donate" href="donations.php">
                <div class="service-icon">✦</div><span class="card-tag">SUPPORT THE MISSION</span>
                <h3>Donations</h3><p>Help strengthen access to educational support and learning resources.</p><span class="card-link">Support students <b>→</b></span>
            </a>
        </div>
    </section>

    <section class="feature-section" id="about">
        <div class="feature-image-wrap"><img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1100&q=85" alt="Students participating in a classroom learning session"><div class="image-accent"></div></div>
        <div class="feature-copy"><span class="section-kicker">ABOUT YOUTH EDUCATION SUPPORT</span><h2>More than information. <span>A pathway forward.</span></h2><p>Youth Education Support brings important educational opportunities and support services together in one accessible platform. Whether you are looking for funding, academic guidance or useful learning resources, the aim is to make it easier to take your next step.</p><ul class="check-list"><li><span>✓</span> Discover educational opportunities</li><li><span>✓</span> Connect with academic support</li><li><span>✓</span> Stay informed and involved</li></ul><a class="text-link" href="register.php">Become part of the community <b>→</b></a></div>
    </section>

    <section class="quote-band"><div class="quote-mark">“</div><p>Every opportunity to learn is a chance to shape a brighter future.</p><span>YOUTH EDUCATION SUPPORT</span></section>

    <section class="cta-section">
        <div class="cta-content"><span class="section-kicker">TAKE YOUR NEXT STEP</span><h2>Your goals matter.<br><span>Let's work towards them.</span></h2><p>Join Youth Education Support and start exploring the opportunities available to you.</p><div class="hero-actions"><a class="btn btn-orange" href="<?= $isLoggedIn ? ($isAdmin ? 'admin_dashboard.php' : 'dashboard.php') : 'register.php' ?>"><?= $isLoggedIn ? 'Visit your dashboard' : 'Create your account' ?> <span>→</span></a><a class="btn btn-light-outline" href="contact.php">Contact us</a></div></div>
        <div class="cta-decoration"><div class="cta-circle circle-one"></div><div class="cta-circle circle-two"></div><div class="cta-cap">✦</div></div>
    </section>
</main>

<footer class="site-footer">
    <div class="footer-main">
        <div class="footer-brand"><a class="brand brand-footer" href="index.php"><img src="assets/yes-logo.svg" alt=""><span>Youth Education <strong>Support</strong></span></a><p>Connecting students with educational opportunities, resources and support for a brighter future.</p></div>
        <div class="footer-column"><h4>Explore</h4><a href="scholarships.php">Scholarships</a><a href="tutors.php">Tutoring</a><a href="materials.php">Study materials</a><a href="events.php">Events</a></div>
        <div class="footer-column"><h4>Get involved</h4><a href="volunteer.php">Volunteer</a><a href="donations.php">Donate</a><a href="contact.php">Contact us</a></div>
        <div class="footer-column"><h4>Your account</h4><?php if ($isLoggedIn): ?><a href="profile.php">My profile</a><a href="logout.php">Log out</a><?php else: ?><a href="login.php">Log in</a><a href="register.php">Register</a><?php endif; ?></div>
    </div>
    <div class="footer-bottom"><span>© <?= date('Y') ?> Youth Education Support. All rights reserved.</span><span>Learning today. Building tomorrow.</span></div>
</footer>

<script src="darkmode.js"></script>
<script>
function toggleSiteMenu() {
    const nav = document.getElementById('mainNav');
    const button = document.querySelector('.menu-toggle');
    const isOpen = nav.classList.toggle('is-open');
    button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
}
</script>
</body>
</html>
