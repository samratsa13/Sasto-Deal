<?php
session_start();

// Handle form submission only on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli("localhost", "root", "", "sastodeal_db");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // DELETE functionality
    if (isset($_POST['model_id'])) {
        $model_id = intval($_POST['model_id']);

        // Delete image file first
        $result = $conn->query("SELECT image_path FROM car_models WHERE id = $model_id");
        if ($row = $result->fetch_assoc()) {
            $image_path = $row['image_path'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        // Then delete record
        $stmt = $conn->prepare("DELETE FROM car_models WHERE id = ?");
        $stmt->bind_param("i", $model_id);
        if ($stmt->execute()) {
            header("Location: admin.php?deleted=1");
            exit();
        } else {
            echo "<script>alert('Error deleting: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }

    // INSERT functionality
    elseif (isset($_POST['model_name']) && isset($_POST['model_description'])) {
        $model_name = $_POST['model_name'];
        $description = $_POST['model_description'];

        // Handle uploaded image
        $image_path = "";
        if (isset($_FILES['model_image']) && $_FILES['model_image']['error'] === UPLOAD_ERR_OK) {
            $image_name = uniqid() . '_' . basename($_FILES['model_image']['name']);
            $image_tmp = $_FILES['model_image']['tmp_name'];
            $upload_dir = "uploads/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $image_path = $upload_dir . $image_name;
            move_uploaded_file($image_tmp, $image_path);
        }

        $stmt = $conn->prepare("INSERT INTO car_models (model_name, description, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $model_name, $description, $image_path);

        if ($stmt->execute()) {
            header("Location: admin.php?success=1");
            exit();
        } else {
            echo "<script>alert('Error inserting: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Sasto Deal - Admin</title>

<link rel="stylesheet" href="ad.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<style>

</style>

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

  const userIcon = document.getElementById('user-icon');
  const userDropdown = document.getElementById('user-dropdown');

  userIcon.addEventListener('click', () => {
    userDropdown.classList.toggle('show');
  });

  document.addEventListener('click', (event) => {
    if (!userIcon.contains(event.target) && !userDropdown.contains(event.target)) {
      userDropdown.classList.remove('show');
    }
  });
});

let deleteForm = null;

function openDeleteModal(modelId) {
  deleteForm = document.querySelector(`form.delete-form[data-model-id='${modelId}']`);
  document.getElementById('deleteModal').style.display = 'flex';
}

document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('cancelDelete').onclick = function() {
    deleteForm = null;
    document.getElementById('deleteModal').style.display = 'none';
  };

  document.getElementById('confirmDelete').onclick = function() {
    if (deleteForm) {
      deleteForm.submit();
    }
  };

  window.onclick = function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target === modal) {
      modal.style.display = "none";
      deleteForm = null;
    }
  };
});
</script>

</head>
<body>

<header class="hero-section">
  <nav class="navbar">
    <div class="logo">
      <img src="img/sasto deal single.png" alt="Sasto Deal Logo" />
      <span>SASTO <span class="highlight">DEAL</span><span class="high">ADMIN</span></span>
    </div>
    <div class="menu-icon" id="menu-icon"><i class="fas fa-bars"></i></div>
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
  <div style="text-align: right; margin-bottom: 10px;">
    <button onclick="openModelForm()" class="add-model-btn">
      <i class="fas fa-plus-circle"></i> Add New Exclusive Vehicle
    </button>
  </div>

  <!-- Modal for adding model -->
  <div id="modelFormModal" class="modal" style="display:none;">
    <div class="modal-content">
      <span class="close-btn" onclick="closeModelForm()" style="cursor:pointer; float:right; font-size:28px;">&times;</span>
      <form id="modelForm" action="admin.php" method="POST" enctype="multipart/form-data">
        <h2>Add New Vehicle Model</h2>
        <input type="text" name="model_name" placeholder="Model Name" required /><br />
        <textarea name="model_description" placeholder="Description" required></textarea><br />
        <input type="file" name="model_image" accept="image/*" required /><br />
        <button type="submit" class="submit-btn">Add Model</button>
      </form>
    </div>
  </div>

  <div class="model-cards">
    <?php
    $conn = new mysqli("localhost", "root", "", "sastodeal_db");
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    $result = $conn->query("SELECT * FROM car_models");

    while ($row = $result->fetch_assoc()) {
      echo '
      <div class="card">
        <div class="card-image">
          <img src="' . htmlspecialchars($row["image_path"]) . '" alt="' . htmlspecialchars($row["model_name"]) . '" />
          <div class="card-overlay">
            <h3>' . htmlspecialchars($row["model_name"]) . '</h3>
            <p>' . htmlspecialchars($row["description"]) . '</p>
            <a href="order.php?model=' . urlencode($row["model_name"]) . '" class="order-btn">Order Now</a>
            <form method="POST" action="admin.php" class="delete-form" data-model-id="' . $row["id"] . '">
              <input type="hidden" name="model_id" value="' . $row["id"] . '" />
              <button type="button" class="delete-btn" onclick="openDeleteModal(' . $row["id"] . ')">Delete</button>
            </form>
          </div>
        </div>
      </div>';
    }

    $conn->close();
    ?>
  </div>
</section>

<section class="about-sasto-deal">
  <div class="about-content">
    <h2>About Sasto Deal</h2>
    <p>At Sasto Deal, we redefine luxury and affordability in the world of automobiles. Our mission is to provide high-end vehicles that combine elegance, performance, and futuristic technology, all at unbeatable prices. Explore the finest selection of exclusive car models tailored to deliver an exceptional driving experience.</p>
    <p>We believe that luxury should not be limited to a select few. Our dedicated team of experts handpicks the finest cars from around the globe, ensuring each model we offer is a masterpiece of design and innovation. From electric marvels that shape the future of driving to powerful machines that embody speed and precision, Sasto Deal brings you the best of both worlds.</p>
    <p>Discover the thrill of driving excellence—only with Sasto Deal.</p>
  </div>
  <div class="about-image">
    <div class="image-overlay"></div>
    <img src="img/supra.webp" alt="Luxury Car" />
  </div>
</section>

<section class="contact-us">
  <div class="contact-left">
    <img src="sasto deal.png" alt="Sasto Deal Logo" class="contact-logo" />
    <h2>Contact Us</h2>
    <p><strong>Address:</strong> 123 Luxury Street, Kathmandu, Nepal</p>
    <p><strong>Phone:</strong> +977 9876543210</p>
    <p><strong>Email:</strong> support@sastodeal.com</p>
    <div class="social-media">
      <a href="https://facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
      <a href="https://linkedin.com" target="_blank"><i class="fab fa-linkedin-in"></i></a>
      <a href="https://instagram.com" target="_blank"><i class="fab fa-instagram"></i></a>
    </div>
  </div>
  <div class="contact-right">
    <iframe
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3532.697598084336!2d84.43498147510937!3d27.678123224799552!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3994fb61f3664b81%3A0x8e373d5dcfdc9a2e!2sBharatpur%2C%20Nepal!5e0!3m2!1sen!2snp!4v1684239274567!5m2!1sen!2snp"
      width="400"
      height="400"
      style="border: 0;"
      allowfullscreen=""
      loading="lazy"
    ></iframe>
  </div>
</section>

<footer>
  <p>&copy; 2025 Sasto Deal. All rights reserved.</p>
</footer>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
  <div class="modal-content modal-delete">
    <h3>Confirm Deletion</h3>
    <p>Are you sure you want to delete this model?</p>
    <div class="modal-actions">
      <button id="cancelDelete" class="btn cancel-btn">Cancel</button>
      <button id="confirmDelete" class="btn confirm-btn">Delete</button>
    </div>
  </div>
</div>

<script>
  // Open model form modal functions
  function openModelForm() {
    document.getElementById('modelFormModal').style.display = 'flex';
  }
  function closeModelForm() {
    document.getElementById('modelFormModal').style.display = 'none';
  }
</script>

</body>
</html>
