<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$id = (int) $_GET["id"];

$stmt = $conn->prepare(
    "SELECT id, title, content, created_at 
     FROM posts 
     WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

$post = null;
if ($result && $result->num_rows === 1) {
    $post = $result->fetch_assoc();
}

$stmt->close();

if (!function_exists('formatPostContent')) {
    function formatPostContent($text) {
        $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        
        // BBCode style [b], [i], [u]
        $escaped = preg_replace('/\[b\](.*?)\[\/b\]/is', '<strong>$1</strong>', $escaped);
        $escaped = preg_replace('/\[i\](.*?)\[\/i\]/is', '<em>$1</em>', $escaped);
        $escaped = preg_replace('/\[u\](.*?)\[\/u\]/is', '<u>$1</u>', $escaped);
        
        // Markdown style **bold**, *italic*, __underline__
        $escaped = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $escaped);
        $escaped = preg_replace('/(?<!\*)\*(?!\*)(.*?)(?<!\*)\*(?!\*)/s', '<em>$1</em>', $escaped);
        $escaped = preg_replace('/__(.*?)__/s', '<u>$1</u>', $escaped);
        
        return nl2br($escaped);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $post ? htmlspecialchars($post["title"]) : "Post Not Found"; ?> - ApexPlanet Task 3</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="container">

    <div class="header-bar">
        <div>
            <h1>Student Management System</h1>
            <p class="header-user">
                Logged in as <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong>
            </p>
        </div>
        <div class="nav-actions">
            <a href="index.php" class="btn btn-secondary">← Back to Posts</a>
            <a href="create.php" class="btn btn-primary">+ Create Post</a>
            <a href="../auth/logout.php" class="btn btn-secondary">Logout</a>
        </div>
    </div>

    <?php if ($post): ?>

        <div class="single-post-container">
            <div class="single-post-header">
                <div style="margin-bottom: 12px;">
                    <a href="index.php" style="font-size: 13px; font-weight: 600;">← All Posts</a>
                    <span style="color: var(--text-muted); margin: 0 8px;">/</span>
                    <span style="color: var(--text-muted); font-size: 13px;">Post #<?php echo $post["id"]; ?></span>
                </div>

                <h2 class="single-post-title">
                    <?php echo htmlspecialchars($post["title"]); ?>
                </h2>

                <div class="single-post-meta">
                    <span class="meta-pill">
                        🕒 Published: <?php echo date("M d, Y - h:i A", strtotime($post["created_at"])); ?>
                    </span>
                    <span class="meta-pill">
                        📌 Post ID: #<?php echo $post["id"]; ?>
                    </span>
                </div>
            </div>

            <div class="single-post-body">
                <?php echo formatPostContent($post["content"]); ?>
            </div>

            <div class="single-post-footer">
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <a href="index.php" class="btn btn-secondary">← Back to All Posts</a>
                    <button type="button" class="btn btn-secondary" onclick="copyPostLink()">📋 Share / Copy Link</button>
                </div>

                <div class="article-actions">
                    <a href="edit.php?id=<?php echo $post["id"]; ?>" class="btn btn-secondary btn-sm">
                        ✏️ Edit Post
                    </a>
                    <a href="delete.php?id=<?php echo $post["id"]; ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Are you sure you want to delete this post?');">
                        🗑️ Delete Post
                    </a>
                </div>
            </div>
        </div>

    <?php else: ?>

        <div class="empty-state">
            <div class="alert alert-error" style="display: inline-flex; margin-bottom: 20px;">
                ⚠️ Post not found or it may have been deleted.
            </div>
            <p>
                <a href="index.php" class="btn btn-primary">← Return to All Posts</a>
            </p>
        </div>

    <?php endif; ?>

</div>

<div id="copyToast" class="copy-toast">Link copied to clipboard!</div>

<script>
function copyPostLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        const toast = document.getElementById('copyToast');
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 2200);
    }).catch(function() {
        alert('Post URL: ' + window.location.href);
    });
}
</script>

</body>
</html>

