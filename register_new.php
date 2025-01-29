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
    $_SESSION['type']="register";
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
    <title>Registration</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
         background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        }

        form {
            width: 60%;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        fieldset {
            border: none;
            display: none;
        }

        fieldset.active {
            display: block;
        }

        legend {
            font-size: 1.5rem;
            font-weight: bold;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 1rem;
        }

        button {
            cursor: pointer;
            background: #007bff;
            color: white;
            border: none;
            font-weight: bold;
        }

        button:hover {
            background: #0056b3;
        }

        .error {
            color: red;
            font-size: 0.9rem;
        }

        .form-navigation {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <div class="registration-card">
            <h2></h2>
            <form id="registrationForm" method="POST" action="register_new.php" enctype="multipart/form-data">
                <!-- Step 1 -->
                <fieldset id="step-1" class="step active">
                    <legend>Step 1: Personal Information</legend>
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required>
                    <span id="error-first_name" class="error"></span>

                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>
                    <span id="error-last_name" class="error"></span>

                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" required>
                    <span id="error-dob" class="error"></span>

                    <label for="height">Height (in cm):</label>
                    <input type="number" id="height" name="height" required>
                    <span id="error-height" class="error"></span>

                    <label for="weight">Weight (in kg):</label>
                    <input type="number" id="weight" name="weight" required>
                    <span id="error-weight" class="error"></span>

                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>

                    <label for="marital_status">Marital Status:</label>
                    <select id="marital_status" name="marital_status" required>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Divorced">Divorced</option>
                        <option value="Widowed">Widowed</option>
                    </select>

                    <div class="form-navigation">
                        <button type="button" onclick="nextStep()">Next</button>
                    </div>
                </fieldset>

                <!-- Step 2 -->
                <fieldset id="step-2" class="step">
                    <legend>Step 2: Family Information</legend>
                    <label for="family_members">Number of Family Members:</label>
                    <input type="number" id="family_members" name="family_members" required>
                    <span id="error-step_2" class="error"></span>

                    <label for="hiv_positive_members">Number of HIV Positive Family Members:</label>
                    <input type="number" id="hiv_positive_members" name="hiv_positive_members" required>
                    <span id="error-step_2" class="error"></span>

                    <div class="form-navigation">
                        <button type="button" onclick="prevStep()">Previous</button>
                        <button type="button" onclick="nextStep()">Next</button>
                    </div>
                </fieldset>

                <!-- Step 3 -->
                <fieldset id="step-3" class="step">
                    <legend>Step 3: Health Information</legend>
                    <label for="hiv_detection">HIV Detection Date:</label>
                    <input type="date" id="hiv_detection" name="hiv_detection" required>
                    <span id="error-step_3" class="error"></span>

                    <label for="art_status">ART Status:</label>
                    <select id="art_status" name="art_status" required>
                        <option value="Positive">Positive</option>
                        <option value="Negative">Negative</option>
                    </select>

                    <label for="cd4_count">CD4 Count:</label>
                    <input type="number" id="cd4_count" name="cd4_count" required>
                    <span id="error-step_3" class="error"></span>

                    <div class="form-navigation">
                        <button type="button" onclick="prevStep()">Previous</button>
                        <button type="button" onclick="nextStep()">Next</button>
                    </div>
                </fieldset>

                <!-- Step 4 -->
                <fieldset id="step-4" class="step">
                    <legend>Step 4: Employment Information</legend>
                    <label for="employment">Employment:</label>
                    <input type="text" id="employment" name="employment" required>
                    <span id="error-step_4" class="error"></span>

                    <label for="income">Annual Income:</label>
                    <input type="number" id="income" name="income" required>
                    <span id="error-step_4" class="error"></span>

                    <div class="form-navigation">
                        <button type="button" onclick="prevStep()">Previous</button>
                        <button type="button" onclick="nextStep()">Next</button>
                    </div>
                </fieldset>

                <!-- Step 5 -->
                <fieldset id="step-5" class="step">
                    <legend>Step 5: Property Information</legend>
                    <label for="property_type">Property Type:</label>
                    <input type="text" id="property_type" name="property_type" required>
                    <span id="error-step_5" class="error"></span>

                    <label for="property_value">Property Value:</label>
                    <input type="number" id="property_value" name="property_value" required>
                    <span id="error-step_5" class="error"></span>

                    <div class="form-navigation">
                        <button type="button" onclick="prevStep()">Previous</button>
                        <button type="button" onclick="nextStep()">Next</button>
                    </div>
                </fieldset>

                <!-- Step 6 -->
                <fieldset id="step-6" class="step">
                    <legend>Step 6: Reference Information</legend>
                    <label for="ref_name">Reference Name:</label>
                    <input type="text" id="ref_name" name="ref_name" required>
                    <span id="error-step_6" class="error"></span>

                    <label for="ref_contact">Reference Contact:</label>
                    <input type="text" id="ref_contact" name="ref_contact" required>
                    <span id="error-step_6" class="error"></span>

                    <div class="form-navigation">
                        <button type="button" onclick="prevStep()">Previous</button>
                        <button type="button" onclick="nextStep()">Next</button>
                    </div>
                </fieldset>

                <!-- Step 7 -->
                <fieldset id="step-7" class="step">
                    <legend>Step 7: Account Information</legend>
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                    <span id="error-step_7" class="error"></span>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    <span id="error-email" class="error"></span>

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <span id="error-password" class="error"></span>

                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <span id="error-confirm_password" class="error"></span>

                    <div class="form-navigation">
                        <button type="button" onclick="prevStep()">Previous</button>
                        <button type="button" onclick="nextStep()">Next</button>
                    </div>
                </fieldset>

                <!-- Step 8 -->
                <fieldset id="step-8" class="step">
                    <legend>Step 8: Document Upload</legend>
                    <label for="photo">Photograph:</label>
                    <input type="file" id="photo" name="photo" accept="image/*" required>
                    <span id="error-step_8" class="error"></span>

                    <label for="hiv_report">HIV Report:</label>
                    <input type="file" id="hiv_report" name="hiv_report" accept="application/pdf,image/*" required>
                    <span id="error-step_8" class="error"></span>

                    <label for="address_proof">Address Proof:</label>
                    <input type="file" id="address_proof" name="address_proof" accept="application/pdf,image/*" required>
                    <span id="error-step_8" class="error"></span>

                    <label for="id_proof">ID Proof:</label>
                    <input type="file" id="id_proof" name="id_proof" accept="application/pdf,image/*" required>
                    <span id="error-step_8" class="error"></span>

                    <div class="form-navigation">
                        <button type="button" onclick="prevStep()">Previous</button>
                        <button type="submit">Submit</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <script>
        // JavaScript code from validators.js
        let currentStep = 1;

        function showStep(step) {
            document.querySelectorAll(".step").forEach((stepDiv) => stepDiv.classList.remove("active"));
            document.getElementById(`step-${step}`).classList.add("active");
        }

        function validateStep(step) {
            let isValid = true;
            const errors = document.querySelectorAll(".error");
            errors.forEach((error) => (error.innerText = "")); // Clear previous errors

            if (step === 1) {
                const first_name = document.getElementById("first_name").value.trim();
                const last_name = document.getElementById("last_name").value.trim();
                const dob = document.getElementById("dob").value.trim();
                const height = document.getElementById("height").value.trim();
                const weight = document.getElementById("weight").value.trim();
                if (!first_name) {
                    document.getElementById("error-first_name").innerText = "First name is required";
                    isValid = false;
                }
                if (!last_name) {
                    document.getElementById("error-last_name").innerText = "Last name is required";
                    isValid = false;
                }
                if (!dob) {
                    document.getElementById("error-dob").innerText = "Date of Birth is required";
                    isValid = false;
                }
                if (!height) {
                    document.getElementById("error-height").innerText = "Height is required";
                    isValid = false;
                }
                if (!weight) {
                    document.getElementById("error-weight").innerText = "Weight is required";
                    isValid = false;
                }
            } else if (step === 2) {
                const family_members = document.getElementById("family_members").value.trim();
                const hiv_positive_members = document.getElementById("hiv_positive_members").value.trim();
                if (!family_members || !hiv_positive_members) {
                    document.getElementById("error-step_2").innerText = "All fields required";
                    isValid = false;
                } else if (family_members < hiv_positive_members) {
                    document.getElementById("error-step_2").innerText = "Please check the inputs";
                    isValid = false;
                }
            } else if (step === 3) {
                const hiv_detection = document.getElementById("hiv_detection").value.trim();
                const art_status = document.getElementById("art_status").value.trim();
                const cd4_count = document.getElementById("cd4_count").value.trim();
                if (!hiv_detection || !art_status || !cd4_count) {
                    document.getElementById("error-step_3").innerText = "All fields required";
                    isValid = false;
                }
            } else if (step === 4) {
                const income = document.getElementById("income").value.trim();
                if (!income) {
                    document.getElementById("error-step_4").innerText = "All fields required";
                    isValid = false;
                }
            } else if (step === 5) {
                const property_value = document.getElementById("property_value").value.trim();
                if (!property_value) {
                    document.getElementById("error-step_5").innerText = "All fields required";
                    isValid = false;
                }
            } else if (step === 7) {
                const username = document.getElementById("username").value.trim();
                const email = document.getElementById("email").value.trim();
                const password = document.getElementById("password").value.trim();
                const confirm_password = document.getElementById("confirm_password").value.trim();
                if (!username || !email || !password || !confirm_password) {
                    document.getElementById("error-step_7").innerText = "All fields required";
                    isValid = false;
                } else if (password !== confirm_password) {
                    document.getElementById("error-password").innerText = "Passwords do not match";
                    isValid = false;
                } else if (!/\S+@\S+\.\S+/.test(email)) {
                    document.getElementById("error-email").innerText = "Valid email required";
                    isValid = false;
                }
            }
            return isValid;
        }

        function nextStep() {
            if (validateStep(currentStep)) {
                currentStep++;
                showStep(currentStep);
            }
        }

        function prevStep() {
            currentStep--;
            showStep(currentStep);
        }

        function validateFileUpload() {
            const allowedTypes = ["image/jpeg", "image/png", "application/pdf"];
            const maxSize = 50 * 1024 * 1024; // 50MB in bytes
            let isValid = true;
            let errorMessage = "";

            // Validate files for each input field
            const fields = ["photo", "hiv_report", "address_proof", "id_proof"];

            fields.forEach((field) => {
                const fileInput = document.getElementById(field);
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];

                    // Check file type
                    if (!allowedTypes.includes(file.type)) {
                        errorMessage += `${field} must be a JPEG image, PNG, or PDF. `;
                        isValid = false;
                    }

                    // Check file size
                    if (file.size > maxSize) {
                        errorMessage += `${field} size exceeds 50MB limit. `;
                        isValid = false;
                    }
                }
            });

            // Display error messages
            if (!isValid) {
                document.getElementById("error-step_8").innerText = errorMessage;
            }

            return isValid;
        }

        // Add validation on form submission
        document.getElementById("registrationForm").onsubmit = function (event) {
            if (!validateFileUpload()) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        };

        // Initial step display
        showStep(currentStep);
    </script>
</body>
</html>