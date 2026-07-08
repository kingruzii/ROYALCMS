<?php
/**
 * Admin Panel Dashboard Home - Premium Enhanced
 */
require_once __DIR__ . '/layout.php';

// Fetch record counts
$counts = [
    'beneficiaries'   => 0,
    'team_members'    => 0,
    'programs'        => 0,
    'partners'        => 0,
    'blog_posts'      => 0,
    'contact_messages'=> 0,
    'donations'       => 0,
];

foreach ($counts as $table => $c) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM `$table`");
        $stmt->execute();
        $counts[$table] = (int)$stmt->fetchColumn();
    } catch (Exception $e) {}
}

// Recent donations total
$totalDonations = 0;
try {
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount_cents),0) FROM donations WHERE status='completed'");
    $stmt->execute();
    $totalDonations = $stmt->fetchColumn() / 100;
} catch (Exception $e) {}

// Recent messages (last 3)
$recentMessages = [];
try {
    $stmt = $pdo->prepare("SELECT name, subject, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 4");
    $stmt->execute();
    $recentMessages = $stmt->fetchAll();
} catch (Exception $e) {}

// Recent donations (last 3)
$recentDonations = [];
try {
    $stmt = $pdo->prepare("SELECT donor_name, donor_email, amount_cents, status, created_at FROM donations ORDER BY created_at DESC LIMIT 4");
    $stmt->execute();
    $recentDonations = $stmt->fetchAll();
} catch (Exception $e) {}

// Cards config
$cards = [
    ['key' => 'beneficiaries',    'label' => 'Beneficiaries',   'icon' => 'graduation-cap', 'to' => 'beneficiaries.php', 'from' => '#4c1d95', 'to_color' => '#7c3aed', 'desc' => 'Scholars enrolled'],
    ['key' => 'team_members',     'label' => 'Team Members',    'icon' => 'users',           'to' => 'team.php',          'from' => '#92400e', 'to_color' => '#d97706', 'desc' => 'Staff profiles'],
    ['key' => 'programs',         'label' => 'Programs',        'icon' => 'briefcase',       'to' => 'programs.php',      'from' => '#7c2d12', 'to_color' => '#ea580c', 'desc' => 'Active initiatives'],
    ['key' => 'partners',         'label' => 'Partners',        'icon' => 'handshake',       'to' => 'partners.php',      'from' => '#9d174d', 'to_color' => '#db2777', 'desc' => 'Support networks'],
    ['key' => 'blog_posts',       'label' => 'Blog Posts',      'icon' => 'file-text',       'to' => 'blog.php',          'from' => '#164e63', 'to_color' => '#0891b2', 'desc' => 'Published stories'],
    ['key' => 'contact_messages', 'label' => 'Messages',        'icon' => 'mail',            'to' => 'messages.php',      'from' => '#065f46', 'to_color' => '#059669', 'desc' => 'Inbox inquiries'],
    ['key' => 'donations',        'label' => 'Donations',       'icon' => 'dollar-sign',     'to' => 'donations.php',     'from' => '#831843', 'to_color' => '#db2777', 'desc' => 'Total transactions'],
];

renderAdminHeader('dashboard');
?>

<div style="padding:36px 40px;min-height:100vh;background:#f8f7ff;">

    <!-- PAGE HEADER -->
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:36px;flex-wrap:wrap;gap:16px;">
        <div>
            <div style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,rgba(124,58,237,0.1),rgba(245,158,11,0.08));border:1px solid rgba(124,58,237,0.15);border-radius:50px;padding:5px 14px;margin-bottom:10px;">
                <span style="width:7px;height:7px;border-radius:50%;background:#7c3aed;display:inline-block;animation:pulse 2s infinite;"></span>
                <span style="font-family:'Outfit',sans-serif;font-size:0.7rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#7c3aed;">Admin Dashboard</span>
            </div>
            <h1 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:2.2rem;font-weight:700;color:#1e0a40;line-height:1.1;margin-bottom:4px;">
                Welcome back, <span style="color:#7c3aed;">Admin</span>
            </h1>
            <p style="font-family:'Outfit',sans-serif;font-size:0.9rem;color:#9ca3af;">
                <?php echo date('l, F j, Y'); ?> — Manage your entire RVIF website from here.
            </p>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="<?php echo BASE_PATH; ?>/index.php" target="_blank"
               style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:12px;background:#fff;border:1.5px solid #e5e7eb;color:#374151;font-family:'Outfit',sans-serif;font-size:0.82rem;font-weight:600;text-decoration:none;transition:all 0.2s;box-shadow:0 1px 4px rgba(0,0,0,0.05);">
                <i data-lucide="external-link" style="width:14px;height:14px;"></i> View Site
            </a>
            <a href="<?php echo BASE_PATH; ?>/admin/settings.php"
               style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:12px;background:linear-gradient(135deg,#7c3aed,#a855f7);color:#fff;font-family:'Outfit',sans-serif;font-size:0.82rem;font-weight:600;text-decoration:none;transition:all 0.2s;box-shadow:0 4px 15px rgba(124,58,237,0.3);">
                <i data-lucide="settings" style="width:14px;height:14px;"></i> Settings
            </a>
        </div>
    </div>

    <!-- TOTAL DONATIONS HIGHLIGHT BANNER -->
    <div style="background:linear-gradient(105deg,#1e0a40 0%,#3b0764 50%,#4c1d95 100%);border-radius:20px;padding:28px 36px;margin-bottom:32px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;position:relative;overflow:hidden;">
        <div style="position:absolute;right:-40px;top:-40px;width:200px;height:200px;border-radius:50%;background:rgba(245,158,11,0.08);"></div>
        <div style="position:absolute;left:40%;bottom:-60px;width:240px;height:240px;border-radius:50%;background:rgba(168,85,247,0.07);"></div>
        <div style="position:relative;z-index:2;">
            <div style="font-family:'Outfit',sans-serif;font-size:0.7rem;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;color:rgba(253,230,138,0.7);margin-bottom:8px;">Total Donations Received</div>
            <div style="font-family:'Cormorant Garamond',Georgia,serif;font-size:3rem;font-weight:700;color:#fff;line-height:1;" id="total-counter" data-val="<?php echo number_format($totalDonations, 2); ?>">
                $0.00
            </div>
            <div style="font-family:'Outfit',sans-serif;font-size:0.82rem;color:rgba(233,213,255,0.6);margin-top:4px;"><?php echo $counts['donations']; ?> total transactions recorded</div>
        </div>
        <div style="display:flex;gap:20px;position:relative;z-index:2;flex-wrap:wrap;">
            <?php
            $highlights = [
                ['icon' => 'graduation-cap', 'val' => $counts['beneficiaries'], 'label' => 'Scholars'],
                ['icon' => 'globe',          'val' => 4,                         'label' => 'Countries'],
                ['icon' => 'heart',          'val' => '1K+',                     'label' => 'Lives Touched'],
            ];
            foreach ($highlights as $h): ?>
            <div style="text-align:center;padding:16px 20px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:14px;">
                <i data-lucide="<?php echo $h['icon']; ?>" style="width:20px;height:20px;color:#f59e0b;margin:0 auto 8px;display:block;"></i>
                <div style="font-family:'Cormorant Garamond',Georgia,serif;font-size:1.5rem;font-weight:700;color:#fff;"><?php echo $h['val']; ?></div>
                <div style="font-family:'Outfit',sans-serif;font-size:0.68rem;color:rgba(233,213,255,0.6);text-transform:uppercase;letter-spacing:0.08em;"><?php echo $h['label']; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- STATS CARDS GRID -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;margin-bottom:36px;">
        <?php foreach ($cards as $idx => $c): ?>
        <a href="<?php echo BASE_PATH; ?>/admin/<?php echo $c['to']; ?>"
           style="background:#fff;border-radius:18px;padding:24px;text-decoration:none;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1.5px solid #f3e8ff;transition:all 0.3s;display:block;position:relative;overflow:hidden;group;"
           onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 35px rgba(0,0,0,0.1)';this.style.borderColor='<?php echo $c['to_color']; ?>44';"
           onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 12px rgba(0,0,0,0.06)';this.style.borderColor='#f3e8ff';">

            <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,<?php echo $c['from']; ?>12,<?php echo $c['to_color']; ?>08);"></div>

            <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px;">
                <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,<?php echo $c['from']; ?>,<?php echo $c['to_color']; ?>);display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px <?php echo $c['to_color']; ?>40;">
                    <i data-lucide="<?php echo $c['icon']; ?>" style="width:20px;height:20px;color:#fff;"></i>
                </div>
                <i data-lucide="arrow-up-right" style="width:14px;height:14px;color:#d1d5db;margin-top:4px;"></i>
            </div>

            <div style="font-family:'Cormorant Garamond',Georgia,serif;font-size:2.2rem;font-weight:700;color:#1e0a40;line-height:1;margin-bottom:4px;">
                <?php echo $counts[$c['key']]; ?>
            </div>
            <div style="font-family:'Outfit',sans-serif;font-size:0.85rem;font-weight:600;color:#374151;margin-bottom:2px;"><?php echo $c['label']; ?></div>
            <div style="font-family:'Outfit',sans-serif;font-size:0.72rem;color:#9ca3af;"><?php echo $c['desc']; ?></div>
        </a>
        <?php endforeach; ?>

        <!-- Site Settings card -->
        <a href="<?php echo BASE_PATH; ?>/admin/settings.php"
           style="background:linear-gradient(135deg,#1e0a40,#3b0764);border-radius:18px;padding:24px;text-decoration:none;box-shadow:0 4px 20px rgba(59,7,100,0.3);border:1.5px solid rgba(168,85,247,0.2);transition:all 0.3s;display:block;position:relative;overflow:hidden;"
           onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 16px 40px rgba(59,7,100,0.5)';"
           onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 20px rgba(59,7,100,0.3)';">
            <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;background:rgba(245,158,11,0.08);"></div>
            <div style="width:44px;height:44px;border-radius:12px;background:rgba(245,158,11,0.2);border:1px solid rgba(245,158,11,0.3);display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                <i data-lucide="settings" style="width:20px;height:20px;color:#f59e0b;"></i>
            </div>
            <div style="font-family:'Cormorant Garamond',Georgia,serif;font-size:1.5rem;font-weight:700;color:#fff;margin-bottom:4px;">Site Settings</div>
            <div style="font-family:'Outfit',sans-serif;font-size:0.72rem;color:rgba(233,213,255,0.5);">Logo, music, branding, hero content</div>
        </a>
    </div>

    <!-- RECENT ACTIVITY: Messages + Donations side by side -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;flex-wrap:wrap;" class="activity-grid">

        <!-- Recent Messages -->
        <div style="background:#fff;border-radius:20px;padding:28px;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1.5px solid #f3e8ff;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#065f46,#059669);display:flex;align-items:center;justify-content:center;">
                        <i data-lucide="mail" style="width:16px;height:16px;color:#fff;"></i>
                    </div>
                    <div style="font-family:'Outfit',sans-serif;font-size:0.92rem;font-weight:700;color:#1e0a40;">Recent Messages</div>
                </div>
                <a href="<?php echo BASE_PATH; ?>/admin/messages.php" style="font-family:'Outfit',sans-serif;font-size:0.72rem;font-weight:600;color:#7c3aed;text-decoration:none;padding:4px 10px;background:#f3e8ff;border-radius:8px;">View All</a>
            </div>

            <?php if (!empty($recentMessages)): ?>
                <?php foreach ($recentMessages as $msg): ?>
                <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #f9f5ff;">
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#059669,#065f46);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span style="font-family:'Outfit',sans-serif;font-size:0.75rem;font-weight:700;color:#fff;"><?php echo strtoupper(substr($msg['name'],0,1)); ?></span>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-family:'Outfit',sans-serif;font-size:0.83rem;font-weight:600;color:#1e0a40;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo htmlspecialchars($msg['name']); ?></div>
                        <div style="font-family:'Outfit',sans-serif;font-size:0.72rem;color:#9ca3af;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo htmlspecialchars($msg['subject'] ?: 'General Inquiry'); ?></div>
                    </div>
                    <div style="font-family:'Outfit',sans-serif;font-size:0.68rem;color:#c4b5fd;flex-shrink:0;"><?php echo date('M j', strtotime($msg['created_at'])); ?></div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align:center;padding:24px;color:#d1d5db;">
                    <i data-lucide="inbox" style="width:32px;height:32px;margin:0 auto 8px;display:block;"></i>
                    <div style="font-family:'Outfit',sans-serif;font-size:0.82rem;">No messages yet</div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recent Donations -->
        <div style="background:#fff;border-radius:20px;padding:28px;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1.5px solid #f3e8ff;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#9d174d,#db2777);display:flex;align-items:center;justify-content:center;">
                        <i data-lucide="dollar-sign" style="width:16px;height:16px;color:#fff;"></i>
                    </div>
                    <div style="font-family:'Outfit',sans-serif;font-size:0.92rem;font-weight:700;color:#1e0a40;">Recent Donations</div>
                </div>
                <a href="<?php echo BASE_PATH; ?>/admin/donations.php" style="font-family:'Outfit',sans-serif;font-size:0.72rem;font-weight:600;color:#db2777;text-decoration:none;padding:4px 10px;background:#fdf2f8;border-radius:8px;">View All</a>
            </div>

            <?php if (!empty($recentDonations)): ?>
                <?php foreach ($recentDonations as $don): ?>
                <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #fdf2f8;">
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#db2777,#9d174d);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i data-lucide="heart" style="width:14px;height:14px;color:#fff;"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-family:'Outfit',sans-serif;font-size:0.83rem;font-weight:600;color:#1e0a40;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo htmlspecialchars($don['donor_name'] ?: 'Anonymous'); ?></div>
                        <div style="font-family:'Outfit',sans-serif;font-size:0.72rem;color:#9ca3af;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo htmlspecialchars($don['donor_email'] ?: '—'); ?></div>
                    </div>
                    <div>
                        <div style="font-family:'Outfit',sans-serif;font-size:0.85rem;font-weight:700;color:#059669;text-align:right;">$<?php echo number_format($don['amount_cents']/100, 0); ?></div>
                        <div style="font-family:'Outfit',sans-serif;font-size:0.65rem;color:<?php echo $don['status']==='completed'?'#059669':'#d97706'; ?>;text-align:right;text-transform:capitalize;"><?php echo $don['status']; ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align:center;padding:24px;color:#d1d5db;">
                    <i data-lucide="gift" style="width:32px;height:32px;margin:0 auto 8px;display:block;"></i>
                    <div style="font-family:'Outfit',sans-serif;font-size:0.82rem;">No donations yet</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions Footer -->
    <div style="margin-top:32px;padding:24px 28px;background:linear-gradient(135deg,#f3e8ff,#fefce8);border-radius:18px;border:1px solid rgba(124,58,237,0.1);">
        <div style="font-family:'Outfit',sans-serif;font-size:0.7rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#9ca3af;margin-bottom:14px;">Quick Actions</div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <?php
            $quickLinks = [
                ['Add Scholar', 'graduation-cap', 'beneficiaries.php', '#7c3aed'],
                ['Add Team',    'user-plus',      'team.php',          '#d97706'],
                ['New Post',    'edit',           'blog.php',          '#059669'],
                ['Add Program', 'plus-circle',    'programs.php',      '#ea580c'],
                ['View Msgs',   'mail',           'messages.php',      '#0891b2'],
            ];
            foreach ($quickLinks as $ql): ?>
            <a href="<?php echo BASE_PATH; ?>/admin/<?php echo $ql[2]; ?>"
               style="display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:10px;background:#fff;border:1.5px solid <?php echo $ql[3]; ?>22;color:<?php echo $ql[3]; ?>;font-family:'Outfit',sans-serif;font-size:0.78rem;font-weight:700;text-decoration:none;transition:all 0.2s;box-shadow:0 2px 8px rgba(0,0,0,0.05);"
               onmouseover="this.style.background='<?php echo $ql[3]; ?>14';this.style.transform='translateY(-1px)';"
               onmouseout="this.style.background='#fff';this.style.transform='translateY(0)';">
                <i data-lucide="<?php echo $ql[1]; ?>" style="width:13px;height:13px;"></i>
                <?php echo $ql[0]; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: 0.6; transform: scale(0.85); }
}
@media (max-width: 768px) {
    .activity-grid { grid-template-columns: 1fr !important; }
}
</style>

<script>
// Animate total counter
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('total-counter');
    if (!el) return;
    const target = parseFloat(el.dataset.val.replace(/,/g,''));
    const duration = 1800;
    const start = performance.now();
    (function update(now) {
        const progress = Math.min((now - start) / duration, 1);
        const ease = 1 - Math.pow(1 - progress, 3);
        el.textContent = '$' + (target * ease).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (progress < 1) requestAnimationFrame(update);
    })(start);
});
</script>

<?php
renderAdminFooter();
?>
