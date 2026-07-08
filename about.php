<?php
/**
 * About Page - Professional NGO Layout
 */
require_once __DIR__ . '/header.php';

$milestones = [];
try {
    $stmt = $pdo->query("SELECT * FROM milestones WHERE visible = 1 ORDER BY year ASC, display_order ASC");
    $milestones = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- PAGE HERO -->
<section class="bg-navy-900 text-white py-20">
    <div class="max-w-7xl mx-auto px-6">
        <p class="text-gold-400 font-semibold tracking-widest uppercase text-sm mb-2">About Us</p>
        <h1 class="text-4xl md:text-5xl font-serif font-bold mb-4">Who We Are</h1>
        <p class="text-xl text-gray-300 max-w-2xl">We are a non-profit organization dedicated to empowering African youth through education, training, and community support.</p>
    </div>
</section>

<!-- BREADCRUMB -->
<div class="bg-white border-b py-3">
    <div class="max-w-7xl mx-auto px-6 text-sm">
        <a href="<?php echo BASE_PATH; ?>/" class="text-gold-500 hover:underline">Home</a> <span class="text-gray-400">/</span> <span class="text-navy-900">About Us</span>
    </div>
</div>

<!-- STORY SECTION -->
<section class="py-20 bg-cream">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <p class="text-gold-500 font-semibold tracking-widest uppercase text-sm mb-2">Our Story</p>
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-navy-900 mb-6">Founded on Faith and Purpose</h2>
                <div class="w-20 h-1 bg-gold-500 mb-6"></div>
                <p class="text-stone-600 mb-4 leading-relaxed">
                    Royal Village International Foundation was founded by <strong>Queen Georgia T. Nuahn</strong>, a registered nurse in the United States who left Liberia at age 8 and returned with a mission: to ensure no child's potential is limited by their circumstances.
                </p>
                <p class="text-stone-600 mb-4 leading-relaxed">
                    What started as a small effort to support children in her home country has grown into a pan-African movement, providing scholarships, vocational training, and community development programs.
                </p>
                <p class="text-stone-600 leading-relaxed">
                    We believe that education is the most powerful tool for generational change, and we're committed to unlocking the potential of every young person we serve.
                </p>
            </div>
            <div>
                <img src="uploads/founder.png" alt="Our Mission" class="rounded-lg shadow-xl">
            </div>
        </div>
    </div>
</section>

<!-- MISSION/VISION/VALUES -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center p-8 bg-cream rounded-lg">
                <div class="w-16 h-16 bg-navy-900 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-xl font-serif font-bold text-navy-900 mb-3">Our Mission</h3>
                <p class="text-stone-600">To empower African youth through quality education, vocational training, and community development.</p>
            </div>
            <div class="text-center p-8 bg-cream rounded-lg">
                <div class="w-16 h-16 bg-navy-900 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <h3 class="text-xl font-serif font-bold text-navy-900 mb-3">Our Vision</h3>
                <p class="text-stone-600">A future where every African child has access to quality education and opportunity.</p>
            </div>
            <div class="text-center p-8 bg-cream rounded-lg">
                <div class="w-16 h-16 bg-navy-900 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <h3 class="text-xl font-serif font-bold text-navy-900 mb-3">Our Values</h3>
                <p class="text-stone-600">Compassion, integrity, excellence, and commitment to transformative impact.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16 bg-navy-900 text-white text-center">
    <div class="max-w-4xl mx-auto px-6">
        <h2 class="text-2xl font-serif font-bold mb-4">Join Our Mission</h2>
        <p class="text-gray-300 mb-6">Your support can transform the life of a young African scholar.</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="<?php echo BASE_PATH; ?>/donate" class="btn-gold">Donate Now</a>
            <a href="<?php echo BASE_PATH; ?>/contact" class="btn-outline border-white text-white hover:bg-white hover:text-navy-900">Partner With Us</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>