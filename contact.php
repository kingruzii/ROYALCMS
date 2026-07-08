<?php
/**
 * Contact Page - Professional NGO Layout
 */
require_once __DIR__ . '/header.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $subject, $message]);
            $success = 'Thank you for your message! We will get back to you soon.';
        } catch (Exception $e) {
            $error = 'Something went wrong. Please try again.';
        }
    }
}
?>

<!-- PAGE HERO -->
<section class="bg-navy-900 text-white py-20">
    <div class="max-w-7xl mx-auto px-6">
        <p class="text-gold-400 font-semibold tracking-widest uppercase text-sm mb-2">Get In Touch</p>
        <h1 class="text-4xl md:text-5xl font-serif font-bold mb-4">Contact Us</h1>
        <p class="text-xl text-gray-300 max-w-2xl">We'd love to hear from you. Reach out with questions, partnership inquiries, or just to say hello.</p>
    </div>
</section>

<!-- BREADCRUMB -->
<div class="bg-white border-b py-3">
    <div class="max-w-7xl mx-auto px-6 text-sm">
        <a href="<?php echo BASE_PATH; ?>/" class="text-gold-500 hover:underline">Home</a> <span class="text-gray-400">/</span> <span class="text-navy-900">Contact</span>
    </div>
</div>

<!-- CONTACT SECTION -->
<section class="py-20 bg-cream">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-12">
            <!-- Contact Info -->
            <div>
                <p class="text-gold-500 font-semibold tracking-widest uppercase text-sm mb-2">Send Us a Message</p>
                <h2 class="text-3xl font-serif font-bold text-navy-900 mb-6">Let's Connect</h2>
                <div class="w-20 h-1 bg-gold-500 mb-6"></div>
                <p class="text-stone-600 mb-8">Have questions about our programs? Want to partner with us? Fill out the form and we'll get back to you within 24-48 hours.</p>
                
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-navy-900 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-navy-900">Address</h4>
                            <p class="text-stone-500"><?php echo htmlspecialchars($settings['address']); ?></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-navy-900 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-navy-900">Email</h4>
                            <p class="text-stone-500"><?php echo htmlspecialchars($settings['contactEmail']); ?></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-navy-900 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-navy-900">Phone</h4>
                            <p class="text-stone-500"><?php echo htmlspecialchars($settings['contactPhone']); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Social Links -->
                <div class="mt-8 pt-8 border-t border-stone-200">
                    <h4 class="font-semibold text-navy-900 mb-4">Follow Us</h4>
                    <div class="flex gap-3">
                        <a href="<?php echo htmlspecialchars($settings['facebook']); ?>" class="w-10 h-10 bg-navy-900 rounded-full flex items-center justify-center hover:bg-gold-500 transition">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                        </a>
                        <a href="<?php echo htmlspecialchars($settings['twitter']); ?>" class="w-10 h-10 bg-navy-900 rounded-full flex items-center justify-center hover:bg-gold-500 transition">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
                        </a>
                        <a href="<?php echo htmlspecialchars($settings['instagram']); ?>" class="w-10 h-10 bg-navy-900 rounded-full flex items-center justify-center hover:bg-gold-500 transition">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <?php if($success): ?>
                <div class="bg-green-50 border border-green-300 text-green-800 px-6 py-4 rounded mb-6">
                    <?php echo htmlspecialchars($success); ?>
                </div>
                <?php endif; ?>
                
                <?php if($error): ?>
                <div class="bg-red-50 border border-red-300 text-red-800 px-6 py-4 rounded mb-6">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-navy-900 mb-1">Name *</label>
                        <input type="text" name="name" required class="w-full px-4 py-3 border border-stone-300 rounded focus:border-gold-500 focus:outline-none" placeholder="Your name">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy-900 mb-1">Email *</label>
                        <input type="email" name="email" required class="w-full px-4 py-3 border border-stone-300 rounded focus:border-gold-500 focus:outline-none" placeholder="you@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy-900 mb-1">Subject</label>
                        <input type="text" name="subject" class="w-full px-4 py-3 border border-stone-300 rounded focus:border-gold-500 focus:outline-none" placeholder="What is this about?">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy-900 mb-1">Message *</label>
                        <textarea name="message" rows="5" required class="w-full px-4 py-3 border border-stone-300 rounded focus:border-gold-500 focus:outline-none" placeholder="Your message..."></textarea>
                    </div>
                    <button type="submit" class="w-full btn-gold justify-center text-lg py-4">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>