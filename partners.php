<?php
/**
 * Partners Page
 * Professional NGO Design
 */
require_once __DIR__ . '/header.php';

$partners = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM partners ORDER BY display_order ASC");
    $stmt->execute();
    $partners = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- Hero -->
<section class="relative bg-[#0A1628] py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23D4A72C\" fill-opacity=\"0.4\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>
    <div class="max-w-7xl mx-auto px-6 relative">
        <span class="inline-block text-xs font-semibold tracking-wider uppercase text-[#D4A72C] mb-3">Together We Thrive</span>
        <h1 class="font-serif text-4xl md:text-5xl font-semibold text-white mb-4">Our Partners</h1>
        <p class="text-stone-300 text-lg max-w-2xl">None of this work is possible alone. Together with our partners, we multiply our impact across Africa and beyond.</p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="bg-white border-b border-stone-200">
    <div class="max-w-7xl mx-auto px-6 py-3">
        <nav class="text-sm">
            <a href="<?php echo BASE_PATH; ?>/" class="text-stone-600 hover:text-[#D4A72C] transition-colors">Home</a>
            <span class="mx-2 text-stone-400">/</span>
            <span class="text-stone-800">Partners</span>
        </nav>
    </div>
</div>

<!-- Partners Grid -->
<section class="py-16 bg-[#FAF8F5]">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-12">
            <span class="text-xs font-semibold tracking-wider uppercase text-[#D4A72C]">Institutions & Organizations</span>
            <h2 class="font-serif text-3xl font-semibold text-[#0A1628] mt-2">Who We Work With</h2>
            <p class="text-stone-600 mt-3 max-w-2xl mx-auto">Our partners are leading institutions, governments, and organizations committed to education and youth development.</p>
        </div>

        <?php if (!empty($partners)): ?>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($partners as $p): ?>
            <div class="bg-white rounded-xl p-6 text-center border border-stone-200 hover:shadow-xl hover:border-[#D4A72C]/30 transition-all group">
                <div class="w-14 h-14 mx-auto mb-4 rounded-xl flex items-center justify-center" style="background:<?php echo htmlspecialchars($p['color']); ?>15;">
                    <svg class="w-7 h-7" fill="none" stroke="<?php echo htmlspecialchars($p['color']); ?>" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="font-semibold text-[#0A1628] mb-3 group-hover:text-[#D4A72C] transition-colors"><?php echo htmlspecialchars($p['name']); ?></h3>
                <div class="flex flex-wrap justify-center gap-2">
                    <span class="text-xs px-3 py-1 bg-[#FAF8F5] text-[#0A1628] rounded-full"><?php echo htmlspecialchars($p['country']); ?></span>
                    <span class="text-xs px-3 py-1 bg-amber-50 text-amber-800 rounded-full"><?php echo htmlspecialchars($p['category']); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-16">
            <p class="text-stone-500">No partners added yet. Check back soon!</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Why Partner -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="text-xs font-semibold tracking-wider uppercase text-[#D4A72C]">Partnership Benefits</span>
                <h2 class="font-serif text-3xl font-semibold text-[#0A1628] mt-2 mb-6">Why Partner With Us?</h2>
                <div class="w-12 h-0.5 bg-[#D4A72C] mb-6"></div>
                <p class="text-stone-600 leading-relaxed mb-8">Partnering with RVIF is more than a corporate social responsibility checkbox — it's a genuine investment in Africa's human capital and future prosperity.</p>
                
                <div class="space-y-4">
                    <?php 
                    $benefits = [
                        ['icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'text'=>'Direct Impact - see exactly where your support goes'],
                        ['icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'text'=>'Measurable outcomes with transparent reporting'],
                        ['icon'=>'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'text'=>'International reach across Africa and Asia'],
                        ['icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'text'=>'Join a network of change-makers and thought leaders'],
                    ];
                    foreach ($benefits as $b): ?>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-[#D4A72C]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-[#D4A72C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $b['icon']; ?>"/></svg>
                        </div>
                        <span class="text-stone-700"><?php echo $b['text']; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-[#0A1628] to-[#1a2d4a] rounded-2xl p-8 text-white">
                <h3 class="font-serif text-2xl font-semibold mb-4">Become a Partner Today</h3>
                <p class="text-stone-300 mb-6">Whether you're a university, corporation, faith organization, or individual philanthropist — we have a partnership model that works for you.</p>
                
                <div class="space-y-3 mb-8">
                    <div class="bg-white/10 rounded-lg p-4 border border-white/10">
                        <div class="font-semibold mb-1">🌱 Community Partner</div>
                        <div class="text-sm text-stone-400">For NGOs, churches, and community groups</div>
                    </div>
                    <div class="bg-white/10 rounded-lg p-4 border border-white/10">
                        <div class="font-semibold mb-1">🌟 Corporate Partner</div>
                        <div class="text-sm text-stone-400">For businesses seeking CSR and visibility</div>
                    </div>
                    <div class="bg-white/10 rounded-lg p-4 border border-white/10">
                        <div class="font-semibold mb-1">👑 Strategic Partner</div>
                        <div class="text-sm text-stone-400">For universities and international organizations</div>
                    </div>
                </div>
                
                <a href="<?php echo BASE_PATH; ?>/contact.php" class="flex items-center justify-center gap-2 bg-[#D4A72C] text-[#0A1628] py-3 rounded-lg font-semibold hover:bg-[#c4992a] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Reach Out
                </a>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="bg-[#0A1628] py-16">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="font-serif text-3xl font-semibold text-white mb-4">Partner With Purpose</h2>
        <p class="text-stone-300 mb-8 max-w-2xl mx-auto">Contact us to explore a partnership that aligns with your organization's values and impact goals.</p>
        <a href="<?php echo BASE_PATH; ?>/contact.php" class="inline-flex items-center gap-2 border border-white/30 text-white px-8 py-4 rounded-lg font-semibold hover:bg-white/10 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Start a Conversation
        </a>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>