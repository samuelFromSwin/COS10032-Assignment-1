<?php
require_once("settings.php");

$conn = mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) {
    die("DB connection failed");
}

$message = "";

/* UPDATE STATUS */
if (isset($_POST["update_status"])) {
    $order_id = (int)($_POST["order_id"] ?? 0);
    $new_status = strtoupper(trim($_POST["new_status"] ?? ""));

    $allowed = ["PENDING", "FULFILLED", "PAID", "ARCHIVED"];

    if ($order_id > 0 && in_array($new_status, $allowed)) {
        $sql = "UPDATE orders SET order_status='$new_status' WHERE order_id=$order_id";
        if (mysqli_query($conn, $sql)) {
            $message = "Order status updated";
        } else {
            $message = "Update failed";
        }
    } else {
        $message = "Invalid status";
    }
}

/* DELETE PENDING ONLY */
if (isset($_POST["delete_order"])) {
    $order_id = (int)($_POST["order_id"] ?? 0);

    if ($order_id > 0) {
        $sql = "DELETE FROM orders WHERE order_id=$order_id AND order_status='PENDING'";
        if (mysqli_query($conn, $sql)) {
            if (mysqli_affected_rows($conn) > 0) {
                $message = "Pending order deleted";
            } else {
                $message = "Only pending orders can be deleted";
            }
        } else {
            $message = "Delete failed";
        }
    }
}

/* FILTERS */
$filter = $_GET["filter"] ?? "all";
$customer_name = trim($_GET["customer_name"] ?? "");
$product = trim($_GET["product"] ?? "");

/* BASE QUERY */
$sql = "SELECT order_id, order_time, product, order_cost, first_name, last_name, order_status
        FROM orders
        WHERE 1=1";

if ($filter === "customer" && $customer_name !== "") {
    $safe = mysqli_real_escape_string($conn, $customer_name);
    $sql .= " AND CONCAT(first_name,' ',last_name) LIKE '%$safe%'";
}

if ($filter === "product" && $product !== "") {
    $safe = mysqli_real_escape_string($conn, $product);
    $sql .= " AND product LIKE '%$safe%'";
}

if ($filter === "pending") {
    $sql .= " AND order_status='PENDING'";
}

if ($filter === "cost") {
    $sql .= " ORDER BY order_cost DESC";
} else {
    $sql .= " ORDER BY order_time DESC";
}

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manager Orders</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="site-header">
    <div class="logo">EZ-Accounting</div>
    <?php include "menu.inc"; ?>
</header>

<main class="content">
    <h1>Manager Orders</h1>

    <?php
    if ($message != "") {
        echo "<p>" . htmlspecialchars($message) . "</p>";
    }
    ?>

    <h2>Order Queries</h2>

    <form method="get" action="manager.php">
        <label>Choose report:</label>
        <select name="filter">
            <option value="all" <?php if ($filter==="all") echo "selected"; ?>>All orders</option>
            <option value="customer" <?php if ($filter==="customer") echo "selected"; ?>>Orders by customer name</option>
            <option value="product" <?php if ($filter==="product") echo "selected"; ?>>Orders by product</option>
            <option value="pending" <?php if ($filter==="pending") echo "selected"; ?>>Pending orders</option>
            <option value="cost" <?php if ($filter==="cost") echo "selected"; ?>>Orders sorted by total cost</option>
        </select>

        <p>
            <label>Customer name:</label>
            <input type="text" name="customer_name" value="<?php echo htmlspecialchars($customer_name); ?>">
        </p>

        <p>
            <label>Product:</label>
            <input type="text" name="product" value="<?php echo htmlspecialchars($product); ?>">
        </p>

        <button type="submit">Run</button>
    </form>

    <h2>Results</h2>

    <table>
        <tr>
            <th>Order #</th>
            <th>Order date</th>
            <th>Product</th>
            <th>Total cost</th>
            <th>Customer</th>
            <th>Status</th>
            <th>Update status</th>
            <th>Cancel</th>
        </tr>

        <?php
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {

                $order_id = (int)$row["order_id"];
                $status = strtoupper($row["order_status"]);

                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["order_id"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["order_time"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["product"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["order_cost"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["first_name"] . " " . $row["last_name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["order_status"]) . "</td>";

                echo "<td>
                        <form method='post' action='manager.php'>
                            <input type='hidden' name='order_id' value='$order_id'>
                            <select name='new_status'>
                                <option value='PENDING'" . ($status==="PENDING" ? " selected" : "") . ">PENDING</option>
                                <option value='FULFILLED'" . ($status==="FULFILLED" ? " selected" : "") . ">FULFILLED</option>
                                <option value='PAID'" . ($status==="PAID" ? " selected" : "") . ">PAID</option>
                                <option value='ARCHIVED'" . ($status==="ARCHIVED" ? " selected" : "") . ">ARCHIVED</option>
                            </select>
                            <button type='submit' name='update_status'>Update</button>
                        </form>
                      </td>";

                if ($status === "PENDING") {
                    echo "<td>
                            <form method='post' action='manager.php'>
                                <input type='hidden' name='order_id' value='$order_id'>
                                <button type='submit' name='delete_order'>Delete</button>
                            </form>
                          </td>";
                } else {
                    echo "<td>Not allowed</td>";
                }

                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No results found</td></tr>";
        }
        ?>
    </table>

    <p>No credit card details are displayed on this page.</p>
</main>

</body>
</html>
<?php
mysqli_close($conn);
?>
