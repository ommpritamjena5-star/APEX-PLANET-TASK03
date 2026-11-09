<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);

    if (empty($title) || empty($content)) {
        $message = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO posts (title, content) VALUES (?, ?)"
        );

        $stmt->bind_param("ss", $title, $content);

        if ($stmt->execute()) {
            $newId = $stmt->insert_id;
            $stmt->close();
            header("Location: view.php?id=" . $newId);
            exit();
        } else {
            $message = "Failed to create post. Please try again.";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post - ApexPlanet Task 3</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="container">

    <div class="header-bar">
        <div>
            <h1>Create New Post</h1>
            <p class="header-user">Share an update with rich text formatting</p>
        </div>
        <div class="nav-actions">
            <a href="index.php" class="btn btn-secondary">← Back to Posts</a>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-error">
            ⚠️ <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label for="title">Title</label>
            <input
                type="text"
                id="title"
                name="title"
                placeholder="Enter post title..."
                required
            >
        </div>

        <div class="form-group">
            <label for="content-input">Content</label>
            <div class="formatting-toolbar">
                <button type="button" class="toolbar-btn bold" onclick="formatText('b')" title="Bold (Ctrl+B)">B</button>
                <button type="button" class="toolbar-btn italic" onclick="formatText('i')" title="Italic (Ctrl+I)">I</button>
                <button type="button" class="toolbar-btn underline" onclick="formatText('u')" title="Underline (Ctrl+U)">U</button>
                <span class="toolbar-hint">Tip: Select text and click <strong>B</strong>, <em>I</em>, or <u>U</u></span>
            </div>
            <textarea
                id="content-input"
                name="content"
                rows="8"
                placeholder="Write your post content here... You can use bold, italic, or underline tools above."
                required
            ></textarea>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 10px;">
            <button type="submit" class="btn btn-primary">Publish Post</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>

    </form>

</div>

<script>
function formatText(tag) {
    const textarea = document.getElementById('content-input');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selected = textarea.value.substring(start, end) || 'formatted text';
    const replacement = '[' + tag + ']' + selected + '[/' + tag + ']';
    
    textarea.setRangeText(replacement, start, end, 'end');
    textarea.focus();
}

// Keyboard shortcuts for Ctrl+B, Ctrl+I, Ctrl+U
document.getElementById('content-input').addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey)) {
        if (e.key === 'b' || e.key === 'B') {
            e.preventDefault();
            formatText('b');
        } else if (e.key === 'i' || e.key === 'I') {
            e.preventDefault();
            formatText('i');
        } else if (e.key === 'u' || e.key === 'U') {
            e.preventDefault();
            formatText('u');
        }
    }
});
</script>

</body>
</html>