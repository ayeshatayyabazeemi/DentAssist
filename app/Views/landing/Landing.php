<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DentAssist - Dental Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

  <link rel="stylesheet" href="assets/css/landing.css" />
</head>
<body>

  <!-- ===== Navbar ===== -->
  <nav class="navbar">
    <div class="container">
      <div class="logo">DentAssist</div>

      <ul class="nav-links">
        <li><a href="#home">Home</a></li>
        <li><a href="#features">Features</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#contact">Contact</a></li>
      </ul>

      <!--  CodeIgniter dynamic link -->
      <a href="<?= base_url('/login'); ?>" class="btn-login">Login</a>
       


      <!-- Hamburger (for mobile) -->
      <div class="menu-toggle" id="menuToggle">☰</div>
    </div>
  </nav>

  <!-- ===== Hero Section ===== -->
  <section id="home" class="hero">
    <div class="overlay"></div>
    <div class="hero-content">
      <h1>DentAssist — Smart Care for Every Smile.</h1>
      <p>Streamline appointments, patient records, and billing with our comprehensive hospital management system.</p>
      <div class="hero-buttons">
        <a href="#features" class="btn-login">Get Started</a>
        <a href="#about" class="btn-outline">Learn More</a>
</div>

      </div>
    </div>
  </section>

  <!-- ===== Features Section ===== -->
  <!-- ===== Features Section (Blue Theme Refined) ===== -->
<section id="features" class="features">
  <div class="container">
    <h2 class="section-title">Powerful Features</h2>
    <p class="subtitle">All-in-one solution for smooth dental hospital management.</p>

    <div class="feature-grid">
      <div class="feature-card">
        <div class="icon-box">
          <i class="fa-solid fa-calendar-check"></i>
        </div>
        <h3>Appointment Scheduling</h3>
        <p>Efficiently manage patient appointments with our intuitive calendar system. Reduce no-shows and optimize your clinic workflow.</p>
      </div>

      <div class="feature-card">
        <div class="icon-box">
          <i class="fa-solid fa-file-medical"></i>
        </div>
        <h3>Patient Records</h3>
        <p>Securely store and access complete patient histories, treatment plans, and medical records from one centralized location.</p>
      </div>

      <div class="feature-card">
        <div class="icon-box">
          <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
        <h3>Billing & Payments</h3>
        <p>Streamline your billing process with automated invoicing, payment tracking, and insurance claim management.</p>
      </div>
    </div>
  </div>
</section>

      

  <!-- ===== Why Choose Us Section ===== -->
   <section id="about" class="why-choose-us">

    <div class="why-container">
      <div class="why-image">
     <img src="assets/images/why.png" alt="Dental Clinic Team">

    </div>

    <div class="why-content">
      <h2>Why Choose DentAssist?</h2>
      <p>
        DentAssist simplifies your clinic operations by combining all essential tools
        into one easy-to-use platform. Our goal is to help you focus more on patients
        and less on paperwork.
      </p>

      <ul class="why-points">
        <li><i class="fa-solid fa-check-circle"></i> Easy appointment scheduling</li>
        <li><i class="fa-solid fa-check-circle"></i> Secure patient data management</li>
        <li><i class="fa-solid fa-check-circle"></i> Real-time analytics and reports</li>
        <li><i class="fa-solid fa-check-circle"></i> 24/7 customer support</li>
      </ul>

      <a href="#features" class="learn-more-btn">Learn More</a>
    </div>
  </div>
</section>




<!-- ===== Contact Section ===== -->
<section id="contact" class="contact-section">
  <div class="container contact-container">
    <!-- Left: Contact Info -->
    <div class="contact-info">
      <h2>Contact Us</h2>
      <p>We'd love to hear from you! Get in touch for inquiries, demos, or support.</p>

      <div class="info-card">
        <i class="fa-solid fa-envelope"></i>
        <div>
          <h4>Email</h4>
          <p>support@dentassist.com</p>
        </div>
      </div>

      <div class="info-card">
        <i class="fa-solid fa-phone"></i>
        <div>
          <h4>Phone</h4>
          <p>(021) 34931627</p>
        </div>
      </div>

      <div class="info-card">
        <i class="fa-solid fa-location-dot"></i>
        <div>
          <h4>Address</h4>
          <p>A-128, Block 8, F.B. Area</p>
        </div>
      </div>
    </div>

    <!-- Right: Contact Form -->
    <form class="contact-form">
      <input type="text" placeholder="Your Name" required>
      <input type="email" placeholder="Your Email" required>
      <textarea placeholder="Your Message" rows="5" required></textarea>
      <button type="submit" class="btn-outline">Send Message</button>
    </form>
  </div>
</section>


  
  <!-- ===== Footer ===== -->
  <footer class="footer">
  <div class="footer-container">
    <div class="footer-logo">
      <h3>DentAssist</h3>
      <p>Modern dental management made simple and efficient.</p>
    </div>

    <div class="footer-links">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="#features">Features</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#contact">Contact</a></li>
      </ul>
    </div>

    <div class="footer-contact">
      <h4>Contact</h4>
      <p><i class="fa-solid fa-envelope"></i> support@dentassist.com</p>
      <p><i class="fa-solid fa-phone"></i> (021) 34931627</p>
    </div>

    <div class="footer-social">
      <h4>Follow Us</h4>
      <div class="social-icons">
        <a href="#"><i class="fa-brands fa-facebook"></i></a>
        <a href="#"><i class="fa-brands fa-twitter"></i></a>
        <a href="#"><i class="fa-brands fa-linkedin"></i></a>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <p>© 2025 DentAssist. All Rights Reserved.</p>
  </div>
</footer>


  <script src="assets/js/landing.js"></script>
</body>
</html>