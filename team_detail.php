<?php
/**
 * Team Member Detail Page
 * Professional NGO Design
 */
require_once __DIR__ . '/db.php';

$id = isset($_GET['id']) ? trim($_GET['id']) : '';
$member = null;

try {
    $stmt = $pdo->prepare("SELECT * FROM team_members WHERE id = ? AND visible = 1");
    $stmt->execute([$id]);
    $member = $stmt->fetch();
} catch (Exception $e) {}

if (!$member || $id === '') {
    header('Location: ' . BASE_PATH . '/team.php');
    exit;
}

require_once __DIR__ . '/header.php';
?>

<!-- Breadcrumb -->
<div class="bg-white border-b border-stone-200">
    <div class="max-w-7xl mx-auto px-6 py-3">
        <nav class="text-sm">
            <a href="<?php echo BASE_PATH; ?>/" class="text-stone-600 hover:text-[#D4A72C] transition-colors">Home</a>
            <span class="mx-2 text-stone-400">/</span>
            <a href="<?php echo BASE_PATH; ?>/team.php" class="text-stone-600 hover:text-[#D4A72C] transition-colors">Team</a>
            <span class="mx-2 text-stone-400">/</span>
            <span class="text-stone-800"><?php echo htmlspecialchars($member['name']); ?></span>
        </nav>
    </div>
</div>

<!-- Hero Banner with Photo -->
<div class="relative">
    <div class="h-56 bg-gradient-to-r from-[#0A1628] to-[#1a2d4a] w-full"></div>
    <div class="absolute left-1/2 -translate-x-1/2 bottom-0 translate-y-1/2">
        <?php if (!empty($member['photo'])): ?>
        <img src="<?php echo htmlspecialchars($member['photo']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>"
             class="w-44 h-44 md:w-56 md:h-56 rounded-full object-cover object-top shadow-xl border-4 border-white">
        <?php else: ?>
        <div class="w-44 h-44 md:w-56 md:h-56 rounded-full bg-stone-200 shadow-xl border-4 border-white flex items-center justify-center">
            <svg class="w-20 h-20 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Member Detail -->
<section class="pt-36 pb-16 bg-[#FAF8F5]">
    <div class="max-w-3xl mx-auto px-6">

        <!-- Name & Role -->
        <div class="text-center mb-10">
            <span class="inline-block text-xs font-semibold px-3 py-1 bg-[#D4A72C]/10 text-[#D4A72C] rounded-full mb-3"><?php echo htmlspecialchars($member['role']); ?></span>
            <h1 class="font-serif text-3xl md:text-4xl font-semibold text-[#0A1628]"><?php echo htmlspecialchars($member['name']); ?></h1>
        </div>

        <!-- Bio -->
        <div class="bg-white rounded-2xl border border-stone-200 shadow-lg p-8">
            <?php $paragraphs = array_filter(array_map('trim', explode("\n", $member['bio'] ?? ''))); ?>
            <div class="space-y-4">
                <?php foreach ($paragraphs as $para): ?>
                <p class="text-stone-700 leading-relaxed"><?php echo htmlspecialchars($para); ?></p>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mt-8 flex justify-center">
            <a href="<?php echo BASE_PATH; ?>/team.php" class="inline-flex items-center gap-2 text-sm text-stone-600 hover:text-[#D4A72C] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Team
            </a>
        </div>
    </div>
</section>

<!-- Other Team Members -->
<?php
$others = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM team_members WHERE visible = 1 AND id != ? ORDER BY display_order ASC LIMIT 3");
    $stmt->execute([$id]);
    $others = $stmt->fetchAll();
} catch (Exception $e) {}
?>
<?php if (!empty($others)): ?>
<section class="py-16 bg-white">
    <div class="max-w-5xl mx-auto px-6">
        <h2 class="font-serif text-2xl font-bold text-[#0A1628] mb-8 text-center">Meet the Rest of the Team</h2>
        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach ($others as $o): ?>
            <div class="bg-[#FAF8F5] rounded-xl overflow-hidden border border-stone-200 hover:shadow-lg transition-all text-center group">
                <div class="h-56 overflow-hidden">
                    <?php if (!empty($o['photo'])): ?>
                    <img src="<?php echo htmlspecialchars($o['photo']); ?>" alt="<?php echo htmlspecialchars($o['name']); ?>"
                         class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500">
                    <?php else: ?>
                    <div class="w-full h-full bg-stone-200 flex items-center justify-center">
                        <svg class="w-16 h-16 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="p-5">
                    <h3 class="font-serif font-bold text-[#0A1628] mb-1"><?php echo htmlspecialchars($o['name']); ?></h3>
                    <p class="text-[#D4A72C] text-sm font-semibold mb-3"><?php echo htmlspecialchars($o['role']); ?></p>
                    <a href="<?php echo BASE_PATH; ?>/team_detail?id=<?php echo urlencode($o['id']); ?>" class="text-[#D4A72C] font-semibold text-sm hover:text-[#c4992a]">View Profile →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>