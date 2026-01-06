<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli("localhost", "root", "", "toyworld");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $price = $_POST['price'];

        // استخدم Prepared Statements لتجنب SQL Injection
        $stmt = $conn->prepare("INSERT INTO toys (name, price) VALUES (?, ?)");
        $stmt->bind_param("sd", $name, $price); // "s" للـ String و "d" للـ Double
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        // استخدم Prepared Statements لتجنب SQL Injection
        $stmt = $conn->prepare("DELETE FROM toys WHERE id=?");
        $stmt->bind_param("i", $id); // "i" للـ Integer
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $price = $_POST['price'];

        // استخدم Prepared Statements لتجنب SQL Injection
        $stmt = $conn->prepare("UPDATE toys SET name=?, price=? WHERE id=?");
        $stmt->bind_param("sdi", $name, $price, $id); // "s" للـ String، "d" للـ Double، "i" للـ Integer
        $stmt->execute();
        $stmt->close();
    }
}

$result = $conn->query("SELECT * FROM toys");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Toy World</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Admin Panel</h1>
    <form method="post">
        <input type="text" name="name" placeholder="Toy Name" required>
        <input type="number" name="price" placeholder="Price" required>
        <button name="add">Add Toy</button>
    </form>
    <h2>Existing Toys</h2>
    <table border="1">
        <tr><th>ID</th><th>Name</th><th>Price</th><th>Actions</th></tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <form method="post">
                <td><input type="hidden" name="id" value="<?= $row['id'] ?>"><?= $row['id'] ?></td>
                <td><input type="text" name="name" value="<?= $row['name'] ?>"></td>
                <td><input type="number" name="price" value="<?= $row['price'] ?>"></td>
                <td>
                    <button name="edit">Edit</button>
                    <button name="delete">Delete</button>
                </td>
            </form>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

<?php
$conn->close(); // اغلاق الاتصال بقاعدة البيانات بعد الانتهاء
?>
