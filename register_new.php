<?php
// Start session for form progress tracking
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

try {
    // Database connection using PDO
    $dsn = "mysql:host=localhost;dbname=gsnpplus;charset=utf8mb4";
    $username = "root";
    $password = "";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $username, $password, $options);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Initialize variables for form sections
$current_step = isset($_POST['step']) ? $_POST['step'] : 1;
$total_steps = 8;
$error = '';
$success = '';

// Function to validate file upload
function validate_file($file, $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'], $max_size = 52428800) {
    if ($file['size'] > $max_size) {
        return "File size exceeds 50MB limit";
    }
    
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types)) {
        return "Invalid file type. Allowed types: " . implode(', ', $allowed_types);
    }
    
    return true;
}

// Function to sanitize input and prevent XSS attacks
function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    try {
        $pdo->beginTransaction();
        
        switch($current_step) {
            case 1: // Personal Information
                $stmt = $pdo->prepare("INSERT INTO candidate (name, dob, height, weight, marital_status) 
                                     VALUES (:name, :dob, :height, :weight, :marital_status)");
                $stmt->execute([
                    'name' => sanitize_input($_POST['name']),
                    'dob' => sanitize_input($_POST['dob']),
                    'height' => sanitize_input($_POST['height']),
                    'weight' => sanitize_input($_POST['weight']),
                    'marital_status' => sanitize_input($_POST['marital_status'])
                ]);
                $_SESSION['candidate_id'] = $pdo->lastInsertId();
                break;
                
            case 2: // Family Information
                $stmt = $pdo->prepare("INSERT INTO family (candidate_id, family_members, hiv_affected) 
                                     VALUES (:candidate_id, :family_members, :hiv_affected)");
                $stmt->execute([
                    'candidate_id' => $_SESSION['candidate_id'],
                    'family_members' => sanitize_input($_POST['family_members']),
                    'hiv_affected' => sanitize_input($_POST['hiv_affected'])
                ]);
                break;
                
            case 3: // Health Details
                $stmt = $pdo->prepare("INSERT INTO health (candidate_id, hiv_detection, art_status, cd4_count) 
                                     VALUES (:candidate_id, :hiv_detection, :art_status, :cd4_count)");
                $stmt->execute([
                    'candidate_id' => $_SESSION['candidate_id'],
                    'hiv_detection' => sanitize_input($_POST['hiv_detection']),
                    'art_status' => sanitize_input($_POST['art_status']),
                    'cd4_count' => sanitize_input($_POST['cd4_count'])
                ]);
                break;
                
            case 4: // Business Information
                $stmt = $pdo->prepare("INSERT INTO business (candidate_id, employment, income) 
                                     VALUES (:candidate_id, :employment, :income)");
                $stmt->execute([
                    'candidate_id' => $_SESSION['candidate_id'],
                    'employment' => sanitize_input($_POST['employment']),
                    'income' => sanitize_input($_POST['income'])
                ]);
                break;
                
            case 5: // Property Details
                $stmt = $pdo->prepare("INSERT INTO property (candidate_id, property_type, property_value) 
                                     VALUES (:candidate_id, :property_type, :property_value)");
                $stmt->execute([
                    'candidate_id' => $_SESSION['candidate_id'],
                    'property_type' => sanitize_input($_POST['property_type']),
                    'property_value' => sanitize_input($_POST['property_value'])
                ]);
                break;
                
            case 6: // References
                $stmt = $pdo->prepare("INSERT INTO reference (candidate_id, name, contact) 
                                     VALUES (:candidate_id, :name, :contact)");
                $stmt->execute([
                    'candidate_id' => $_SESSION['candidate_id'],
                    'ref_name' => sanitize_input($_POST['ref_name']),
                    'ref_contact' => sanitize_input($_POST['ref_contact'])
                ]);
                break;
                
            case 7: // Account Setup
                // Check if password matches
                if ($_POST['password'] !== $_POST['confirm_password']) {
                    throw new Exception("Passwords do not match");
                }
                $stmt = $pdo->prepare("INSERT INTO login (candidate_id, username, email, password) 
                                     VALUES (:candidate_id, :username, :email, :password)");
                $stmt->execute([
                    'candidate_id' => $_SESSION['candidate_id'],
                    'username' => sanitize_input($_POST['username']),
                    'email' => sanitize_input($_POST['email']),
                    'password' => password_hash($_POST['password'], PASSWORD_BCRYPT)
                ]);
                $_SESSION['username'] = $_POST['username'];
                break;
                
            case 8: // File Uploads
                $upload_dir = "uploads/";
                $files = ['photo', 'hiv_report', 'address_proof', 'id_proof'];
                
                foreach ($files as $file_type) {
                    if (isset($_FILES[$file_type])) {
                        $file = $_FILES[$file_type];
                        $validation = validate_file($file);
                        
                        if ($validation === true) {
                            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                            $new_filename = $_SESSION['username'] . '_' . $file_type . '.' . $ext;
                            move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename);
                            
                            $stmt = $pdo->prepare("UPDATE candidate SET {$file_type}_path = :filepath 
                                                 WHERE id = :candidate_id");
                            $stmt->execute([
                                'filepath' => $new_filename,
                                'candidate_id' => $_SESSION['candidate_id']
                            ]);
                        } else {
                            throw new Exception($validation);
                        }
                    }
                }
                
                $pdo->commit();
                header("Location: confirm.html");
                exit();
                break;
        }
        
        $current_step++;
        $success = "Step $current_step completed successfully";
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <script src="validation.js"></script>
    <style>
        fieldset {
            display: none;
        }

        fieldset.active {
            display: block;
        }
    </style>
</head>
<body>
    <form action="register_new.php" method="POST" enctype="multipart/form-data" id="registrationForm">
        <!-- Step 1: Personal Information -->
        <fieldset class="active">
            <legend>Step 1: Personal Information</legend>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required>

            <label for="height">Height:</label>
            <input type="number" id="height" name="height" required>

            <label for="marital_status">Marital Status:</label>
            <select id="marital_status" name="marital_status">
                <option value="Single">Single</option>
                <option value="Married">Married</option>
                <option value="Divorced">Divorced</option>
                <option value="Widowed">Widowed</option>
            </select>

            <button type="button" onclick="nextStep()">Next</button>
        </fieldset>

        <!-- Step 2: Family Information -->
        <fieldset>
            <legend>Step 2: Family Information</legend>
            <label for="family_members">Number of Family Members:</label>
            <input type="number" id="family_members" name="family_members" required min="1" step="1">
    
            <label for="hiv_positive_members">Number of HIV Positive Family Members:</label>
            <input type="number" id="hiv_positive_members" name="hiv_positive_members" required min="0" step="1">

            <span id="hiv_error" style="color: red; display: none;">The number of HIV positive members cannot exceed the total number of family members.</span>
            <button type="button" onclick="prevStep()">Previous</button>
            <button type="button" onclick="nextStep()">Next</button>
        </fieldset>

        <!-- Step 3: Health Details -->
        <fieldset>
            <legend>Step 3: Health Details</legend>
            <label for="hiv_detection">HIV Detection Date:</label>
            <input type="date" id="hiv_detection" name="hiv_detection" required>

            <label for="art_status">ART Status:</label>
            <select id="art_status" name="art_status">
                <option value="Positive">Positive</option>
                <option value="Negative">Negative</option>
            </select>
            <label for="cd4_count">CD4 Count(per cubic mm):</label>
            <input type="text" id="cd4_count" name="cd4_count" required>

            <button type="button" onclick="prevStep()">Previous</button>
            <button type="button" onclick="nextStep()">Next</button>
        </fieldset>

        <!-- Step 4: Business Information -->
        <fieldset>
            <legend>Step 4: Business Information</legend>
            <label for="employment">Type of Employment:</label>
            <select id="employment" name="employment">
                <option value="Private Sector">Private Sector</option>
                <option value="Public Sector">Public Sector</option>
                <option value="Personal Business">Personal Business</option>
                <option value="Unemployed">Unemployed</option>
            </select>

            <label for="income"> Annual Income:</label>
            <input type="text" id="income" name="income" required>

            <button type="button" onclick="prevStep()">Previous</button>
            <button type="button" onclick="nextStep()">Next</button>
        </fieldset>

        <!-- Step 5: Property Details -->
        <fieldset>
            <legend>Step 5: Property Details</legend>
            <label for="property_type">Property Type:</label>
            <select id="property_type" name="property_type">
                <option value="Land">Land</option>
                <option value="Flat">Flat</option>
                <option value="Bunagalow">Bunagalow</option>
                <option value="Others">Others</option>
                <option value="-NA-">-NA-</option>
            </select>
            <label for="property_type">Property Type:</label>
            <select id="property_type" name="property_type">
                <option value="Land">Land</option>
                <option value="Flat">Flat</option>
                <option value="Bunagalow">Bunagalow</option>
                <option value="Others">Others</option>
                <option value="-NA-">-NA-</option>
            </select>

            <label for="property_value">Property Value:</label>
            <input type="text" id="property_value" name="property_value" required>

            <button type="button" onclick="prevStep()">Previous</button>
            <button type="button" onclick="nextStep()">Next</button>
        </fieldset>

        <!-- Step 6: References -->
        <fieldset>
            <legend>Step 6: References</legend>
            <label for="ref_name">Reference Name:</label>
            <input type="text" id="ref_name" name="ref_name" required>

            <label for="ref_contact">Reference Contact:</label>
            <input type="text" id="ref_contact" name="ref_contact" required>

            <button type="button" onclick="prevStep()">Previous</button>
            <button type="button" onclick="nextStep()">Next</button>
        </fieldset>

        <!-- Step 7: Account Setup -->
        <fieldset>
            <legend>Step 7: Account Setup</legend>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="button" onclick="prevStep()">Previous</button>
            <button type="button" onclick="nextStep()">Next</button>
        </fieldset>

        <!-- Step 8: File Uploads -->
        <fieldset>
            <legend>Step 8: File Uploads</legend>
            <label for="photo">Photograph:</label>
            <input type="file" id="photo" name="photo" accept="image/*" required>

            <label for="hiv_report">HIV Report:</label>
            <input type="file" id="hiv_report" name="hiv_report" accept="application/pdf,image/*" required>

            <label for="address_proof">Address Proof:</label>
            <input type="file" id="address_proof" name="address_proof" accept="application/pdf,image/*" required>

            <label for="id_proof">ID Proof:</label>
            <input type="file" id="id_proof" name="id_proof" accept="application/pdf,image/*" required>

            <button type="button" onclick="prevStep()">Previous</button>
            <button type="submit" name="submit">Register</button>
        </fieldset>
    </form>

    <script>
    let currentStep = 0;
    const fieldsets = document.querySelectorAll('fieldset');

    // Function to validate that all fields in the current step are filled
    function validateCurrentStep() {
        const inputs = fieldsets[currentStep].querySelectorAll('input, textarea, select');
        for (let input of inputs) {
            if (input.required && input.value.trim() === '') {
                alert('Please fill out all fields.');
                return false;
            }
        }
        return true;
    }

    // Function to show the current step
    function showStep(step) {
        fieldsets.forEach((fieldset, index) => {
            fieldset.classList.toggle('active', index === step);
        });
    }

    // Function to proceed to the next step
    function nextStep() {
        if (validateCurrentStep()) {
            if (currentStep < fieldsets.length - 1) {
                currentStep++;
                showStep(currentStep);
            }
        }
    }

    // Function to go back to the previous step
    function prevStep() {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
        }
    }


    // Initially show the first step
    showStep(currentStep);

    // Client-side validation on form submission (for passwords)
    document.getElementById('registrationForm').addEventListener('submit', function(event) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        if (password !== confirmPassword) {
            alert('Passwords do not match!');
            event.preventDefault();
        }
    });
</script>
</body>
</html>
