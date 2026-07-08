<?php
/**
 * Scholar Detail Page
 */
require_once __DIR__ . '/db.php';

$id = isset($_GET['id']) ? trim($_GET['id']) : '';
$scholar = null;

try {
    $stmt = $pdo->prepare("SELECT * FROM beneficiaries WHERE id = ? AND visible = 1");
    $stmt->execute([$id]);
    $scholar = $stmt->fetch();
} catch (Exception $e) {}

if (!$scholar || $id === '') {
    header('Location: ' . BASE_PATH . '/beneficiaries');
    exit;
}

// Fetch other scholars for "Meet More Scholars" section
$others = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM beneficiaries WHERE visible = 1 AND id != ? ORDER BY display_order ASC LIMIT 3");
    $stmt->execute([$id]);
    $others = $stmt->fetchAll();
} catch (Exception $e) {}

require_once __DIR__ . '/header.php';
?>

<!-- BREADCRUMB -->
<div class="bg-white border-b py-3">
    <div class="max-w-7xl mx-auto px-6 text-sm">
        <a href="<?php echo BASE_PATH; ?>/" class="text-gold-500 hover:underline">Home</a>
        <span class="text-gray-400"> / </span>
        <a href="<?php echo BASE_PATH; ?>/beneficiaries" class="text-gold-500 hover:underline">Our Scholars</a>
        <span class="text-gray-400"> / </span>
        <span class="text-navy-900"><?php echo htmlspecialchars($scholar['name']); ?></span>
    </div>
</div>

<!-- SCHOLAR DETAIL -->
<section class="py-20 bg-cream">
    <div class="max-w-5xl mx-auto px-6">

        <a href="<?php echo BASE_PATH; ?>/beneficiaries" class="inline-flex items-center gap-2 text-sm text-stone-600 hover:text-gold-500 mb-8 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Scholars
        </a>

        <div class="grid md:grid-cols-3 gap-12 items-start">

            <!-- Photo & Quick Info -->
            <div class="md:col-span-1">
                <img src="<?php echo htmlspecialchars($scholar['photo'] ?? 'https://images.unsplash.com/photo-1531123897727-8f129e1688ce?w=500&q=80'); ?>"
                     alt="<?php echo htmlspecialchars($scholar['name']); ?>"
                     class="w-full rounded-xl shadow-lg h-auto block mb-6">

                <div class="card p-5 space-y-3 text-sm">
                    <?php if($scholar['study_field']): ?>
                    <div class="flex justify-between gap-2">
                        <span class="text-stone-500 font-medium">Field</span>
                        <span class="text-gold-500 font-semibold text-right"><?php echo htmlspecialchars($scholar['study_field']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($scholar['institution']): ?>
                    <div class="flex justify-between gap-2">
                        <span class="text-stone-500 font-medium">Institution</span>
                        <span class="text-navy-900 font-semibold text-right"><?php echo htmlspecialchars($scholar['institution']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($scholar['destination']): ?>
                    <div class="flex justify-between gap-2">
                        <span class="text-stone-500 font-medium">Studying In</span>
                        <span class="text-navy-900 font-semibold text-right"><?php echo htmlspecialchars($scholar['destination']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($scholar['hometown']): ?>
                    <div class="flex justify-between gap-2">
                        <span class="text-stone-500 font-medium">Hometown</span>
                        <span class="text-navy-900 font-semibold text-right"><?php echo htmlspecialchars($scholar['hometown']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($scholar['age']): ?>
                    <div class="flex justify-between gap-2">
                        <span class="text-stone-500 font-medium">Age</span>
                        <span class="text-navy-900 font-semibold"><?php echo htmlspecialchars($scholar['age']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($scholar['year_sent']): ?>
                    <div class="flex justify-between gap-2">
                        <span class="text-stone-500 font-medium">Year Sent</span>
                        <span class="text-navy-900 font-semibold"><?php echo htmlspecialchars($scholar['year_sent']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if($scholar['program']): ?>
                    <div class="flex justify-between gap-2">
                        <span class="text-stone-500 font-medium">Program</span>
                        <span class="text-navy-900 font-semibold text-right capitalize"><?php echo htmlspecialchars($scholar['program']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="mt-6">
                    <a href="<?php echo BASE_PATH; ?>/donate" class="btn-gold w-full text-center block">Support a Scholar</a>
                </div>
            </div>

            <!-- Story -->
            <div class="md:col-span-2">
                <p class="text-gold-500 font-semibold tracking-widest uppercase text-sm mb-2">Scholar Story</p>
                <h1 class="text-3xl md:text-4xl font-serif font-bold text-navy-900 mb-4"><?php echo htmlspecialchars($scholar['name']); ?></h1>
                <div class="w-16 h-1 bg-gold-500 mb-6"></div>

                <?php if($scholar['quote']): ?>
                <blockquote class="border-l-4 border-gold-500 pl-5 mb-8 bg-amber-50 py-4 pr-4 rounded-r-lg">
                    <p class="text-lg font-serif italic text-navy-900 leading-relaxed">"<?php echo htmlspecialchars($scholar['quote']); ?>"</p>
                </blockquote>
                <?php endif; ?>

                <div class="text-stone-600 leading-relaxed space-y-4">
                    <?php
                    $story = $scholar['full_story'] ?: $scholar['short_story'] ?? '';
                    foreach (explode("\n", $story) as $para):
                        $para = trim($para);
                        if ($para): ?>
                        <p><?php echo htmlspecialchars($para); ?></p>
                    <?php endif; endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MEET MORE SCHOLARS -->
<?php if (!empty($others)): ?>
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-2xl font-serif font-bold text-navy-900 mb-8">Meet More Scholars</h2>
        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach($others as $o): ?>
            <div class="card overflow-hidden">
                <div class="overflow-hidden">
                    <img src="<?php echo htmlspecialchars($o['photo'] ?? 'https://images.unsplash.com/photo-1531123897727-8f129e1688ce?w=500&q=80'); ?>"
                         alt="<?php echo htmlspecialchars($o['name']); ?>"
                         class="w-full h-auto block">
                </div>
                <div class="p-5">
                    <h3 class="font-serif font-bold text-navy-900 mb-1"><?php echo htmlspecialchars($o['name']); ?></h3>
                    <p class="text-gold-500 text-sm font-semibold mb-3"><?php echo htmlspecialchars($o['study_field']); ?></p>
                    <a href="<?php echo BASE_PATH; ?>/beneficiary_view?id=<?php echo urlencode($o['id']); ?>" class="text-gold-500 font-semibold text-sm hover:text-gold-600">Read More →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="py-16 bg-gold-500">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="text-2xl font-serif font-bold text-navy-900 mb-4">Help More Scholars Like <?php echo htmlspecialchars(explode(' ', $scholar['name'])[0]); ?></h2>
        <p class="text-navy-800 mb-6">Your donation gives a young African the chance to change their life through education.</p>
        <a href="<?php echo BASE_PATH; ?>/donate" class="bg-navy-900 text-white px-8 py-3 rounded font-semibold hover:bg-navy-800 transition">Donate Now</a>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>
