<?php
/**
 * Homepage - Professional NGO Layout (UNICEF/Oxfam Style)
 */
require_once __DIR__ . '/header.php';

// Get data
$heroTitle = $settings['heroTitle'] ?? "Empowering Africa's Future Through Education";
$heroSubtitle = $settings['heroSubtitle'] ?? "Royal Village International Foundation provides scholarships, vocational training, and community support.";

try {
    $stmt = $pdo->query("SELECT * FROM programs WHERE visible = 1 ORDER BY display_order ASC LIMIT 3");
    $programs = $stmt->fetchAll();
    $stmt = $pdo->query("SELECT * FROM blog_posts WHERE published = 1 ORDER BY created_at DESC LIMIT 3");
    $posts = $stmt->fetchAll();
} catch (Exception $e) { $programs = []; $posts = []; }
?>

<!-- HERO SECTION -->
<section class="relative min-h-[85vh] flex items-center overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=1920&q=80" alt="Education" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-navy-900/80"></div>
    </div>
    
    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-6 py-20">
        <div class="max-w-3xl">
            <p class="text-gold-400 font-semibold tracking-widest uppercase text-sm mb-4">Since 2021</p>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif font-bold text-white leading-tight mb-6">
                <?php echo htmlspecialchars($heroTitle); ?>
            </h1>
            <p class="text-xl text-gray-300 mb-8 leading-relaxed max-w-2xl">
                <?php echo htmlspecialchars($heroSubtitle); ?>
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="<?php echo BASE_PATH; ?>/donate" class="btn-gold">Donate Now</a>
                <a href="<?php echo BASE_PATH; ?>/about" class="btn-outline border-white text-white hover:bg-white hover:text-navy-900">Learn More</a>
            </div>
        </div>
    </div>
    
    <!-- Stats Bar -->
    <div class="absolute bottom-0 left-0 right-0 bg-gold-500 text-navy-900">
        <div class="max-w-7xl mx-auto px-6 py-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center md:text-left">
                <div>
                    <div class="font-serif text-3xl font-bold" data-count="<?php echo $settings['stats']['scholars'] ?? 50; ?>">0</div>
                    <div class="text-sm font-semibold">Scholars Sponsored</div>
                </div>
                <div>
                    <div class="font-serif text-3xl font-bold" data-count="<?php echo $settings['stats']['countries'] ?? 4; ?>">0</div>
                    <div class="text-sm font-semibold">Countries Reached</div>
                </div>
                <div>
                    <div class="font-serif text-3xl font-bold" data-count="<?php echo $settings['stats']['programs'] ?? 4; ?>">0</div>
                    <div class="text-sm font-semibold">Active Programs</div>
                </div>
                <div>
                    <div class="font-serif text-3xl font-bold" data-count="<?php echo $settings['stats']['years'] ?? 5; ?>">0</div>
                    <div class="text-sm font-semibold">Years of Service</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ABOUT SECTION -->
<section class="py-20 bg-cream">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <p class="text-gold-500 font-semibold tracking-widest uppercase text-sm mb-2">Who We Are</p>
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-navy-900 mb-6"><?php echo htmlspecialchars($settings['aboutTitle'] ?? 'About Royal Village International Foundation'); ?></h2>
                <div class="w-20 h-1 bg-gold-500 mb-6"></div>
                <p class="text-stone-600 mb-6 leading-relaxed">
                    <?php echo htmlspecialchars($settings['aboutText'] ?? ''); ?>
                </p>
                <a href="<?php echo BASE_PATH; ?>/about" class="text-gold-500 font-semibold hover:text-gold-600 flex items-center gap-2">
                    Read Our Story <span>→</span>
                </a>
            </div>
            <div class="relative">
                <img src="uploads/about.jpg" alt="Students" class="rounded-lg shadow-xl">
                <div class="absolute -bottom-6 -left-6 bg-navy-900 text-white p-6 rounded-lg shadow-lg">
                    <div class="font-serif text-3xl font-bold text-gold-400">5+</div>
                    <div class="text-sm">Years of Impact</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PROGRAMS SECTION -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-14">
            <p class="text-gold-500 font-semibold tracking-widest uppercase text-sm mb-2">What We Do</p>
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-navy-900">Our Programs</h2>
            <p class="text-stone-500 mt-4 max-w-2xl mx-auto">Focused initiatives creating lasting change across Africa</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <?php if(!empty($programs)): foreach($programs as $prog): ?>
            <div class="card p-6">
                <div class="w-14 h-14 bg-amber-50 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="text-xl font-serif font-bold text-navy-900 mb-3"><?php echo htmlspecialchars($prog['title']); ?></h3>
                <p class="text-stone-500 text-sm leading-relaxed mb-4"><?php echo htmlspecialchars(mb_substr($prog['description'] ?? '', 0, 150)); ?></p>
                <a href="<?php echo BASE_PATH; ?>/programs" class="text-gold-500 font-semibold text-sm hover:text-gold-600">Learn more →</a>
            </div>
            <?php endforeach; else: ?>
            <div class="card p-6">
                <div class="w-14 h-14 bg-amber-50 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="text-xl font-serif font-bold text-navy-900 mb-3">Scholarship Program</h3>
                <p class="text-stone-500 text-sm leading-relaxed mb-4">Full and partial scholarships for secondary and university students across Africa, covering tuition, books, and living expenses.</p>
                <a href="<?php echo BASE_PATH; ?>/programs" class="text-gold-500 font-semibold text-sm hover:text-gold-600">Learn more →</a>
            </div>
            <div class="card p-6">
                <div class="w-14 h-14 bg-blue-50 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
                <h3 class="text-xl font-serif font-bold text-navy-900 mb-3">Vocational Training</h3>
                <p class="text-stone-500 text-sm leading-relaxed mb-4">Hands-on skill-building in technology, trades, and entrepreneurship for youth outside formal education.</p>
                <a href="<?php echo BASE_PATH; ?>/programs" class="text-gold-500 font-semibold text-sm hover:text-gold-600">Learn more →</a>
            </div>
            <div class="card p-6">
                <div class="w-14 h-14 bg-green-50 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-xl font-serif font-bold text-navy-900 mb-3">Community Development</h3>
                <p class="text-stone-500 text-sm leading-relaxed mb-4">Outreach, mentorship, and support for underserved communities including clean water and healthcare initiatives.</p>
                <a href="<?php echo BASE_PATH; ?>/programs" class="text-gold-500 font-semibold text-sm hover:text-gold-600">Learn more →</a>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-10">
            <a href="<?php echo BASE_PATH; ?>/programs" class="btn-outline">View All Programs</a>
        </div>
    </div>
</section>

<!-- IMPACT SECTION -->
<section class="py-20 bg-navy-900 text-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <img src="uploads/hero/tttt.jpeg" alt="Impact" class="rounded-lg shadow-2xl">
            </div>
            <div>
                <p class="text-gold-400 font-semibold tracking-widest uppercase text-sm mb-2">Our Impact</p>
                <h2 class="text-3xl md:text-4xl font-serif font-bold mb-6"><?php echo htmlspecialchars($settings['impactTitle'] ?? 'Changing Lives Across Africa'); ?></h2>
                <div class="w-20 h-1 bg-gold-500 mb-6"></div>
                <p class="text-gray-300 mb-8 leading-relaxed">
                    <?php echo htmlspecialchars($settings['impactText'] ?? ''); ?>
                </p>
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div class="bg-navy-800 p-4 rounded-lg">
                        <div class="font-serif text-3xl font-bold text-gold-400" data-count="<?php echo $settings['impactLivesTouched'] ?? 1000; ?>">0</div>
                        <div class="text-sm text-gray-400">Lives Touched</div>
                    </div>
                    <div class="bg-navy-800 p-4 rounded-lg">
                        <div class="font-serif text-3xl font-bold text-gold-400" data-count="<?php echo $settings['impactGradEmployment'] ?? 95; ?>">0</div>
                        <div class="text-sm text-gray-400">% Graduate Employment</div>
                    </div>
                    <div class="bg-navy-800 p-4 rounded-lg">
                        <div class="font-serif text-3xl font-bold text-gold-400" data-count="<?php echo $settings['impactPartners'] ?? 15; ?>">0</div>
                        <div class="text-sm text-gray-400">Partner Organizations</div>
                    </div>
                    <div class="bg-navy-800 p-4 rounded-lg">
                        <div class="font-serif text-3xl font-bold text-gold-400" data-count="<?php echo $settings['impactContinents'] ?? 3; ?>">0</div>
                        <div class="text-sm text-gray-400">Continents</div>
                    </div>
                </div>
                <a href="<?php echo BASE_PATH; ?>/impact" class="btn-outline border-gold-500 text-gold-400 hover:bg-gold-500 hover:text-navy-900">See Full Impact →</a>
            </div>
        </div>
    </div>
</section>

<!-- BLOG SECTION -->
<?php if(!empty($posts)): ?>
<section class="py-20 bg-cream">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-14">
            <p class="text-gold-500 font-semibold tracking-widest uppercase text-sm mb-2">Stay Updated</p>
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-navy-900">Latest Stories</h2>
            <p class="text-stone-500 mt-4 max-w-2xl mx-auto">News and updates from our community</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach($posts as $post): ?>
            <div class="card overflow-hidden">
                <div class="h-48 overflow-hidden">
                    <img src="<?php echo htmlspecialchars($post['image'] ?? 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=600&q=80'); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                </div>
                <div class="p-6">
                    <div class="text-xs text-gold-500 font-semibold mb-2"><?php echo date('M j, Y', strtotime($post['created_at'])); ?></div>
                    <h3 class="text-lg font-serif font-bold text-navy-900 mb-2"><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p class="text-stone-500 text-sm mb-4"><?php echo htmlspecialchars(mb_substr(strip_tags($post['excerpt'] ?? $post['content'] ?? ''), 0, 100)); ?>...</p>
                    <a href="<?php echo BASE_PATH; ?>/blog" class="text-gold-500 font-semibold text-sm hover:text-gold-600">Read more →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- TESTIMONIALS SECTION -->
<?php
$testimonials = [];
try {
    $stmt = $pdo->query("SELECT * FROM testimonials WHERE visible = 1 ORDER BY display_order ASC");
    $testimonials = $stmt->fetchAll();
} catch (Exception $e) {}
?>
<?php if (!empty($testimonials)): ?>
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-14">
            <p class="text-gold-500 font-semibold tracking-widest uppercase text-sm mb-2">Testimonials</p>
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-navy-900">What People Say</h2>
            <p class="text-stone-500 mt-4 max-w-2xl mx-auto">Voices from our scholars, partners, and community</p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($testimonials as $t): ?>
            <div class="card p-6 flex flex-col">
                <div class="text-gold-400 text-4xl font-serif leading-none mb-4">&ldquo;</div>
                <p class="text-stone-600 leading-relaxed text-sm flex-1 mb-6"><?php echo htmlspecialchars($t['quote']); ?></p>
                <div class="flex items-center gap-3 pt-4 border-t border-stone-100">
                    <?php if (!empty($t['photo'])): ?>
                    <img src="<?php echo htmlspecialchars($t['photo']); ?>" alt="<?php echo htmlspecialchars($t['name']); ?>" class="w-11 h-11 rounded-full object-cover flex-shrink-0">
                    <?php else: ?>
                    <div class="w-11 h-11 rounded-full bg-gold-500 flex items-center justify-center text-navy-900 font-bold flex-shrink-0">
                        <?php echo strtoupper(substr($t['name'], 0, 1)); ?>
                    </div>
                    <?php endif; ?>
                    <div>
                        <div class="font-semibold text-navy-900 text-sm"><?php echo htmlspecialchars($t['name']); ?></div>
                        <?php if (!empty($t['role'])): ?>
                        <div class="text-gold-500 text-xs"><?php echo htmlspecialchars($t['role']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA SECTION -->
<section class="py-20 bg-gold-500">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-serif font-bold text-navy-900 mb-4"><?php echo htmlspecialchars($settings['ctaTitle'] ?? 'Support Our Mission'); ?></h2>
        <p class="text-navy-800 mb-8 text-lg"><?php echo htmlspecialchars($settings['ctaText'] ?? 'Your donation helps provide education, vocational training, and hope to young Africans. Every contribution makes a difference.'); ?></p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="<?php echo BASE_PATH; ?>/donate" class="bg-navy-900 text-white px-8 py-3 rounded font-semibold hover:bg-navy-800 transition">Donate Now</a>
            <a href="<?php echo BASE_PATH; ?>/contact" class="border-2 border-navy-900 text-navy-900 px-8 py-3 rounded font-semibold hover:bg-navy-900 hover:text-white transition">Partner With Us</a>
        </div>
    </div>
</section>

<script>
// Counter animation
document.querySelectorAll('[data-count]').forEach(el => {
    const target = +el.dataset.count;
    let current = 0, step = target / 40;
    const update = () => { current += step; el.textContent = Math.floor(current) + (target >= 50 ? '+' : target >= 10 ? '' : ''); if(current < target) requestAnimationFrame(update); };
    const obs = new IntersectionObserver(entries => { if(entries[0].isIntersecting){ update(); obs.disconnect(); }});
    obs.observe(el);
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>