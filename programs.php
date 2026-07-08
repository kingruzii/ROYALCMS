<?php
/**
 * Programs Page
 * Professional NGO Design
 */
require_once __DIR__ . '/header.php';

$programs = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM programs WHERE visible = 1 ORDER BY display_order ASC");
    $stmt->execute();
    $programs = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- Hero -->
<section class="relative bg-[#0A1628] py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23D4A72C\" fill-opacity=\"0.4\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>
    <div class="max-w-7xl mx-auto px-6 relative">
        <span class="inline-block text-xs font-semibold tracking-wider uppercase text-[#D4A72C] mb-3">Our Initiatives</span>
        <h1 class="font-serif text-4xl md:text-5xl font-semibold text-white mb-4">Our Programs</h1>
        <p class="text-stone-300 text-lg max-w-2xl">Transformative initiatives that unlock potential and create lasting change across Africa.</p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="bg-white border-b border-stone-200">
    <div class="max-w-7xl mx-auto px-6 py-3">
        <nav class="text-sm">
            <a href="<?php echo BASE_PATH; ?>/" class="text-stone-600 hover:text-[#D4A72C] transition-colors">Home</a>
            <span class="mx-2 text-stone-400">/</span>
            <span class="text-stone-800">Programs</span>
        </nav>
    </div>
</div>

<!-- Programs Grid -->
<section class="py-16 bg-[#FAF8F5]">
    <div class="max-w-7xl mx-auto px-6">
        <?php if (!empty($programs)): ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($programs as $p): ?>
            <div class="bg-white rounded-xl overflow-hidden border border-stone-200 hover:shadow-xl hover:border-[#D4A72C]/30 transition-all duration-300 group">
                <div class="relative h-48 overflow-hidden">
                    <img src="<?php echo htmlspecialchars($p['image'] ?? 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=600&q=80'); ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-4 left-4">
                        <span class="text-xs font-semibold px-3 py-1 bg-[#D4A72C] text-[#0A1628] rounded-full">Active Program</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="font-serif text-xl font-semibold text-[#0A1628] mb-2 group-hover:text-[#D4A72C] transition-colors"><?php echo htmlspecialchars($p['title']); ?></h3>
                    <div class="w-12 h-0.5 bg-[#D4A72C] mb-4"></div>
                    <p class="text-stone-600 text-sm leading-relaxed mb-4"><?php echo htmlspecialchars(mb_substr($p['description'], 0, 120)); ?>...</p>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="text-xs px-3 py-1 bg-stone-100 text-stone-600 rounded-full">Open for Applications</span>
                        <a href="<?php echo BASE_PATH; ?>/program_view?id=<?php echo urlencode($p['id']); ?>" class="text-[#D4A72C] font-semibold text-sm hover:text-[#c4992a] transition-colors">Read More →</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-16">
            <p class="text-stone-500">No active programs found. Check back soon!</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- How We Work -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-12">
            <span class="text-xs font-semibold tracking-wider uppercase text-[#D4A72C]">The Process</span>
            <h2 class="font-serif text-3xl font-semibold text-[#0A1628] mt-2">How We Work</h2>
        </div>
        
        <div class="grid md:grid-cols-5 gap-6">
            <?php 
            $steps = [
                ['num'=>1, 'title'=>'Apply', 'desc'=>'Deserving students submit applications through our community network.', 'icon'=>'M22 10v6M2 10l10-5 10 5-10 5zM6 12v5c3 3 9 3 12 0v-5'],
                ['num'=>2, 'title'=>'Review', 'desc'=>'Our team evaluates academic merit, financial need, and potential.', 'icon'=>'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
                ['num'=>3, 'title'=>'Select', 'desc'=>'Top candidates are chosen and onboarded into our scholar community.', 'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['num'=>4, 'title'=>'Support', 'desc'=>'Scholars receive funding, mentorship, and holistic support throughout.', 'icon'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                ['num'=>5, 'title'=>'Grow', 'desc'=>'Graduates become leaders and give back to their communities.', 'icon'=>'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6']
            ];
            foreach ($steps as $step): ?>
            <div class="text-center">
                <div class="relative w-16 h-16 mx-auto mb-4 bg-[#FAF8F5] rounded-full flex items-center justify-center border-2 border-[#D4A72C]">
                    <span class="absolute -top-1 -right-1 w-6 h-6 bg-[#D4A72C] text-white text-xs font-bold rounded-full flex items-center justify-center"><?php echo $step['num']; ?></span>
                    <svg class="w-6 h-6 text-[#D4A72C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $step['icon']; ?>"/></svg>
                </div>
                <h3 class="font-semibold text-[#0A1628] mb-2"><?php echo $step['title']; ?></h3>
                <p class="text-sm text-stone-600"><?php echo $step['desc']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="bg-[#0A1628] py-16">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="font-serif text-3xl font-semibold text-white mb-4">Support Our Programs</h2>
        <p class="text-stone-300 mb-8 max-w-2xl mx-auto">Your donation directly funds scholarships, vocational training, and community programs across Africa.</p>
        <a href="<?php echo BASE_PATH; ?>/donate.php" class="inline-flex items-center gap-2 bg-[#D4A72C] text-[#0A1628] px-8 py-4 rounded-lg font-semibold hover:bg-[#c4992a] transition-colors">
            Make a Donation →
        </a>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>