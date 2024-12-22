let currentStep = 1;

    function showStep(step) {
        document.querySelectorAll('.step').forEach(stepDiv => stepDiv.classList.remove('active'));
        document.getElementById(`step-${step}`).classList.add('active');

    }

    function validateStep(step) {
        let isValid = true;
        const errors = document.querySelectorAll('.error');
        errors.forEach(error => error.innerText = ''); // Clear previous errors

        if (step === 1) {
            const first_name = document.getElementById('first_name').value.trim();
            const last_name = document.getElementById('last_name').value.trim();
            const dob = document.getElementById('dob').value.trim();
            const height = document.getElementById('height').value.trim();
            const weight = document.getElementById('weight').value.trim();
            if (!first_name) {
                document.getElementById('error-first_name').innerText = 'First name is required';
                isValid = false;
            }
            if (!last_name) {
                document.getElementById('error-last_name').innerText = 'Last name is required';
                isValid = false;
            }
            if (!dob) {
                document.getElementById('error-dob').innerText = 'Date of Birth is required';
                isValid = false;
            }
            if (!height) {
                document.getElementById('error-height').innerText = 'Height is required';
                isValid = false;
            }
            if (!weight) {
                document.getElementById('error-weight').innerText = 'Weight is required';
                isValid = false;
            }
            
        } else if (step === 2) {
            const family_members = document.getElementById('family_members').value.trim();
            const hiv_positive_members = document.getElementById('hiv_positive_members').value.trim();
            if (!family_members || !hiv_positive_members) {
                document.getElementById('error-step_2').innerText = 'All fields required';
                isValid = false;
            }else if(family_members<hiv_positive_members){
                document.getElementById('error-step_2').innerText = 'Please check the inputs';
                isValid = false;
            }
        }else if (step === 3) {
            const hiv_detection = document.getElementById('hiv_detection').value.trim();
            const art_status = document.getElementById('art_status').value.trim();
            const cd4_count = document.getElementById('cd4_count').value.trim();
            if (!hiv_detection || !art_status || !cd4_count) {
                document.getElementById('error-step_3').innerText = 'All fields required';
                isValid = false;
            }
        }else if (step === 4) {
            const income = document.getElementById('income').value.trim();
            if (!income) {
                document.getElementById('error-step_4').innerText = 'All fields required';
                isValid = false;
            }
        }else if (step === 5) {
            const property_value = document.getElementById('property_value').value.trim();
            if (!property_value) {
                document.getElementById('error-step_5').innerText = 'All fields required';
                isValid = false;
            }
        }else if (step === 7) {
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const confirm_password = document.getElementById('confirm_password').value.trim();
            if (!username||!email||!password||!confirm_password) {
                document.getElementById('error-step_7').innerText = 'All fields required';
                isValid = false;
            }else if(password!=confirm_password){
                document.getElementById('error-password').innerText = 'Passwords do not match';
                isValid = false;
            }else if(!email || !/\S+@\S+\.\S+/.test(email)){
                document.getElementById('error-email').innerText = 'Valid email required';
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
        const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        const maxSize = 50 * 1024 * 1024; // 50MB in bytes
        let isValid = true;
        let errorMessage = '';

        // Validate files for each input field
        const fields = ['photo', 'hiv_report', 'address_proof', 'id_proof'];
        
        fields.forEach(field => {
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
            document.getElementById('error-step_8').innerText = errorMessage;
        }

        return isValid;
    }

    // Add validation on form submission
    document.getElementById('registrationForm').onsubmit = function(event) {
        if (!validateFileUpload()) {
            event.preventDefault(); // Prevent form submission if validation fails
        }
    };