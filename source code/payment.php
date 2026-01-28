<?php
/**
 * payment.php
 * Payment form for product/service purchase
 * Collects customer details, product selection, and payment information
 * HTML5 validation is disabled (novalidate) for server-side validation testing
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Complete your purchase">
    <meta name="keywords" content="payment, purchase, checkout">
    <meta name="author" content="EZ-Accounting">
    <title>Payment - EZ-Accounting</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>

<header>
    <?php include 'header.inc'; ?>
    <?php include 'menu.inc'; ?>
</header>

<main>
    <h1>Complete Your Purchase</h1>
    <p>Please fill in all required fields to complete your order.</p>

    <form method="post" action="process_order.php" novalidate>
        
        <fieldset>
            <legend>Customer Details</legend>
            
            <label for="first_name">First Name: <span class="required">*</span></label>
            <input type="text" id="first_name" name="first_name" maxlength="50" required>
            
            <label for="last_name">Last Name: <span class="required">*</span></label>
            <input type="text" id="last_name" name="last_name" maxlength="50" required>
            
            <label for="email">Email Address: <span class="required">*</span></label>
            <input type="email" id="email" name="email" required>
            
            <label for="street_address">Street Address: <span class="required">*</span></label>
            <input type="text" id="street_address" name="street_address" maxlength="100" required>
            
            <label for="suburb">Suburb/Town: <span class="required">*</span></label>
            <input type="text" id="suburb" name="suburb" maxlength="50" required>
            
            <label for="state">State: <span class="required">*</span></label>
            <select id="state" name="state" required>
                <option value="">Please select</option>
                <option value="VIC">VIC</option>
                <option value="NSW">NSW</option>
                <option value="QLD">QLD</option>
                <option value="NT">NT</option>
                <option value="WA">WA</option>
                <option value="SA">SA</option>
                <option value="TAS">TAS</option>
                <option value="ACT">ACT</option>
            </select>
            
            <label for="postcode">Postcode: <span class="required">*</span></label>
            <input type="text" id="postcode" name="postcode" pattern="[0-9]{4}" maxlength="4" required>
            
            <label for="phone">Phone Number: <span class="required">*</span></label>
            <input type="tel" id="phone" name="phone" required>
            <small>8 to 15 digits (spaces allowed)</small>
            
            <label>Preferred Contact Method: <span class="required">*</span></label>
            <label>
                <input type="radio" name="preferred_contact" value="email" required> Email
            </label>
            <label>
                <input type="radio" name="preferred_contact" value="phone" required> Phone
            </label>
        </fieldset>

        <fieldset>
            <legend>Product/Service Selection</legend>
            
            <label for="product">Select Product/Service: <span class="required">*</span></label>
            <select id="product" name="product" required>
                <option value="">Please select</option>
                <option value="Basic Accounting Package">Basic Accounting Package - $299.00</option>
                <option value="Professional Accounting Package">Professional Accounting Package - $599.00</option>
                <option value="Enterprise Accounting Package">Enterprise Accounting Package - $1,299.00</option>
                <option value="Tax Preparation Service">Tax Preparation Service - $399.00</option>
                <option value="Bookkeeping Service">Bookkeeping Service - $199.00/month</option>
            </select>
            
            <label for="product_options">Additional Options/Features:</label>
            <textarea id="product_options" name="product_options" rows="4" 
                      placeholder="Enter any specific requirements or features you need..."></textarea>
            
            <label for="quantity">Quantity: <span class="required">*</span></label>
            <input type="number" id="quantity" name="quantity" min="1" value="1" required>
            <small>For services, this may represent number of months or units</small>
        </fieldset>

        <fieldset>
            <legend>Payment Details</legend>
            
            <label for="cc_type">Credit Card Type: <span class="required">*</span></label>
            <select id="cc_type" name="cc_type" required>
                <option value="">Please select</option>
                <option value="Visa">Visa</option>
                <option value="Mastercard">Mastercard</option>
                <option value="American Express">American Express</option>
            </select>
            
            <label for="cc_name">Name on Card: <span class="required">*</span></label>
            <input type="text" id="cc_name" name="cc_name" maxlength="100" 
                   placeholder="As it appears on card" required>
            
            <label for="cc_number">Card Number: <span class="required">*</span></label>
            <input type="text" id="cc_number" name="cc_number" 
                   pattern="[0-9]{15,16}" maxlength="16" 
                   placeholder="15 or 16 digits (no spaces)" required>
            <small>Visa: 16 digits starting with 4 | Mastercard: 16 digits starting with 51-55 | Amex: 15 digits starting with 34 or 37</small>
            
            <label for="cc_expiry">Expiry Date (mm-yy): <span class="required">*</span></label>
            <input type="text" id="cc_expiry" name="cc_expiry" 
                   pattern="(0[1-9]|1[0-2])-[0-9]{2}" 
                   placeholder="mm-yy" maxlength="5" required>
            <small>Format: mm-yy (e.g., 12-25)</small>
            
            <label for="cc_cvv">CVV: <span class="required">*</span></label>
            <input type="text" id="cc_cvv" name="cc_cvv" 
                   pattern="[0-9]{3,4}" maxlength="4" 
                   placeholder="3 or 4 digits" required>
            <small>3 digits on back (4 for Amex on front)</small>
        </fieldset>

        <div class="form-buttons">
            <input type="submit" value="Check Out" class="checkout-btn">
            <input type="reset" value="Reset Form" class="reset-btn">
        </div>
        
        <p class="security-note">
            <strong>🔒 Secure Payment:</strong> Your payment information is encrypted and secure.
        </p>
    </form>
</main>

<footer>
    <?php include 'footer.inc'; ?>
</footer>

<style>
/* Form styling */
fieldset {
    border: 1px solid #ddd;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border-radius: 5px;
}

legend {
    font-weight: bold;
    color: #333;
    padding: 0 0.5rem;
}

label {
    display: block;
    margin-top: 1rem;
    font-weight: 600;
    color: #555;
}

input[type="text"],
input[type="email"],
input[type="tel"],
input[type="number"],
select,
textarea {
    width: 100%;
    padding: 0.75rem;
    margin-top: 0.25rem;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1rem;
}

input[type="radio"] {
    margin-right: 0.5rem;
}

small {
    display: block;
    color: #666;
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

.required {
    color: red;
}

.form-buttons {
    margin-top: 2rem;
    text-align: center;
}

.checkout-btn,
.reset-btn {
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin: 0.5rem;
}

.checkout-btn {
    background-color: #28a745;
    color: white;
}

.checkout-btn:hover {
    background-color: #218838;
}

.reset-btn {
    background-color: #6c757d;
    color: white;
}

.reset-btn:hover {
    background-color: #545b62;
}

.security-note {
    text-align: center;
    margin-top: 1rem;
    padding: 1rem;
    background-color: #e7f3ff;
    border-radius: 5px;
}
</style>

</body>
</html>
