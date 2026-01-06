<?php
session_start();

// تأكد أن المستخدم مسجل دخوله
if (!isset($_SESSION['user_id'])) {
    echo "<p>You need to log in first.</p>";
    exit();
}

$user_id = $_SESSION['user_id'];

// معلومات قاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "toyworld_db";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// تحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 🛒 استبدل هذا بإجمالي المشتريات الفعلي من الواجهة الأمامية أو الجلسة
$total_price = 99.99;
$toy_id = 1; // toy_id مثال، استبدله بـ toy_id الفعلي
$quantity = 1; // الكمية، استبدلها بالكمية الفعلية

// تحقق من إذا كان المستخدم موجود في قاعدة البيانات
$user_check = $conn->prepare("SELECT id FROM users WHERE id = ?");
$user_check->bind_param("i", $user_id);
$user_check->execute();
$user_result = $user_check->get_result();

if ($user_result->num_rows === 0) {
    die("User does not exist. Please register or login again.");
}

// تحقق من إذا كان النموذج تم إرساله
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['card_number'])) {
    // تفاصيل الدفع (قم بتكامل مع بوابة الدفع هنا)
    $card_number = $_POST['card_number'];
    $expiry_date = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];

    // محاكاة التحقق من الدفع (يجب دمج بوابة دفع حقيقية هنا)
    $payment_verified = true; // ضعها false إذا فشل الدفع

    // إذا تم الدفع بنجاح، قم بإدخال الطلب في قاعدة البيانات
    if ($payment_verified) {
        $sql = "INSERT INTO orders (user_id, toy_id, quantity, total_price, order_date) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiid", $user_id, $toy_id, $quantity, $total_price);

        $order_created = false;
        $order_id = 0;

        if ($stmt->execute()) {
            $order_id = $conn->insert_id;
            $order_created = true;
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <?php if (isset($order_created) && $order_created): ?>
        <div class="alert alert-success text-center">
            ✅ Your order has been placed successfully!<br>
            🧾 Order Number: <strong><?php echo $order_id; ?></strong>
        </div>
        <div class="text-center mt-4">
            <a href="home.html" class="btn btn-primary">🔙 Back to Home</a>
        </div>
        <script>
            // Clear cart from localStorage (if used)
            localStorage.removeItem("cart");
        </script>
    <?php else: ?>
        <div class="alert alert-info text-center">
            Please enter your payment details to complete the checkout process.
        </div>
        
        <!-- Payment Form -->
        <form method="POST" action="checkout.php">
            <div class="mb-3">
                <label for="card_number" class="form-label">Card Number</label>
                <input type="text" class="form-control" id="card_number" name="card_number" required>
            </div>
            <div class="mb-3">
                <label for="expiry_date" class="form-label">Expiry Date</label>
                <input type="text" class="form-control" id="expiry_date" name="expiry_date" required placeholder="MM/YY">
            </div>
            <div class="mb-3">
                <label for="cvv" class="form-label">CVV</label>
                <input type="text" class="form-control" id="cvv" name="cvv" required>
            </div>
            <button type="submit" class="btn btn-success">Complete Checkout</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
