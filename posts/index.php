<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";

if (!empty($search)) {
    $searchTerm = "%" . $search . "%";
    $stmt = $conn->prepare(
        "SELECT id, title, content, created_at 
         FROM posts 
         WHERE title LIKE ? OR content LIKE ? 
         ORDER BY created_at DESC"
    );
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query(
        "SELECT id, title, content, created_at 
         FROM posts 
         ORDER BY created_at DESC"
    );
}

// Store results into an array
$posts = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}
$totalCount = count($posts);

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

function highlightKeyword($text, $keyword) {
    if (empty($keyword)) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
    $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    $pattern = '/' . preg_quote(htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'), '/') . '/i';
    return preg_replace($pattern, '<mark class="search-highlight">$0</mark>', $escaped);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts & Search - ApexPlanet Task 3</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="container">

    <div class="header-bar">
        <div>
            <h1>Student Management System</h1>
            <p class="header-user">
                Welcome, <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong>!
            </p>
        </div>
        <div class="nav-actions">
            <a href="create.php" class="btn btn-primary">+ Create Post</a>
            <a href="../auth/logout.php" class="btn btn-secondary">Logout</a>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-section">
        <form method="GET" action="index.php" class="search-form" id="searchForm">
            <div class="search-input-wrapper">
                <span class="search-icon">🔍</span>
                <input 
                    type="text" 
                    id="searchInput" 
                    name="search" 
                    class="search-input" 
                    placeholder="Search posts by title or keyword..." 
                    value="<?php echo htmlspecialchars($search); ?>" 
                    autocomplete="off"
                >
                <button type="button" id="searchClearBtn" class="search-clear-btn" title="Clear search">✕</button>
                <div id="searchDropdown" class="search-dropdown"></div>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
            <?php if (!empty($search)): ?>
                <a href="index.php" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </form>

        <div class="search-info-bar">
            <div id="searchCounter">
                <?php if (!empty($search)): ?>
                    Showing results for <span class="search-badge">🔍 "<?php echo htmlspecialchars($search); ?>"</span> &mdash; <strong><?php echo $totalCount; ?></strong> <?php echo $totalCount === 1 ? 'post' : 'posts'; ?> found
                    <a href="index.php" class="search-badge-clear" title="View all posts">(View all)</a>
                <?php else: ?>
                    <span>Showing <strong><?php echo $totalCount; ?></strong> <?php echo $totalCount === 1 ? 'post' : 'posts'; ?></span>
                <?php endif; ?>
            </div>
            <span class="toolbar-hint" style="margin-left: 0;">Type to filter instantly or click any post title/view button to open it</span>
        </div>
    </div>

    <h2>My Posts</h2>

    <div id="postsList">
        <?php if ($totalCount > 0): ?>

            <?php foreach ($posts as $post): ?>

                <article class="post-card" data-post-id="<?php echo $post["id"]; ?>" data-title="<?php echo htmlspecialchars(strtolower($post["title"])); ?>" data-content="<?php echo htmlspecialchars(strtolower(strip_tags($post["content"]))); ?>">
                    <div class="article-header">
                        <h3 class="article-title">
                            <a href="view.php?id=<?php echo $post["id"]; ?>" class="post-title-link" title="Click to view this post">
                                <?php echo !empty($search) ? highlightKeyword($post["title"], $search) : htmlspecialchars($post["title"]); ?>
                            </a>
                        </h3>
                    </div>

                    <div class="article-body">
                        <?php echo formatPostContent($post["content"]); ?>
                    </div>

                    <div class="article-footer">
                        <small class="meta">
                            🕒 <em>Created: <?php echo date("M d, Y - h:i A", strtotime($post["created_at"])); ?></em>
                        </small>

                        <div class="article-actions">
                            <a href="view.php?id=<?php echo $post["id"]; ?>" class="btn btn-view btn-sm" title="Open and read this post">
                                👁️ View Post
                            </a>
                            <a href="edit.php?id=<?php echo $post["id"]; ?>" class="btn btn-secondary btn-sm">
                                ✏️ Edit
                            </a>
                            <a href="delete.php?id=<?php echo $post["id"]; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this post?');">
                                🗑️ Delete
                            </a>
                        </div>
                    </div>
                </article>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="empty-state">
                <?php if (!empty($search)): ?>
                    <p><em>No posts found matching "<strong><?php echo htmlspecialchars($search); ?></strong>".</em></p>
                    <a href="index.php" class="btn btn-secondary">Clear Search & View All</a>
                <?php else: ?>
                    <p><em>No posts available yet. Create your first post!</em></p>
                    <a href="create.php" class="btn btn-primary">+ Create New Post</a>
                <?php endif; ?>
            </div>

        <?php endif; ?>
    </div>

    <!-- Container for dynamic "no results found" when live filtering -->
    <div id="noLiveResults" class="empty-state" style="display: none;">
        <p><em>No posts match your live search keyword.</em></p>
        <button type="button" class="btn btn-secondary" onclick="resetLiveSearch()">Reset Search Filter</button>
    </div>

</div>

<script>
// JSON representation of posts for client-side search autocomplete
const allPosts = <?php echo json_encode(array_map(function($p) {
    return [
        'id' => $p['id'],
        'title' => $p['title'],
        'date' => date("M d, Y", strtotime($p['created_at']))
    ];
}, $posts)); ?>;

const searchInput = document.getElementById('searchInput');
const searchClearBtn = document.getElementById('searchClearBtn');
const searchDropdown = document.getElementById('searchDropdown');
const postCards = document.querySelectorAll('.post-card');
const noLiveResults = document.getElementById('noLiveResults');
const searchCounter = document.getElementById('searchCounter');
const totalPostsCount = postCards.length;

function updateClearButton() {
    if (searchInput.value.trim().length > 0) {
        searchClearBtn.style.display = 'inline-flex';
    } else {
        searchClearBtn.style.display = 'none';
    }
}

// Initial clear button state
updateClearButton();

// Live client-side filtering as user types
searchInput.addEventListener('input', function() {
    const query = this.value.trim().toLowerCase();
    updateClearButton();
    
    let visibleCount = 0;
    postCards.forEach(card => {
        const title = card.getAttribute('data-title') || '';
        const content = card.getAttribute('data-content') || '';
        if (query === '' || title.includes(query) || content.includes(query)) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    // Toggle live empty state
    if (totalPostsCount > 0) {
        if (visibleCount === 0) {
            noLiveResults.style.display = 'block';
        } else {
            noLiveResults.style.display = 'none';
        }
    }

    // Update counter display
    if (query !== '') {
        searchCounter.innerHTML = `Showing <strong>${visibleCount}</strong> of <strong>${totalPostsCount}</strong> matching posts`;
    }

    // Render autocomplete dropdown suggestions
    renderDropdown(query);
});

// Render suggestions dropdown
function renderDropdown(query) {
    if (!query || query.length < 1) {
        searchDropdown.style.display = 'none';
        searchDropdown.innerHTML = '';
        return;
    }

    const matches = allPosts.filter(p => p.title.toLowerCase().includes(query)).slice(0, 5);
    
    if (matches.length === 0) {
        searchDropdown.style.display = 'none';
        searchDropdown.innerHTML = '';
        return;
    }

    searchDropdown.innerHTML = matches.map(p => `
        <a href="view.php?id=${p.id}" class="search-dropdown-item">
            <div class="search-dropdown-title">
                <span>📄 ${escapeHtml(p.title)}</span>
                <span class="search-dropdown-meta">👁️ View</span>
            </div>
            <div class="search-dropdown-meta">🕒 ${p.date}</div>
        </a>
    `).join('');
    
    searchDropdown.style.display = 'block';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Hide dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
        searchDropdown.style.display = 'none';
    }
});

// Clear button click handler
searchClearBtn.addEventListener('click', function() {
    searchInput.value = '';
    updateClearButton();
    searchDropdown.style.display = 'none';
    
    // If a server-side search was executed, reload index.php to clear GET params
    if (window.location.search.includes('search=')) {
        window.location.href = 'index.php';
        return;
    }
    
    // Reset cards visibility
    postCards.forEach(card => card.style.display = '');
    noLiveResults.style.display = 'none';
    searchCounter.innerHTML = `Showing <strong>${totalPostsCount}</strong> ${totalPostsCount === 1 ? 'post' : 'posts'}`;
    searchInput.focus();
});

function resetLiveSearch() {
    searchClearBtn.click();
}
</script>

</body>
</html>
