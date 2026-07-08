<?php
/**
 * Admin Panel Blog Posts CRUD with Gallery Support & Social Media Integration
 */
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/layout.php';

checkAdminAuth();

$error = '';
$success = '';

// 1. HANDLE GALLERY IMAGE DELETION
if (isset($_GET['delete_gallery']) && isset($_GET['post_id'])) {
    $galleryId = trim($_GET['delete_gallery']);
    $postId = trim($_GET['post_id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM blog_gallery WHERE id = ?");
        $stmt->execute([$galleryId]);
        $success = 'Gallery image successfully deleted.';
        header("Location: blog.php?edit=" . urlencode($postId));
        exit();
    } catch (Exception $e) {
        $error = 'Failed to delete gallery image.';
    }
}

// 2. HANDLE BLOG POST DELETION
if (isset($_GET['delete'])) {
    $id = trim($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM blog_gallery WHERE blog_post_id = ?");
        $stmt->execute([$id]);
        
        // Delete social posts associated with this blog
        $stmt = $pdo->prepare("DELETE FROM social_posts WHERE blog_post_id = ?");
        $stmt->execute([$id]);
        
        $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Blog post successfully deleted.';
        header("Location: blog.php");
        exit();
    } catch (Exception $e) {
        $error = 'Failed to delete blog post.';
    }
}

// 3. HANDLE SOCIAL MEDIA POST ADDITION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_social_post'])) {
    $blogId = trim($_POST['blog_id']);
    $socialUrl = trim($_POST['social_url']);
    $platform = trim($_POST['platform']);
    
    if (empty($socialUrl)) {
        $error = 'Please enter a social media post URL.';
    } else {
        // Extract embed code based on platform
        $embedCode = '';
        $thumbnail = '';
        $author = '';
        $content = '';
        
        if ($platform === 'twitter') {
            // Extract tweet ID from URL
            preg_match('/twitter\.com\/\w+\/status\/(\d+)/', $socialUrl, $matches);
            if (isset($matches[1])) {
                $tweetId = $matches[1];
                $embedCode = '<blockquote class="twitter-tweet" data-align="center"><a href="' . htmlspecialchars($socialUrl) . '"></a></blockquote>';
            } else {
                $error = 'Invalid Twitter URL format.';
            }
        } 
        elseif ($platform === 'instagram') {
            // Extract Instagram post ID
            preg_match('/instagram\.com\/p\/([A-Za-z0-9_-]+)/', $socialUrl, $matches);
            if (isset($matches[1])) {
                $embedCode = '<blockquote class="instagram-media" data-instgrm-permalink="' . htmlspecialchars($socialUrl) . '" data-instgrm-version="14" style="background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:540px; min-width:326px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);"></blockquote>';
            } else {
                $error = 'Invalid Instagram URL format.';
            }
        }
        elseif ($platform === 'facebook') {
            // Facebook requires the page URL, not just the post URL
            // Try to extract post ID
            if (strpos($socialUrl, 'facebook.com') !== false) {
                // Store the URL and let the frontend handle it via FB SDK
                $embedCode = '<div class="fb-post" data-href="' . htmlspecialchars($socialUrl) . '" data-width="500" data-show-text="true"></div>';
            } else {
                $error = 'Invalid Facebook URL format.';
            }
        }
        elseif ($platform === 'youtube') {
            // Extract video ID
            preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_-]+)/', $socialUrl, $matches);
            if (isset($matches[1])) {
                $videoId = $matches[1];
                $embedCode = '<div class="youtube-embed" style="position:relative; padding-bottom:56.25%; height:0; overflow:hidden;"><iframe style="position:absolute; top:0; left:0; width:100%; height:100%;" src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allowfullscreen></iframe></div>';
            } else {
                $error = 'Invalid YouTube URL format.';
            }
        }
        elseif ($platform === 'tiktok') {
            // Extract TikTok video ID
            preg_match('/tiktok\.com\/@[\w]+\/video\/(\d+)/', $socialUrl, $matches);
            if (isset($matches[1])) {
                $videoId = $matches[1];
                $embedCode = '<blockquote class="tiktok-embed" cite="' . htmlspecialchars($socialUrl) . '" data-video-id="' . $videoId . '" style="max-width: 605px;min-width: 325px;" ><section></section></blockquote>';
            } else {
                $error = 'Invalid TikTok URL format.';
            }
        }
        
        if (empty($error)) {
            try {
                $socialId = 's-' . time() . '-' . rand(100, 999);
                $stmt = $pdo->prepare("INSERT INTO social_posts (id, blog_post_id, platform, url, embed_code, created_at) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$socialId, $blogId, $platform, $socialUrl, $embedCode, date('Y-m-d H:i:s')]);
                $success = 'Social media post added successfully.';
                header("Location: blog.php?edit=" . urlencode($blogId));
                exit();
            } catch (Exception $e) {
                $error = 'Failed to add social post: ' . $e->getMessage();
            }
        }
    }
}

// 4. HANDLE SOCIAL MEDIA POST DELETION
if (isset($_GET['delete_social'])) {
    $socialId = trim($_GET['delete_social']);
    $postId = trim($_GET['post_id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM social_posts WHERE id = ?");
        $stmt->execute([$socialId]);
        $success = 'Social media post deleted successfully.';
        header("Location: blog.php?edit=" . urlencode($postId));
        exit();
    } catch (Exception $e) {
        $error = 'Failed to delete social post.';
    }
}

// 5. HANDLE GALLERY IMAGE ADDITION (File Upload)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_gallery'])) {
    $blogId = trim($_POST['blog_id']);
    $caption = trim($_POST['gallery_caption']);
    
    if (isset($_FILES['gallery_image']) && $_FILES['gallery_image']['error'] === UPLOAD_ERR_OK) {
        require_once __DIR__ . '/upload_helper.php';
        $uploadRes = handleAdminUpload('gallery_image', 'blog/gallery');
        if ($uploadRes['success']) {
            try {
                $galleryId = 'g-' . time() . '-' . rand(100, 999);
                $stmt = $pdo->prepare("INSERT INTO blog_gallery (id, blog_post_id, image_url, caption, display_order, created_at) VALUES (?, ?, ?, ?, 0, ?)");
                $stmt->execute([$galleryId, $blogId, $uploadRes['url'], $caption, date('Y-m-d H:i:s')]);
                $success = 'Gallery image successfully added.';
                header("Location: blog.php?edit=" . urlencode($blogId));
                exit();
            } catch (Exception $e) {
                $error = 'Failed to add gallery image: ' . $e->getMessage();
            }
        } else {
            $error = $uploadRes['error'];
        }
    } else {
        $error = 'Please select an image to upload.';
    }
}

// 6. HANDLE GALLERY IMAGE ADDITION (URL)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_gallery_url'])) {
    $blogId = trim($_POST['blog_id']);
    $imageUrl = trim($_POST['gallery_image_url']);
    $caption = trim($_POST['gallery_caption']);
    
    if (empty($imageUrl)) {
        $error = 'Please provide an image URL.';
    } else {
        if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            $error = 'Please provide a valid image URL.';
        } else {
            try {
                $galleryId = 'g-' . time() . '-' . rand(100, 999);
                $stmt = $pdo->prepare("INSERT INTO blog_gallery (id, blog_post_id, image_url, caption, display_order, created_at) VALUES (?, ?, ?, ?, 0, ?)");
                $stmt->execute([$galleryId, $blogId, $imageUrl, $caption, date('Y-m-d H:i:s')]);
                $success = 'Gallery image successfully added from URL.';
                header("Location: blog.php?edit=" . urlencode($blogId));
                exit();
            } catch (Exception $e) {
                $error = 'Failed to add gallery image: ' . $e->getMessage();
            }
        }
    }
}

// 7. HANDLE CAPTION UPDATE (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_caption'])) {
    $imageId = trim($_POST['image_id']);
    $caption = trim($_POST['caption']);
    
    try {
        $stmt = $pdo->prepare("UPDATE blog_gallery SET caption = ? WHERE id = ?");
        $stmt->execute([$caption, $imageId]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit();
}

// 8. HANDLE SAVE SUBMISSION (ADD / EDIT)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_blog'])) {
    $id = isset($_POST['id']) ? trim($_POST['id']) : '';
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $author = isset($_POST['author']) ? trim($_POST['author']) : '';
    $excerpt = isset($_POST['excerpt']) ? trim($_POST['excerpt']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $image = isset($_POST['image']) ? trim($_POST['image']) : '';
    $published = isset($_POST['published']) ? 1 : 0;

    // Handle file upload
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        require_once __DIR__ . '/upload_helper.php';
        $uploadRes = handleAdminUpload('image_file', 'blog');
        if ($uploadRes['success']) {
            $image = $uploadRes['url'];
        } else {
            $error = $uploadRes['error'];
        }
    }

    if (empty($title)) {
        $error = 'Title is required.';
    }

    if (empty($error)) {
        try {
            if (empty($id)) {
                // INSERT NEW
                $newId = 'b-' . time() . '-' . rand(100, 999);
                $stmt = $pdo->prepare("INSERT INTO blog_posts (id, title, excerpt, content, author, image, published, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$newId, $title, $excerpt, $content, $author, $image, $published, date('Y-m-d H:i:s')]);
                $success = 'New blog post successfully created.';
                header("Location: blog.php");
                exit();
            } else {
                // UPDATE EXISTING
                $stmt = $pdo->prepare("UPDATE blog_posts SET title=?, excerpt=?, content=?, author=?, image=?, published=? WHERE id=?");
                $stmt->execute([$title, $excerpt, $content, $author, $image, $published, $id]);
                $success = 'Blog post details updated.';
                header("Location: blog.php?edit=" . urlencode($id));
                exit();
            }
        } catch (Exception $e) {
            $error = 'Failed to save blog post: ' . $e->getMessage();
        }
    }
}

// 9. FETCH EDIT DETAILS IF REQUESTED
$editPost = null;
$galleryImages = [];
$socialPosts = [];
if (isset($_GET['edit'])) {
    $editId = trim($_GET['edit']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
        $stmt->execute([$editId]);
        $editPost = $stmt->fetch();
        
        // Fetch gallery images for this post
        if ($editPost) {
            $stmt = $pdo->prepare("SELECT * FROM blog_gallery WHERE blog_post_id = ? ORDER BY display_order ASC, created_at ASC");
            $stmt->execute([$editId]);
            $galleryImages = $stmt->fetchAll();
            
            // Fetch social posts for this blog
            $stmt = $pdo->prepare("SELECT * FROM social_posts WHERE blog_post_id = ? ORDER BY created_at DESC");
            $stmt->execute([$editId]);
            $socialPosts = $stmt->fetchAll();
        }
    } catch (Exception $e) {
        $error = 'Failed to load post data.';
    }
}

// 10. FETCH ALL BLOG POSTS FOR LISTING
$posts = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM blog_posts ORDER BY created_at DESC, title ASC");
    $stmt->execute();
    $posts = $stmt->fetchAll();
} catch (Exception $e) {
    // Silent fail
}

// Check if social_posts table exists, create if not
try {
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'social_posts'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $createTable = "
        CREATE TABLE `social_posts` (
            `id` varchar(50) NOT NULL,
            `blog_post_id` varchar(50) NOT NULL,
            `platform` varchar(50) NOT NULL,
            `url` text NOT NULL,
            `embed_code` text NOT NULL,
            `created_at` datetime NOT NULL,
            PRIMARY KEY (`id`),
            KEY `blog_post_id` (`blog_post_id`),
            CONSTRAINT `social_posts_ibfk_1` FOREIGN KEY (`blog_post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $pdo->exec($createTable);
    }
} catch (Exception $e) {}

// Render the header
renderAdminHeader('blog');
?>

<style>
.social-card {
    background: #fff;
    border: 1px solid #e9d5ff;
    border-radius: 0.75rem;
    padding: 1rem;
    margin-bottom: 1rem;
    transition: all 0.2s ease;
}
.social-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(109, 40, 217, 0.1);
    border-color: #c4b5fd;
}
.social-preview {
    max-height: 400px;
    overflow-y: auto;
    background: #faf5ff;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-top: 0.5rem;
    display: flex;
    justify-content: center;
}
.platform-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 2rem;
    font-size: 0.7rem;
    font-weight: 600;
}
.platform-twitter { background: #1da1f2; color: #fff; }
.platform-facebook { background: #1877f2; color: #fff; }
.platform-instagram { background: #e1306c; color: #fff; }
.platform-youtube { background: #ff0000; color: #fff; }
.platform-tiktok { background: #000000; color: #fff; }
.social-preview iframe,
.social-preview blockquote {
    max-width: 100% !important;
    margin: 0 auto !important;
}
.social-preview .fb-post {
    width: 100% !important;
}
</style>

<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="font-serif text-3xl font-bold text-purple-900">Blog Posts</h1>
            <p class="text-gray-600 text-sm">Manage news updates, stories, and social media embeds.</p>
        </div>
        <a href="blog.php?add=1" class="bg-purple-900 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-purple-800 transition shadow">
            <i data-lucide="plus" class="w-4 h-4"></i> New Post
        </a>
    </div>

    <!-- Alert Messages -->
    <?php if (!empty($success)): ?>
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 p-3.5 rounded-xl text-sm font-medium">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 p-3.5 rounded-xl text-sm font-medium">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- BLOG LISTING -->
    <div class="space-y-4 max-w-5xl">
        <?php foreach ($posts as $p): ?>
            <div class="bg-white rounded-xl shadow p-4 flex items-center gap-4 border border-purple-50 hover:shadow-md transition">
                <?php if (!empty($p['image'])): ?>
                    <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="" class="w-20 h-20 object-cover rounded-lg border shrink-0" />
                <?php else: ?>
                    <div class="w-20 h-20 rounded-lg bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-300 shrink-0">
                        <i data-lucide="file-text" class="w-8 h-8"></i>
                    </div>
                <?php endif; ?>
                
                <div class="flex-1">
                    <div class="font-bold text-purple-900 text-base">
                        <?php echo htmlspecialchars($p['title']); ?>
                        <?php if (!$p['published']): ?>
                            <span class="text-xs text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-full font-semibold border border-yellow-200 ml-1.5 align-middle">Draft</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-xs text-gray-500 font-semibold mt-0.5">
                        Author: <?php echo htmlspecialchars($p['author'] ?: 'Anonymous'); ?> 
                        &middot; <?php echo date('M j, Y', strtotime($p['created_at'])); ?>
                    </div>
                    <p class="text-sm text-gray-600 mt-1.5 line-clamp-2"><?php echo htmlspecialchars($p['excerpt']); ?></p>
                </div>
                
                <div class="flex gap-2">
                    <a href="blog.php?edit=<?php echo urlencode($p['id']); ?>" class="text-purple-700 hover:text-purple-950 p-2 hover:bg-purple-50 rounded-lg transition" title="Edit">
                        <i data-lucide="edit" class="w-4 h-4"></i>
                    </a>
                    <a href="blog.php?delete=<?php echo urlencode($p['id']); ?>" onclick="return confirm('Are you sure you want to delete this blog post?');" class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-lg transition" title="Delete">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($posts)): ?>
            <div class="text-center py-12">
                <i data-lucide="file-text" class="w-16 h-16 mx-auto text-gray-300 mb-4"></i>
                <p class="text-gray-500 font-medium">No blog posts found.</p>
                <a href="blog.php?add=1" class="inline-block mt-4 text-purple-600 hover:text-purple-800">Create your first post →</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ADD/EDIT MODAL -->
<?php if (isset($_GET['add']) || isset($_GET['edit'])): 
    $p = $editPost ?: [
        'id' => '', 'title' => '', 'author' => '', 'excerpt' => '', 'content' => '', 'image' => '', 'published' => 1
    ];
?>
<div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto p-6 shadow-2xl">
        <div class="flex justify-between items-center border-b border-purple-100 pb-4 mb-4 sticky top-0 bg-white">
            <h2 class="font-serif text-xl font-bold text-purple-900">
                <?php echo $p['id'] ? 'Edit' : 'Create'; ?> Blog Post
            </h2>
            <a href="blog.php" class="text-gray-500 hover:text-gray-700 transition">
                <i data-lucide="x" class="w-6 h-6"></i>
            </a>
        </div>
        
        <form method="POST" action="blog.php" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="save_blog" value="1" />
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($p['id']); ?>" />
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title *</label>
                <input type="text" name="title" required value="<?php echo htmlspecialchars($p['title']); ?>" 
                       class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                       placeholder="Enter post title" />
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Author</label>
                <input type="text" name="author" value="<?php echo htmlspecialchars($p['author']); ?>" 
                       class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                       placeholder="Author name" />
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Excerpt</label>
                <textarea name="excerpt" rows="3" 
                          class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                          placeholder="Brief summary of the post..."><?php echo htmlspecialchars($p['excerpt']); ?></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Content</label>
                <textarea name="content" rows="8" 
                          class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent font-mono text-sm" 
                          placeholder="Write your post content here..."><?php echo htmlspecialchars($p['content']); ?></textarea>
            </div>
            
            <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Featured Image</label>
                
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Upload Image</label>
                    <input type="file" name="image_file" accept="image/*" class="w-full text-sm" />
                </div>
                
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Or Image URL</label>
                    <input type="text" name="image" value="<?php echo htmlspecialchars($p['image']); ?>" 
                           class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" 
                           placeholder="https://example.com/image.jpg" />
                </div>
                
                <?php if (!empty($p['image'])): ?>
                    <div class="mt-2">
                        <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="Current featured image" class="w-32 h-32 object-cover rounded-lg border" />
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="flex items-center gap-2">
                <input type="checkbox" name="published" value="1" <?php echo $p['published'] ? 'checked' : ''; ?> class="w-4 h-4 accent-purple-600" />
                <label class="text-sm font-medium text-gray-700">Publish immediately</label>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-purple-100 mt-4">
                <a href="blog.php" class="px-5 py-2.5 rounded-lg text-sm bg-gray-100 text-gray-700 hover:bg-gray-200 transition font-medium">Cancel</a>
                <button type="submit" class="bg-purple-900 text-white px-6 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 hover:bg-purple-800 transition shadow">
                    <i data-lucide="save" class="w-4 h-4"></i> Save Post
                </button>
            </div>
        </form>
        
        <!-- SOCIAL MEDIA POSTS SECTION -->
        <?php if (!empty($p['id']) && $p['id'] !== ''): ?>
        <div class="border-t border-purple-100 pt-6 mt-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-serif text-lg font-bold text-purple-900 flex items-center gap-2">
                    <i data-lucide="share-2" class="w-5 h-5"></i> Social Media Posts
                    <?php if (!empty($socialPosts)): ?>
                        <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-2 py-1 rounded-full">
                            <?php echo count($socialPosts); ?> embeds
                        </span>
                    <?php endif; ?>
                </h3>
            </div>
            
            <!-- Add Social Media Post Form -->
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 p-4 rounded-xl mb-6 border border-purple-100">
                <h4 class="font-semibold text-purple-900 text-sm mb-3 flex items-center gap-2">
                    <i data-lucide="link" class="w-4 h-4"></i> Embed Social Media Post
                </h4>
                
                <form method="POST" action="blog.php?edit=<?php echo urlencode($p['id']); ?>" class="space-y-3">
                    <input type="hidden" name="add_social_post" value="1" />
                    <input type="hidden" name="blog_id" value="<?php echo htmlspecialchars($p['id']); ?>" />
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Platform</label>
                        <select name="platform" required class="w-full border border-purple-200 p-2 rounded-lg text-sm bg-white">
                            <option value="twitter">🐦 Twitter / X</option>
                            <option value="facebook">📘 Facebook</option>
                            <option value="instagram">📸 Instagram</option>
                            <option value="youtube">🎥 YouTube</option>
                            <option value="tiktok">🎵 TikTok</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Post URL</label>
                        <input type="url" name="social_url" required class="w-full border border-purple-200 p-2 rounded-lg text-sm" placeholder="https://twitter.com/username/status/123456789" />
                        <p class="text-xs text-gray-400 mt-1">Paste the full URL of the social media post</p>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-700 transition">
                            <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i> Embed Post
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Display Existing Social Posts -->
            <?php if (!empty($socialPosts)): ?>
                <div class="space-y-4">
                    <?php foreach ($socialPosts as $social): ?>
                        <div class="social-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="platform-badge platform-<?php echo $social['platform']; ?>">
                                        <i data-lucide="<?php 
                                            echo $social['platform'] === 'twitter' ? 'twitter' : 
                                                ($social['platform'] === 'instagram' ? 'instagram' : 
                                                ($social['platform'] === 'facebook' ? 'facebook' : 
                                                ($social['platform'] === 'youtube' ? 'youtube' : 
                                                ($social['platform'] === 'tiktok' ? 'music' : 'link')))); 
                                        ?>" class="w-3 h-3"></i>
                                        <?php echo ucfirst($social['platform']); ?>
                                    </span>
                                </div>
                                <a href="blog.php?delete_social=<?php echo urlencode($social['id']); ?>&post_id=<?php echo urlencode($p['id']); ?>" 
                                   onclick="return confirm('Are you sure you want to remove this embedded post?');"
                                   class="text-red-500 hover:text-red-700 p-1 rounded-lg transition" title="Remove">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </a>
                            </div>
                            <div class="social-preview">
                                <?php echo $social['embed_code']; ?>
                            </div>
                            <div class="text-xs text-gray-400 mt-2">
                                Added: <?php echo date('M j, Y g:i A', strtotime($social['created_at'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Social Media SDK Scripts -->
                <div style="display: none;">
                    <!-- Twitter Widgets -->
                    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                    
                    <!-- Instagram Embed -->
                    <script async src="https://www.instagram.com/embed.js"></script>
                    
                    <!-- Facebook SDK -->
                    <div id="fb-root"></div>
                    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v18.0"></script>
                    
                    <!-- TikTok Embed -->
                    <script async src="https://www.tiktok.com/embed.js"></script>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                    <i data-lucide="share-2" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p class="text-sm">No social media posts embedded yet.</p>
                    <p class="text-xs text-gray-400 mt-1">Paste a Twitter, Facebook, Instagram, YouTube, or TikTok post URL above</p>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- GALLERY MANAGEMENT SECTION (Only for existing posts) -->
        <?php if (!empty($p['id']) && $p['id'] !== ''): ?>
        <div class="border-t border-purple-100 pt-6 mt-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-serif text-lg font-bold text-purple-900 flex items-center gap-2">
                    <i data-lucide="images" class="w-5 h-5"></i> Photo Gallery
                    <?php if (!empty($galleryImages)): ?>
                        <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-2 py-1 rounded-full">
                            <?php echo count($galleryImages); ?> images
                        </span>
                    <?php endif; ?>
                </h3>
                <?php if (!empty($galleryImages)): ?>
                    <button onclick="toggleBulkMode()" id="bulkModeBtn" class="text-xs bg-gray-100 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-200 transition font-medium">
                        <i data-lucide="check-square" class="w-3 h-3 inline mr-1"></i> Bulk Actions
                    </button>
                <?php endif; ?>
            </div>
            
            <!-- Add Gallery Image Form -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-4 rounded-xl mb-6 border border-purple-100">
                <h4 class="font-semibold text-purple-900 text-sm mb-3 flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Add Images to Gallery
                </h4>
                
                <form method="POST" action="blog.php?edit=<?php echo urlencode($p['id']); ?>" enctype="multipart/form-data" class="space-y-3">
                    <input type="hidden" name="add_gallery" value="1" />
                    <input type="hidden" name="blog_id" value="<?php echo htmlspecialchars($p['id']); ?>" />
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Upload Image</label>
                        <input type="file" name="gallery_image" accept="image/*" required class="w-full text-sm border border-purple-200 rounded-lg p-2" />
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Caption (Optional)</label>
                        <input type="text" name="gallery_caption" class="w-full border border-purple-200 p-2 rounded-lg text-sm" placeholder="Describe this image..." />
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-700 transition">
                            <i data-lucide="upload" class="w-4 h-4 inline mr-1"></i> Upload Image
                        </button>
                        
                        <button type="button" onclick="showUrlForm()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                            <i data-lucide="link" class="w-4 h-4 inline mr-1"></i> Add by URL
                        </button>
                    </div>
                </form>
                
                <!-- URL Form (Hidden by default) -->
                <div id="urlForm" class="hidden mt-4 pt-4 border-t border-purple-200">
                    <form method="POST" action="blog.php?edit=<?php echo urlencode($p['id']); ?>" class="space-y-3">
                        <input type="hidden" name="add_gallery_url" value="1" />
                        <input type="hidden" name="blog_id" value="<?php echo htmlspecialchars($p['id']); ?>" />
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Image URL</label>
                            <input type="url" name="gallery_image_url" required class="w-full border border-purple-200 p-2 rounded-lg text-sm" placeholder="https://example.com/image.jpg" />
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Caption (Optional)</label>
                            <input type="text" name="gallery_caption" class="w-full border border-purple-200 p-2 rounded-lg text-sm" placeholder="Describe this image..." />
                        </div>
                        
                        <div class="flex gap-2">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition">
                                <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i> Add from URL
                            </button>
                            <button type="button" onclick="hideUrlForm()" class="bg-gray-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-600 transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Bulk Actions Bar -->
            <div id="bulkActionsBar" class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4 hidden">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-semibold text-yellow-800">
                            <span id="selectedCount">0</span> images selected
                        </span>
                        <button onclick="selectAllImages()" class="text-xs text-yellow-700 hover:text-yellow-900 font-semibold">Select All</button>
                        <button onclick="deselectAllImages()" class="text-xs text-yellow-700 hover:text-yellow-900 font-semibold">Deselect All</button>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="bulkDelete()" class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-600 transition">
                            <i data-lucide="trash-2" class="w-3 h-3 inline mr-1"></i> Delete Selected
                        </button>
                        <button onclick="toggleBulkMode()" class="bg-gray-500 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-gray-600 transition">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Gallery Images Display -->
            <?php if (!empty($galleryImages)): ?>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <?php foreach ($galleryImages as $img): ?>
                        <div class="gallery-item relative group bg-white rounded-lg border border-purple-100 overflow-hidden" data-id="<?php echo htmlspecialchars($img['id']); ?>">
                            <!-- Bulk Selection Checkbox -->
                            <div class="bulk-checkbox absolute top-2 left-2 z-10 hidden">
                                <input type="checkbox" class="w-4 h-4 text-purple-600 bg-white border-2 border-white rounded shadow-lg" onchange="updateBulkSelection()" />
                            </div>
                            
                            <!-- Image -->
                            <div class="relative">
                                <img src="<?php echo htmlspecialchars($img['image_url']); ?>" alt="<?php echo htmlspecialchars($img['caption']); ?>" class="w-full h-32 object-cover" />
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200"></div>
                                
                                <!-- Action Buttons -->
                                <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button onclick="editCaption('<?php echo htmlspecialchars($img['id']); ?>', '<?php echo htmlspecialchars(addslashes($img['caption'])); ?>')" 
                                            class="bg-blue-500 text-white p-1.5 rounded-full hover:bg-blue-600 transition" title="Edit Caption">
                                        <i data-lucide="edit-3" class="w-3 h-3"></i>
                                    </button>
                                    <button onclick="deleteImage('<?php echo htmlspecialchars($img['id']); ?>', '<?php echo htmlspecialchars($p['id']); ?>')" 
                                            class="bg-red-500 text-white p-1.5 rounded-full hover:bg-red-600 transition" title="Delete Image">
                                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Caption Display -->
                            <div class="p-2">
                                <div class="caption-display">
                                    <?php if (!empty($img['caption'])): ?>
                                        <p class="text-xs text-gray-600 truncate" title="<?php echo htmlspecialchars($img['caption']); ?>">
                                            <?php echo htmlspecialchars(substr($img['caption'], 0, 50)); ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="text-xs text-gray-400 italic">No caption</p>
                                    <?php endif; ?>
                                </div>
                                <div class="caption-edit hidden">
                                    <input type="text" class="w-full text-xs border border-gray-300 rounded px-2 py-1" value="<?php echo htmlspecialchars($img['caption']); ?>" />
                                    <div class="flex gap-1 mt-1">
                                        <button onclick="saveCaption('<?php echo htmlspecialchars($img['id']); ?>')" class="text-xs bg-green-500 text-white px-2 py-0.5 rounded hover:bg-green-600">Save</button>
                                        <button onclick="cancelEditCaption('<?php echo htmlspecialchars($img['id']); ?>')" class="text-xs bg-gray-500 text-white px-2 py-0.5 rounded hover:bg-gray-600">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                    <i data-lucide="image" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p class="text-sm">No gallery images yet. Upload some above!</p>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
let bulkModeActive = false;
let currentPostId = '<?php echo htmlspecialchars($p['id'] ?? ''); ?>';

function showUrlForm() {
    document.getElementById('urlForm').classList.remove('hidden');
}

function hideUrlForm() {
    document.getElementById('urlForm').classList.add('hidden');
}

function toggleBulkMode() {
    bulkModeActive = !bulkModeActive;
    const bulkBtn = document.getElementById('bulkModeBtn');
    const bulkBar = document.getElementById('bulkActionsBar');
    const checkboxes = document.querySelectorAll('.bulk-checkbox');
    
    if (bulkModeActive) {
        bulkBtn.innerHTML = '<i data-lucide="x" class="w-3 h-3 inline mr-1"></i> Exit Bulk';
        bulkBtn.classList.add('bg-red-100', 'text-red-700');
        bulkBtn.classList.remove('bg-gray-100', 'text-gray-700');
        bulkBar.classList.remove('hidden');
        checkboxes.forEach(cb => cb.classList.remove('hidden'));
    } else {
        bulkBtn.innerHTML = '<i data-lucide="check-square" class="w-3 h-3 inline mr-1"></i> Bulk Actions';
        bulkBtn.classList.remove('bg-red-100', 'text-red-700');
        bulkBtn.classList.add('bg-gray-100', 'text-gray-700');
        bulkBar.classList.add('hidden');
        checkboxes.forEach(cb => {
            cb.classList.add('hidden');
            if(cb.querySelector('input')) cb.querySelector('input').checked = false;
        });
    }
    updateBulkSelection();
    lucide.createIcons();
}

function updateBulkSelection() {
    const selected = document.querySelectorAll('.bulk-checkbox input:checked');
    const countSpan = document.getElementById('selectedCount');
    if (countSpan) countSpan.textContent = selected.length;
}

function selectAllImages() {
    document.querySelectorAll('.bulk-checkbox input').forEach(cb => cb.checked = true);
    updateBulkSelection();
}

function deselectAllImages() {
    document.querySelectorAll('.bulk-checkbox input').forEach(cb => cb.checked = false);
    updateBulkSelection();
}

function bulkDelete() {
    const selected = document.querySelectorAll('.bulk-checkbox input:checked');
    if (selected.length === 0) {
        alert('Please select images to delete.');
        return;
    }
    
    if (!confirm(`Are you sure you want to delete ${selected.length} selected image(s)?`)) {
        return;
    }
    
    selected.forEach(checkbox => {
        const imageId = checkbox.closest('.gallery-item').dataset.id;
        window.location.href = `blog.php?delete_gallery=${imageId}&post_id=${currentPostId}`;
    });
}

function editCaption(imageId, currentCaption) {
    const item = document.querySelector(`.gallery-item[data-id="${imageId}"]`);
    const display = item.querySelector('.caption-display');
    const edit = item.querySelector('.caption-edit');
    const input = edit.querySelector('input');
    
    input.value = currentCaption;
    display.classList.add('hidden');
    edit.classList.remove('hidden');
    input.focus();
}

function saveCaption(imageId) {
    const item = document.querySelector(`.gallery-item[data-id="${imageId}"]`);
    const input = item.querySelector('.caption-edit input');
    const newCaption = input.value.trim();
    
    // Update display
    const displayText = item.querySelector('.caption-display p');
    if (newCaption) {
        displayText.textContent = newCaption.length > 50 ? newCaption.substring(0, 47) + '...' : newCaption;
        displayText.classList.remove('italic', 'text-gray-400');
        displayText.classList.add('text-gray-600');
    } else {
        displayText.textContent = 'No caption';
        displayText.classList.add('italic', 'text-gray-400');
        displayText.classList.remove('text-gray-600');
    }
    
    cancelEditCaption(imageId);
    
    // AJAX call to save caption
    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `update_caption=1&image_id=${imageId}&caption=${encodeURIComponent(newCaption)}`
    });
}

function cancelEditCaption(imageId) {
    const item = document.querySelector(`.gallery-item[data-id="${imageId}"]`);
    const display = item.querySelector('.caption-display');
    const edit = item.querySelector('.caption-edit');
    
    display.classList.remove('hidden');
    edit.classList.add('hidden');
}

function deleteImage(imageId, postId) {
    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }
    window.location.href = `blog.php?delete_gallery=${imageId}&post_id=${postId}`;
}

// Initialize Lucide icons
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
<?php endif; ?>

<?php
renderAdminFooter();
?>