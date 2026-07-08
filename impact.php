<?php
/**
 * Impact Page
 * Professional NGO Design
 */
require_once __DIR__ . '/header.php';
?>

<!-- Hero -->
<section class="relative bg-[#0A1628] py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23D4A72C\" fill-opacity=\"0.4\"%3E%3Ccircle cx=\"30\" cy=\"30\" r=\"4\"/%3E%3Ccircle cx=\"10\" cy=\"10\" r=\"3\"/%3E%3Ccircle cx=\"50\" cy=\"10\" r=\"2\"/%3E%3Ccircle cx=\"10\" cy=\"50\" r=\"2\"/%3E%3Ccircle cx=\"50\" cy=\"50\" r=\"3\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>
    <div class="max-w-7xl mx-auto px-6 relative">
        <span class="inline-block text-xs font-semibold tracking-wider uppercase text-[#D4A72C] mb-3">By the Numbers</span>
        <h1 class="font-serif text-4xl md:text-5xl font-semibold text-white mb-4">Our Impact</h1>
        <p class="text-stone-300 text-lg max-w-2xl">Numbers that represent real lives transformed through education, hope, and opportunity.</p>
    </div>
</section>

<!-- Breadcrumb -->
<div class="bg-white border-b border-stone-200">
    <div class="max-w-7xl mx-auto px-6 py-3">
        <nav class="text-sm">
            <a href="<?php echo BASE_PATH; ?>/" class="text-stone-600 hover:text-[#D4A72C] transition-colors">Home</a>
            <span class="mx-2 text-stone-400">/</span>
            <span class="text-stone-800">Impact</span>
        </nav>
    </div>
</div>

<!-- Stats -->
<section class="py-16 bg-[#FAF8F5]">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-12">
            <span class="text-xs font-semibold tracking-wider uppercase text-[#D4A72C]">The Numbers</span>
            <h2 class="font-serif text-3xl font-semibold text-[#0A1628] mt-2">Impact at a Glance</h2>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <?php
            $statsArray = [
                ['v' => $settings['stats']['scholars'] ?? 50, 'suffix' => '+', 'l' => 'Scholars Sponsored', 'color' => '#0A1628'],
                ['v' => $settings['stats']['countries'] ?? 4, 'suffix' => '', 'l' => 'Countries Reached', 'color' => '#D4A72C'],
                ['v' => $settings['stats']['programs'] ?? 4, 'suffix' => '', 'l' => 'Active Programs', 'color' => '#0A1628'],
                ['v' => $settings['stats']['years'] ?? 6, 'suffix' => '+', 'l' => 'Years of Service', 'color' => '#D4A72C'],
                ['v' => $settings['stats']['lives_touched'] ?? 1000, 'suffix' => '+', 'l' => 'Lives Touched', 'color' => '#0A1628'],
                ['v' => $settings['stats']['employment'] ?? 95, 'suffix' => '%', 'l' => 'Graduate Employment', 'color' => '#D4A72C'],
                ['v' => $settings['stats']['partner_orgs'] ?? 15, 'suffix' => '', 'l' => 'Partner Organizations', 'color' => '#0A1628'],
                ['v' => $settings['stats']['continents'] ?? 3, 'suffix' => '', 'l' => 'Continents Impacted', 'color' => '#D4A72C'],
            ];
            foreach ($statsArray as $idx => $stat):
            ?>
            <div class="bg-white rounded-xl p-6 text-center border border-stone-200 hover:shadow-lg hover:border-[#D4A72C]/30 transition-all">
                <div class="counter text-4xl font-bold text-[#0A1628] mb-2" data-target="<?php echo $stat['v']; ?>">0</div>
                <div class="text-sm text-stone-600 font-medium"><?php echo htmlspecialchars($stat['l']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Sectors -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-12">
            <span class="text-xs font-semibold tracking-wider uppercase text-[#D4A72C]">Sectors</span>
            <h2 class="font-serif text-3xl font-semibold text-[#0A1628] mt-2">Where We Make Impact</h2>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <?php
            $sectors = !empty($settings['sectors']) ? $settings['sectors'] : [
                ['title'=>'Education','desc'=>'Scholars enrolled in top institutions across Africa and Asia.','stat'=>'50+ Scholars Active','bar'=>85,'color'=>'#D4A72C'],
                ['title'=>'Health & Wellness','desc'=>'Future nurses, doctors, and health workers trained to serve.','stat'=>'12 Healthcare Trainees','bar'=>55,'color'=>'#0A1628'],
                ['title'=>'Leadership & Business','desc'=>'Future leaders, lawyers, and entrepreneurs from our programs.','stat'=>'20+ Future Leaders','bar'=>70,'color'=>'#D4A72C'],
                ['title'=>'Vocational Training','desc'=>'Practical skills for youth outside the formal education system.','stat'=>'8+ Trades Taught','bar'=>40,'color'=>'#0A1628'],
            ];
            foreach ($sectors as $s):
            ?>
            <div class="bg-[#FAF8F5] rounded-xl p-6 border border-stone-200 hover:shadow-lg transition-all">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0" style="background:<?php echo $s['color']; ?>20;">
                        <svg class="w-6 h-6" style="color:<?php echo $s['color']; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <h3 class="font-serif text-xl font-semibold text-[#0A1628]"><?php echo $s['title']; ?></h3>
                        <p class="text-sm text-stone-600 mt-1"><?php echo $s['desc']; ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="font-medium" style="color:<?php echo $s['color']; ?>"><?php echo $s['stat']; ?></span>
                        <span class="text-stone-500"><?php echo $s['bar']; ?>%</span>
                    </div>
                    <div class="h-2 bg-stone-200 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-1000" style="width:<?php echo $s['bar']; ?>%;background:<?php echo $s['color']; ?>"></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Testimonials -->
<?php
$testis = [];
try {
    $stmt = $pdo->query("SELECT * FROM testimonials WHERE visible = 1 ORDER BY display_order ASC");
    $testis = $stmt->fetchAll();
} catch (Exception $e) {}
?>
<?php if (!empty($testis)): ?>
<section class="py-16 bg-[#FAF8F5]">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-12">
            <span class="text-xs font-semibold tracking-wider uppercase text-[#D4A72C]">Testimonials</span>
            <h2 class="font-serif text-3xl font-semibold text-[#0A1628] mt-2">Words from Our Scholars</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            <?php foreach ($testis as $t): ?>
            <div class="bg-white rounded-xl p-6 border border-stone-200 hover:shadow-lg transition-all">
                <div class="text-4xl text-[#D4A72C] mb-4">&ldquo;</div>
                <p class="text-stone-700 italic mb-6"><?php echo htmlspecialchars($t['quote']); ?></p>
                <div class="flex items-center gap-3 pt-4 border-t border-stone-100">
                    <?php if (!empty($t['photo'])): ?>
                    <img src="<?php echo htmlspecialchars($t['photo']); ?>" alt="" class="w-10 h-10 rounded-full object-cover">
                    <?php else: ?>
                    <div class="w-10 h-10 rounded-full bg-[#D4A72C] flex items-center justify-center text-[#0A1628] font-bold text-sm flex-shrink-0">
                        <?php echo strtoupper(substr($t['name'], 0, 1)); ?>
                    </div>
                    <?php endif; ?>
                    <div>
                        <div class="font-semibold text-[#0A1628] text-sm"><?php echo htmlspecialchars($t['name']); ?></div>
                        <?php if (!empty($t['role'])): ?>
                        <div class="text-xs text-[#D4A72C]"><?php echo htmlspecialchars($t['role']); ?></div>
                        <?php endif; ?>
                    </div>
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
        <h2 class="font-serif text-3xl font-semibold text-white mb-4">Help Grow Our Impact</h2>
        <p class="text-stone-300 mb-8 max-w-2xl mx-auto">Every dollar donated brings us closer to the next 1,000 lives transformed. Be a part of this incredible journey.</p>
        <a href="<?php echo BASE_PATH; ?>/donate.php" class="inline-flex items-center gap-2 bg-[#D4A72C] text-[#0A1628] px-8 py-4 rounded-lg font-semibold hover:bg-[#c4992a] transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            Donate Now
        </a>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.counter');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.getAttribute('data-target'));
                let current = 0;
                const increment = target / 50;
                const update = () => {
                    current += increment;
                    if (current < target) {
                        counter.textContent = Math.floor(current);
                        requestAnimationFrame(update);
                    } else {
                        counter.textContent = target;
                    }
                };
                update();
                observer.unobserve(counter);
            }
        });
    }, { threshold: 0.5 });
    counters.forEach(c => observer.observe(c));
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>