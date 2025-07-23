<?php
session_start();
// Connect to DB
$conn = new mysqli("localhost", "root", "", "sastodeal_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all car models from the updated table name all_car_models
$sql = "SELECT model_name, image_path, description, type FROM all_car_models";
$result = $conn->query($sql);

$carModels = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $type = $row['type'] ?? 'others';  // fallback type if null
        if (!isset($carModels[$type])) {
            $carModels[$type] = [];
        }
        $carModels[$type][] = [
            'name' => $row['model_name'],
            'image' => $row['image_path'],
            'description' => $row['description']
        ];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Car Models - Sasto Deal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="gadi.css" />
    <!-- <style>
        /* Example minimal styling, adjust as needed */
        body {
            font-family: Arial, sans-serif;
            margin: 0; padding: 0;
            background: #f9f9f9;
        }
        .navbar {
            display: flex;
            align-items: center;
            background: #800000;
            padding: 10px 20px;
            color: white;
            justify-content: space-between;
        }
        .logo img {
            height: 40px;
            vertical-align: middle;
        }
        .logo span {
            font-size: 24px;
            margin-left: 10px;
            font-weight: bold;
        }
        .highlight {
            color: #ffcc00;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 600;
        }
        .nav-links.active {
            display: block;
        }
        .model-selector, .search-container {
            margin: 20px;
            text-align: center;
        }
        .dropdown, .search-bar {
            padding: 8px 10px;
            font-size: 16px;
            width: 200px;
            max-width: 90%;
            margin-top: 10px;
        }
        .model-cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            width: 280px;
            overflow: hidden;
            transition: transform 0.2s ease;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card-image {
            position: relative;
            overflow: hidden;
        }
        .card-image img {
            width: 100%;
            display: block;
            height: 180px;
            object-fit: cover;
        }
        .card-overlay {
            position: absolute;
            bottom: 0;
            background: rgba(0,0,0,0.6);
            color: white;
            width: 100%;
            padding: 15px;
            opacity: 0;
            transition: opacity 0.3s ease;
            box-sizing: border-box;
        }
        .card:hover .card-overlay {
            opacity: 1;
        }
        .order-btn {
            display: inline-block;
            margin-top: 10px;
            background: #ffcc00;
            color: #800000;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }
        h3 {
            margin: 10px;
            text-align: center;
            color: #333;
        }
    </style> -->
</head>
<body>

    <div class="navbar">
        <div class="logo">
            <a href="index.php"><img src="img/sasto deal single.png" alt="Logo" /></a>
            <span>Sasto <span class="highlight">Deal</span></span>
        </div>
        <div class="menu-icon" id="menu-icon" style="cursor:pointer;">
            <i class="fas fa-bars" style="font-size: 24px;"></i>
        </div>
        <div class="nav-links" id="nav-links" style="display:flex; gap:15px;">
            <a href="index.php">Home</a>
            <a href="carmodels.php">Models</a>
            <a href="order.html">Order Now</a>
            <a href="contact.html">Contact</a>
        </div>
    </div>

    <div class="model-selector">
        <h2>Choose Car Type:</h2>
        <select id="car-type-selector" class="dropdown">
            <option value="all">All Cars</option>
        </select>
    </div>

    <div class="search-container">
        <input type="text" id="search-bar" class="search-bar" placeholder="Search car models..." />
        <i class="fa fa-search search-icon" style="position: relative; left: -30px; color: #aaa;"></i>
    </div>

    <div class="models-section">
        <h2 style="text-align:center;">Explore Our Car Models</h2>
        <div id="model-cards" class="model-cards">
            <!-- Cards injected here -->
        </div>
    </div>

    <script>
        
    const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;


        const carModels = <?php echo json_encode($carModels); ?>;

        function populateDropdown() {
            const carTypeSelector = document.getElementById('car-type-selector');
            const types = Object.keys(carModels);
            carTypeSelector.innerHTML = '<option value="all">All Cars</option>';
            types.forEach(type => {
                const option = document.createElement('option');
                option.value = type;
                option.textContent = type.charAt(0).toUpperCase() + type.slice(1);
                carTypeSelector.appendChild(option);
            });
        }

        function displayCars(type, searchTerm = "") {
            const modelCards = document.getElementById('model-cards');
            modelCards.style.opacity = 0;

            setTimeout(() => {
                modelCards.innerHTML = '';
                const cars = type === 'all' ? Object.values(carModels).flat() : (carModels[type] || []);
                const filteredCars = cars.filter(car => car.name.toLowerCase().includes(searchTerm.toLowerCase()));

                if(filteredCars.length === 0) {
                    modelCards.innerHTML = '<p style="text-align:center; width: 100%;">No models found.</p>';
                } else {
                    filteredCars.forEach(car => {
                        const card = document.createElement('div');
                        card.classList.add('card');
                        card.innerHTML = `
    <div class="card-image">
        <img src="${car.image}" alt="${car.name}" />
        <div class="card-overlay">
            <p>${car.description}</p>
            <a href="${isLoggedIn ? 'order.php?model=' + encodeURIComponent(car.name) : 'login.php'}" class="order-btn">Order Now</a>
        </div>
    </div>
    <h3>${car.name}</h3>
`;

                        modelCards.appendChild(card);
                    });
                }

                modelCards.style.opacity = 1;
            }, 200);
        }

        document.getElementById('car-type-selector').addEventListener('change', (event) => {
            const selectedType = event.target.value;
            const searchTerm = document.getElementById('search-bar').value;
            displayCars(selectedType, searchTerm);
        });

        document.getElementById('search-bar').addEventListener('input', (event) => {
            const searchTerm = event.target.value;
            const selectedType = document.getElementById('car-type-selector').value;
            displayCars(selectedType, searchTerm);
        });

        window.onload = function () {
            populateDropdown();
            displayCars('all');
        };

        // Navbar toggle for small screens (optional)
        const menuIcon = document.getElementById('menu-icon');
        const navLinks = document.getElementById('nav-links');

        menuIcon.addEventListener('click', () => {
            if(navLinks.style.display === "flex") {
                navLinks.style.display = "none";
            } else {
                navLinks.style.display = "flex";
                navLinks.style.flexDirection = "column";
                navLinks.style.backgroundColor = "#800000";
                navLinks.style.position = "absolute";
                navLinks.style.top = "60px";
                navLinks.style.right = "10px";
                navLinks.style.padding = "10px";
                navLinks.style.borderRadius = "5px";
            }
        });

        // Close nav menu on link click (optional)
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if(window.innerWidth < 768) {
                    navLinks.style.display = "none";
                }
            });
        });
    </script>
</body>
</html>
