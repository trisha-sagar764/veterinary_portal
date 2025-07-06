
$(document).ready(function() {
    const BASE_URL = '/veterinary_portal/'; 
    // Password validation
    function validatePassword() {
        const password = $('#password').val();
        const confirm = $('#confirm_password').val();
        
        // Password requirements
        $('#lengthReq').toggleClass('valid', password.length >= 8);
        $('#upperReq').toggleClass('valid', /[A-Z]/.test(password));
        $('#lowerReq').toggleClass('valid', /[a-z]/.test(password));
        $('#numberReq').toggleClass('valid', /\d/.test(password));
        $('#specialReq').toggleClass('valid', /[@#$%^&*!]/.test(password));
        
        // Password match
        const matchFeedback = $('#passwordMatchFeedback');
        if (password && confirm) {
            if (password === confirm) {
                matchFeedback.text('✓ Passwords match').removeClass('invalid').addClass('valid');
            } else {
                matchFeedback.text('✗ Passwords do not match').removeClass('valid').addClass('invalid');
            }
        } else {
            matchFeedback.text('');
        }
    }

    // Username availability check
    function checkUsernameAvailability() {
    const username = $('#username').val().trim();
    const feedback = $('#usernameFeedback');
    
    if (username.length < 3) {
        feedback.html('<span class="invalid">Username must be at least 3 characters</span>');
        return;
    }
    
    $.ajax({
        url: 'ajax/check_username.php', // Relative path is better
        type: 'POST',
        data: { 
            username: username,
            csrf_token: $('input[name="csrf_token"]').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.available) {
                feedback.html('<span class="valid">✓ Username available</span>');
            } else {
                feedback.html('<span class="invalid">✗ Username already taken</span>');
            }
        },
        error: function(xhr, status, error) {
            let errorMsg = 'Error checking username';
            // Try to extract error message from response
            if (xhr.responseText) {
                try {
                    // Handle cases where server returns HTML error
                    if (xhr.responseText.startsWith('<')) {
                        const doc = new DOMParser().parseFromString(xhr.responseText, 'text/html');
                        errorMsg = doc.body.textContent.trim() || errorMsg;
                    } else {
                        const response = JSON.parse(xhr.responseText);
                        errorMsg = response.message || errorMsg;
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                }
            }
            feedback.html('<span class="invalid">' + errorMsg + '</span>');
            console.error("AJAX Error:", status, error, xhr.responseText);
        }
    });
}

    // Load subdistricts when district changes
$('#district').change(function() {
    const districtCode = $(this).val();
    const $subdistrict = $('#subdistrict');
    const csrfToken = $('input[name="csrf_token"]').val();

    if (!csrfToken) {
        console.error('CSRF token missing');
        return;
    }

    if (!districtCode) {
        $subdistrict.html('<option value="">Select Subdistrict</option>')
                   .prop('disabled', true);
        $('#village').html('<option value="">Select Village</option>')
                   .prop('disabled', true);
        return;
    }

    $subdistrict.prop('disabled', false)
               .html('<option value="">Loading subdistricts...</option>');

    $.ajax({
        url: BASE_URL + 'location/get_subdistricts.php',
        type: 'POST',
        dataType: 'html',
        data: {
            district_code: districtCode,
            csrf_token: csrfToken
        },
        success: function(data) {
            $subdistrict.html(data);
        },
        error: function(xhr) {
            let errorMsg = 'Error loading subdistricts';
            if (xhr.responseText) {
                errorMsg += ': ' + xhr.responseText.substring(0, 100);
            }
            $subdistrict.html(`<option value="">${errorMsg}</option>`);
            console.error("AJAX Error:", xhr.status, errorMsg);
        }
    });
});

// Load villages when subdistrict changes
let villageAjax;
$('#subdistrict').change(function() {
    const subdistrictCode = $(this).val();
    const $village = $('#village');
    const csrfToken = $('input[name="csrf_token"]').val();

    if (villageAjax) villageAjax.abort();

    if (!subdistrictCode) {
        $village.html('<option value="">Select Village</option>')
               .prop('disabled', true);
        return;
    }

    $village.prop('disabled', false)
            .html('<option value="">Loading villages...</option>');

    villageAjax = $.ajax({
        url:BASE_URL +'location/get_villages.php',
        type: 'POST',
        dataType: 'html',
        data: { 
            subdistrict_code: subdistrictCode,
            csrf_token: csrfToken
        },
        success: function(data) {
            $village.html(data);
            if ($('#village option:first').text().startsWith('Error:')) {
                console.error('Server error:', $('#village option:first').text());
            }
        },
        error: function(xhr) {
            let errorMsg = 'Error loading villages';
            if (xhr.responseText) {
                errorMsg += ': ' + xhr.responseText.substring(0, 100);
            }
            $village.html(`<option value="">${errorMsg}</option>`);
            console.error('AJAX Error:', xhr.status, errorMsg);
        }
    });
});
    // Input validation and filtering
    $('#username').on('input', function() {
        $(this).val($(this).val().replace(/[^a-zA-Z0-9_]/g, ''));
        clearTimeout(window.usernameTimeout);
        window.usernameTimeout = setTimeout(checkUsernameAvailability, 500);
    });
    
    $('#phone').on('input', function() {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
    });
    
    $('#pincode').on('input', function() {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
    });
    
    // Password field event listeners
    $('#password, #confirm_password').on('input', validatePassword);
    
    // OTP Verification System
    $('#sendOtpBtn').click(function() {
        const mobile = $('#phone').val();
        if (!/^\d{10}$/.test(mobile)) {
            alert('Please enter a valid 10-digit mobile number');
            return;
        }
        
        $('#sendOtpBtn').prop('disabled', true).text('Sending...');
        
        $.ajax({
            url: BASE_URL + '/ajax/send_otp.php',
            type: 'POST',
            data: { 
                mobile: mobile,
                csrf_token: $('input[name="csrf_token"]').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#otpVerification').show();
                    $('#verificationSuccess').hide();
                    $('#mobileVerified').val('0');
                    $('#otpStatus').html('<div class="alert alert-info">' + response.message + 
                        (response.demo_otp ? '<br>Demo OTP: ' + response.demo_otp : '') + '</div>');
                } else {
                    alert(response.message);
                }
                $('#sendOtpBtn').prop('disabled', false).text('Send OTP');
            },
            error: function() {
                alert('Error sending OTP. Please try again.');
                $('#sendOtpBtn').prop('disabled', false).text('Send OTP');
            }
        });
    });
    
    $('#verifyOtpBtn').click(function() {
        const otp = $('#otp').val();
        if (!/^\d{6}$/.test(otp)) {
            alert('Please enter a valid 6-digit OTP');
            return;
        }
        
        $('#verifyOtpBtn').prop('disabled', true).text('Verifying...');
        
        $.ajax({
            url: BASE_URL + '/ajax/verify_otp.php',
            type: 'POST',
            data: { 
                otp: otp,
                csrf_token: $('input[name="csrf_token"]').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#otpStatus').html('<div class="alert alert-success">Mobile number verified successfully</div>');
                    $('#verificationSuccess').show();
                    $('#mobileVerified').val('1');
                    checkFormCompletion(); // Enable register button if all validations pass
                } else {
                    $('#otpStatus').html('<div class="alert alert-danger">' + response.message + '</div>');
                    $('#verificationSuccess').hide();
                    $('#mobileVerified').val('0');
                }
                $('#verifyOtpBtn').prop('disabled', false).text('Verify OTP');
            },
            error: function() {
                alert('Error verifying OTP. Please try again.');
                $('#verifyOtpBtn').prop('disabled', false).text('Verify OTP');
                $('#verificationSuccess').hide();
                $('#mobileVerified').val('0');
            }
        });
    });

    // Check if form is complete to enable register button
    function checkFormCompletion() {
        // Check all required fields are filled and valid
        const isFormValid = (
            $('#name').val() &&
            $('#username').val() &&
            $('#phone').val() &&
            $('#address').val() &&
            $('#district').val() &&
            $('#subdistrict').val() &&
            $('#village').val() &&
            $('#pincode').val() &&
            $('#password').val() &&
            $('#confirm_password').val() &&
            $('#mobileVerified').val() === '1' &&
            $('#passwordMatchFeedback').hasClass('valid') &&
            $('#usernameFeedback').find('.valid').length > 0
        );

        $('#registerBtn').prop('disabled', !isFormValid);
    }

    // Add event listeners for form validation
    $('input, select').on('input change', checkFormCompletion);
});