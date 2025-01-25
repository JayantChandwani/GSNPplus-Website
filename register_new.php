<?php
// Start session for form progress tracking
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
session_unset();

require_once 'dbconn.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

// Function to sanitize input and prevent XSS attacks
function sanitize_input($input) {
    $input = is_string($input) ? trim($input) : '';
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = sanitize_input($_POST['first_name']);
    $middle_name = sanitize_input($_POST['middle_name']);
    $last_name = sanitize_input($_POST['last_name']);
    $dob = sanitize_input($_POST['dob']);
    $height = sanitize_input($_POST['height']);
    $weight = sanitize_input($_POST['weight']);
    $gender = sanitize_input($_POST['gender']);
    $marital_status = sanitize_input($_POST['marital_status']);
    $family_members = sanitize_input($_POST['family_members']);
    $hiv_positive_members = sanitize_input($_POST['hiv_positive_members']);
    $hiv_detection = sanitize_input($_POST['hiv_detection']);
    $art_status = sanitize_input($_POST['art_status']);
    $cd4_count = sanitize_input($_POST['cd4_count']);
    $employment = sanitize_input($_POST['employment']);
    $income = sanitize_input($_POST['income']);
    $property_type = sanitize_input($_POST['property_type']);
    $property_value = sanitize_input($_POST['property_value']);
    $ref_name = sanitize_input($_POST['ref_name']);
    $ref_contact = sanitize_input($_POST['ref_contact']);
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);
    $confirm_password = sanitize_input($_POST['confirm_password']);

    // Handle file uploads
    $photo = $_FILES['photo'];
    $hiv_report = $_FILES['hiv_report'];
    $address_proof = $_FILES['address_proof'];
    $id_proof = $_FILES['id_proof'];

    $upload_dir = 'uploads/';
    $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];

    // Function to handle file upload
    function handle_file_upload($file, $upload_dir, $allowed_types) {
        if ($file['error'] === UPLOAD_ERR_OK) {
            if (in_array($file['type'], $allowed_types)) {
                $file_name = basename($file['name']);
                $target_file = $upload_dir . $file_name;
                if (move_uploaded_file($file['tmp_name'], $target_file)) {
                    return $target_file;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    $photo_path = handle_file_upload($photo, $upload_dir, $allowed_types);
    $hiv_report_path = handle_file_upload($hiv_report, $upload_dir, $allowed_types);
    $address_proof_path = handle_file_upload($address_proof, $upload_dir, $allowed_types);
    $id_proof_path = handle_file_upload($id_proof, $upload_dir, $allowed_types);

    if ($photo_path && $hiv_report_path && $address_proof_path && $id_proof_path) {
        // Insert data into the database
        $sql = "INSERT INTO users (first_name, middle_name, last_name, dob, height, weight, gender, marital_status, family_members, hiv_positive_members, hiv_detection, art_status, cd4_count, employment, income, property_type, property_value, ref_name, ref_contact, username, email, password, photo, hiv_report, address_proof, id_proof) VALUES (:first_name, :middle_name, :last_name, :dob, :height, :weight, :gender, :marital_status, :family_members, :hiv_positive_members, :hiv_detection, :art_status, :cd4_count, :employment, :income, :property_type, :property_value, :ref_name, :ref_contact, :username, :email, :password, :photo, :hiv_report, :address_proof, :id_proof)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':middle_name', $middle_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':height', $height);
        $stmt->bindParam(':weight', $weight);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':marital_status', $marital_status);
        $stmt->bindParam(':family_members', $family_members);
        $stmt->bindParam(':hiv_positive_members', $hiv_positive_members);
        $stmt->bindParam(':hiv_detection', $hiv_detection);
        $stmt->bindParam(':art_status', $art_status);
        $stmt->bindParam(':cd4_count', $cd4_count);
        $stmt->bindParam(':employment', $employment);
        $stmt->bindParam(':income', $income);
        $stmt->bindParam(':property_type', $property_type);
        $stmt->bindParam(':property_value', $property_value);
        $stmt->bindParam(':ref_name', $ref_name);
        $stmt->bindParam(':ref_contact', $ref_contact);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':photo', $photo_path);
        $stmt->bindParam(':hiv_report', $hiv_report_path);
        $stmt->bindParam(':address_proof', $address_proof_path);
        $stmt->bindParam(':id_proof', $id_proof_path);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Registration successful';
        } else {
            $response['message'] = 'Database error: ' . $conn->error;
        }
    } else {
        $response['message'] = 'File upload failed';
    }
} else {
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    
    <style>
        .step { display: none; } 
        .step.active { display: block; }
        .error { color: red; }
    </style>
</head>
<body>
<<form action="register_new.php" method="post" enctype="multipart/form-data" id="registrationForm">
        <!-- Step 1 -->
        <fieldset class="step active" id="step-1">
            <legend>Step 1: Personal Info</legend>
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name">
            <span class="error" id="error-first_name"></span>
            <br><br>
            <label for="middle_name">Middle Name:</label>
            <input type="text" id="middle_name" name="middle_name">
            <span class="error" id="error-middle_name"></span>
            <br><br>
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name">
            <span class="error" id="error-last_name"></span>
            <br><br>
            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob">
            <span class="error" id="error-dob"></span>
            <br><br>
            <label for="height">Height (in inches):</label>
            <input type="number" id="height" name="height">
            <span class="error" id="error-height"></span>
            <br><br>
            <label for="weight">Weight (in kg):</label>
            <input type="number" id="weight" name="weight">
            <span class="error" id="error-weight"></span>
            <br><br>
            <label for="gender">Gender:</label>
            <select id="gender" name="gender">
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
            <span class="error" id="error-gender"></span>
            <br><br>
            <label for="marital_status">Marital Status:</label>
            <select id="marital_status" name="marital_status">
                <option value="Single">Single</option>
                <option value="Married">Married</option>
                <option value="Divorced">Divorced</option>
                <option value="Widowed">Widowed</option>
            </select>
            <span class="error" id="error-marital_status"></span>
            <br><br>
            <button type="button" onclick="nextStep()">Next</button>
        </fieldset>
 
        <!-- Step 2 -->
        <fieldset class="step" id="step-2">
            <legend>Step 2: Family Info</legend>
            <label for="family_members">Number of Family Members:</label>
            <input type="number" id="family_members" name="family_members">
            <br><br>
            <label for="hiv_positive_members">Number of HIV Positive Family Members:</label>
            <input type="number" id="hiv_positive_members" name="hiv_positive_members">
            <br><br>
            <span class="error" id="error-step_2"></span>
            <br><br>
            <button type="button" onclick="prevStep()">Previous</button>
            <button type="button" onclick="nextStep()">Next</button>
        </fieldset>
 
        <!-- Step 3 -->
        <fieldset class="step" id="step-3">
            <legend>Step 3: Health Details</legend>
            <label for="hiv_detection">HIV Detection Date:</label>
            <input type="date" id="hiv_detection" name="hiv_detection">
            <br><br>
            <label for="art_status">ART Status:</label>
            <select id="art_status" name="art_status">
                <option value="Positive">Positive</option>
                <option value="Negative">Negative</option>
            </select>
            <br><br>
            <label for="cd4_count">CD4 Count(per cubic mm):</label>
            <input type="text" id="cd4_count" name="cd4_count">
            <br><br>
            <span class="error" id="error-step_3"></span>
            <br><br>
            <button type="button" onclick="prevStep()">Previous</button>
            <button type="button" onclick="nextStep()">Next</button>
        </fieldset>
 
        <!-- Step 4 -->
        <fieldset class="step" id="step-4">
            <legend>Step 4: Business Info</legend>
            <label for="employment">Type of Employment:</label>
            <select id="employment" name="employment">
                <option value="Private Sector">Private Sector</option>
                <option value="Public Sector">Public Sector</option>
                <option value="Personal Business">Personal Business</option>
                <option value="Unemployed">Unemployed</option>
            </select>
            <br><br>
            <label for="income"> Annual Income:</label>
            <input type="text" id="income" name="income">
            <br><br>
            <span class="error" id="error-step_4"></span>
            <br><br>
            <button type="button" onclick="prevStep()">Previous</button>
            <button type="button" onclick="nextStep()">Next</button>
        </fieldset>
 
        <!-- Step 5 -->
        <fieldset class="step" id="step-5">
            <legend>Step 5: Property Details</legend>
            <label for="property_type">Property Type:</label>
            <select id="property_type" name="property_type">
                <option value="Land">Land</option>
                <option value="Flat">Flat</option>
                <option value="Bunagalow">Bunagalow</option>
                <option value="Others">Others</option>
                <option value="-NA-">-NA-</option>
            </select>
            <br><br>
            <label for="property_value">Property Value:</label>
            <input type="text" id="property_value" name="property_value">
            <br><br>
            <span class="error" id="error-step_5"></span>
            <br><br>
            <button type="button" onclick="prevStep()">Previous</button>
            <button type="button" onclick="nextStep()">Next</button>
        </fieldset>
 
        <!-- Step 6 -->
        <fieldset class="step" id="step-6">
            <legend>Step 6: References</legend>
            <label for="ref_name">Reference Name:</label>
            <input type="text" id="ref_name" name="ref_name">
            <br><br>
            <label for="ref_contact">Reference Contact:</label>
            <input type="text" id="ref_contact" name="ref_contact">
            <br><br>
            <span class="error" id="error-step_6"></span>
            <br><br>
            <button type="button" onclick="prevStep()">Previous</button>
            <button type="button" onclick="nextStep()">Next</button>
        </fieldset>
 
        <!-- Step 7 -->
        <fieldset class="step" id="step-7">
            <legend>Step 7: Account Setup</legend>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" onkeyup="checkUsername()">
            <span id="usernameFeedback"></span>
            <br><br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email">
            <br><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password">
            <br><br>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password">
            <br><br>
            <span class="error" id="error-password"></span>
            <span class="error" id="error-email"></span>
            <span class="error" id="error-username"></span>
            <span class="error" id="error-step_7"></span>
            <br><br>
            <button type="button" onclick="prevStep()">Previous</button>
            <button type="button" onclick="nextStep()">Next</button>
        </fieldset>
 
        <!-- Step 8 -->
        <fieldset class="step" id="step-8">
            <label for="photo">Photograph:</label>
            <input type="file" id="photo" name="photo" accept="image/*" required>
            <br><br>
            <label for="hiv_report">HIV Report:</label>
            <input type="file" id="hiv_report" name="hiv_report" accept="application/pdf,image/*" required>
            <br><br>
            <label for="address_proof">Address Proof:</label>
            <input type="file" id="address_proof" name="address_proof" accept="application/pdf,image/*" required>
            <br><br>
            <label for="id_proof">ID Proof:</label>
            <input type="file" id="id_proof" name="id_proof" accept="application/pdf,image/*"required>
            <br><br>
            <span class="error" id="error-step_8"></span>
            <br><br>
            <button type="button" onclick="prevStep()">Previous</button>
            <button type="submit">Register</button>
        </fieldset>
    </form>
 
    <script src="validators.js">
    </script>
    <script>
    function checkUsername() {
        const username = document.getElementById("username").value;
        const feedback = document.getElementById("usernameFeedback");

        if (username.length === 0) {
            feedback.textContent = "";
            return;
        }

        fetch(`check_username.php?username=${encodeURIComponent(username)}`)
            .then(response => response.text())
            .then(data => {
                if (data === "exists") {
                    feedback.textContent = "Username already exists. Please choose another.";
                    feedback.style.color = "red";
                    
                } else {
                    feedback.textContent = "Username is available.";
                    feedback.style.color = "green";
                    
                }
            })
            .catch(error => console.error("Error:", error));
    }
    </script>
</body>
</html>