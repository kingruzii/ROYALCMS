<?php
/**
 * Shared Footer - Professional NGO Layout
 */
?>
<script>document.querySelectorAll('#mobile-menu a').forEach(a => a.addEventListener('click', () => document.getElementById('mobile-menu').classList.add('hidden')));</script>

</main>

<!-- FOOTER -->
<footer class="bg-navy-950 text-white pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
            <!-- Brand -->
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="<?php echo htmlspecialchars($settings['logo']); ?>" alt="RVIF" class="h-14 w-14 object-contain bg-white rounded-lg p-1">
                    <div>
                        <div class="font-serif font-bold text-lg">Royal Village</div>
                        <div class="text-gold-500 text-xs">International Foundation</div>
                    </div>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed mb-4">Empowering African youth through quality education, vocational training, and community development since 2021.</p>
                <div class="flex gap-3">
                    <?php if (!empty($settings['facebook'])): ?>
                    <a href="<?php echo htmlspecialchars($settings['facebook']); ?>" class="w-9 h-9 bg-navy-800 rounded-full flex items-center justify-center hover:bg-gold-500 hover:text-navy-950 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($settings['youtube'])): ?>
                    <a href="<?php echo htmlspecialchars($settings['youtube']); ?>" class="w-9 h-9 bg-navy-800 rounded-full flex items-center justify-center hover:bg-gold-500 hover:text-navy-950 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02" fill="#0A1628"/></svg>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($settings['instagram'])): ?>
                    <a href="<?php echo htmlspecialchars($settings['instagram']); ?>" class="w-9 h-9 bg-navy-800 rounded-full flex items-center justify-center hover:bg-gold-500 hover:text-navy-950 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/></svg>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h4 class="text-gold-500 font-semibold text-sm uppercase tracking-wider mb-4">Quick Links</h4>
                <ul class="space-y-2">
                    <li><a href="<?php echo BASE_PATH; ?>/about" class="text-gray-400 hover:text-gold-400 transition text-sm">About Us</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>/our-work" class="text-gray-400 hover:text-gold-400 transition text-sm">Our Work</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>/beneficiaries" class="text-gray-400 hover:text-gold-400 transition text-sm">Our Scholars</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>/impact" class="text-gray-400 hover:text-gold-400 transition text-sm">Our Impact</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>/team" class="text-gray-400 hover:text-gold-400 transition text-sm">Our Team</a></li>
                </ul>
            </div>
            
            <!-- Resources -->
            <div>
                <h4 class="text-gold-500 font-semibold text-sm uppercase tracking-wider mb-4">Resources</h4>
                <ul class="space-y-2">
                    <li><a href="<?php echo BASE_PATH; ?>/blog" class="text-gray-400 hover:text-gold-400 transition text-sm">News & Stories</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>/partners" class="text-gray-400 hover:text-gold-400 transition text-sm">Our Partners</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>/programs" class="text-gray-400 hover:text-gold-400 transition text-sm">Programs</a></li>
                    <li><a href="<?php echo BASE_PATH; ?>/contact" class="text-gray-400 hover:text-gold-400 transition text-sm">Contact</a></li>
                </ul>
            </div>
            
            <!-- Contact -->
            <div>
                <h4 class="text-gold-500 font-semibold text-sm uppercase tracking-wider mb-4">Contact</h4>
                <ul class="space-y-3 text-gray-400 text-sm">
                    <li class="flex items-center gap-2">📍 <?php echo htmlspecialchars($settings['address']); ?></li>
                    <li class="flex items-center gap-2">📞 <?php echo htmlspecialchars($settings['contactPhone']); ?></li>
                    <?php if (!empty($settings['contactPhone2'])): ?>
                    <li class="flex items-center gap-2">📞 <?php echo htmlspecialchars($settings['contactPhone2']); ?></li>
                    <?php endif; ?>
                    <li class="flex items-center gap-2">✉️ <?php echo htmlspecialchars($settings['contactEmail']); ?></li>
                </ul>
            </div>
        </div>
        
        <!-- Newsletter -->
        <div class="border-t border-navy-800 pt-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h4 class="text-white font-serif font-semibold mb-1">Stay Updated</h4>
                    <p class="text-gray-500 text-sm">Get latest news and impact stories delivered to your inbox.</p>
                </div>
                <form class="flex gap-2">
                    <input type="email" placeholder="Your email" class="px-4 py-2 bg-navy-800 border border-navy-700 rounded text-white text-sm focus:border-gold-500 outline-none w-64">
                    <button type="submit" class="btn-gold text-sm">Subscribe</button>
                </form>
            </div>
        </div>
        
        <!-- Bottom -->
        <div class="border-t border-navy-800 mt-8 pt-6 flex flex-col md:flex-row justify-between items-center gap-4 text-gray-500 text-sm">
            <p>© <?php echo date('Y'); ?> Royal Village International Foundation. All rights reserved.</p>
            <div class="flex gap-4">
                <a href="#" class="hover:text-gold-400">Privacy Policy</a>
                <a href="#" class="hover:text-gold-400">Terms</a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>