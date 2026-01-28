<?php
/**
 * fix_order.php
 * Displays validation errors from process_order.php
 * Shows form with previously entered data (except credit card details)
 * Allows user to fix errors and resubmit to process_order.php
 */

// Start session to retrieve error data
session_start();

// Prevent direct access - redirect if no errors in session
if (!isset($_SESSION['errors']) || !isset($_SESSION['form_data'])) {
    header("Location: payment.php");
    exit();
}

// Get errors and form data from session
$errors = $_SESSION['errors'];
$form_data = $_SESSION['form_data'];

// Clear session data after retrieving
unset($_SESSION['errors']);
unset($_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Fix your order details">
    <meta name="keywords" content="order, payment, fix errors">
    <meta name="author" content="EZ-Accounting">
    <title>Fix Order - EZ-Accounting</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>

<header>
    <?php include 'header.inc'; ?>
    <?php include 'menu.inc'; ?>
</header>

<main>
    <h1>Please Fix the Following Errors</h1>
    
    <div class="error-messages">
        <h2>Validation Errors:</h2>
        <ul>
            <?php
            foreach ($errors as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            ?>
        </ul>
        <p><strong>Please correct the errors below and resubmit your order.</strong></p>
    </div>

    <form method="post" action="process_order.php" novalidate>
        
        <fieldset>
            <legend>Customer Details</legend>
            
            <label for="first_name">First Name: <span class="required">*</span></label>
            <input type="text" id="first_name" name="first_name" 
                   value="<?php echo htmlspecialchars($form_data['first_name'] ?? ''); ?>" 
                   maxlength="50" required>
            
            <label for="last_name">Last Name: <span class="required">*</span></label>
            <input type="text" id="last_name" name="last_name" 
                   value="<?php echo htmlspecialchars($form_data['last_name'] ?? ''); ?>" 
                   maxlength="50" required>
            
            <label for="email">Email Address: <span class="required">*</span></label>
            <input type="email" id="email" name="email" 
                   value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" 
                   required>
            
            <label for="street_address">Street Address: <span class="required">*</span></label>
            <input type="text" id="street_address" name="street_address" 
                   value="<?php echo htmlspecialchars($form_data['street_address'] ?? ''); ?>" 
                   maxlength="100" required>
            
            <label for="suburb">Suburb/Town: <span class="required">*</span></label>
            <input type="text" id="suburb" name="suburb" 
                   value="<?php echo htmlspecialchars($form_data['suburb'] ?? ''); ?>" 
                   maxlength="50" required>
            
            <label for="state">State: <span class="required">*</span></label>
            <select id="state" name="state" required>
                <option value="">Please select</option>
                <?php
                $states = array('VIC', 'NSW', 'QLD', 'NT', 'WA', 'SA', 'TAS', 'ACT');
                foreach ($states as $state) {
                    $selected = ($form_data['state'] ?? '') === $state ? 'selected' : '';
                    echo "<option value=\"$state\" $selected>$state</option>";
                }
                ?>
            </select>
            
            <label for="postcode">Postcode: <span class="required">*</span></label>
            <input type="text" id="postcode" name="postcode" 
                   value="<?php echo htmlspecialchars($form_data['postcode'] ?? ''); ?>" 
                   pattern="[0-9]{4}" maxlength="4" required>
            
            <label for="phone">Phone Number: <span class="required">*</span></label>
            <input type="tel" id="phone" name="phone" 
                   value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>" 
                   required>
            
            <label>Preferred Contact Method: <span class="required">*</span></label>
            <label>
                <input type="radio" name="preferred_contact" value="email" 
                       <?php echo ($form_data['preferred_contact'] ?? '') === 'email' ? 'checked' : ''; ?> required>
                Email
            </label>
            <label>
                <input type="radio" name="preferred_contact" value="phone" 
                       <?php echo ($form_data['preferred_contact'] ?? '') === 'phone' ? 'checked' : ''; ?> required>
                Phone
            </label>
        </fieldset>

        <fieldset>
            <legend>Product/Service Details</legend>
            
            <label for="product">Select Product/Service: <span class="required">*</span></label>
            <select id="product" name="product" required>
                <option value="">Please select</option>
                <?php
                $products = array(
                    'Basic Accounting Package',
                    'Professional Accounting Package',
                    'Enterprise Accounting Package',
                    'Tax Preparation Service',
                    'Bookkeeping Service'
                );
                foreach ($products as $prod) {
                    $selected = ($form_data['product'] ?? '') === $prod ? 'selected' : '';
                    echo "<option value=\"$prod\" $selected>$prod</option>";
                }
                ?>
            </select>
            
            <label for="product_options">Product Options/Features:</label>
            <textarea id="product_options" name="product_options" rows="4"><?php echo htmlspecialchars($form_data['product_options'] ?? ''); ?></textarea>
            
            <label for="quantity">Quantity: <span class="required">*</span></label>
            <input type="number" id="quantity" name="quantity" 
                   value="<?php echo htmlspecialchars($form_data['quantity'] ?? '1'); ?>" 
                   min="1" required>
        </fieldset>

        <fieldset>
            <legend>Payment Details</legend>
            <p class="note"><strong>Note:</strong> For security reasons, please re-enter your credit card details.</p>
            
            <label for="cc_type">Credit Card Type: <span class="required">*</span></label>
            <select id="cc_type" name="cc_type" required>
                <option value="">Please select</option>
                <option value="Visa">Visa</option>
                <option value="Mastercard">Mastercard</option>
                <option value="American Express">American Express</option>
            </select>
            
            <label for="cc_name">Name on Card: <span class="required">*</span></label>
            <input type="text" id="cc_name" name="cc_name" maxlength="100" required>
            
            <label for="cc_number">Card Number: <span class="required">*</span></label>
            <input type="text" id="cc_number" name="cc_number" 
                   pattern="[0-9]{15,16}" maxlength="16" 
                   placeholder="15 or 16 digits" required>
            
            <label for="cc_expiry">Expiry Date (mm-yy): <span class="required">*</span></label>
            <input type="text" id="cc_expiry" name="cc_expiry" 
                   pattern="(0[1-9]|1[0-2])-[0-9]{2}" 
                   placeholder="mm-yy" maxlength="5" required>
            
            <label for="cc_cvv">CVV: <span class="required">*</span></label>
            <input type="text" id="cc_cvv" name="cc_cvv" 
                   pattern="[0-9]{3,4}" maxlength="4" 
                   placeholder="3 or 4 digits" required>
        </fieldset>

        <div class="form-buttons">
            <input type="submit" value="Submit Order" class="submit-btn">
            <input type="reset" value="Reset Form" class="reset-btn">
            <a href="payment.php" class="cancel-btn">Cancel and Start Over</a>
        </div>
    </form>
</main>

<footer>
    <?php include 'footer.inc'; ?>
</footer>

</body>
</html>
