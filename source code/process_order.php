<?php
/**
 * process_order.php
 * Processes order submissions from payment.php
 * Validates all form data server-side
 * Creates orders table if it doesn't exist
 * Stores valid orders in database
 * Redirects to fix_order.php if errors exist
 * Redirects to receipt.php on success
 */

// Prevent direct access
if (!isset($_POST['first_name'])) {
    header("Location: payment.php");
    exit();
}

// Include database settings
require_once("settings.php");

// Connect to database
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Create orders table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    street_address VARCHAR(100) NOT NULL,
    suburb VARCHAR(50) NOT NULL,
    state VARCHAR(3) NOT NULL,
    postcode VARCHAR(4) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    preferred_contact VARCHAR(10) NOT NULL,
    product VARCHAR(100) NOT NULL,
    product_options TEXT,
    quantity INT NOT NULL,
    order_cost DECIMAL(10,2) NOT NULL,
    cc_type VARCHAR(20) NOT NULL,
    cc_name VARCHAR(100) NOT NULL,
    cc_number VARCHAR(16) NOT NULL,
    cc_expiry VARCHAR(5) NOT NULL,
    cc_cvv VARCHAR(4) NOT NULL,
    order_time DATETIME NOT NULL,
    order_status ENUM('PENDING', 'FULFILLED', 'PAID', 'ARCHIVED') NOT NULL DEFAULT 'PENDING'
)";

mysqli_query($conn, $create_table_sql);

// Initialize errors array
$errors = array();

// Sanitize function - removes leading/trailing spaces, backslashes, and HTML characters
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Sanitize all POST data
$first_name = sanitize_input($_POST['first_name'] ?? '');
$last_name = sanitize_input($_POST['last_name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$street_address = sanitize_input($_POST['street_address'] ?? '');
$suburb = sanitize_input($_POST['suburb'] ?? '');
$state = sanitize_input($_POST['state'] ?? '');
$postcode = sanitize_input($_POST['postcode'] ?? '');
$phone = sanitize_input($_POST['phone'] ?? '');
$preferred_contact = sanitize_input($_POST['preferred_contact'] ?? '');
$product = sanitize_input($_POST['product'] ?? '');
$product_options = sanitize_input($_POST['product_options'] ?? '');
$quantity = sanitize_input($_POST['quantity'] ?? '');
$cc_type = sanitize_input($_POST['cc_type'] ?? '');
$cc_name = sanitize_input($_POST['cc_name'] ?? '');
$cc_number = sanitize_input($_POST['cc_number'] ?? '');
$cc_expiry = sanitize_input($_POST['cc_expiry'] ?? '');
$cc_cvv = sanitize_input($_POST['cc_cvv'] ?? '');

// VALIDATION RULES

// 1. First Name - alphabetic characters only, max 50 chars
if (empty($first_name)) {
    $errors[] = "First name is required.";
} elseif (!preg_match("/^[a-zA-Z\s-]+$/", $first_name)) {
    $errors[] = "First name must contain only alphabetic characters.";
} elseif (strlen($first_name) > 50) {
    $errors[] = "First name must not exceed 50 characters.";
}

// 2. Last Name - alphabetic characters only, max 50 chars
if (empty($last_name)) {
    $errors[] = "Last name is required.";
} elseif (!preg_match("/^[a-zA-Z\s-]+$/", $last_name)) {
    $errors[] = "Last name must contain only alphabetic characters.";
} elseif (strlen($last_name) > 50) {
    $errors[] = "Last name must not exceed 50 characters.";
}

// 3. Email - must be valid email format
if (empty($email)) {
    $errors[] = "Email address is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}

// 4. Street Address - required, max 100 chars
if (empty($street_address)) {
    $errors[] = "Street address is required.";
} elseif (strlen($street_address) > 100) {
    $errors[] = "Street address must not exceed 100 characters.";
}

// 5. Suburb - required, max 50 chars
if (empty($suburb)) {
    $errors[] = "Suburb/town is required.";
} elseif (strlen($suburb) > 50) {
    $errors[] = "Suburb/town must not exceed 50 characters.";
}

// 6. State - must be valid Australian state
$valid_states = array('VIC', 'NSW', 'QLD', 'NT', 'WA', 'SA', 'TAS', 'ACT');
if (empty($state)) {
    $errors[] = "State is required.";
} elseif (!in_array($state, $valid_states)) {
    $errors[] = "Please select a valid state.";
}

// 7. Postcode - exactly 4 digits, must match state
if (empty($postcode)) {
    $errors[] = "Postcode is required.";
} elseif (!preg_match("/^[0-9]{4}$/", $postcode)) {
    $errors[] = "Postcode must be exactly 4 digits.";
} else {
    // Check postcode matches state
    $postcode_first_digit = substr($postcode, 0, 1);
    $state_postcode_rules = array(
        'VIC' => array('3', '8'),
        'NSW' => array('1', '2'),
        'QLD' => array('4', '9'),
        'NT' => array('0'),
        'WA' => array('6'),
        'SA' => array('5'),
        'TAS' => array('7'),
        'ACT' => array('0', '2')
    );
    
    if (isset($state_postcode_rules[$state])) {
        if (!in_array($postcode_first_digit, $state_postcode_rules[$state])) {
            $errors[] = "Postcode does not match the selected state.";
        }
    }
}

// 8. Phone - 8 to 15 digits, can include spaces
if (empty($phone)) {
    $errors[] = "Phone number is required.";
} elseif (!preg_match("/^[0-9\s]{8,15}$/", $phone)) {
    $errors[] = "Phone number must be 8 to 15 digits.";
}

// 9. Preferred Contact - must be email or phone
$valid_contacts = array('email', 'phone');
if (empty($preferred_contact)) {
    $errors[] = "Preferred contact method is required.";
} elseif (!in_array($preferred_contact, $valid_contacts)) {
    $errors[] = "Please select a valid contact method.";
}

// 10. Product - required
if (empty($product)) {
    $errors[] = "Please select a product/service.";
}

// 11. Quantity - must be positive integer
if (empty($quantity)) {
    $errors[] = "Quantity is required.";
} elseif (!is_numeric($quantity) || $quantity < 1 || $quantity != floor($quantity)) {
    $errors[] = "Quantity must be a positive whole number.";
}

// 12. Credit Card Type - must be Visa, Mastercard, or American Express
$valid_cc_types = array('Visa', 'Mastercard', 'American Express');
if (empty($cc_type)) {
    $errors[] = "Credit card type is required.";
} elseif (!in_array($cc_type, $valid_cc_types)) {
    $errors[] = "Please select a valid credit card type (Visa, Mastercard, or American Express).";
}

// 13. Credit Card Name - alphabetic characters only
if (empty($cc_name)) {
    $errors[] = "Name on credit card is required.";
} elseif (!preg_match("/^[a-zA-Z\s-]+$/", $cc_name)) {
    $errors[] = "Name on credit card must contain only alphabetic characters.";
}

// 14. Credit Card Number - 15 or 16 digits, must match card type
if (empty($cc_number)) {
    $errors[] = "Credit card number is required.";
} elseif (!preg_match("/^[0-9]{15,16}$/", $cc_number)) {
    $errors[] = "Credit card number must be 15 or 16 digits.";
} else {
    // Validate against card type
    $cc_length = strlen($cc_number);
    $cc_first_two = substr($cc_number, 0, 2);
    $cc_first_digit = substr($cc_number, 0, 1);
    
    if ($cc_type === 'Visa') {
        if ($cc_length != 16 || $cc_first_digit != '4') {
            $errors[] = "Visa cards must have 16 digits and start with 4.";
        }
    } elseif ($cc_type === 'Mastercard') {
        $cc_first_two_int = intval($cc_first_two);
        if ($cc_length != 16 || $cc_first_two_int < 51 || $cc_first_two_int > 55) {
            $errors[] = "Mastercard must have 16 digits and start with 51-55.";
        }
    } elseif ($cc_type === 'American Express') {
        if ($cc_length != 15 || ($cc_first_two != '34' && $cc_first_two != '37')) {
            $errors[] = "American Express must have 15 digits and start with 34 or 37.";
        }
    }
}

// 15. Credit Card Expiry - must be mm-yy format
if (empty($cc_expiry)) {
    $errors[] = "Credit card expiry date is required.";
} elseif (!preg_match("/^(0[1-9]|1[0-2])-[0-9]{2}$/", $cc_expiry)) {
    $errors[] = "Credit card expiry must be in mm-yy format.";
}

// 16. CVV - must be 3 or 4 digits
if (empty($cc_cvv)) {
    $errors[] = "CVV is required.";
} elseif (!preg_match("/^[0-9]{3,4}$/", $cc_cvv)) {
    $errors[] = "CVV must be 3 or 4 digits.";
}

// If there are errors, pass data to fix_order.php
if (!empty($errors)) {
    // Store all data in session to pass to fix_order.php
    session_start();
    $_SESSION['form_data'] = array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'street_address' => $street_address,
        'suburb' => $suburb,
        'state' => $state,
        'postcode' => $postcode,
        'phone' => $phone,
        'preferred_contact' => $preferred_contact,
        'product' => $product,
        'product_options' => $product_options,
        'quantity' => $quantity
        // DO NOT pass credit card details
    );
    $_SESSION['errors'] = $errors;
    
    header("Location: fix_order.php");
    exit();
}

// If validation passes, calculate total cost and insert into database

// Calculate order cost (example pricing - adjust based on your products)
$product_prices = array(
    'Basic Accounting Package' => 299.00,
    'Professional Accounting Package' => 599.00,
    'Enterprise Accounting Package' => 1299.00,
    'Tax Preparation Service' => 399.00,
    'Bookkeeping Service' => 199.00
);

$unit_price = $product_prices[$product] ?? 100.00; // Default price if product not found
$order_cost = $unit_price * intval($quantity);

// Get current date and time
$order_time = date('Y-m-d H:i:s');

// Prepare SQL statement to prevent SQL injection
$stmt = mysqli_prepare($conn, "INSERT INTO orders 
    (first_name, last_name, email, street_address, suburb, state, postcode, phone, 
    preferred_contact, product, product_options, quantity, order_cost, cc_type, cc_name, 
    cc_number, cc_expiry, cc_cvv, order_time, order_status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'PENDING')");

mysqli_stmt_bind_param($stmt, "sssssssssssidsssss", 
    $first_name, $last_name, $email, $street_address, $suburb, $state, $postcode, $phone,
    $preferred_contact, $product, $product_options, $quantity, $order_cost, $cc_type, 
    $cc_name, $cc_number, $cc_expiry, $cc_cvv, $order_time);

if (mysqli_stmt_execute($stmt)) {
    // Get the order_id of the inserted record
    $order_id = mysqli_insert_id($conn);
    
    // Store order details in session for receipt page
    session_start();
    $_SESSION['receipt_data'] = array(
        'order_id' => $order_id,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'street_address' => $street_address,
        'suburb' => $suburb,
        'state' => $state,
        'postcode' => $postcode,
        'phone' => $phone,
        'preferred_contact' => $preferred_contact,
        'product' => $product,
        'product_options' => $product_options,
        'quantity' => $quantity,
        'order_cost' => $order_cost,
        'cc_type' => $cc_type,
        'cc_name' => $cc_name,
        'cc_number' => $cc_number, // Will be masked in receipt
        'order_time' => $order_time,
        'order_status' => 'PENDING'
    );
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    // Redirect to receipt page
    header("Location: receipt.php");
    exit();
} else {
    // Database insert failed
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    session_start();
    $_SESSION['errors'] = array("An error occurred while processing your order. Please try again.");
    $_SESSION['form_data'] = array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'street_address' => $street_address,
        'suburb' => $suburb,
        'state' => $state,
        'postcode' => $postcode,
        'phone' => $phone,
        'preferred_contact' => $preferred_contact,
        'product' => $product,
        'product_options' => $product_options,
        'quantity' => $quantity
    );
    
    header("Location: fix_order.php");
    exit();
}
?>
