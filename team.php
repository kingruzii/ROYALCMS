<?php
/**
 * Team Page - Professional NGO Layout
 */
require_once __DIR__ . '/header.php';

$team = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM team_members WHERE visible = 1 ORDER BY display_order ASC");
    $stmt->execute();
    $team = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- PAGE HERO -->
<section class="bg-navy-900 text-white py-20">
    <div class="max-w-7xl mx-auto px-6">
        <p class="text-gold-400 font-semibold tracking-widest uppercase text-sm mb-2">The People</p>
        <h1 class="text-4xl md:text-5xl font-serif font-bold mb-4">Meet Our Team</h1>
        <p class="text-xl text-gray-300 max-w-2xl">The passionate, dedicated individuals who bring RVIF's mission to life every single day.</p>
    </div>
</section>

<!-- BREADCRUMB -->
<div class="bg-white border-b py-3">
    <div class="max-w-7xl mx-auto px-6 text-sm">
        <a href="<?php echo BASE_PATH; ?>/" class="text-gold-500 hover:underline">Home</a> <span class="text-gray-400">/</span> <span class="text-navy-900">Our Team</span>
    </div>
</div>

<!-- TEAM GRID -->
<section class="py-20 bg-cream">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-14">
            <p class="text-gold-500 font-semibold tracking-widest uppercase text-sm mb-2">Leadership</p>
            <h2 class="text-3xl font-serif font-bold text-navy-900">Our Leadership Team</h2>
            <p class="text-stone-500 mt-4 max-w-xl mx-auto">Driven by purpose, guided by values, and united by a shared vision for Africa's future.</p>
        </div>
        
        <?php if(!empty($team)): ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach($team as $m): ?>
            <div class="card overflow-hidden text-center">
                <div class="h-72 bg-gray-100 flex items-center justify-center">
                    <?php if(!empty($m['photo'])): ?>
                    <img src="<?php echo htmlspecialchars($m['photo']); ?>" alt="<?php echo htmlspecialchars($m['name']); ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                    <svg class="w-24 h-24 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <?php endif; ?>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-serif font-bold text-navy-900 mb-1"><?php echo htmlspecialchars($m['name']); ?></h3>
                    <p class="text-gold-500 font-semibold text-sm mb-3"><?php echo htmlspecialchars($m['role']); ?></p>
                    <p class="text-stone-500 text-sm mb-4"><?php echo htmlspecialchars(mb_substr($m['bio'] ?? '', 0, 100)); ?>...</p>
                    <a href="<?php echo BASE_PATH; ?>/team_detail?id=<?php echo urlencode($m['id']); ?>" class="inline-flex items-center gap-1 text-gold-500 font-semibold text-sm hover:text-gold-600">Read More →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php $placeholder = [
                ['name' => 'Dr. James Kofi', 'role' => 'Founder & Executive Director'],
                ['name' => 'Sarah Amara', 'role' => 'Programs Director'],
                ['name' => 'Emmanuel Lartey', 'role' => 'Partnerships & Fundraising'],
            ]; foreach($placeholder as $p): ?>
            <div class="card overflow-hidden text-center">
                <div class="h-72 bg-gray-100 flex items-center justify-center">
                    <svg class="w-24 h-24 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-serif font-bold text-navy-900 mb-1"><?php echo $p['name']; ?></h3>
                    <p class="text-gold-500 font-semibold text-sm mb-3"><?php echo $p['role']; ?></p>
                    <p class="text-stone-500 text-sm mb-4">Passionate leader dedicated to transforming lives.</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- VALUES -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-10">
            <p class="text-gold-500 font-semibold tracking-widest uppercase text-sm mb-2">Culture</p>
            <h2 class="text-2xl font-serif font-bold text-navy-900">What Drives Us</h2>
        </div>
        <div class="flex flex-wrap justify-center gap-3">
            <?php $values = ['Excellence', 'Compassion', 'Integrity', 'Collaboration', 'Pan-Africanism', 'Sustainability', 'Accountability']; foreach($values as $v): ?>
            <span class="px-5 py-2 bg-cream text-navy-900 rounded-full text-sm font-semibold"><?php echo $v; ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16 bg-navy-900 text-white text-center">
    <div class="max-w-4xl mx-auto px-6">
        <h2 class="text-2xl font-serif font-bold mb-4">Join Our Team</h2>
        <p class="text-gray-300 mb-6">We're always looking for passionate individuals to join our mission.</p>
        <a href="<?php echo BASE_PATH; ?>/contact" class="btn-gold">Get in Touch</a>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>