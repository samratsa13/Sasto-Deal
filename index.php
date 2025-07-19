<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sasto Deal - Home</title>
    <link rel="stylesheet" href="idx.css">
<link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- <script>
        window.addEventListener("load", function() {
            const loader = document.getElementById("loader");
            setTimeout(() => {
                loader.style.animation = "fadeOut 1s forwards";
                loader.addEventListener('animationend', () => {
                    loader.style.display = "none";
                    document.body.style.overflow = "auto";
                });
            }, 2000);
        });
    </script> -->








    <!-- navigation bar ko lagi -->
        <script>
            
    document.addEventListener('DOMContentLoaded', () => {
  const menuIcon = document.getElementById('menu-icon');
  const navLinks = document.getElementById('nav-links');

  menuIcon.addEventListener('click', () => {
    navLinks.classList.toggle('active');
  });

  navLinks.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      navLinks.classList.remove('active');
    });
  });
});

// dropdown user nav

document.addEventListener('DOMContentLoaded', () => {
  const userIcon = document.getElementById('user-icon');
  const userDropdown = document.getElementById('user-dropdown');

  userIcon.addEventListener('click', () => {
    userDropdown.classList.toggle('show');
  });

  // Close dropdown if clicked outside
  document.addEventListener('click', (event) => {
    if (!userIcon.contains(event.target) && !userDropdown.contains(event.target)) {
      userDropdown.classList.remove('show');
    }
  });
});

</script>

</head>
<body>
    <!-- <div id="loader">
        <img src="img/sasto deal single.png" alt="Loading...">
    </div> -->

    <header class="hero-section">
        <nav class="navbar">
            <div class="logo">
                <img src="img/sasto deal single.png" alt="Sasto Deal Logo">
                <span>SASTO <span class="highlight">DEAL</span></span>
            </div>
              <div class="menu-icon" id="menu-icon">
            <i class="fas fa-bars"></i>
        </div>
            <ul class="nav-links" id="nav-links">
                <li><a href="#">Home</a></li>
                <li><a href="#model">Exclusive Models</a></li>
                <li><a href="#">Order Now</a></li>
                <li class="user-menu">
  <i class="fas fa-user" id="user-icon" style="cursor:pointer; font-size: 20px; color: maroon;"></i>
  <ul class="user-dropdown" id="user-dropdown">
    <li><a href="login.php">Login</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</li>

            </ul>
        </nav>
        <div class="hero-content">
            <h1>Welcome to the Future of <span class="highlight">Luxury Cars</span></h1>
            <p>Experience speed, elegance, and unmatched design with Sasto Deal.</p>
            <a href="carmodels.php"><button class="explore-btn">Explore Models</button></a>
        </div>
    </header>

<section class="models-section" id="model">
    <h2>Our Exclusive Models</h2>
    <?php
$conn = new mysqli("localhost", "root", "", "sastodeal_db"); // Adjust DB name as needed
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM car_models");

while ($row = $result->fetch_assoc()) {
    echo '
    <div class="card">
        <div class="card-image">
            <img src="' . $row["image_path"] . '" alt="' . $row["model_name"] . '">
            <div class="card-overlay">
                <h3>' . $row["model_name"] . '</h3>
                <p>' . $row["description"] . '</p>
                <a href="order.php?model=' . urlencode($row["model_name"]) . '" class="order-btn">Order Now</a>
            </div>
        </div>
    </div>';
}
$conn->close();
?>
</section>


    <!-- About Sasto Deal Section -->
    <section class="about-sasto-deal">
        <div class="about-content">
            <h2>About Sasto Deal</h2>
            <p>At Sasto Deal, we redefine luxury and affordability in the world of automobiles. Our mission is to provide high-end vehicles that combine elegance, performance, and futuristic technology, all at unbeatable prices. Explore the finest selection of exclusive car models tailored to deliver an exceptional driving experience.</p>
            <p>We believe that luxury should not be limited to a select few. Our dedicated team of experts handpicks the finest cars from around the globe, ensuring each model we offer is a masterpiece of design and innovation. From electric marvels that shape the future of driving to powerful machines that embody speed and precision, Sasto Deal brings you the best of both worlds.</p>
            <p>Discover the thrill of driving excellence—only with Sasto Deal.</p>
        </div>
        <div class="about-image">
            <div class="image-overlay"></div>
            <img src="img/supra.webp" alt="Luxury Car">
        </div>
    </section>

 <!-- Contact Us Section -->
 <section class="contact-us">
        <div class="contact-left">
            <img src="img/sasto deal.png" alt="Sasto Deal Logo" class="contact-logo">
            <h2>Contact Us</h2>
            <p><strong>Address:</strong> 123 Luxury Street, Kathmandu, Nepal</p>
            <p><strong>Phone:</strong> +977 9876543210</p>
            <p><strong>Email:</strong> support@sastodeal.com</p>

            <div class="social-media">
                <a href="https://facebook.com" target="_blank">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://linkedin.com" target="_blank">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="https://instagram.com" target="_blank">
                    <i class="fab fa-instagram"></i>
                </a>
            </div>
        </div>

        <div class="contact-right">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3532.697598084336!2d84.43498147510937!3d27.678123224799552!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3994fb61f3664b81%3A0x8e373d5dcfdc9a2e!2sBharatpur%2C%20Nepal!5e0!3m2!1sen!2snp!4v1684239274567!5m2!1sen!2snp"
                width="400"
                height="400"
                style="border: 0;"
                allowfullscreen=""
                loading="lazy">
            </iframe>
        </div>
    </section>

    <footer>
        <p>&copy; 2025 Sasto Deal. All rights reserved.</p>
    </footer>
</body>
</html>