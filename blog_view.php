<?php
/**
 * Blog Post View Page
 * Professional NGO Design
 */
require_once __DIR__ . '/header.php';

$error = '';
$post = null;
$gallery = [];
$relatedPosts = [];

// Get post ID from URL
$postId = isset($_GET['id']) ? trim($_GET['id']) : '';

// Handle comment submission
$commentSuccess = false;
$commentError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $cName    = trim($_POST['comment_name'] ?? '');
    $cEmail   = trim($_POST['comment_email'] ?? '');
    $cComment = trim($_POST['comment_text'] ?? '');
    if (empty($cName) || empty($cEmail) || empty($cComment)) {
        $commentError = 'Please fill in all fields.';
    } elseif (!filter_var($cEmail, FILTER_VALIDATE_EMAIL)) {
        $commentError = 'Please enter a valid email address.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO blog_comments (id, blog_post_id, name, email, comment) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute(['c-' . time() . '-' . rand(100,999), $postId, $cName, $cEmail, $cComment]);
            $commentSuccess = true;
        } catch (Exception $e) {
            $commentError = 'Could not save your comment. Please try again.';
        }
    }
}

if (empty($postId)) {
    header('Location: ' . BASE_PATH . '/blog.php');
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ? AND published = 1");
    $stmt->execute([$postId]);
    $post = $stmt->fetch();
    
    if (!$post) {
        $error = 'Post not found or has not been published yet.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM blog_gallery WHERE blog_post_id = ? ORDER BY display_order ASC, created_at ASC");
        $stmt->execute([$postId]);
        $gallery = $stmt->fetchAll();
        
        $stmt = $pdo->prepare("SELECT id, title, excerpt, image, created_at, author FROM blog_posts WHERE published = 1 AND id != ? ORDER BY created_at DESC LIMIT 3");
        $stmt->execute([$postId]);
        $relatedPosts = $stmt->fetchAll();
    }
} catch (Exception $e) {
    $error = 'Unable to load the post. Please try again later.';
}
?>

<!-- Breadcrumb -->
<div class="bg-white border-b border-stone-200">
    <div class="max-w-7xl mx-auto px-6 py-3">
        <nav class="text-sm">
            <a href="<?php echo BASE_PATH; ?>/" class="text-stone-600 hover:text-[#D4A72C] transition-colors">Home</a>
            <span class="mx-2 text-stone-400">/</span>
            <a href="<?php echo BASE_PATH; ?>/blog.php" class="text-stone-600 hover:text-[#D4A72C] transition-colors">Blog</a>
            <span class="mx-2 text-stone-400">/</span>
            <span class="text-stone-800"><?php echo $error ? 'Not Found' : htmlspecialchars(substr($post['title'] ?? '', 0, 40)); ?></span>
        </nav>
    </div>
</div>

<?php if ($error): ?>
<!-- Error State -->
<section class="py-20 bg-[#FAF8F5]">
    <div class="max-w-7xl mx-auto px-6 text-center">
        <h1 class="font-serif text-4xl font-semibold text-[#0A1628] mb-4">Post Not Found</h1>
        <p class="text-stone-600 mb-8"><?php echo htmlspecialchars($error); ?></p>
        <a href="<?php echo BASE_PATH; ?>/blog.php" class="inline-flex items-center gap-2 bg-[#0A1628] text-white px-6 py-3 rounded-lg hover:bg-[#1a2d4a] transition-colors">
            ← Back to Blog
        </a>
    </div>
</section>
<?php else: ?>

<!-- Post Hero -->
<section class="bg-[#0A1628] py-16">
    <div class="max-w-4xl mx-auto px-6">
        <span class="inline-block text-xs font-semibold tracking-wider uppercase text-[#D4A72C] mb-3">Blog</span>
        <h1 class="font-serif text-3xl md:text-4xl font-semibold text-white leading-tight mb-4"><?php echo htmlspecialchars($post['title']); ?></h1>
        <div class="flex items-center gap-4 text-stone-400 text-sm">
            <span>By <?php echo htmlspecialchars($post['author'] ?? 'RVIF Team'); ?></span>
            <span>•</span>
            <span><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
        </div>
    </div>
</section>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-6 py-12">
    <div class="grid lg:grid-cols-3 gap-12">
        <!-- Post Content -->
        <article class="lg:col-span-2">
            <a href="<?php echo BASE_PATH; ?>/blog.php" class="inline-flex items-center gap-2 text-sm text-stone-600 hover:text-[#D4A72C] mb-6 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Blog
            </a>
            
            <?php if (!empty($post['image'])): ?>
            <div class="rounded-xl overflow-hidden mb-8">
                <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full h-auto">
            </div>
            <?php endif; ?>
            
            <div class="prose prose-stone max-w-none">
                <p class="text-lg text-stone-700 leading-relaxed whitespace-pre-line"><?php echo htmlspecialchars($post['content']); ?></p>
            </div>
            
            <!-- Share -->
            <div class="mt-10 pt-6 border-t border-stone-200">
                <p class="text-sm font-semibold text-[#0A1628] mb-3">Share this story:</p>
                <div class="flex gap-3">
                    <?php 
                    $shareUrl = urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
                    $shareTitle = urlencode($post['title']);
                    ?>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo $shareUrl; ?>&text=<?php echo $shareTitle; ?>" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-[#1DA1F2] text-white text-sm rounded-lg hover:opacity-90 transition-opacity">
                        Twitter
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $shareUrl; ?>" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-[#1877F2] text-white text-sm rounded-lg hover:opacity-90 transition-opacity">
                        Facebook
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $shareUrl; ?>" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-[#0A66C2] text-white text-sm rounded-lg hover:opacity-90 transition-opacity">
                        LinkedIn
                    </a>
                </div>
            </div>
            
            <!-- Comments Section -->
            <?php
            $comments = [];
            try {
                $stmt = $pdo->prepare("SELECT * FROM blog_comments WHERE blog_post_id = ? AND approved = 1 ORDER BY created_at ASC");
                $stmt->execute([$postId]);
                $comments = $stmt->fetchAll();
            } catch (Exception $e) {}
            ?>
            <div class="mt-12 pt-8 border-t border-stone-200">
                <h3 class="font-serif text-2xl font-semibold text-[#0A1628] mb-6">Comments (<?php echo count($comments); ?>)</h3>

                <!-- Existing Comments -->
                <?php if (!empty($comments)): ?>
                <div class="space-y-6 mb-10">
                    <?php foreach ($comments as $c): ?>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full bg-[#D4A72C] flex items-center justify-center text-[#0A1628] font-bold text-sm flex-shrink-0">
                            <?php echo strtoupper(substr($c['name'], 0, 1)); ?>
                        </div>
                        <div class="flex-1 bg-[#FAF8F5] rounded-xl p-4 border border-stone-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-[#0A1628] text-sm"><?php echo htmlspecialchars($c['name']); ?></span>
                                <span class="text-xs text-stone-400"><?php echo date('M j, Y', strtotime($c['created_at'])); ?></span>
                            </div>
                            <p class="text-stone-600 text-sm leading-relaxed"><?php echo nl2br(htmlspecialchars($c['comment'])); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-stone-400 text-sm mb-8">No comments yet. Be the first to share your thoughts!</p>
                <?php endif; ?>

                <!-- Comment Form -->
                <div class="bg-white rounded-xl border border-stone-200 p-6">
                    <h4 class="font-serif text-lg font-semibold text-[#0A1628] mb-4">Leave a Comment</h4>

                    <?php if ($commentSuccess): ?>
                    <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mb-4 text-sm">
                        ✓ Your comment has been posted. Thank you!
                    </div>
                    <?php endif; ?>
                    <?php if ($commentError): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-4 text-sm">
                        <?php echo htmlspecialchars($commentError); ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="submit_comment" value="1">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-stone-700 mb-1">Name *</label>
                                <input type="text" name="comment_name" required
                                       value="<?php echo htmlspecialchars($_POST['comment_name'] ?? ''); ?>"
                                       class="w-full border border-stone-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#D4A72C] focus:ring-1 focus:ring-[#D4A72C]" placeholder="Your name">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-stone-700 mb-1">Email *</label>
                                <input type="email" name="comment_email" required
                                       value="<?php echo htmlspecialchars($_POST['comment_email'] ?? ''); ?>"
                                       class="w-full border border-stone-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#D4A72C] focus:ring-1 focus:ring-[#D4A72C]" placeholder="your@email.com">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-stone-700 mb-1">Comment *</label>
                            <textarea name="comment_text" required rows="4"
                                      class="w-full border border-stone-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#D4A72C] focus:ring-1 focus:ring-[#D4A72C] resize-none" placeholder="Share your thoughts..."><?php echo htmlspecialchars($_POST['comment_text'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" class="bg-[#D4A72C] text-[#0A1628] px-6 py-2.5 rounded-lg font-semibold text-sm hover:bg-[#c4992a] transition-colors">
                            Post Comment →
                        </button>
                    </form>
                </div>
            </div>

            <!-- Social Media Posts (YouTube, etc) -->
            <?php 
            $socialPosts = [];
            try {
                $stmt = $pdo->prepare("SELECT * FROM social_posts WHERE blog_post_id = ? ORDER BY created_at DESC");
                $stmt->execute([$postId]);
                $socialPosts = $stmt->fetchAll();
            } catch (Exception $e) {}
            ?>
            <?php if (!empty($socialPosts)): ?>
            <div class="mt-12">
                <h3 class="font-serif text-xl font-semibold text-[#0A1628] mb-6">Featured Videos</h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <?php foreach ($socialPosts as $social): ?>
                    <div class="rounded-xl overflow-hidden">
                        <?php echo $social['embed_code']; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Social Media SDK Scripts -->
            <div style="display: none;">
                <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                <script async src="https://www.instagram.com/embed.js"></script>
                <div id="fb-root"></div>
                <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v18.0"></script>
                <script async src="https://www.tiktok.com/embed.js"></script>
            </div>
            <?php endif; ?>
            
            <!-- Gallery -->
            <?php if (!empty($gallery)): ?>
            <div class="mt-12">
                <h3 class="font-serif text-xl font-semibold text-[#0A1628] mb-6">Photo Gallery (<?php echo count($gallery); ?>)</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php foreach ($gallery as $idx => $img): ?>
                    <div class="gallery-item rounded-lg overflow-hidden cursor-pointer hover:opacity-90 transition-opacity" onclick="openGallery(<?php echo $idx; ?>)">
                        <img src="<?php echo htmlspecialchars($img['image_url']); ?>" alt="<?php echo htmlspecialchars($img['caption'] ?? ''); ?>" class="w-full h-32 object-cover">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </article>
        
        <!-- Sidebar -->
        <aside class="lg:col-span-1">
            <div class="sticky top-24 space-y-6">
                <!-- Related Posts -->
                <?php if (!empty($relatedPosts)): ?>
                <div class="bg-white rounded-xl p-6 border border-stone-200">
                    <h3 class="font-serif text-lg font-semibold text-[#0A1628] mb-4">Recent Stories</h3>
                    <div class="space-y-4">
                        <?php foreach ($relatedPosts as $related): ?>
                        <a href="<?php echo BASE_PATH; ?>/blog_view.php?id=<?php echo urlencode($related['id']); ?>" class="flex gap-3 group">
                            <?php if (!empty($related['image'])): ?>
                            <img src="<?php echo htmlspecialchars($related['image']); ?>" alt="" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                            <?php endif; ?>
                            <div>
                                <h4 class="text-sm font-medium text-[#0A1628] group-hover:text-[#D4A72C] transition-colors line-clamp-2"><?php echo htmlspecialchars($related['title']); ?></h4>
                                <span class="text-xs text-stone-500"><?php echo date('M j, Y', strtotime($related['created_at'])); ?></span>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Donate CTA -->
                <div class="bg-[#0A1628] rounded-xl p-6 text-white">
                    <h3 class="font-serif text-lg font-semibold mb-2">Support Our Work</h3>
                    <p class="text-sm text-stone-300 mb-4">Your donation helps provide education and opportunities to young Africans.</p>
                    <a href="<?php echo BASE_PATH; ?>/donate.php" class="block text-center bg-[#D4A72C] text-[#0A1628] py-3 rounded-lg font-semibold hover:bg-[#c4992a] transition-colors">
                        Donate Now →
                    </a>
                </div>
                
                <!-- Subscribe -->
                <div class="bg-[#FAF8F5] rounded-xl p-6 border border-stone-200">
                    <h3 class="font-serif text-lg font-semibold text-[#0A1628] mb-2">Subscribe</h3>
                    <p class="text-sm text-stone-600 mb-4">Get the latest stories delivered to your inbox.</p>
                    <a href="<?php echo BASE_PATH; ?>/contact.php" class="block text-center border border-[#0A1628] text-[#0A1628] py-3 rounded-lg font-medium hover:bg-[#0A1628] hover:text-white transition-colors">
                        Subscribe →
                    </a>
                </div>
            </div>
        </aside>
    </div>
</div>

<!-- Gallery Modal -->
<div id="galleryModal" class="fixed inset-0 bg-black/95 z-50 hidden items-center justify-center">
    <button onclick="closeGallery()" class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/20 text-white flex items-center justify-center hover:bg-white/30 transition-colors text-2xl">×</button>
    <button onclick="prevImage()" class="absolute left-4 w-10 h-10 rounded-full bg-white/20 text-white flex items-center justify-center hover:bg-white/30 transition-colors">‹</button>
    <div class="text-center max-w-4xl px-4">
        <img id="modalImg" src="" alt="" class="max-h-[70vh] mx-auto rounded">
        <p id="modalCaption" class="text-white mt-4 text-sm"></p>
        <p id="modalCounter" class="text-white/60 text-xs mt-2"></p>
    </div>
    <button onclick="nextImage()" class="absolute right-4 w-10 h-10 rounded-full bg-white/20 text-white flex items-center justify-center hover:bg-white/30 transition-colors">›</button>
</div>

<script>
const galleryImages = <?php echo json_encode(array_map(fn($i) => ['url' => $i['image_url'], 'caption' => $i['caption'] ?? ''], $gallery)); ?>;
let currentIndex = 0;

function openGallery(idx) {
    currentIndex = idx;
    updateModal();
    document.getElementById('galleryModal').classList.remove('hidden');
    document.getElementById('galleryModal').classList.add('flex');
}
function closeGallery() {
    document.getElementById('galleryModal').classList.add('hidden');
    document.getElementById('galleryModal').classList.remove('flex');
}
function prevImage() { if(currentIndex > 0) { currentIndex--; updateModal(); } }
function nextImage() { if(currentIndex < galleryImages.length - 1) { currentIndex++; updateModal(); } }
function updateModal() {
    const img = galleryImages[currentIndex];
    document.getElementById('modalImg').src = img.url;
    document.getElementById('modalCaption').textContent = img.caption;
    document.getElementById('modalCounter').textContent = (currentIndex + 1) + ' of ' + galleryImages.length;
}
</script>

<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>