<?php
require_once __DIR__ . '\config.php';

/*****************************
 * DATABASE CONNECTION FUNCTIONS 
 *****************************/

function getDatabaseConnection() {
    try {
        $db = new PDO(
            "mysql:host=".DB_HOST.";dbname=".DB_NAME, 
            DB_USER, 
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        return $db;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        die("Database connection error. Please try again later.");
    }
}

/*****************************
 * LOCATION-RELATED FUNCTIONS 
 *****************************/

function fetchDistricts() {
    try {
        $db = getDatabaseConnection();
        $stmt = $db->query("SELECT district_code, district_name FROM districts ORDER BY district_name");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}

function fetchSubdistricts($districtCode) {
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("SELECT subdistrict_code, subdistrict_name 
                            FROM subdistricts 
                            WHERE district_code = ? 
                            ORDER BY subdistrict_name");
        $stmt->execute([$districtCode]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database error [fetchSubdistricts]: " . $e->getMessage());
        return [];
    }
}

function fetchVillages($subdistrictCode) {
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("SELECT village_code, village_name 
                            FROM villages 
                            WHERE subdistrict_code = ? 
                            ORDER BY village_name");
        $stmt->execute([$subdistrictCode]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database error [fetchVillages]: " . $e->getMessage());
        return [];
    }
}

function sanitizeFormInputs($postData) {
    return [
        'name' => trim($postData['name'] ?? ''),
        'username' => trim($postData['username'] ?? ''),
        'email' => trim($postData['email'] ?? ''),
        'phone' => trim($postData['phone'] ?? ''),
        'address' => trim($postData['address'] ?? ''),
        'district' => $postData['district'] ?? '',
        'subdistrict' => $postData['subdistrict'] ?? '',
        'village' => $postData['village'] ?? '',
        'pincode' => trim($postData['pincode'] ?? '')
    ];
}

function validateRegistrationForm($formData, $password, $confirmPassword) {
    $errors = [];
    
    if (empty($formData['name'])) $errors[] = "Full name is required";
    if (empty($formData['username'])) $errors[] = "Username is required";
    if (!empty($formData['email']) && !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    if (empty($formData['phone'])) $errors[] = "Phone number is required";
    elseif (!preg_match('/^[0-9]{10}$/', $formData['phone'])) $errors[] = "Phone number must be 10 digits";
    if (empty($formData['address'])) $errors[] = "Address is required";
    if (empty($formData['district'])) $errors[] = "District is required";
    if (empty($formData['subdistrict'])) $errors[] = "Subdistrict is required";
    if (empty($formData['village'])) $errors[] = "Village is required";
    if (empty($formData['pincode'])) $errors[] = "Pincode is required";
    elseif (!preg_match('/^[0-9]{6}$/', $formData['pincode'])) $errors[] = "Pincode must be 6 digits";
    if (empty($password)) $errors[] = "Password is required";
    elseif (strlen($password) < 8) $errors[] = "Password must be at least 8 characters";
    if ($password !== $confirmPassword) $errors[] = "Passwords do not match";
    if (!isset($_SESSION['mobile_verified']) || $_SESSION['mobile_verified'] !== true || $_SESSION['mobile'] !== $formData['phone']) {
        $errors[] = "Mobile number not verified. Please complete OTP verification.";
    }
    
    return $errors;
}

function insertLocation($db, $formData) {
    try {
        $stmt = $db->prepare("INSERT INTO locations 
            (address, village_code, pincode) 
            VALUES (?, ?, ?)");
        $stmt->execute([
            $formData['address'],
            $formData['village'],  // This should be the village_code
            $formData['pincode']
        ]);
        return $db->lastInsertId();
    } catch (PDOException $e) {
        error_log("Location insert error: " . $e->getMessage());
        throw new Exception("Could not save location information");
    }
}

/*****************************
 * PET-RELATED FUNCTIONS 
 *****************************/

function insertPetOwner($db, $formData, $locationId, $password) {
    try {
        // Validate required fields exist in $formData
        if (empty($formData['name'])) throw new Exception("Name is required");
        if (empty($formData['username'])) throw new Exception("Username is required");
        if (empty($formData['phone'])) throw new Exception("Mobile number is required");
        
        // Generate unique pet owner ID
        $petOwnerId = 'PO-' . bin2hex(random_bytes(2));
        
        $stmt = $db->prepare("INSERT INTO pet_owners 
            (pet_owner_id, name, email, mobile, location_id, username, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $petOwnerId,
            trim($formData['name']),
            !empty($formData['email']) ? trim($formData['email']) : null,
            trim($formData['phone']),
            $locationId,
            trim($formData['username']),
            password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])
        ]);
        
        return [
            'id' => $db->lastInsertId(),
            'pet_owner_id' => $petOwnerId,
            'username' => $formData['username'],  // Return username for session
            'mobile' => $formData['phone']       // Return mobile for session
        ];
    } catch (PDOException $e) {
        error_log("Pet owner insert error: " . $e->getMessage());
        throw new Exception("Could not create user account");
    }
}

function setRegistrationSuccessSession($insertResult, $name, $username, $mobile) {
    $_SESSION['registration_success'] = true;
    $_SESSION['pet_owner_id'] = $insertResult['pet_owner_id'];
    $_SESSION['pet_owner_db_id'] = $insertResult['id'];
    $_SESSION['name'] = $name;
    $_SESSION['username'] = $username;
    $_SESSION['mobile'] = $mobile;
    $_SESSION['mobile_verified'] = true; // Add this line
}

// New function to get pet owner by auto-increment ID
function getPetOwnerById($id) {
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("SELECT * FROM pet_owners WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Pet owner fetch error: " . $e->getMessage());
        return false;
    }
}

// New function to get pet owner by custom ID
function getPetOwnerByPetOwnerId($petOwnerId) {
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("SELECT * FROM pet_owners WHERE pet_owner_id = ?");
        $stmt->execute([$petOwnerId]);
        
        if ($stmt->rowCount() === 0) {
            error_log("No pet owner found with ID: " . $petOwnerId);
            return false;
        }
        
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}


function displayErrors($errors) {
    if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p class="mb-1"><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif;
}

function generateOTP() {
    return rand(100000, 999999);
}

/**
 * Retrieves pet owner by username
 * @param string $username The username to search for
 * @return array|false Returns user data as array or false if not found/error
 */
function getPetOwnerByUsername($username) {
    // Validate input
    if (!is_string($username) || trim($username) === '') {
        return false;
    }

    try {
        $db = getDatabaseConnection();
        
        // Use explicit column names instead of * for security
        $stmt = $db->prepare("
            SELECT 
                pet_owner_id, 
                username, 
                password, 
                name, 
                email,
                mobile,
                location_id,
                last_login,
                registration_date
            FROM pet_owners 
            WHERE username = :username 
            LIMIT 1
        ");
        
        // Use named parameter for clarity
        $stmt->bindValue(':username', trim($username), PDO::PARAM_STR);
        $stmt->execute();
        
        // Fetch as associative array
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Explicit return false if no user found
        return $user ? $user : false;
        
    } catch (PDOException $e) {
        error_log("[".date('Y-m-d H:i:s')."] Database error in getPetOwnerByUsername: " 
                  . $e->getMessage() . " for username: " . $username);
        return false;
    }
}

function updateLastLogin($petOwnerId) {
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("UPDATE pet_owners SET last_login = NOW() WHERE pet_owner_id = ?");
        $stmt->execute([$petOwnerId]);
        return true;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

function generateRandomString($length = 6) {
    $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // Avoid confusing characters like 0/O, 1/I
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAvailableTimeSlots($facilityId, $date) {
    try {
        $db = getDatabaseConnection();
        
        // Get facility working hours
        $stmt = $db->prepare("SELECT opening_time, closing_time FROM facility_hours WHERE facility_id = ?");
        $stmt->execute([$facilityId]);
        $hours = $stmt->fetch();
        
        if (!$hours) {
            return ['error' => 'Facility hours not configured'];
        }

        // Get booked appointments
        $stmt = $db->prepare("
            SELECT appointment_date 
            FROM appointments 
            WHERE facility_id = ? 
            AND DATE(appointment_date) = ?
            AND status = 'Scheduled'
        ");
        $stmt->execute([$facilityId, $date]);
        $bookedSlots = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        
        // Generate available slots (every 30 minutes)
        $start = new DateTime($date . ' ' . $hours['opening_time']);
        $end = new DateTime($date . ' ' . $hours['closing_time']);
        $interval = new DateInterval('PT30M');
        
        $slots = [];
        while ($start < $end) {
            $slot = $start->format('Y-m-d H:i:s');
            if (!in_array($slot, $bookedSlots)) {
                $slots[] = $slot;
            }
            $start->add($interval);
        }
        
        return $slots;
    } catch (PDOException $e) {
        error_log("Database error [getAvailableTimeSlots]: " . $e->getMessage());
        return ['error' => 'Could not fetch available slots'];
    }
}

// Add these with the other pet-related functions
function getPetsByOwnerId($ownerId) {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("SELECT pet_id, pet_name FROM pets WHERE pet_owner_id = ? ORDER BY name ASC");
    $stmt->execute([$ownerId]);
    return $stmt->fetchAll();
}

/*****************************
 * FACILITY-RELATED FUNCTIONS 
 *****************************/

// Add these with the other facility-related functions
function getActiveVeterinaryFacilities() {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("
        SELECT 
            vf.facility_id, 
            vf.official_name, 
            vf.address_line2, 
            d.district_name as district,
            ft.full_name as facility_type
        FROM veterinary_facilities vf
        JOIN facility_types ft ON vf.facility_type = ft.short_code
        JOIN districts d ON vf.district = d.district_code
        WHERE vf.is_active = 1
        ORDER BY vf.official_name ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

function getFacilitiesByDistrict($districtCode) {
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("
            SELECT f.*, ft.full_name as facility_type_name 
            FROM veterinary_facilities f
            JOIN facility_types ft ON f.facility_type = ft.short_code
            WHERE f.district = ? AND f.is_active = 1
            ORDER BY ft.full_name, f.official_name
        ");
        $stmt->execute([$districtCode]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database error [getFacilitiesByDistrict]: " . $e->getMessage());
        return [];
    }
}

/*****************************
 * AUTHENTICATION/SESSION FUNCTIONS 
 *****************************/

// Add this with the other session/authentication functions
function validatePetOwnerSession() {
    if (empty($_SESSION['pet_owner_id']) || empty($_SESSION['logged_in'])) {
        header('Location: login.php?reason=not_logged_in');
        exit;
    }
    
    return getPetOwnerByPetOwnerId($_SESSION['pet_owner_id']);
}


?>