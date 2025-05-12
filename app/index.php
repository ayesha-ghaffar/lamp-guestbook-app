
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $message = $_POST["message"];

    $stmt = $conn->prepare("INSERT INTO messages (name, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $message);
    $stmt->execute();
    $stmt->close();
}

$result = $conn->query("SELECT * FROM messages ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Guestbook App</title>
</head>
<body>
    <h1>Leave a Message</h1>
    <form method="POST" action="">
        Name: <input type="text" name="name" required><br><br>
        Message:<br>
        <textarea name="message" required></textarea><br><br>
        <button type="submit">Post</button>
    </form>

    <h2>Messages</h2>
    <?php while($row = $result->fetch_assoc()): ?>
        <p><strong><?= htmlspecialchars($row["name"]) ?></strong>: <?= htmlspecialchars($row["message"]) ?></p>
    <?php endwhile; ?>
</body>
</html>
