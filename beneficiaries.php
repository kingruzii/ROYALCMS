<?php
/**
 * Beneficiaries Page - Professional NGO Layout
 */
require_once __DIR__ . '/header.php';

$beneficiaries = [];
try {
    $stmt = $pdo->query("SELECT * FROM beneficiaries WHERE visible = 1 ORDER BY display_order ASC");
    $beneficiaries = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- PAGE HERO -->
<section class="bg-navy-900 text-white py-20">
    <div class="max-w-7xl mx-auto px-6">
        <p class="text-gold-400 font-semibold tracking-widest uppercase text-sm mb-2">Our Scholars</p>
        <h1 class="text-4xl md:text-5xl font-serif font-bold mb-4">Meet Our Scholars</h1>
        <p class="text-xl text-gray-300 max-w-2xl">Young brilliant minds from across Africa whose lives have been transformed through education.</p>
    </div>
</section>

<!-- BREADCRUMB -->
<div class="bg-white border-b py-3">
    <div class="max-w-7xl mx-auto px-6 text-sm">
        <a href="<?php echo BASE_PATH; ?>/" class="text-gold-500 hover:underline">Home</a> <span class="text-gray-400">/</span> <span class="text-navy-900">Our Scholars</span>
    </div>
</div>

<!-- SCHOLARS GRID -->
<section class="py-20 bg-cream">
    <div class="max-w-7xl mx-auto px-6">
        <?php if(!empty($beneficiaries)): ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach($beneficiaries as $b): ?>
            <div class="card overflow-hidden flex flex-col">
                <div class="h-64 overflow-hidden flex-shrink-0">
                    <img src="<?php echo htmlspecialchars($b['photo'] ?? 'https://images.unsplash.com/photo-1531123897727-8f129e1688ce?w=500&q=80'); ?>" alt="<?php echo htmlspecialchars($b['name']); ?>" class="w-full h-full object-cover object-top">
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-serif font-bold text-navy-900 mb-1"><?php echo htmlspecialchars($b['name']); ?></h3>
                    <p class="text-gold-500 font-semibold text-sm mb-2"><?php echo htmlspecialchars($b['study_field']); ?></p>
                    <div class="flex items-center gap-2 text-xs text-stone-500 mb-3">
                        <span>📍 <?php echo htmlspecialchars($b['hometown']); ?></span>
                        <span>•</span>
                        <span><?php echo htmlspecialchars($b['institution']); ?></span>
                    </div>
                    <p class="text-stone-500 text-sm mb-4"><?php echo htmlspecialchars(mb_substr($b['short_story'] ?? '', 0, 100)); ?>...</p>
                    <a href="<?php echo BASE_PATH; ?>/beneficiary_view?id=<?php echo urlencode($b['id']); ?>" class="text-gold-500 font-semibold text-sm hover:text-gold-600">Read More →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php $placeholder = [
                ['name'=>'Amara K.','field'=>'Medicine','location'=>'Rwanda','institution'=>'University of Kigali','story'=>'RVIF changed my life completely. Without their scholarship I would never have been able to study medicine.'],
                ['name'=>'Emmanuel T.','field'=>'Law','location'=>'Liberia','institution'=>'University of Liberia','story'=>'The mentorship I received from RVIF went beyond academics. They helped me believe in myself.'],
                ['name'=>'Fatima J.','field'=>'Engineering','location'=>'Ghana','institution'=>'KNUST','story'=>'Being an RVIF scholar opened doors I never knew existed. Today I am building the Africa I always dreamed of.'],
            ]; foreach($placeholder as $p): ?>
            <div class="card overflow-hidden">
                <div class="h-56 bg-gray-200 flex items-center justify-center">
                    <svg class="w-20 h-20 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-serif font-bold text-navy-900 mb-1"><?php echo $p['name']; ?></h3>
                    <p class="text-gold-500 font-semibold text-sm mb-2"><?php echo $p['field']; ?></p>
                    <div class="flex items-center gap-2 text-xs text-stone-500 mb-3">
                        <span>📍 <?php echo $p['location']; ?></span>
                        <span>•</span>
                        <span><?php echo $p['institution']; ?></span>
                    </div>
                    <p class="text-stone-500 text-sm"><?php echo $p['story']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section class="py-16 bg-gold-500">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="text-2xl font-serif font-bold text-navy-900 mb-4">Help More Scholars</h2>
        <p class="text-navy-800 mb-6">Your donation can help more young people access education.</p>
        <a href="<?php echo BASE_PATH; ?>/donate" class="bg-navy-900 text-white px-8 py-3 rounded font-semibold hover:bg-navy-800 transition">Donate Now</a>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>