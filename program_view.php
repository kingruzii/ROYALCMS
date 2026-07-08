<?php
/**
 * Program Detail Page
 */
require_once __DIR__ . '/db.php';

$id = isset($_GET['id']) ? trim($_GET['id']) : '';
$program = null;

try {
    $stmt = $pdo->prepare("SELECT * FROM programs WHERE id = ? AND visible = 1");
    $stmt->execute([$id]);
    $program = $stmt->fetch();
} catch (Exception $e) {}

if (!$program || $id === '') {
    header('Location: ' . BASE_PATH . '/programs');
    exit;
}

// Other programs
$others = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM programs WHERE visible = 1 AND id != ? ORDER BY display_order ASC LIMIT 3");
    $stmt->execute([$id]);
    $others = $stmt->fetchAll();
} catch (Exception $e) {}

require_once __DIR__ . '/header.php';
?>

<!-- Hero -->
<section class="relative bg-[#0A1628] py-20 overflow-hidden">
    <div class="absolute inset-0">
        <img src="<?php echo htmlspecialchars($program['image'] ?? 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=1600&q=80'); ?>" alt="" class="w-full h-full object-cover opacity-20">
    </div>
    <div class="max-w-7xl mx-auto px-6 relative">
        <span class="inline-block text-xs font-semibold tracking-wider uppercase text-[#D4A72C] mb-3">Our Programs</span>
        <h1 class="font-serif text-4xl md:text-5xl font-semibold text-white mb-4"><?php echo htmlspecialchars($program['title']); ?></h1>
        <span class="text-xs font-semibold px-3 py-1 bg-[#D4A72C] text-[#0A1628] rounded-full">Active Program</span>
    </div>
</section>

<!-- Breadcrumb -->
<div class="bg-white border-b border-stone-200">
    <div class="max-w-7xl mx-auto px-6 py-3 text-sm">
        <a href="<?php echo BASE_PATH; ?>/" class="text-stone-600 hover:text-[#D4A72C] transition-colors">Home</a>
        <span class="mx-2 text-stone-400">/</span>
        <a href="<?php echo BASE_PATH; ?>/programs" class="text-stone-600 hover:text-[#D4A72C] transition-colors">Programs</a>
        <span class="mx-2 text-stone-400">/</span>
        <span class="text-stone-800"><?php echo htmlspecialchars($program['title']); ?></span>
    </div>
</div>

<!-- Content -->
<section class="py-20 bg-[#FAF8F5]">
    <div class="max-w-5xl mx-auto px-6">

        <a href="<?php echo BASE_PATH; ?>/programs" class="inline-flex items-center gap-2 text-sm text-stone-600 hover:text-[#D4A72C] mb-8 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Programs
        </a>

        <div class="grid md:grid-cols-3 gap-12 items-start">

            <!-- Image -->
            <div class="md:col-span-1">
                <img src="<?php echo htmlspecialchars($program['image'] ?? 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=600&q=80'); ?>"
                     alt="<?php echo htmlspecialchars($program['title']); ?>"
                     class="w-full rounded-xl shadow-lg object-cover aspect-square mb-6">

                <a href="<?php echo BASE_PATH; ?>/donate" class="block text-center bg-[#D4A72C] text-[#0A1628] py-3 rounded-lg font-semibold hover:bg-[#c4992a] transition-colors">
                    Support This Program
                </a>
                <a href="<?php echo BASE_PATH; ?>/contact" class="block text-center border border-[#0A1628] text-[#0A1628] py-3 rounded-lg font-medium hover:bg-[#0A1628] hover:text-white transition-colors mt-3">
                    Get Involved
                </a>
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <p class="text-[#D4A72C] font-semibold tracking-widest uppercase text-sm mb-2">Program Overview</p>
                <h2 class="font-serif text-3xl font-bold text-[#0A1628] mb-4"><?php echo htmlspecialchars($program['title']); ?></h2>
                <div class="w-16 h-1 bg-[#D4A72C] mb-6"></div>
                <div class="text-stone-600 leading-relaxed space-y-4">
                    <?php foreach (explode("\n", $program['description'] ?? '') as $para):
                        $para = trim($para);
                        if ($para): ?>
                        <p><?php echo htmlspecialchars($para); ?></p>
                    <?php endif; endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Other Programs -->
<?php if (!empty($others)): ?>
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <h2 class="font-serif text-2xl font-bold text-[#0A1628] mb-8">Other Programs</h2>
        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach ($others as $o): ?>
            <div class="bg-white rounded-xl overflow-hidden border border-stone-200 hover:shadow-lg transition-all group">
                <div class="h-44 overflow-hidden">
                    <img src="<?php echo htmlspecialchars($o['image'] ?? 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=600&q=80'); ?>"
                         alt="<?php echo htmlspecialchars($o['title']); ?>"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="p-5">
                    <h3 class="font-serif font-semibold text-[#0A1628] mb-2 group-hover:text-[#D4A72C] transition-colors"><?php echo htmlspecialchars($o['title']); ?></h3>
                    <p class="text-stone-500 text-sm mb-3"><?php echo htmlspecialchars(mb_substr($o['description'], 0, 80)); ?>...</p>
                    <a href="<?php echo BASE_PATH; ?>/program_view?id=<?php echo urlencode($o['id']); ?>" class="text-[#D4A72C] font-semibold text-sm hover:text-[#c4992a]">Read More →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="bg-[#0A1628] py-16">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="font-serif text-3xl font-semibold text-white mb-4">Support <?php echo htmlspecialchars($program['title']); ?></h2>
        <p class="text-stone-300 mb-8">Your donation directly funds this program and transforms lives across Africa.</p>
        <a href="<?php echo BASE_PATH; ?>/donate" class="inline-flex items-center gap-2 bg-[#D4A72C] text-[#0A1628] px-8 py-4 rounded-lg font-semibold hover:bg-[#c4992a] transition-colors">
            Make a Donation →
        </a>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>
