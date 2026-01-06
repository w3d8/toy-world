<?php
session_start();

// تحقق من الجلسة
if (!isset($_SESSION['user_id'])) {
    echo "<p>You need to log in to view your profile.</p>";
    exit();
}

// معلومات قاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "toyworld_db";

// الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// تحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$success_message = "";

// تحديث بيانات المستخدم إذا تم إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];

    $update_sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssi", $new_username, $new_email, $user_id);
    if ($stmt->execute()) {
        $success_message = "✅ Profile updated successfully!";
    }
    $stmt->close();
}

// جلب بيانات المستخدم
$user_sql = "SELECT username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();

// حساب عدد الطلبات
$sql = "SELECT COUNT(*) AS order_count FROM orders WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$order_count = 0;
if ($row = $result->fetch_assoc()) {
    $order_count = $row['order_count'];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Profile 👤</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container">
      <a class="navbar-brand" href="index.html">
        <img src="Toy World Logo.webp" alt="logo" style="max-height: 50px;">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="content-background" id="mainNavbar">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="index.html">Home 🏠</a></li>
          <li class="nav-item"><a class="nav-link" href="profile.php">Profile 👤</a></li>
          <li class="nav-item"><a class="nav-link" href="menu.html">Menu 🎮</a></li>
          <li class="nav-item"><a class="nav-link" href="cart.html">Cart 🛒</a></li>
          <li class="nav-item"><a class="nav-link" href="FAQ.html">FAQ 🔍</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.html">Contact Us 📞</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.html">Logout🔑</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container mt-5">
    <h2 class="text-center">👤 User Profile</h2>

    <?php if (!empty($success_message)): ?>
      <div class="alert alert-success text-center"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <div class="card mt-4">
      <div class="card-body">
        <h5 class="card-title">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h5>
        <p class="card-text">📧 Email: <?php echo htmlspecialchars($user['email']); ?></p>
        <p class="card-text">🛒 Total Orders: <strong><?php echo $order_count; ?></strong></p>
      </div>
    </div>

    <div class="card mt-4">
      <div class="card-body">
        <h5 class="card-title">Edit Your Information</h5>
        <form method="POST">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
          </div>
          <button type="submit" name="update_info" class="btn btn-primary">Update Information</button>
        </form>
      </div>
    </div>
  </div>

</body>
</html>
