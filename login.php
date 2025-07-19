<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$username = "root";
$password = "";
$database = "sastodeal_db";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Initialize variables and error array
$username = $password = "";
$errors = [];

// Function to sanitize inputs
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Username
    if (empty($_POST["username"])) {
        $errors['username'] = "Username is required.";
    } else {
        $username = sanitizeInput($_POST["username"]);
    }

    // Validate Password
    if (empty($_POST["password"])) {
        $errors['password'] = "Password is required.";
    } else {
        $password = sanitizeInput($_POST["password"]);
    }

    // If no errors, proceed with login check (mock check for example purposes)
      if (empty($errors)) {
        // SQL to find the user in the database
        $sql = "SELECT * FROM registration WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify the hashed password
         if (password_verify($password, $user['password'])) {
    // Check user type and redirect accordingly
    if ($user['user_type'] === 'admin') {
        header("Location: admin.php");
        exit();
    } elseif ($user['user_type'] === 'user') {
        header("Location: index.php");
        exit();
    } else {
        $errors['login'] = "Unknown user type.";
    }
} else {
    $errors['login'] = "Invalid username or password.";
}

    }
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(315deg,#ffffff, #151515);
        }

        .login-container {
            background-color:rgb(224, 218, 218);
            border-radius: 20px;
            padding: 40px;
            width: 400px;
            box-shadow: 0 8px 32px rgba(13, 13, 13, 0.1);
            text-align: center;
            transition:0.3s ease;
        }
                .login-container:hover{
                transform:translateY(-10px);
                }

        .login-container h2 {
            margin-bottom: 20px;
            color: maroon;
        }

        .input-container {
            position: relative;
            margin: 15px 0;
        }

        .input-container input {
            width: 100%;
            padding: 12px;
            margin: 0;
            border: 1px solid maroon;
            border-radius: 5px;
            outline: none;
            background-color: whitesmoke;
            color: #333;
            font-size: 16px;
        }

        .input-container label {
            position: absolute;
            left: 12px;
            top: 12px;
            font-size: 16px;
            color: #666;
            transition: all 0.2s ease;
            pointer-events: none; /* Ensures the label doesn't interfere with input interaction */
        }

        .input-container input:focus + label,
        .input-container input:not(:placeholder-shown) + label {
            top: -10px;
            left: 10px;
            font-size: 12px;
            color: maroon;
            background: whitesmoke;
            border:1px solid maroon;
            padding:2px;
        }

        .error-message {
            color: red;
            font-size: 14px;
            text-align: left;
            margin-top: 5px;
            display: none;
        }

        .login-container button {
            width: 150px;
            padding: 12px;
            margin: 20px 0;
            border: none;
            border-radius: 5px;
            background-color: rgb(169, 40, 40);
            color: #fff;
            font-size: 15px;
            font-family:bronx;
            cursor: pointer;
            transition: background-color 0.3s;
        }
                .login-container .log {
            width: 150px;
            padding: 12px;
            margin: 20px 0;
            border: none;
            border-radius: 5px;
            background-color: rgb(98, 94, 94);
            color: #fff;
            font-size: 15px;
            font-family:bronx;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .login-container .log:hover{
            color:white;
            background: black;
        }

        .login-container button:disabled {
            background-color: grey;
            cursor: not-allowed;
        }
        .login-container .reg h4{
            font-size: 18px;
            display: inline-block;
        }
        .login-container .reg a span{
            color: black;
            text-decoration: none;
        }
        img{
            width: 60px;
        }
        /* Responsive styles */
        @media (max-width: 768px) {
            .login-container {
                padding: 20px;
                width: 65%;
            }

            .input-container input {
                padding: 10px;
                margin-bottom:1px;
                width: 90%;
               
            }
            .input-container label{
                margin-left:10px;
            }
               

            .login-container button {
                width: 50%;
                padding: 15px;
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 20px;
                width: 65%;
            }

            .input-container input {
                padding: 10px;
                font-size: 14px;
                margin-bottom:1px;
                width: 90%;
            }
            

            .login-container button {
                padding: 10px;
                font-size: 14px;
            }
        }
        
    </style>
</head>
<body>
    <div class="login-container">
        <img src="img/sasto deal single.png" alt="">

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

            <div class="input-container">
            <input type="text" id="username" placeholder=" " name="username" value="<?php echo $username; ?>" required>
            <label for="username">Email</label>
            <div class="error-message" id="username-error"><?php echo $errors['username'] ?? ''; ?></div>
            </div>

            <div class="input-container">
            <input type="password" id="password" placeholder=" " name="password" required>
            <label for="password">Password</label>
            <div class="error-message" id="password-error"><?php echo $errors['password'] ?? ''; ?></div>
            </div>

        <div class="error-message" id="login-error"
     style="<?php echo isset($errors['login']) ? 'display:block;' : 'display:none;'; ?>">
    <?php echo $errors['login'] ?? ''; ?>
</div>

        <button id="login-btn" type="submit">Login</button>
        <input type="reset" value="reset" class="log">
        
        
        <div class="reg">
                    <h4>New Here?</h4> <a href="registration.php"><span>Register Here</span></a>
                </div>
    </form>
    </div>

    <script>
        const fields = [
            {
                id: 'username',
                regex: /^\S+@\S+\.\S+$/,  // Alphanumeric username, can adjust the pattern
                message: "Username must be at least 3 characters."
            },
            {
                id: 'password',
                regex: /(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}/, // Simple password rule
                message: "Password must be at least 8 characters, including letters uppercase and numbers."
            }
        ];


        // login button and error showing
        const loginError = document.getElementById('login-error');

fields.forEach(field => {
    const input = document.getElementById(field.id);
    input.addEventListener('input', () => {
        validate(input);
        validateAll();
        // Hide login error when user modifies input
        if (loginError) {
            loginError.style.display = 'none';
        }
    });
});

        const loginBtn = document.getElementById('login-btn');

        const validate = (input) => {
            const field = fields.find(f => f.id === input.id);
            const errorElement = document.getElementById(`${input.id}-error`);

            if (input.value.length === 0) {
                errorElement.style.display = 'none';
                return false;
            }

            if (field) {
                if (!field.regex.test(input.value)) {
                    errorElement.innerText = field.message;
                    errorElement.style.display = 'block';
                    return false;
                } else {
                    errorElement.style.display = 'none';
                    return true;
                }
            }
            return false;
        };

        const validateAll = () => {
            let allValid = true;
            fields.forEach(field => {
                const input = document.getElementById(field.id);
                if (!validate(input)) {
                    allValid = false;
                }
            });

            loginBtn.disabled = !allValid;
        };

        fields.forEach(field => {
            const input = document.getElementById(field.id);
            input.addEventListener('input', () => {
                validate(input);
                validateAll();
            });
        });

        // Handle reset button
document.querySelector("form").addEventListener("reset", function () {
    // Enable login button
    loginBtn.disabled = false;

    // Hide all error messages
    fields.forEach(field => {
        const errorElement = document.getElementById(`${field.id}-error`);
        errorElement.style.display = 'none';
    });
});

    </script>
</body>
</html>
