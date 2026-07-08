<?php
/**
 * Donate Page - Professional NGO Layout
 */
require_once __DIR__ . '/header.php';

$success = $error = '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

if ($status === 'success' && !empty($_GET['session_id'])) {
    $success = ['amount' => 50, 'name' => 'Friend'];
} elseif ($status === 'cancelled') {
    $error = 'Your donation was cancelled. You can try again any time.';
}
?>

<!-- PAGE HERO -->
<section class="bg-navy-900 text-white py-20">
    <div class="max-w-7xl mx-auto px-6 text-center">
        <p class="text-gold-400 font-semibold tracking-widest uppercase text-sm mb-2">Make a Difference</p>
        <h1 class="text-4xl md:text-5xl font-serif font-bold mb-4">Give the Gift of Education</h1>
        <p class="text-xl text-gray-300 max-w-2xl mx-auto">Every dollar you contribute opens doors for a young African scholar. Your generosity transforms lives.</p>
    </div>
</section>

<!-- BREADCRUMB -->
<div class="bg-white border-b py-3">
    <div class="max-w-7xl mx-auto px-6 text-sm">
        <a href="<?php echo BASE_PATH; ?>/" class="text-gold-500 hover:underline">Home</a> <span class="text-gray-400">/</span> <span class="text-navy-900">Donate</span>
    </div>
</div>

<?php if($success): ?>
<!-- SUCCESS -->
<section class="py-20 bg-cream">
    <div class="max-w-2xl mx-auto px-6 text-center">
        <div class="bg-white p-12 rounded-lg shadow-xl">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2 class="text-2xl font-serif font-bold text-navy-900 mb-2">Thank You!</h2>
            <p class="text-stone-500 mb-4">Your generous gift has been received and is already changing lives.</p>
            <div class="flex justify-center gap-4 mt-8">
                <a href="/" class="btn-gold">Return Home</a>
                <a href="<?php echo BASE_PATH; ?>/beneficiaries" class="btn-outline">Meet Our Scholars</a>
            </div>
        </div>
    </div>
</section>
<?php else: ?>

<!-- DONATION SECTION -->
<section class="py-20 bg-cream">
    <div class="max-w-7xl mx-auto px-6">
        <?php if($error): ?>
        <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 px-6 py-4 rounded mb-8 max-w-4xl mx-auto"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="text-center mb-12">
            <p class="text-gold-500 font-semibold tracking-widest uppercase text-sm mb-2">Choose Your Impact</p>
            <h2 class="text-3xl font-serif font-bold text-navy-900">Every Gift Counts</h2>
            <p class="text-stone-500 mt-4 max-w-xl mx-auto">Select a giving level below or enter your own amount</p>
        </div>
        
        <!-- Amount Tiers -->
        <div class="grid md:grid-cols-3 lg:grid-cols-6 gap-4 mb-12 max-w-5xl mx-auto">
            <?php $tiers = [
                ['amount' => 25, 'title' => 'Supporter', 'impact' => 'Provides textbooks for one scholar'],
                ['amount' => 50, 'title' => 'Friend', 'impact' => 'Covers examination fees', 'popular' => true],
                ['amount' => 100, 'title' => 'Champion', 'impact' => 'Sponsors a month of meals'],
                ['amount' => 250, 'title' => 'Patron', 'impact' => 'Contributes to tuition'],
                ['amount' => 500, 'title' => 'Visionary', 'impact' => 'Funds a full semester'],
                ['amount' => 1000, 'title' => 'Guardian', 'impact' => 'Sponsors a full year'],
            ]; foreach($tiers as $tier): ?>
            <div class="tier-card bg-white rounded-lg p-5 text-center cursor-pointer hover:shadow-lg transition border-2 <?php echo ($tier['amount'] ?? '') === 50 ? 'border-gold-500' : 'border-transparent'; ?>" data-amount="<?php echo $tier['amount']; ?>">
                <div class="font-serif text-2xl font-bold text-navy-900">$<?php echo $tier['amount']; ?></div>
                <div class="text-xs text-gold-500 font-semibold uppercase mt-1"><?php echo $tier['title']; ?></div>
                <?php if(!empty($tier['popular'])): ?>
                <div class="text-xs bg-gold-500 text-navy-900 py-0.5 rounded mt-2 font-semibold">Most Popular</div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Form -->
        <div class="max-w-4xl mx-auto">
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h3 class="text-xl font-serif font-bold text-navy-900 mb-6">Your Information</h3>
                    <form id="donate-form" method="POST" action="<?php echo BASE_PATH; ?>/checkout" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-navy-900 mb-1">Donation Amount (USD) *</label>
                            <input type="number" id="amount-input" name="amount" value="50" min="1" class="w-full px-4 py-3 border border-stone-300 rounded focus:border-gold-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-900 mb-1">Your Name</label>
                            <input type="text" name="donor_name" class="w-full px-4 py-3 border border-stone-300 rounded focus:border-gold-500 focus:outline-none" placeholder="John Doe">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-900 mb-1">Email *</label>
                            <input type="email" name="donor_email" required class="w-full px-4 py-3 border border-stone-300 rounded focus:border-gold-500 focus:outline-none" placeholder="you@example.com">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-900 mb-1">Message (optional)</label>
                            <textarea name="message" rows="3" class="w-full px-4 py-3 border border-stone-300 rounded focus:border-gold-500 focus:outline-none" placeholder="Share why you're giving..."></textarea>
                        </div>
                        <button type="submit" class="w-full btn-gold justify-center text-lg py-4">
                            Donate $<span id="btn-amount">50</span> Securely
                        </button>
                        <p class="text-xs text-center text-stone-400 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Secured by Stripe · PCI Compliant
                        </p>
                    </form>
                </div>
                
                <div>
                    <div class="bg-navy-900 text-white p-8 rounded-lg mb-6">
                        <h3 class="font-serif font-bold text-lg mb-4 text-gold-400">Your Donation Impact</h3>
                        <p id="impact-text" class="text-gray-300 leading-relaxed">Your $50 donation covers examination registration fees and learning materials for one scholar.</p>
                    </div>
                    
                    <!-- Trust Signals -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 bg-white p-4 rounded shadow">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
                            <div><div class="font-semibold text-navy-900 text-sm">100% goes to scholars</div><div class="text-xs text-stone-500">Zero administrative fees</div></div>
                        </div>
                        <div class="flex items-center gap-3 bg-white p-4 rounded shadow">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center"><svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
                            <div><div class="font-semibold text-navy-900 text-sm">Fully Transparent</div><div class="text-xs text-stone-500">Annual reports published</div></div>
                        </div>
                        <div class="flex items-center gap-3 bg-white p-4 rounded shadow">
                            <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center"><svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                            <div><div class="font-semibold text-navy-900 text-sm">Instant Receipt</div><div class="text-xs text-stone-500">Confirmation sent to your email</div></div>
                        </div>
                    </div>
                    
                    <!-- Quote -->
                    <div class="mt-6 bg-cream p-6 rounded-lg border-l-4 border-gold-500">
                        <p class="text-stone-600 italic mb-3">"Without RVIF's support, I would never have made it to university. Today I'm studying medicine and I will give back to my community."</p>
                        <div class="font-semibold text-navy-900 text-sm">— Amara J.</div>
                        <div class="text-xs text-gold-500">RVIF Scholar, Rwanda</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
const impactMap = {
    25: 'Your $25 donation provides textbooks and school supplies for one scholar for an entire term.',
    50: 'Your $50 donation covers examination registration fees and learning materials for one scholar.',
    100: 'Your $100 donation sponsors a full month of meals, accommodation, and learning supplies.',
    250: 'Your $250 donation contributes to a full semester of tuition fees and living expenses.',
    500: 'Your $500 donation funds an entire semester for one scholar — a complete life transformation.',
    1000: 'Your $1000 donation sponsors a full academic year for one scholar — a future secured.'
};

document.querySelectorAll('.tier-card').forEach(card => {
    card.addEventListener('click', () => {
        const amount = card.dataset.amount;
        document.getElementById('amount-input').value = amount;
        document.getElementById('btn-amount').textContent = amount;
        document.getElementById('impact-text').textContent = impactMap[amount] || 'Your donation makes a meaningful difference.';
        document.querySelectorAll('.tier-card').forEach(c => c.classList.remove('border-gold-500'));
        card.classList.add('border-gold-500');
    });
});

document.getElementById('amount-input').addEventListener('input', e => {
    const amount = parseInt(e.target.value) || 0;
    document.getElementById('btn-amount').textContent = amount;
    const closest = Object.keys(impactMap).sort((a,b) => Math.abs(a-amount) - Math.abs(b-amount))[0] || 50;
    document.getElementById('impact-text').textContent = impactMap[closest] || 'Your donation makes a meaningful difference.';
});
</script>
<?php endif; ?>

<!-- CTA -->
<section class="py-16 bg-gold-500">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="text-2xl font-serif font-bold text-navy-900 mb-4">Other Ways to Help</h2>
        <p class="text-navy-800 mb-6">Can't donate? You can still make a difference by sharing our mission or partnering with us.</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="<?php echo BASE_PATH; ?>/contact" class="bg-navy-900 text-white px-6 py-3 rounded font-semibold hover:bg-navy-800 transition">Partner With Us</a>
            <a href="<?php echo BASE_PATH; ?>/beneficiaries" class="border-2 border-navy-900 text-navy-900 px-6 py-3 rounded font-semibold hover:bg-navy-900 hover:text-white transition">Meet Our Scholars</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>