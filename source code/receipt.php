<?php
/**
 * receipt.php
 * Displays order confirmation receipt
 * Shows all order details including order_id and order_status
 * Credit card number is masked for security
 */

// Start session to retrieve receipt data
session_start();

// Prevent direct access - redirect if no receipt data in session
if (!isset($_SESSION['receipt_data'])) {
    header("Location: payment.php");
    exit();
}

// Get receipt data from session
$receipt = $_SESSION['receipt_data'];

// Clear session data after retrieving
unset($_SESSION['receipt_data']);

// Function to mask credit card number (show only last 4 digits)
function mask_cc_number($cc_number) {
    $length = strlen($cc_number);
    $visible = 4;
    $masked = str_repeat('*', $length - $visible) . substr($cc_number, -$visible);
    return $masked;
}

$masked_cc = mask_cc_number($receipt['cc_number']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Order receipt and confirmation">
    <meta name="keywords" content="receipt, order confirmation, payment">
    <meta name="author" content="EZ-Accounting">
    <title>Order Receipt - EZ-Accounting</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>

<header>
    <?php include 'header.inc'; ?>
    <?php include 'menu.inc'; ?>
</header>

<main>
    <div class="receipt-container">
        <h1>Order Confirmation</h1>
        
        <div class="success-message">
            <h2>✓ Thank you for your order!</h2>
            <p>Your order has been successfully processed and is now pending fulfillment.</p>
        </div>

        <div class="receipt-details">
            <h2>Order Details</h2>
            
            <div class="receipt-section">
                <h3>Order Information</h3>
                <table class="receipt-table">
                    <tr>
                        <th>Order ID:</th>
                        <td><strong><?php echo htmlspecialchars($receipt['order_id']); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Order Date:</th>
                        <td><?php echo date('F j, Y, g:i a', strtotime($receipt['order_time'])); ?></td>
                    </tr>
                    <tr>
                        <th>Order Status:</th>
                        <td><span class="status-badge status-<?php echo strtolower($receipt['order_status']); ?>">
                            <?php echo htmlspecialchars($receipt['order_status']); ?>
                        </span></td>
                    </tr>
                </table>
            </div>

            <div class="receipt-section">
                <h3>Customer Information</h3>
                <table class="receipt-table">
                    <tr>
                        <th>Name:</th>
                        <td><?php echo htmlspecialchars($receipt['first_name'] . ' ' . $receipt['last_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?php echo htmlspecialchars($receipt['email']); ?></td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td><?php echo htmlspecialchars($receipt['phone']); ?></td>
                    </tr>
                    <tr>
                        <th>Address:</th>
                        <td>
                            <?php 
                            echo htmlspecialchars($receipt['street_address']) . '<br>';
                            echo htmlspecialchars($receipt['suburb']) . ', ' . 
                                 htmlspecialchars($receipt['state']) . ' ' . 
                                 htmlspecialchars($receipt['postcode']);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Preferred Contact:</th>
                        <td><?php echo htmlspecialchars(ucfirst($receipt['preferred_contact'])); ?></td>
                    </tr>
                </table>
            </div>

            <div class="receipt-section">
                <h3>Product/Service Details</h3>
                <table class="receipt-table">
                    <tr>
                        <th>Product/Service:</th>
                        <td><?php echo htmlspecialchars($receipt['product']); ?></td>
                    </tr>
                    <?php if (!empty($receipt['product_options'])): ?>
                    <tr>
                        <th>Options/Features:</th>
                        <td><?php echo nl2br(htmlspecialchars($receipt['product_options'])); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th>Quantity:</th>
                        <td><?php echo htmlspecialchars($receipt['quantity']); ?></td>
                    </tr>
                    <tr>
                        <th>Unit Price:</th>
                        <td>$<?php echo number_format($receipt['order_cost'] / $receipt['quantity'], 2); ?></td>
                    </tr>
                </table>
            </div>

            <div class="receipt-section">
                <h3>Payment Information</h3>
                <table class="receipt-table">
                    <tr>
                        <th>Payment Method:</th>
                        <td><?php echo htmlspecialchars($receipt['cc_type']); ?></td>
                    </tr>
                    <tr>
                        <th>Cardholder Name:</th>
                        <td><?php echo htmlspecialchars($receipt['cc_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Card Number:</th>
                        <td><?php echo $masked_cc; ?></td>
                    </tr>
                    <tr>
                        <th>Total Amount Charged:</th>
                        <td class="total-amount"><strong>$<?php echo number_format($receipt['order_cost'], 2); ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="receipt-actions">
            <p><strong>Important Information:</strong></p>
            <ul>
                <li>A confirmation email will be sent to <?php echo htmlspecialchars($receipt['email']); ?></li>
                <li>Please keep your Order ID for future reference: <strong><?php echo htmlspecialchars($receipt['order_id']); ?></strong></li>
                <li>Your order status is currently: <strong><?php echo htmlspecialchars($receipt['order_status']); ?></strong></li>
                <li>You will be notified when your order status changes</li>
            </ul>
            
            <div class="action-buttons">
                <a href="index.php" class="btn btn-primary">Return to Home</a>
                <a href="product.php" class="btn btn-secondary">Browse More Products</a>
                <button onclick="window.print()" class="btn btn-secondary">Print Receipt</button>
            </div>
        </div>
    </div>
</main>

<footer>
    <?php include 'footer.inc'; ?>
</footer>

<style>
/* Additional styles for receipt page */
.receipt-container {
    max-width: 900px;
    margin: 2rem auto;
    padding: 2rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.success-message {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
    padding: 1.5rem;
    border-radius: 5px;
    margin-bottom: 2rem;
    text-align: center;
}

.success-message h2 {
    margin: 0 0 0.5rem 0;
    color: #155724;
}

.receipt-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 5px;
}

.receipt-section h3 {
    margin-top: 0;
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 0.5rem;
}

.receipt-table {
    width: 100%;
    border-collapse: collapse;
}

.receipt-table th {
    text-align: left;
    padding: 0.75rem;
    width: 35%;
    font-weight: 600;
    color: #555;
}

.receipt-table td {
    padding: 0.75rem;
    color: #333;
}

.receipt-table tr {
    border-bottom: 1px solid #dee2e6;
}

.receipt-table tr:last-child {
    border-bottom: none;
}

.total-amount {
    font-size: 1.3rem;
    color: #28a745;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 3px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.9rem;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-fulfilled {
    background-color: #d1ecf1;
    color: #0c5460;
}

.status-paid {
    background-color: #d4edda;
    color: #155724;
}

.status-archived {
    background-color: #e2e3e5;
    color: #383d41;
}

.receipt-actions {
    margin-top: 2rem;
    padding: 1.5rem;
    background: #e7f3ff;
    border-radius: 5px;
}

.receipt-actions ul {
    margin: 1rem 0;
    padding-left: 1.5rem;
}

.receipt-actions li {
    margin: 0.5rem 0;
}

.action-buttons {
    margin-top: 1.5rem;
    text-align: center;
}

.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    margin: 0.5rem;
    text-decoration: none;
    border-radius: 5px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    font-size: 1rem;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #545b62;
}

@media print {
    .action-buttons, header, footer {
        display: none;
    }
    
    .receipt-container {
        box-shadow: none;
        margin: 0;
        padding: 0;
    }
}
</style>

</body>
</html>
