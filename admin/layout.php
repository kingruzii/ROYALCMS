<?php
/**
 * Admin Panel Layout Wrapper - Premium Enhanced
 */
require_once __DIR__ . '/../db.php';
checkAdminAuth();

$adminSettings = [
    'logo'     => 'https://d64gsuwffb70l.cloudfront.net/6a0c48e7482add8d9312f354_1779190001642_6ba746dd.png',
    'siteName' => "Royal Village Int'l"
];

try {
    $stmt = $pdo->prepare("SELECT value FROM site_settings WHERE id = 'main' LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch();
    if ($row && !empty($row['value'])) {
        $dbSettings = json_decode($row['value'], true);
        if (is_array($dbSettings)) {
            $adminSettings = array_merge($adminSettings, $dbSettings);
        }
    }
} catch (Exception $e) {}

// Flag to track if header has been rendered
if (!isset($GLOBALS['_admin_header_rendered'])) {
    $GLOBALS['_admin_header_rendered'] = false;
}

function renderAdminHeader($activePage = 'dashboard') {
    global $adminSettings;
    
    // Prevent duplicate rendering
    if ($GLOBALS['_admin_header_rendered'] === true) {
        return;
    }
    $GLOBALS['_admin_header_rendered'] = true;
    
    $links = [
        ['to' => 'dashboard.php',    'label' => 'Dashboard',    'icon' => 'layout-dashboard', 'key' => 'dashboard'],
        ['to' => 'donations.php',    'label' => 'Donations',    'icon' => 'dollar-sign',      'key' => 'donations'],
        ['to' => 'beneficiaries.php','label' => 'Beneficiaries','icon' => 'graduation-cap',   'key' => 'beneficiaries'],
        ['to' => 'founder.php',      'label' => 'Founder',      'icon' => 'crown',            'key' => 'founder'],
        ['to' => 'team.php',         'label' => 'Team',         'icon' => 'users',            'key' => 'team'],
        ['to' => 'programs.php',     'label' => 'Programs',     'icon' => 'briefcase',        'key' => 'programs'],
        ['to' => 'partners.php',     'label' => 'Partners',     'icon' => 'handshake',        'key' => 'partners'],
        ['to' => 'impact.php',       'label' => 'Impact',       'icon' => 'trending-up',      'key' => 'impact'],
        ['to' => 'blog.php',         'label' => 'Blog',         'icon' => 'file-text',        'key' => 'blog'],
        ['to' => 'testimonials.php',  'label' => 'Testimonials', 'icon' => 'message-square',   'key' => 'testimonials'],
        ['to' => 'messages.php',     'label' => 'Messages',     'icon' => 'mail',             'key' => 'messages'],
        ['to' => 'banners.php',      'label' => 'Banners',      'icon' => 'image',            'key' => 'banners'],
        ['to' => 'milestones.php',   'label' => 'Milestones',   'icon' => 'clock',            'key' => 'milestones'],
        ['to' => 'settings.php',     'label' => 'Settings',     'icon' => 'settings',         'key' => 'settings'],
    ];
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>RVIF Admin — <?php echo ucfirst(htmlspecialchars($activePage)); ?></title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            *, *::before, *::after { box-sizing: border-box; }
            body {
                font-family: 'Outfit', sans-serif;
                background: #f8f7ff;
                min-height: 100vh;
                display: flex;
                margin: 0;
            }
            ::-webkit-scrollbar { width: 5px; height: 5px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: rgba(124,58,237,0.25); border-radius: 10px; }
            ::-webkit-scrollbar-thumb:hover { background: rgba(124,58,237,0.5); }
            .admin-sidebar {
                width: 256px;
                min-height: 100vh;
                background: linear-gradient(180deg, #0f0523 0%, #1e0a40 40%, #2e1065 100%);
                display: flex;
                flex-direction: column;
                flex-shrink: 0;
                position: sticky;
                top: 0;
                height: 100vh;
                overflow-y: auto;
                overflow-x: hidden;
                z-index: 50;
            }
            .sidebar-orb {
                position: absolute;
                border-radius: 50%;
                pointer-events: none;
            }
            .sidebar-brand {
                padding: 24px 20px 20px;
                border-bottom: 1px solid rgba(255,255,255,0.06);
                position: relative;
                z-index: 2;
            }
            .nav-item {
                display: flex;
                align-items: center;
                gap: 11px;
                padding: 11px 14px;
                border-radius: 12px;
                margin: 2px 8px;
                font-size: 0.83rem;
                font-weight: 500;
                color: rgba(233,213,255,0.65);
                text-decoration: none;
                transition: all 0.25s ease;
                position: relative;
                overflow: hidden;
            }
            .nav-item::before {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: 12px;
                background: linear-gradient(135deg, rgba(255,255,255,0.06), transparent);
                opacity: 0;
                transition: opacity 0.25s;
            }
            .nav-item:hover {
                color: #fff;
                background: rgba(255,255,255,0.07);
            }
            .nav-item:hover::before { opacity: 1; }
            .nav-item.active {
                background: linear-gradient(135deg, rgba(245,158,11,0.9), rgba(217,119,6,0.85));
                color: #1e0a40;
                font-weight: 700;
                box-shadow: 0 4px 18px rgba(245,158,11,0.35);
            }
            .nav-item.active::before { opacity: 0; }
            .nav-icon {
                width: 32px;
                height: 32px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                background: rgba(255,255,255,0.07);
                transition: background 0.25s;
            }
            .nav-item.active .nav-icon {
                background: rgba(30,10,64,0.2);
            }
            .nav-item:hover:not(.active) .nav-icon {
                background: rgba(255,255,255,0.12);
            }
            .nav-section-label {
                font-size: 0.58rem;
                font-weight: 800;
                letter-spacing: 0.18em;
                text-transform: uppercase;
                color: rgba(168,85,247,0.45);
                padding: 12px 22px 4px;
            }
            .sidebar-footer {
                margin-top: auto;
                padding: 12px 8px 20px;
                border-top: 1px solid rgba(255,255,255,0.05);
            }
            .footer-link {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 14px;
                border-radius: 10px;
                margin: 2px 0;
                font-size: 0.78rem;
                font-weight: 500;
                color: rgba(233,213,255,0.5);
                text-decoration: none;
                transition: all 0.2s;
            }
            .footer-link:hover { background: rgba(255,255,255,0.06); color: rgba(233,213,255,0.9); }
            .footer-link.logout:hover { background: rgba(239,68,68,0.1); color: #fca5a5; }
            .admin-main {
                flex: 1;
                overflow-x: hidden;
                overflow-y: auto;
                min-height: 100vh;
            }
            .active-dot {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: #1e0a40;
                margin-left: auto;
                flex-shrink: 0;
                animation: dotPulse 2s ease-in-out infinite;
            }
            @keyframes dotPulse {
                0%, 100% { opacity: 1; transform: scale(1); }
                50%       { opacity: 0.5; transform: scale(0.7); }
            }
            .user-badge {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 14px;
                background: rgba(255,255,255,0.04);
                border: 1px solid rgba(255,255,255,0.07);
                border-radius: 12px;
                margin: 0 8px 8px;
            }
        </style>
    </head>
    <body>
        <aside class="admin-sidebar">
            <div class="sidebar-orb" style="width:300px;height:300px;top:-100px;right:-100px;background:radial-gradient(circle,rgba(124,58,237,0.12),transparent);"></div>
            <div class="sidebar-orb" style="width:200px;height:200px;bottom:100px;left:-80px;background:radial-gradient(circle,rgba(245,158,11,0.08),transparent);"></div>
            <div class="sidebar-brand">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:42px;height:42px;border-radius:12px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;">
                        <img src="<?php echo htmlspecialchars($adminSettings['logo']); ?>" alt="RVIF" style="width:32px;height:32px;object-fit:contain;" />
                    </div>
                    <div>
                        <div style="font-family:'Cormorant Garamond',Georgia,serif;font-size:1rem;font-weight:700;color:#fde68a;line-height:1.1;">RVIF CMS</div>
                        <div style="font-size:0.62rem;color:rgba(168,85,247,0.7);letter-spacing:0.05em;text-transform:uppercase;font-weight:600;">Admin Portal</div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:7px;margin-top:14px;padding:6px 12px;background:rgba(5,150,105,0.12);border:1px solid rgba(5,150,105,0.2);border-radius:8px;">
                    <span style="width:6px;height:6px;border-radius:50%;background:#34d399;display:inline-block;animation:dotPulse 2s ease-in-out infinite;"></span>
                    <span style="font-size:0.65rem;font-weight:600;color:rgba(52,211,153,0.85);letter-spacing:0.06em;text-transform:uppercase;">All Systems Online</span>
                </div>
            </div>
            <nav style="padding:12px 0;flex:1;position:relative;z-index:2;">
                <div class="nav-section-label">Main Navigation</div>
                <?php foreach ($links as $l):
                    $isActive = ($activePage === $l['key']);
                ?>
                <a href="<?php echo BASE_PATH; ?>/admin/<?php echo $l['to']; ?>" class="nav-item <?php echo $isActive ? 'active' : ''; ?>">
                    <div class="nav-icon">
                        <i data-lucide="<?php echo $l['icon']; ?>" style="width:15px;height:15px;"></i>
                    </div>
                    <span><?php echo $l['label']; ?></span>
                    <?php if ($isActive): ?>
                    <div class="active-dot"></div>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </nav>
            <div class="user-badge">
                <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#f59e0b);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <span style="font-size:0.72rem;font-weight:800;color:#fff;">A</span>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:0.78rem;font-weight:700;color:rgba(255,255,255,0.9);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Administrator</div>
                    <div style="font-size:0.65rem;color:rgba(233,213,255,0.45);">Full Access</div>
                </div>
                <div style="width:8px;height:8px;border-radius:50%;background:#34d399;flex-shrink:0;"></div>
            </div>
            <div class="sidebar-footer">
                <a href="<?php echo BASE_PATH; ?>/index.php" class="footer-link" target="_blank">
                    <i data-lucide="globe" style="width:14px;height:14px;"></i>
                    <span>View Website</span>
                </a>
                <a href="<?php echo BASE_PATH; ?>/admin/login.php?logout=1" class="footer-link logout">
                    <i data-lucide="log-out" style="width:14px;height:14px;"></i>
                    <span>Sign Out</span>
                </a>
            </div>
        </aside>
        <main class="admin-main">
    <?php
}

function renderAdminFooter() {
    ?>
        </main>
        <script src="https://unpkg.com/lucide@latest"></script>
        <script>
            lucide.createIcons();
        </script>
    </body>
    </html>
    <?php
}
?>