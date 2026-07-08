<?php
/**
 * Blog Page - Professional NGO Layout
 */
require_once __DIR__ . '/header.php';

$posts = [];
try {
    $stmt = $pdo->query("SELECT * FROM blog_posts WHERE published = 1 ORDER BY created_at DESC");
    $posts = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- PAGE HERO -->
<section class="bg-navy-900 text-white py-20">
    <div class="max-w-7xl mx-auto px-6">
        <p class="text-gold-400 font-semibold tracking-widest uppercase text-sm mb-2">News & Stories</p>
        <h1 class="text-4xl md:text-5xl font-serif font-bold mb-4">Latest Updates</h1>
        <p class="text-xl text-gray-300 max-w-2xl">Stay informed about our programs, scholar success stories, and impact across Africa.</p>
    </div>
</section>

<!-- BREADCRUMB -->
<div class="bg-white border-b py-3">
    <div class="max-w-7xl mx-auto px-6 text-sm">
        <a href="<?php echo BASE_PATH; ?>/" class="text-gold-500 hover:underline">Home</a> <span class="text-gray-400">/</span> <span class="text-navy-900">News</span>
    </div>
</div>

<!-- BLOG GRID -->
<section class="py-20 bg-cream">
    <div class="max-w-7xl mx-auto px-6">
        <?php if(!empty($posts)): ?>
        <!-- Featured Post -->
        <?php 
        $featured = $posts[0]; unset($posts[0]);
        $featuredHasVideo = false;
        $featuredYtUrl = '';
        try {
            $stmt = $pdo->prepare("SELECT url FROM social_posts WHERE blog_post_id = ? AND platform = 'youtube' LIMIT 1");
            $stmt->execute([$featured['id']]);
            $ytRow = $stmt->fetch();
            if ($ytRow) {
                $featuredHasVideo = true;
                $featuredYtUrl = $ytRow['url'];
            }
        } catch (Exception $e) {}
        ?>
        <div class="card overflow-hidden mb-12 relative">
            <?php if ($featuredHasVideo): ?>
            <div class="absolute top-3 right-3 z-10 bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                Video
            </div>
            <?php endif; ?>
            <div class="grid md:grid-cols-2">
                <div class="h-64 md:h-auto">
                    <img src="<?php echo htmlspecialchars($featured['image'] ?? 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=800&q=80'); ?>" alt="<?php echo htmlspecialchars($featured['title']); ?>" class="w-full h-full object-cover">
                </div>
                <div class="p-8 flex flex-col justify-center">
                    <div class="text-xs text-gold-500 font-semibold mb-2">Featured</div>
                    <h2 class="text-2xl font-serif font-bold text-navy-900 mb-3"><?php echo htmlspecialchars($featured['title']); ?></h2>
                    <p class="text-stone-500 mb-4"><?php echo htmlspecialchars(mb_substr(strip_tags($featured['excerpt'] ?? $featured['content'] ?? ''), 0, 200)); ?>...</p>
                    <div class="text-sm text-stone-400 mb-4"><?php echo date('F j, Y', strtotime($featured['created_at'])); ?> • By <?php echo htmlspecialchars($featured['author'] ?? 'RVIF'); ?></div>
                    <?php if ($featuredHasVideo && $featuredYtUrl): ?>
                    <a href="<?php echo htmlspecialchars($featuredYtUrl); ?>" target="_blank" class="text-red-500 font-semibold text-sm hover:text-red-600 flex items-center gap-1 mb-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                        Watch on YouTube →
                    </a>
                    <?php endif; ?>
                    <a href="<?php echo BASE_PATH; ?>/blog_view?id=<?php echo urlencode($featured['id']); ?>" class="text-gold-500 font-semibold hover:text-gold-600">Read full story →</a>
                </div>
            </div>
        </div>
        
        <!-- Other Posts -->
        <?php if(!empty($posts)): ?>
        <div class="text-center mb-8">
            <h3 class="text-xl font-serif font-bold text-navy-900">More Stories</h3>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach($posts as $post): ?>
            <?php 
            // Check if post has social embeds
            $hasVideo = false;
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM social_posts WHERE blog_post_id = ?");
                $stmt->execute([$post['id']]);
                $hasVideo = $stmt->fetch()['cnt'] > 0;
            } catch (Exception $e) {}
            ?>
            <div class="card overflow-hidden relative">
                <?php if ($hasVideo): ?>
                <div class="absolute top-3 right-3 z-10 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                    Video
                </div>
                <?php endif; ?>
                <div class="h-48 overflow-hidden">
                    <img src="<?php echo htmlspecialchars($post['image'] ?? 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=600&q=80'); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                </div>
                <div class="p-6">
                    <div class="text-xs text-gold-500 font-semibold mb-2"><?php echo date('M j, Y', strtotime($post['created_at'])); ?></div>
                    <h3 class="text-lg font-serif font-bold text-navy-900 mb-2"><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p class="text-stone-500 text-sm mb-4"><?php echo htmlspecialchars(mb_substr(strip_tags($post['excerpt'] ?? $post['content'] ?? ''), 0, 80)); ?>...</p>
                    <?php if ($hasVideo): ?>
                    <?php 
                    $ytUrl = '';
                    try {
                        $stmt = $pdo->prepare("SELECT url FROM social_posts WHERE blog_post_id = ? AND platform = 'youtube' LIMIT 1");
                        $stmt->execute([$post['id']]);
                        $ytRow = $stmt->fetch();
                        if ($ytRow) $ytUrl = $ytRow['url'];
                    } catch (Exception $e) {}
                    if ($ytUrl): ?>
                    <a href="<?php echo htmlspecialchars($ytUrl); ?>" target="_blank" class="text-red-500 font-semibold text-sm hover:text-red-600 flex items-center gap-1 mb-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                        Watch on YouTube →
                    </a>
                    <?php endif; ?>
                    <?php endif; ?>
                    <a href="<?php echo BASE_PATH; ?>/blog_view?id=<?php echo urlencode($post['id']); ?>" class="text-gold-500 font-semibold text-sm hover:text-gold-600">Read more →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="text-center py-16">
            <p class="text-stone-500">No blog posts yet. Check back soon!</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- NEWSLETTER -->
<section class="py-16 bg-navy-900 text-white text-center">
    <div class="max-w-4xl mx-auto px-6">
        <h2 class="text-2xl font-serif font-bold mb-4">Stay Updated</h2>
        <p class="text-gray-300 mb-6">Subscribe to our newsletter for the latest news and impact stories.</p>
        <form class="flex flex-wrap justify-center gap-4 max-w-md mx-auto">
            <input type="email" placeholder="Your email" class="px-4 py-3 bg-navy-800 border border-navy-700 rounded text-white focus:border-gold-500 outline-none flex-1 min-w-[200px]">
            <button type="submit" class="btn-gold">Subscribe</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>