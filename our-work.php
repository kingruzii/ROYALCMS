<?php
/**
 * Our Work Page - Professional NGO Layout
 */
require_once __DIR__ . '/header.php';
?>

<!-- PAGE HERO -->
<section class="bg-navy-900 text-white py-20">
    <div class="max-w-7xl mx-auto px-6">
        <p class="text-gold-400 font-semibold tracking-widest uppercase text-sm mb-2">What We Do</p>
        <h1 class="text-4xl md:text-5xl font-serif font-bold mb-4">Our Work</h1>
        <p class="text-xl text-gray-300 max-w-2xl">We empower African youth through education, vocational training, and community development programs.</p>
    </div>
</section>

<!-- BREADCRUMB -->
<div class="bg-white border-b py-3">
    <div class="max-w-7xl mx-auto px-6 text-sm">
        <a href="<?php echo BASE_PATH; ?>/" class="text-gold-500 hover:underline">Home</a> <span class="text-gray-400">/</span> <span class="text-navy-900">Our Work</span>
    </div>
</div>

<!-- WORK POINTS -->
<section class="py-20 bg-cream">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div>
                <img src="uploads/programs/1781340761_301816b4f3016a3c.jpg" alt="Our Work" class="rounded-lg shadow-xl">
            </div>
            <div>
                <p class="text-gold-500 font-semibold tracking-widest uppercase text-sm mb-2">Our Approach</p>
                <h2 class="text-3xl font-serif font-bold text-navy-900 mb-6">Transforming Lives Through Education</h2>
                <div class="w-20 h-1 bg-gold-500 mb-6"></div>
                
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="w-12 h-12 bg-navy-900 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <h4 class="font-serif font-bold text-navy-900 mb-1">Scholarships</h4>
                            <p class="text-stone-500 text-sm">Full and partial scholarships for secondary and university students, covering tuition, books, and living expenses.</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-4">
                        <div class="w-12 h-12 bg-navy-900 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                        </div>
                        <div>
                            <h4 class="font-serif font-bold text-navy-900 mb-1">Vocational Training</h4>
                            <p class="text-stone-500 text-sm">Hands-on skill-building in technology, trades, and entrepreneurship for youth outside formal education.</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-4">
                        <div class="w-12 h-12 bg-navy-900 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-serif font-bold text-navy-900 mb-1">Community Development</h4>
                            <p class="text-stone-500 text-sm">Outreach, mentorship, and support for underserved communities including clean water and healthcare initiatives.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOCUS AREAS -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-12">
            <p class="text-gold-500 font-semibold tracking-widest uppercase text-sm mb-2">Focus Areas</p>
            <h2 class="text-3xl font-serif font-bold text-navy-900">Where We Make Impact</h2>
        </div>
        
        <div class="grid md:grid-cols-4 gap-6">
            <div class="text-center p-6 bg-cream rounded-lg">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h4 class="font-serif font-bold text-navy-900 mb-2">Education</h4>
                <p class="text-stone-500 text-sm">Scholarships and academic support for students at all levels</p>
            </div>
            
            <div class="text-center p-6 bg-cream rounded-lg">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <h4 class="font-serif font-bold text-navy-900 mb-2">Health</h4>
                <p class="text-stone-500 text-sm">Training future nurses, doctors, and healthcare professionals</p>
            </div>
            
            <div class="text-center p-6 bg-cream rounded-lg">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                </div>
                <h4 class="font-serif font-bold text-navy-900 mb-2">Leadership</h4>
                <p class="text-stone-500 text-sm">Developing future leaders, lawyers, and entrepreneurs</p>
            </div>
            
            <div class="text-center p-6 bg-cream rounded-lg">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h4 class="font-serif font-bold text-navy-900 mb-2">Community</h4>
                <p class="text-stone-500 text-sm">Building stronger communities through local engagement</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16 bg-navy-900 text-white text-center">
    <div class="max-w-4xl mx-auto px-6">
        <h2 class="text-2xl font-serif font-bold mb-4">Support Our Work</h2>
        <p class="text-gray-300 mb-6">Your donation helps us continue our mission across Africa.</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="<?php echo BASE_PATH; ?>/donate" class="btn-gold">Donate Today</a>
            <a href="<?php echo BASE_PATH; ?>/beneficiaries" class="btn-outline border-white text-white hover:bg-white hover:text-navy-900">Meet the Scholars →</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>