<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $contact = $_POST["contact"];
    $password = $_POST["password"];
    $retype = $_POST["retype-password"];


$nameErr = $emailErr = $contactErr = $passwordErr = $retypeErr = "";
$name = $email = $contact = "";

     $namePattern = "/^[A-Za-z\s]{3,}$/";
    $emailPattern = "/^[a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com|outlook\.com|hotmail\.com|icloud\.com)$/";
    $contactPattern = "/^\d{10}$/";
    $passwordPattern = "/(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}/";

      // Name validation
    if (!preg_match($namePattern, $name)) {
        $nameErr = "Name must be at least 3 characters and only letters.";
        $valid = false;
    }

    // Email validation
    if (!preg_match($emailPattern, $email)) {
        $emailErr = "Only Gmail, Yahoo, Outlook, Hotmail, or iCloud emails are allowed.";
        $valid = false;
    }

    // Contact validation
    if (!preg_match($contactPattern, $contact)) {
        $contactErr = "Contact must be a 10-digit number.";
        $valid = false;
    }

    // Password validation
    if (!preg_match($passwordPattern, $password)) {
        $passwordErr = "Password must be 8 characters, include uppercase, lowercase, and a number.";
        $valid = false;
    }

    // Retype password
    if ($password !== $retype) {
        $retypeErr = "Passwords do not match.";
        $valid = false;
    }

    if ($valid) {
        // Connect and insert
        $conn = new mysqli("localhost", "root", "", "sastodeal_db");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO registration (name, email, contact, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $contact, $hashed_password);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        $stmt->close();
        $conn->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <link rel="stylesheet" href="regg.css">
</head>
<body>
    <div class="full">
        <div class="login-container">
            <img src="img/sasto deal single.png" alt="">
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

                <!-- Name -->
                <div class="input-container">
                    <input type="text" id="name" placeholder=" " name="name" required>
                    <label for="name">Name</label>
                    <div class="error-message" id="name-error"></div>
                </div>

                <!-- Email -->
                <div class="input-container">
                    <input type="email" id="email" placeholder=" " name="email" required>
                    <label for="email">Email</label>
                    <div class="error-message" id="email-error"></div>
                </div>

                <!-- Contact -->
                <div class="input-container">
                    <input type="text" id="contact" placeholder=" " name="contact" required>
                    <label for="contact">Contact</label>
                    <div class="error-message" id="contact-error"></div>
                </div>

                <!-- Password -->
                <div class="input-container">
                    <input type="password" id="password" placeholder=" " name="password" required>
                    <label for="password">Password</label>
                    <div class="error-message" id="password-error"></div>
                </div>

                <!-- Retype Password -->
                <div class="input-container">
                    <input type="password" id="retype-password" placeholder=" " name="retype-password" required>
                    <label for="retype-password">Retype Password</label>
                    <div class="error-message" id="retype-password-error"></div>
                </div>

                <!-- Buttons -->
                <button id="register-btn" type="submit">Register Now</button>
                <input type="reset" value="Reset" class="reg">
                <div class="log">
                    <h4>Already an user?</h4> <a href="login.php"><span>Login Here</span></a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Define validation fields and regex patterns
        const fields = [
            {
                id: 'name',
                regex: /^[A-Za-z\s]{3,}$/,
                message: "Name must be at least 3 characters and only letters."
            },
            {
                id: 'email',
                regex: /^[a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com|outlook\.com|hotmail\.com|icloud\.com)$/,
                message: "Invalid email format."
            },
            {
                id: 'contact',
                regex: /^\d{10}$/,
                message: "Contact must be a 10-digit number."
            },
            {
                id: 'password',
                regex: /(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}/,
                message: "Password must be 8 characters, include uppercase, lowercase, and a number."
            }
        ];

        // Form elements
        const form = document.querySelector('form');
        const registerBtn = document.getElementById('register-btn');
        const retypePassword = document.getElementById('retype-password');
        const retypeError = document.getElementById('retype-password-error');

        // Validation logic
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

        // Validate password match
        const validatePasswordMatch = () => {
            if (retypePassword.value.length > 0) {
                if (retypePassword.value !== document.getElementById('password').value) {
                    retypeError.innerText = "Passwords do not match.";
                    retypeError.style.display = 'block';
                    return false;
                } else {
                    retypeError.style.display = 'none';
                    return true;
                }
            } else {
                retypeError.style.display = 'none';
                return false;
            }
        };

        // Master validation check
        const validateAll = () => {
            let allValid = true;
            let allEmpty = true;

            fields.forEach(field => {
                const input = document.getElementById(field.id);
                if (input.value.length > 0) {
                    allEmpty = false;
                    if (!validate(input)) {
                        allValid = false;
                    }
                }
            });

            if (!validatePasswordMatch()) {
                allValid = false;
            }

            registerBtn.disabled = true; // add this at the beginning
registerBtn.disabled = !allValid;


            // Clear errors if all are empty
            if (allEmpty) {
                document.querySelectorAll('.error-message').forEach((error) => {
                    error.style.display = 'none';
                    error.innerText = '';
                });
            }
        };

        // Event listeners for real-time validation
        fields.forEach(field => {
            const input = document.getElementById(field.id);
            input.addEventListener('input', () => {
                validate(input);
                validateAll();
            });
        });

        retypePassword.addEventListener('input', () => {
            validatePasswordMatch();
            validateAll();
        });

        // Form reset listener
        form.addEventListener('reset', () => {
            registerBtn.disabled = false;
            document.querySelectorAll('.error-message').forEach((error) => {
                error.style.display = 'none';
                error.innerText = '';
            });
        });
    </script>
</body>
</html>
