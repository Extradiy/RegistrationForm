<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'registration_db');

$fullname = $_SESSION['fullname'] ?? 'Guest User';
$username = $_SESSION['username'] ?? 'guest';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Create posts table if it doesn't exist
$createTableSql = "CREATE TABLE IF NOT EXISTS posts (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    content TEXT,
    image_path VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($createTableSql);

$message = "";

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: register.php");
    exit;
}

// Handle new post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_post'])) {
    $content = trim($_POST['content'] ?? '');
    $imagePath = null;

    if ($content === '' && (!isset($_FILES['post_image']) || $_FILES['post_image']['error'] === 4)) {
        $message = "Write something or upload an image.";
    } else {
        if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] !== 4) {
            $file = $_FILES['post_image'];

            if ($file['error'] === 0) {
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                if (!in_array($extension, $allowedExtensions)) {
                    $message = "Only JPG, JPEG, PNG, GIF, and WEBP files are allowed.";
                } else {
                    $uploadDir = __DIR__ . '/uploads/';

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $newFileName = uniqid('post_', true) . '.' . $extension;
                    $targetPath = $uploadDir . $newFileName;

                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $imagePath = 'uploads/' . $newFileName;
                    } else {
                        $message = "Image upload failed.";
                    }
                }
            } else {
                $message = "There was an error uploading the image.";
            }
        }

        if ($message === "") {
            $stmt = $conn->prepare("INSERT INTO posts (username, fullname, content, image_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $fullname, $content, $imagePath);

            if ($stmt->execute()) {
                $message = "Post created successfully.";
            } else {
                $message = "Failed to save post.";
            }

            $stmt->close();
        }
    }
}

// Fetch posts
$posts = [];
$result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>

    <h1>Dashboard</h1>

    <p>Logged in as: <?php echo htmlspecialchars($fullname); ?> (<?php echo htmlspecialchars($username); ?>)</p>

    <p><a href="?logout=true">Logout</a></p>

    <?php if ($message !== ""): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <h2>Create Post</h2>
    <form method="POST" enctype="multipart/form-data">
        <div>
            <label for="content">Post Content:</label><br>
            <textarea name="content" id="content" rows="5" cols="60"></textarea>
        </div>

        <br>

        <div>
            <label for="post_image">Upload Image:</label><br>
            <input type="file" name="post_image" id="post_image" accept="image/*">
        </div>

        <br>

        <button type="submit" name="submit_post">Post</button>
    </form>

    <hr>

    <h2>All Posts</h2>

    <?php if (empty($posts)): ?>
        <p>No posts yet.</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div>
                <p>
                    <strong><?php echo htmlspecialchars($post['fullname']); ?></strong>
                    (@<?php echo htmlspecialchars($post['username']); ?>)
                    - <?php echo htmlspecialchars($post['created_at']); ?>
                </p>

                <?php if (!empty($post['content'])): ?>
                    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                <?php endif; ?>

                <?php if (!empty($post['image_path'])): ?>
                    <p>
                        <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Post Image" style="max-width:300px;">
                    </p>
                <?php endif; ?>

                <hr>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>