<?php
/**
 * Shared Header - Professional NGO Layout (UNICEF/Oxfam Style)
 */
require_once __DIR__ . '/db.php';

$settings = [
    'logo' => '',
    'siteName' => 'Royal Village International Foundation',
    'tagline' => 'Empowering Africa Through Education',
    'contactEmail' => '',
    'contactPhone' => '',
    'contactPhone2' => '',
    'address' => '',
    'facebook' => '',
    'youtube' => 'https://www.youtube.com/@royalvillageinternational',
    'instagram' => '',
    'stats' => ['scholars' => 50, 'countries' => 4, 'programs' => 4, 'years' => 6]
];

try {
    $stmt = $pdo->prepare("SELECT value FROM site_settings WHERE id = 'main' LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch();
    if ($row && !empty($row['value'])) {
        $dbSettings = json_decode($row['value'], true);
        if (is_array($dbSettings)) $settings = array_merge($settings, $dbSettings);
    }
} catch (Exception $e) {}

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$isHome = in_array($current_page, ['index', '']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($settings['siteName']); ?> – <?php echo htmlspecialchars($settings['tagline']); ?></title>
    <meta name="description" content="Royal Village International Foundation empowers African youth through quality education, vocational training, and community development.">
    
    <!-- Fonts - Professional NGO Style -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Source+Sans+3:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 950: '#050A14', 900: '#0A1628', 800: '#132035', 700: '#1E2D45' },
                        gold: { 400: '#E8C547', 500: '#D4A72C', 600: '#B8931F' },
                        cream: '#FAF8F5',
                    },
                    fontFamily: {
                        serif: ['Libre Baskerville', 'Georgia', 'serif'],
                        sans: ['Source Sans 3', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
    
    <style>
        :root {
            --navy: #0A1628;
            --gold: #D4A72C;
            --cream: #FAF8F5;
        }
        
        body {
            font-family: 'Source Sans 3', sans-serif;
            background: var(--cream);
            color: var(--navy);
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Libre Baskerville', Georgia, serif;
        }
        
        /* Navigation */
        .nav-link {
            position: relative;
            padding: 0.5rem 1rem;
            color: #1E2D45;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        
        .nav-link:hover, .nav-link.active {
            color: var(--gold);
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 1rem;
            right: 1rem;
            height: 2px;
            background: var(--gold);
            transform: scaleX(0);
            transition: transform 0.3s;
        }
        
        .nav-link:hover::after, .nav-link.active::after {
            transform: scaleX(1);
        }
        
        /* Buttons */
        .btn-gold {
            background: var(--gold);
            color: #050A14;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-gold:hover {
            background: #E8C547;
            transform: translateY(-2px);
        }
        
        .btn-outline {
            border: 2px solid var(--gold);
            color: var(--gold);
            font-weight: 600;
            padding: 0.7rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-outline:hover {
            background: var(--gold);
            color: #050A14;
        }
        
        /* Cards */
        .card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(10,22,40,0.08);
            transition: all 0.3s;
        }
        
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(10,22,40,0.12);
        }
        
        /* Stats */
        .stat-number {
            font-family: 'Libre Baskerville', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--gold);
            line-height: 1;
        }
        
        /* Section Titles */
        .section-title span {
            color: var(--gold);
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }
        
        .section-title h2 {
            font-size: 2.25rem;
            margin-top: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .section-title h2 {
                font-size: 1.75rem;
            }
        }
        
        /* Breadcrumb */
        .breadcrumb a {
            color: var(--gold);
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        
        /* Gold divider */
        .gold-divider {
            width: 60px;
            height: 3px;
            background: var(--gold);
            border-radius: 2px;
            margin: 1rem 0;
        }
        
        /* Dropdown */
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 200px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s;
            z-index: 100;
        }
        
        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
    </style>
</head>
<body>
<!-- HEADER -->
<header class="bg-navy-900 sticky top-0 z-50 shadow-lg">
    <!-- Top Bar -->
    <div class="bg-navy-950 text-white text-xs py-2 hidden md:block">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <span>📞 <?php echo htmlspecialchars($settings['contactPhone']); ?></span>
                <?php if (!empty($settings['contactPhone2'])): ?>
                <span>📞 <?php echo htmlspecialchars($settings['contactPhone2']); ?></span>
                <?php endif; ?>
                <span>✉️ <?php echo htmlspecialchars($settings['contactEmail']); ?></span>
            </div>
            <div class="flex items-center gap-3">
                <?php if (!empty($settings['facebook'])): ?>
                <a href="<?php echo htmlspecialchars($settings['facebook']); ?>" class="hover:text-gold-400 transition">Facebook</a>
                <?php endif; ?>
                <?php if (!empty($settings['youtube'])): ?>
                <a href="<?php echo htmlspecialchars($settings['youtube']); ?>" class="hover:text-gold-400 transition">YouTube</a>
                <?php endif; ?>
                <?php if (!empty($settings['instagram'])): ?>
                <a href="<?php echo htmlspecialchars($settings['instagram']); ?>" class="hover:text-gold-400 transition">Instagram</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Main Nav -->
    <nav class="bg-navy-900">
        <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
            <a href="<?php echo BASE_PATH; ?>/" class="flex items-center gap-3">
                <img src="<?php echo htmlspecialchars($settings['logo']); ?>" alt="RVIF" class="h-12 w-12 object-contain">
                <div>
                    <div class="text-white font-serif font-bold text-lg leading-tight">Royal Village</div>
                    <div class="text-gold-500 text-xs tracking-wide">International Foundation</div>
                </div>
            </a>
            
            <!-- Desktop Links -->
            <div class="hidden lg:flex items-center gap-6">
                <a href="<?php echo BASE_PATH; ?>/" class="nav-link <?php echo $isHome ? 'active' : ''; ?>">Home</a>
                
                <div class="dropdown relative">
                    <a href="#" class="nav-link flex items-center gap-1">About <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></a>
                    <div class="dropdown-menu">
                        <a href="<?php echo BASE_PATH; ?>/about" class="block px-4 py-2 hover:bg-cream text-sm">Who We Are</a>
                        <a href="<?php echo BASE_PATH; ?>/team" class="block px-4 py-2 hover:bg-cream text-sm">Our Team</a>
                        <a href="<?php echo BASE_PATH; ?>/partners" class="block px-4 py-2 hover:bg-cream text-sm">Partners</a>
                    </div>
                </div>
                
                <div class="dropdown relative">
                    <a href="#" class="nav-link flex items-center gap-1">Our Work <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></a>
                    <div class="dropdown-menu">
                        <a href="<?php echo BASE_PATH; ?>/our-work" class="block px-4 py-2 hover:bg-cream text-sm">What We Do</a>
                        <a href="<?php echo BASE_PATH; ?>/beneficiaries" class="block px-4 py-2 hover:bg-cream text-sm">Our Scholars</a>
                        <a href="<?php echo BASE_PATH; ?>/impact" class="block px-4 py-2 hover:bg-cream text-sm">Impact</a>
                    </div>
                </div>
                
                <a href="<?php echo BASE_PATH; ?>/blog" class="nav-link">News</a>
                <a href="<?php echo BASE_PATH; ?>/contact" class="nav-link">Contact</a>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="<?php echo BASE_PATH; ?>/donate" class="btn-gold">Donate Now</a>
                <button id="mobile-toggle" class="lg:hidden text-white p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </nav>
    
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden lg:hidden bg-navy-800 px-6 py-4">
        <a href="<?php echo BASE_PATH; ?>/" class="block text-white py-2 border-b border-navy-700">Home</a>
        <a href="<?php echo BASE_PATH; ?>/about" class="block text-white py-2 border-b border-navy-700">About Us</a>
        <a href="<?php echo BASE_PATH; ?>/our-work" class="block text-white py-2 border-b border-navy-700">Our Work</a>
        <a href="<?php echo BASE_PATH; ?>/beneficiaries" class="block text-white py-2 border-b border-navy-700">Our Scholars</a>
        <a href="<?php echo BASE_PATH; ?>/blog" class="block text-white py-2 border-b border-navy-700">News</a>
        <a href="<?php echo BASE_PATH; ?>/contact" class="block text-white py-2 border-b border-navy-700">Contact</a>
    </div>
</header>

<script>
document.getElementById('mobile-toggle').onclick = function() { 
    document.getElementById('mobile-menu').classList.toggle('hidden'); 
}
</script>

<main>