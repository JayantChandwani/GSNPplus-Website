<?php
// Start session for form progress tracking
error_reporting(E_ALL);
ini_set('display_errors', 1);
// session_destroy();
session_start();
session_unset(); 

require_once 'dbconn.php';
 
$error = '';
$success = '';
 
 
 
 
// Function to sanitize input and prevent XSS attacks
function sanitize_input($input) {
    // Ensure $input is a string before applying trim
    $input = is_string($input) ? trim($input) : '';
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}
 
// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name=sanitize_input($_POST['first_name']);
    $middle_name=sanitize_input($_POST['middle_name']);
    $last_name=sanitize_input($_POST['last_name']);
    $dob=sanitize_input($_POST['dob']);
    $height=sanitize_input($_POST['height']);
    $weight=sanitize_input($_POST['weight']);
    $gender = sanitize_input($_POST['gender']);
    $marital_status=sanitize_input($_POST['marital_status']);
    
    $family_members=sanitize_input($_POST['family_members']);
    $hiv_positive_members=sanitize_input($_POST['hiv_positive_members']);
    $hiv_detection=sanitize_input($_POST['hiv_detection']);
    $art_status=sanitize_input($_POST['art_status']);
    $cd4_count=sanitize_input($_POST['cd4_count']);
    $employment=sanitize_input($_POST['employment']);
    $income=sanitize_input($_POST['income']);
    $property_type=sanitize_input($_POST['property_type']);
    $property_value=sanitize_input($_POST['property_value']);
    $ref_name=sanitize_input($_POST['ref_name']);
    $ref_contact=sanitize_input($_POST['ref_contact']);
    $username=sanitize_input($_POST['username']);
    $_SESSION['username']=$username;
    $email=sanitize_input($_POST['email']);
 
    $password=sanitize_input($_POST['password']);
    $candidate_id=uniqid();
    //Have to Make a Candidate_ID generating function
    try {
    
        $conn->beginTransaction();
       
        echo "0";
        $stmt = $conn->prepare("INSERT INTO candidate (cid,FirstName, MiddleName, LastName, Gender, DOB,Height_inch, Weight, MaritalStatus)
                               VALUES (:cid, :first_name, :middle_name, :last_name, :gender, :dob, :height, :weight, :marital_status)");
        $stmt->execute([':cid'=>$candidate_id,':first_name'=>$first_name,
        ':middle_name'=>$middle_name,':last_name'=>$last_name,
        ':gender'=>$gender,
        ':dob'=>$dob,':height'=>$height,
        ':weight'=>$weight,':marital_status'=>$marital_status]);
        echo "1";
    
        //Step 2 SQL Query
        
        $stmt = $conn->prepare("INSERT INTO health (cid, HivDetectDate, ArtStatus, CD4Count) 
                             VALUES (:candidate_id, :hiv_detection, :art_status, :cd4_count)");
        $stmt->execute([
            ':candidate_id' => $candidate_id,
            ':hiv_detection' => $hiv_detection,
            ':art_status' => $art_status,
            ':cd4_count' => $cd4_count
        ]);
        echo "2";
        
 
        $stmt = $conn->prepare("INSERT INTO business (cid, EmploymentType, Income) 
                             VALUES (:candidate_id, :employment, :income)");
        $stmt->execute([
            ':candidate_id' => $candidate_id,
            ':employment' => $employment,
            ':income' => $income
        ]);
        echo "3";

        
        //Step 5 SQL Query
 
        $stmt = $conn->prepare("INSERT INTO reference (cid, ReferenceName1, ReferenceContact1) 
                             VALUES (:candidate_id, :name, :contact)");
        $stmt->execute([
            ':candidate_id' => $candidate_id,
            ':name' => $ref_name,
            ':contact' => $ref_contact
        ]);
        echo "4";
 
        $stmt = $conn->prepare("INSERT INTO login (cid, Username, Email, Password) 
                             VALUES (:candidate_id, :username, :email, :password)");
        $stmt->execute([
            ':candidate_id' => $candidate_id,
            ':username' => $username,
            ':email' => $email,
            ':password' => $password
        ]);
        echo "5";
 
        //Step 8 SQL Query
        
        $conn->commit();
        $success = 'Registration completed successfully!';
        //session_destroy(); // Clear session data
        header('Location: otp_login.php');
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        echo $e->getMessage();
        $error = 'Error during registration: ' . $e->getMessage();
     
    }
}
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