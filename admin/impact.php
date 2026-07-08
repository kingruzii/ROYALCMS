<?php
/**
 * Admin Panel — Impact Management
 * Manages: Stats Counters, Sector Cards, Testimonials
 */
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/layout.php';

checkAdminAuth();

$error   = '';
$success = '';

// ─── Load current settings from DB ───────────────────────────────────────────
$defaults = [
    'logo'       => '',
    'siteName'   => "Royal Village Int'l",
    'tagline'    => '',
    'stats'      => [
        'scholars'     => 50,
        'countries'    => 4,
        'programs'     => 4,
        'years'        => 6,
        'lives_touched'=> 1000,
        'employment'   => 95,
        'partner_orgs' => 15,
        'continents'   => 3,
    ],
    'sectors'      => [],
    'testimonials' => [],
];

$settings = $defaults;

try {
    $stmt = $pdo->prepare("SELECT value FROM site_settings WHERE id = 'main' LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch();
    if ($row && !empty($row['value'])) {
        $db = json_decode($row['value'], true);
        if (is_array($db)) {
            $settings = array_merge($settings, $db);
            if (isset($db['stats']) && is_array($db['stats'])) {
                $settings['stats'] = array_merge($defaults['stats'], $db['stats']);
            }
            if (!isset($settings['sectors']) || !is_array($settings['sectors'])) {
                $settings['sectors'] = [];
            }
            if (!isset($settings['testimonials']) || !is_array($settings['testimonials'])) {
                $settings['testimonials'] = [];
            }
        }
    }
} catch (Exception $e) {
    $error = 'Could not load settings: ' . $e->getMessage();
}

// ─── Seed default sectors if empty ───────────────────────────────────────────
if (empty($settings['sectors'])) {
    $settings['sectors'] = [
        ['icon'=>'graduation-cap','color'=>'#7e22ce','title'=>'Education',          'desc'=>'Scholars enrolled in top institutions across Africa and Asia.','stat'=>'50+ Scholars Active',   'bar'=>85],
        ['icon'=>'activity',      'color'=>'#d97706','title'=>'Health & Wellness',   'desc'=>'Future nurses, doctors, and health workers trained to serve.', 'stat'=>'12 Healthcare Trainees','bar'=>55],
        ['icon'=>'trending-up',   'color'=>'#059669','title'=>'Leadership & Business','desc'=>'Future leaders, lawyers, and entrepreneurs from our programs.','stat'=>'20+ Future Leaders',    'bar'=>70],
        ['icon'=>'hammer',        'color'=>'#dc2626','title'=>'Vocational Training', 'desc'=>'Practical skills for youth outside the formal education system.','stat'=>'8+ Trades Taught',    'bar'=>40],
    ];
}

// ─── Seed default testimonials if empty ──────────────────────────────────────
if (empty($settings['testimonials'])) {
    $settings['testimonials'] = [
        ['quote'=>'RVIF changed my life completely. Without their scholarship I would never have been able to study medicine.','name'=>'Amara K.','role'=>'Medical Student, Rwanda','photo'=>'https://images.unsplash.com/photo-1531123897727-8f129e1688ce?w=200&q=80'],
        ['quote'=>'The mentorship I received from RVIF went beyond academics. They helped me believe in myself.','name'=>'Emmanuel T.','role'=>'Law Student, Liberia','photo'=>'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&q=80'],
        ['quote'=>'Being an RVIF scholar opened doors I never knew existed. Today I am building the Africa I always dreamed.','name'=>'Fatima J.','role'=>'Engineering Graduate, India','photo'=>'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=200&q=80'],
    ];
}

// ─── Handle POST saves ────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['impact_action'])) {
    $action = $_POST['impact_action'];

    // ── 1. Save Stats ─────────────────────────────────────────────────────────
    if ($action === 'save_stats') {
        $settings['stats'] = [
            'scholars'      => intval($_POST['stat_scholars']     ?? 0),
            'countries'     => intval($_POST['stat_countries']    ?? 0),
            'programs'      => intval($_POST['stat_programs']     ?? 0),
            'years'         => intval($_POST['stat_years']        ?? 0),
            'lives_touched' => intval($_POST['stat_lives_touched']?? 0),
            'employment'    => intval($_POST['stat_employment']   ?? 0),
            'partner_orgs'  => intval($_POST['stat_partner_orgs']?? 0),
            'continents'    => intval($_POST['stat_continents']   ?? 0),
        ];
    }

    // ── 2. Add / Edit Sector ──────────────────────────────────────────────────
    elseif ($action === 'save_sector') {
        $sectorIdx  = isset($_POST['sector_idx']) ? intval($_POST['sector_idx']) : -1;
        $sectorData = [
            'icon'  => trim($_POST['sector_icon']  ?? 'graduation-cap'),
            'color' => trim($_POST['sector_color'] ?? '#7e22ce'),
            'title' => trim($_POST['sector_title'] ?? ''),
            'desc'  => trim($_POST['sector_desc']  ?? ''),
            'stat'  => trim($_POST['sector_stat']  ?? ''),
            'bar'   => min(100, max(0, intval($_POST['sector_bar'] ?? 0))),
        ];
        if (empty($sectorData['title'])) {
            $error = 'Sector title is required.';
        } else {
            if ($sectorIdx >= 0 && isset($settings['sectors'][$sectorIdx])) {
                $settings['sectors'][$sectorIdx] = $sectorData;
                $success = 'Sector updated successfully.';
            } else {
                $settings['sectors'][] = $sectorData;
                $success = 'New sector added successfully.';
            }
        }
    }

    // ── 3. Delete Sector ──────────────────────────────────────────────────────
    elseif ($action === 'delete_sector') {
        $idx = intval($_POST['sector_idx'] ?? -1);
        if (isset($settings['sectors'][$idx])) {
            array_splice($settings['sectors'], $idx, 1);
            $settings['sectors'] = array_values($settings['sectors']);
            $success = 'Sector deleted.';
        }
    }

    // ── 4. Add / Edit Testimonial ─────────────────────────────────────────────
    elseif ($action === 'save_testimonial') {
        $tIdx  = isset($_POST['testi_idx']) ? intval($_POST['testi_idx']) : -1;
        $tData = [
            'quote' => trim($_POST['testi_quote'] ?? ''),
            'name'  => trim($_POST['testi_name']  ?? ''),
            'role'  => trim($_POST['testi_role']  ?? ''),
            'photo' => trim($_POST['testi_photo'] ?? ''),
        ];

        // Handle photo upload
        if (isset($_FILES['testi_photo_file']) && $_FILES['testi_photo_file']['error'] === UPLOAD_ERR_OK) {
            require_once __DIR__ . '/upload_helper.php';
            $upRes = handleAdminUpload('testi_photo_file', 'testimonials');
            if ($upRes['success']) {
                $tData['photo'] = $upRes['url'];
            } else {
                $error .= $upRes['error'] . ' ';
            }
        }

        if (empty($tData['quote']) || empty($tData['name'])) {
            $error = 'Quote and Name are required.';
        } elseif (empty($error)) {
            if ($tIdx >= 0 && isset($settings['testimonials'][$tIdx])) {
                $settings['testimonials'][$tIdx] = $tData;
                $success = 'Testimonial updated successfully.';
            } else {
                $settings['testimonials'][] = $tData;
                $success = 'New testimonial added successfully.';
            }
        }
    }

    // ── 5. Delete Testimonial ─────────────────────────────────────────────────
    elseif ($action === 'delete_testimonial') {
        $idx = intval($_POST['testi_idx'] ?? -1);
        if (isset($settings['testimonials'][$idx])) {
            array_splice($settings['testimonials'], $idx, 1);
            $settings['testimonials'] = array_values($settings['testimonials']);
            $success = 'Testimonial deleted.';
        }
    }

    // ── Persist to DB ─────────────────────────────────────────────────────────
    if (empty($error) || !empty($success)) {
        try {
            $jsonValue = json_encode($settings, JSON_UNESCAPED_UNICODE);
            $now = date('Y-m-d H:i:s');
            $stmt = $pdo->prepare("INSERT INTO site_settings (id, value, updated_at) VALUES ('main', ?, ?) ON DUPLICATE KEY UPDATE value = ?, updated_at = ?");
            $stmt->execute([$jsonValue, $now, $jsonValue, $now]);
            if (empty($success)) $success = 'Changes saved successfully!';
            
            // Set active tab based on action
            if ($action === 'save_sector' || $action === 'delete_sector') {
                header("Location: impact.php?tab=sectors");
                exit();
            } elseif ($action === 'save_testimonial' || $action === 'delete_testimonial') {
                header("Location: impact.php?tab=testimonials");
                exit();
            } else {
                header("Location: impact.php?tab=stats");
                exit();
            }
        } catch (Exception $e) {
            $error = 'Failed to save: ' . $e->getMessage();
        }
    }
}

// ─── Edit state from GET params ───────────────────────────────────────────────
$editSectorIdx  = isset($_GET['edit_sector'])  ? intval($_GET['edit_sector'])  : -1;
$editTestiIdx   = isset($_GET['edit_testi'])   ? intval($_GET['edit_testi'])   : -1;
$showAddSector  = isset($_GET['add_sector']);
$showAddTesti   = isset($_GET['add_testi']);

$editSector = ($editSectorIdx >= 0 && isset($settings['sectors'][$editSectorIdx])) ? $settings['sectors'][$editSectorIdx] : null;
$editTesti  = ($editTestiIdx  >= 0 && isset($settings['testimonials'][$editTestiIdx])) ? $settings['testimonials'][$editTestiIdx] : null;

// Determine active tab for UI rendering
$activeTab = 'stats';
if (isset($_GET['tab'])) {
    $activeTab = trim($_GET['tab']);
} elseif ($showAddSector || $editSector) {
    $activeTab = 'sectors';
} elseif ($showAddTesti || $editTesti) {
    $activeTab = 'testimonials';
}

renderAdminHeader('impact');
?>

<style>
/* ── Impact page-specific styles ─────────────────────────────── */
.imp-tab-btn {
    padding: 10px 22px;
    border-radius: 10px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    background: transparent;
    color: rgba(124,58,237,0.6);
    letter-spacing: 0.02em;
}
.imp-tab-btn.active {
    background: linear-gradient(135deg,#7c3aed,#5b21b6);
    color: #fff;
    box-shadow: 0 4px 16px rgba(124,58,237,0.3);
}
.imp-tab-btn:hover:not(.active) { background: #f3e8ff; color: #5b21b6; }

.imp-panel { display: none; }
.imp-panel.active { display: block; }

.stat-card {
    background: #fff;
    border: 1px solid #ede9fe;
    border-radius: 16px;
    padding: 22px 20px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    transition: box-shadow 0.2s, transform 0.2s;
}
.stat-card:hover { box-shadow: 0 8px 28px rgba(124,58,237,0.1); transform: translateY(-2px); }

.stat-icon-wrap {
    width: 46px; height: 46px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 4px;
}

.sector-row, .testi-row {
    background: #fff;
    border: 1px solid #ede9fe;
    border-radius: 16px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: box-shadow 0.2s;
}
.sector-row:hover, .testi-row:hover { box-shadow: 0 6px 24px rgba(124,58,237,0.09); }

.progress-pill {
    height: 6px; border-radius: 3px;
    background: #ede9fe; overflow: hidden; flex: 1;
}
.progress-fill { height: 100%; border-radius: 3px; transition: width 0.6s ease; }

.action-btn {
    padding: 6px 14px; border-radius: 8px;
    font-size: 0.75rem; font-weight: 600;
    cursor: pointer; border: none;
    display: inline-flex; align-items: center; gap: 5px;
    transition: all 0.18s;
    text-decoration: none;
}
.btn-edit  { background: #f3e8ff; color: #5b21b6; }
.btn-edit:hover  { background: #ede9fe; }
.btn-delete { background: #fff1f2; color: #be123c; }
.btn-delete:hover { background: #ffe4e6; }
.btn-primary { background: linear-gradient(135deg,#7c3aed,#5b21b6); color: #fff; box-shadow: 0 3px 12px rgba(124,58,237,0.25); }
.btn-primary:hover { box-shadow: 0 6px 20px rgba(124,58,237,0.35); transform: translateY(-1px); }

.modal-backdrop {
    position: fixed; inset: 0;
    background: rgba(15,5,35,0.6);
    backdrop-filter: blur(4px);
    z-index: 200;
    display: flex; align-items: center; justify-content: center; padding: 20px;
}
.modal-box {
    background: #fff; border-radius: 24px;
    width: 100%; max-width: 540px;
    padding: 32px; box-shadow: 0 24px 80px rgba(0,0,0,0.2);
    max-height: 90vh; overflow-y: auto;
}

.form-label { font-size: 0.72rem; font-weight: 700; color: #6b21a8; text-transform: uppercase; letter-spacing: 0.06em; display: block; margin-bottom: 5px; }
.form-input {
    width: 100%; padding: 10px 14px;
    border: 1.5px solid #ede9fe; border-radius: 10px;
    font-family: 'Outfit', sans-serif; font-size: 0.87rem; color: #1e0a40;
    outline: none; transition: border-color 0.2s, box-shadow 0.2s;
}
.form-input:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,0.12); }
</style>

<div style="padding: 36px 40px; max-width: 1100px;">

    <!-- ── Header ──────────────────────────────────────────────────────── -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 32px; flex-wrap:wrap; gap:16px;">
        <div>
            <h1 style="font-family:'Cormorant Garamond',Georgia,serif; font-size:2rem; font-weight:700; color:#1e0a40; margin:0 0 4px;">Impact Management</h1>
            <p style="font-size:0.85rem; color:#9ca3af; margin:0;">Control stats counters, sector cards, and testimonials shown on the Impact page.</p>
        </div>
        <a href="<?php echo BASE_PATH; ?>/impact.php" target="_blank" class="action-btn btn-edit" style="font-size:0.8rem;">
            <i data-lucide="external-link" style="width:14px;height:14px;"></i> Preview Impact Page
        </a>
    </div>

    <!-- ── Alerts ──────────────────────────────────────────────────────── -->
    <?php if (!empty($success)): ?>
    <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:14px 18px; border-radius:12px; font-size:0.85rem; font-weight:600; margin-bottom:24px; display:flex; align-items:center; gap:10px;">
        <i data-lucide="check-circle-2" style="width:18px;height:18px;flex-shrink:0;"></i> <?php echo htmlspecialchars($success); ?>
    </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
    <div style="background:#fff1f2; border:1px solid #fecdd3; color:#be123c; padding:14px 18px; border-radius:12px; font-size:0.85rem; font-weight:600; margin-bottom:24px; display:flex; align-items:center; gap:10px;">
        <i data-lucide="alert-circle" style="width:18px;height:18px;flex-shrink:0;"></i> <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <!-- ── Tabs ──────────────────────────────────────────────────────────── -->
    <div style="display:flex; gap:6px; background:#faf5ff; padding:6px; border-radius:14px; margin-bottom:32px; width:fit-content;">
        <button class="imp-tab-btn <?php echo $activeTab === 'stats' ? 'active' : ''; ?>" onclick="switchTab('stats', this)">
            <i data-lucide="bar-chart-2" style="width:14px;height:14px;display:inline;vertical-align:-2px;margin-right:5px;"></i>Stats
        </button>
        <button class="imp-tab-btn <?php echo $activeTab === 'sectors' ? 'active' : ''; ?>" onclick="switchTab('sectors', this)">
            <i data-lucide="layout-grid" style="width:14px;height:14px;display:inline;vertical-align:-2px;margin-right:5px;"></i>Sectors
        </button>
        <button class="imp-tab-btn <?php echo $activeTab === 'testimonials' ? 'active' : ''; ?>" onclick="switchTab('testimonials', this)">
            <i data-lucide="quote" style="width:14px;height:14px;display:inline;vertical-align:-2px;margin-right:5px;"></i>Testimonials
        </button>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════
         TAB 1: STATS
    ═══════════════════════════════════════════════════════════════════════ -->
    <div id="tab-stats" class="imp-panel <?php echo $activeTab === 'stats' ? 'active' : ''; ?>">
        <form method="POST" action="impact.php">
            <input type="hidden" name="impact_action" value="save_stats">

            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:18px; margin-bottom:28px;">
                <?php
                $statDefs = [
                    ['key'=>'scholars',      'label'=>'Scholars Sponsored', 'icon'=>'graduation-cap','gradient'=>'linear-gradient(135deg,#7c3aed,#5b21b6)','suffix'=>'+'],
                    ['key'=>'countries',     'label'=>'Countries Reached',  'icon'=>'globe',         'gradient'=>'linear-gradient(135deg,#d97706,#b45309)','suffix'=>''],
                    ['key'=>'programs',      'label'=>'Active Programs',    'icon'=>'award',          'gradient'=>'linear-gradient(135deg,#059669,#047857)','suffix'=>''],
                    ['key'=>'years',         'label'=>'Years of Service',   'icon'=>'calendar',       'gradient'=>'linear-gradient(135deg,#dc2626,#b91c1c)','suffix'=>'+'],
                    ['key'=>'lives_touched', 'label'=>'Lives Touched',      'icon'=>'heart',          'gradient'=>'linear-gradient(135deg,#7c3aed,#4c1d95)','suffix'=>'+'],
                    ['key'=>'employment',    'label'=>'Graduate Employment %','icon'=>'briefcase',    'gradient'=>'linear-gradient(135deg,#0891b2,#0e7490)','suffix'=>'%'],
                    ['key'=>'partner_orgs',  'label'=>'Partner Orgs',       'icon'=>'handshake',      'gradient'=>'linear-gradient(135deg,#db2777,#9d174d)','suffix'=>''],
                    ['key'=>'continents',    'label'=>'Continents Impacted','icon'=>'map',            'gradient'=>'linear-gradient(135deg,#ea580c,#c2410c)','suffix'=>''],
                ];
                foreach ($statDefs as $sd):
                    $val = $settings['stats'][$sd['key']] ?? 0;
                ?>
                <div class="stat-card">
                    <div class="stat-icon-wrap" style="background:<?php echo $sd['gradient']; ?>;">
                        <i data-lucide="<?php echo $sd['icon']; ?>" style="width:20px;height:20px;color:#fff;"></i>
                    </div>
                    <label class="form-label" for="stat_<?php echo $sd['key']; ?>"><?php echo $sd['label']; ?></label>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <input type="number" id="stat_<?php echo $sd['key']; ?>"
                               name="stat_<?php echo $sd['key']; ?>"
                               value="<?php echo intval($val); ?>"
                               min="0"
                               class="form-input"
                               style="max-width:120px;">
                        <?php if ($sd['suffix']): ?>
                        <span style="font-size:1.1rem;font-weight:700;color:#7c3aed;"><?php echo $sd['suffix']; ?></span>
                        <?php endif; ?>
                    </div>
                    <div style="font-size:0.72rem;color:#a78bfa;font-weight:500;">Current: <strong><?php echo number_format($val).$sd['suffix']; ?></strong></div>
                </div>
                <?php endforeach; ?>
            </div>

            <div style="display:flex;justify-content:flex-end;">
                <button type="submit" class="action-btn btn-primary" style="padding:12px 32px;font-size:0.9rem;">
                    <i data-lucide="save" style="width:16px;height:16px;"></i> Save All Stats
                </button>
            </div>
        </form>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════
         TAB 2: SECTORS
    ═══════════════════════════════════════════════════════════════════════ -->
    <div id="tab-sectors" class="imp-panel <?php echo $activeTab === 'sectors' ? 'active' : ''; ?>">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <p style="font-size:0.85rem;color:#6b7280;margin:0;"><?php echo count($settings['sectors']); ?> sector card<?php echo count($settings['sectors']) !== 1 ? 's' : ''; ?> on the Impact page.</p>
            <a href="impact.php?add_sector=1" class="action-btn btn-primary">
                <i data-lucide="plus" style="width:14px;height:14px;"></i> Add Sector
            </a>
        </div>

        <div style="display:flex;flex-direction:column;gap:14px;">
        <?php foreach ($settings['sectors'] as $idx => $sec): ?>
            <div class="sector-row">
                <!-- Icon -->
                <div style="width:48px;height:48px;border-radius:12px;background:<?php echo htmlspecialchars($sec['color']); ?>1a;border:1.5px solid <?php echo htmlspecialchars($sec['color']); ?>33;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i data-lucide="<?php echo htmlspecialchars($sec['icon']); ?>" style="width:22px;height:22px;color:<?php echo htmlspecialchars($sec['color']); ?>;"></i>
                </div>
                <!-- Info -->
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:700;color:#1e0a40;font-size:0.95rem;margin-bottom:2px;"><?php echo htmlspecialchars($sec['title']); ?></div>
                    <div style="font-size:0.78rem;color:#9ca3af;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:400px;"><?php echo htmlspecialchars($sec['desc']); ?></div>
                    <!-- Progress -->
                    <div style="display:flex;align-items:center;gap:10px;margin-top:8px;">
                        <div class="progress-pill">
                            <div class="progress-fill" style="width:<?php echo intval($sec['bar']); ?>%;background:<?php echo htmlspecialchars($sec['color']); ?>;"></div>
                        </div>
                        <span style="font-size:0.72rem;font-weight:700;color:<?php echo htmlspecialchars($sec['color']); ?>;"><?php echo intval($sec['bar']); ?>%</span>
                        <span style="font-size:0.72rem;color:#a78bfa;font-weight:600;"><?php echo htmlspecialchars($sec['stat']); ?></span>
                    </div>
                </div>
                <!-- Actions -->
                <div style="display:flex;gap:8px;flex-shrink:0;">
                    <a href="impact.php?edit_sector=<?php echo $idx; ?>" class="action-btn btn-edit">
                        <i data-lucide="edit-2" style="width:13px;height:13px;"></i> Edit
                    </a>
                    <form method="POST" action="impact.php" style="margin:0;" onsubmit="return confirm('Delete this sector?');">
                        <input type="hidden" name="impact_action" value="delete_sector">
                        <input type="hidden" name="sector_idx" value="<?php echo $idx; ?>">
                        <button type="submit" class="action-btn btn-delete">
                            <i data-lucide="trash-2" style="width:13px;height:13px;"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($settings['sectors'])): ?>
            <div style="text-align:center;padding:48px;color:#c4b5fd;font-size:0.9rem;">No sectors yet. <a href="impact.php?add_sector=1" style="color:#7c3aed;font-weight:600;">Add the first one →</a></div>
        <?php endif; ?>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════
         TAB 3: TESTIMONIALS
    ═══════════════════════════════════════════════════════════════════════ -->
    <div id="tab-testimonials" class="imp-panel <?php echo $activeTab === 'testimonials' ? 'active' : ''; ?>">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <p style="font-size:0.85rem;color:#6b7280;margin:0;"><?php echo count($settings['testimonials']); ?> testimonial<?php echo count($settings['testimonials']) !== 1 ? 's' : ''; ?> displayed on the Impact page.</p>
            <a href="impact.php?add_testi=1" class="action-btn btn-primary">
                <i data-lucide="plus" style="width:14px;height:14px;"></i> Add Testimonial
            </a>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:18px;">
        <?php foreach ($settings['testimonials'] as $idx => $t): ?>
            <div class="testi-row" style="flex-direction:column;align-items:flex-start;">
                <!-- Quote -->
                <div style="font-size:1.6rem;color:#f59e0b;line-height:1;margin-bottom:6px;">"</div>
                <p style="font-size:0.85rem;color:#3b0764;font-style:italic;line-height:1.65;margin:0 0 14px;flex:1;"><?php echo htmlspecialchars($t['quote']); ?></p>
                <!-- Person -->
                <div style="display:flex;align-items:center;gap:10px;width:100%;padding-top:12px;border-top:1px solid #f3e8ff;">
                    <img src="<?php echo htmlspecialchars($t['photo'] ?? ''); ?>" alt="<?php echo htmlspecialchars($t['name']); ?>" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($t['name']); ?>&background=7c3aed&color=fff&size=80'" style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #e9d5ff;flex-shrink:0;">
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:700;font-size:0.85rem;color:#1e0a40;"><?php echo htmlspecialchars($t['name']); ?></div>
                        <div style="font-size:0.72rem;color:#a78bfa;"><?php echo htmlspecialchars($t['role'] ?? ''); ?></div>
                    </div>
                    <div style="display:flex;gap:6px;">
                        <a href="impact.php?edit_testi=<?php echo $idx; ?>" class="action-btn btn-edit" style="padding:5px 10px;">
                            <i data-lucide="edit-2" style="width:12px;height:12px;"></i>
                        </a>
                        <form method="POST" action="impact.php" style="margin:0;" onsubmit="return confirm('Delete this testimonial?');">
                            <input type="hidden" name="impact_action" value="delete_testimonial">
                            <input type="hidden" name="testi_idx" value="<?php echo $idx; ?>">
                            <button type="submit" class="action-btn btn-delete" style="padding:5px 10px;">
                                <i data-lucide="trash-2" style="width:12px;height:12px;"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($settings['testimonials'])): ?>
            <div style="text-align:center;padding:48px;color:#c4b5fd;font-size:0.9rem;grid-column:1/-1;">No testimonials yet. <a href="impact.php?add_testi=1" style="color:#7c3aed;font-weight:600;">Add the first one →</a></div>
        <?php endif; ?>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════════════════
     MODAL: Add / Edit Sector
═════════════════════════════════════════════════════════════════════════ -->
<?php if ($showAddSector || $editSector):
    $sec = $editSector ?: ['icon'=>'graduation-cap','color'=>'#7e22ce','title'=>'','desc'=>'','stat'=>'','bar'=>50];
    $secIdx = $editSectorIdx;
?>
<div class="modal-backdrop">
    <div class="modal-box">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
            <h2 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:1.5rem;font-weight:700;color:#1e0a40;margin:0;">
                <?php echo $editSector ? 'Edit Sector' : 'Add Sector'; ?>
            </h2>
            <a href="impact.php?tab=sectors" style="color:#9ca3af;transition:color 0.2s;" onmouseover="this.style.color='#1e0a40'" onmouseout="this.style.color='#9ca3af'">
                <i data-lucide="x" style="width:22px;height:22px;"></i>
            </a>
        </div>

        <form method="POST" action="impact.php" class="space-y-4">
            <input type="hidden" name="impact_action" value="save_sector">
            <input type="hidden" name="sector_idx" value="<?php echo $secIdx; ?>">

            <div style="margin-bottom:14px;">
                <label class="form-label">Sector Title *</label>
                <input type="text" name="sector_title" value="<?php echo htmlspecialchars($sec['title']); ?>" required class="form-input" placeholder="e.g. Education">
            </div>
            <div style="margin-bottom:14px;">
                <label class="form-label">Description</label>
                <textarea name="sector_desc" rows="3" class="form-input" placeholder="Brief description of this sector..."><?php echo htmlspecialchars($sec['desc']); ?></textarea>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                <div>
                    <label class="form-label">Lucide Icon Name</label>
                    <input type="text" name="sector_icon" value="<?php echo htmlspecialchars($sec['icon']); ?>" class="form-input" placeholder="graduation-cap">
                    <a href="https://lucide.dev/icons/" target="_blank" style="font-size:0.7rem;color:#7c3aed;text-decoration:none;">Browse icons →</a>
                </div>
                <div>
                    <label class="form-label">Theme Color</label>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <input type="color" name="sector_color" value="<?php echo htmlspecialchars($sec['color']); ?>" style="width:42px;height:38px;border:1.5px solid #ede9fe;border-radius:8px;cursor:pointer;padding:2px;">
                        <input type="text" id="sector_color_hex" value="<?php echo htmlspecialchars($sec['color']); ?>" class="form-input" placeholder="#7e22ce" oninput="syncColor(this.value)">
                    </div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:24px;">
                <div>
                    <label class="form-label">Stat Label</label>
                    <input type="text" name="sector_stat" value="<?php echo htmlspecialchars($sec['stat']); ?>" class="form-input" placeholder="50+ Scholars Active">
                </div>
                <div>
                    <label class="form-label">Progress Bar % (0–100)</label>
                    <div style="display:flex;gap:10px;align-items:center;">
                        <input type="range" name="sector_bar" id="sector_bar_range" min="0" max="100" value="<?php echo intval($sec['bar']); ?>" style="flex:1;" oninput="document.getElementById('sector_bar_num').value=this.value;">
                        <input type="number" id="sector_bar_num" min="0" max="100" value="<?php echo intval($sec['bar']); ?>" class="form-input" style="width:65px;" oninput="let v=parseInt(this.value); if(!isNaN(v) && v>=0 && v<=100) { document.getElementById('sector_bar_range').value=v; }">
                    </div>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:16px;border-top:1px solid #f3e8ff;">
                <a href="impact.php?tab=sectors" class="action-btn btn-edit" style="padding:10px 22px;">Cancel</a>
                <button type="submit" class="action-btn btn-primary" style="padding:10px 26px;">
                    <i data-lucide="save" style="width:15px;height:15px;"></i> Save Sector
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- ════════════════════════════════════════════════════════════════════════
     MODAL: Add / Edit Testimonial
═════════════════════════════════════════════════════════════════════════ -->
<?php if ($showAddTesti || $editTesti):
    $t = $editTesti ?: ['quote'=>'','name'=>'','role'=>'','photo'=>''];
    $tIdx = $editTestiIdx;
?>
<div class="modal-backdrop">
    <div class="modal-box">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
            <h2 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:1.5rem;font-weight:700;color:#1e0a40;margin:0;">
                <?php echo $editTesti ? 'Edit Testimonial' : 'Add Testimonial'; ?>
            </h2>
            <a href="impact.php?tab=testimonials" style="color:#9ca3af;transition:color 0.2s;">
                <i data-lucide="x" style="width:22px;height:22px;"></i>
            </a>
        </div>

        <form method="POST" action="impact.php" enctype="multipart/form-data">
            <input type="hidden" name="impact_action" value="save_testimonial">
            <input type="hidden" name="testi_idx" value="<?php echo $tIdx; ?>">

            <div style="margin-bottom:14px;">
                <label class="form-label">Quote *</label>
                <textarea name="testi_quote" rows="4" required class="form-input" placeholder="Write the scholar's testimonial..."><?php echo htmlspecialchars($t['quote']); ?></textarea>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                <div>
                    <label class="form-label">Name *</label>
                    <input type="text" name="testi_name" value="<?php echo htmlspecialchars($t['name']); ?>" required class="form-input" placeholder="Amara K.">
                </div>
                <div>
                    <label class="form-label">Role / Institution</label>
                    <input type="text" name="testi_role" value="<?php echo htmlspecialchars($t['role'] ?? ''); ?>" class="form-input" placeholder="Medical Student, Rwanda">
                </div>
            </div>
            <div style="margin-bottom:14px;">
                <label class="form-label">Photo URL</label>
                <input type="text" name="testi_photo" value="<?php echo htmlspecialchars($t['photo'] ?? ''); ?>" class="form-input" placeholder="https://...">
            </div>
            <div style="margin-bottom:24px;">
                <label class="form-label">Or Upload Photo</label>
                <input type="file" name="testi_photo_file" accept="image/*" style="font-size:0.82rem;color:#6b7280;">
            </div>
            <?php if (!empty($t['photo'])): ?>
            <div style="margin-bottom:18px;display:flex;align-items:center;gap:12px;">
                <img src="<?php echo htmlspecialchars($t['photo']); ?>" alt="" style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid #e9d5ff;">
                <span style="font-size:0.75rem;color:#9ca3af;">Current photo</span>
            </div>
            <?php endif; ?>

            <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:16px;border-top:1px solid #f3e8ff;">
                <a href="impact.php?tab=testimonials" class="action-btn btn-edit" style="padding:10px 22px;">Cancel</a>
                <button type="submit" class="action-btn btn-primary" style="padding:10px 26px;">
                    <i data-lucide="save" style="width:15px;height:15px;"></i> Save Testimonial
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
// ── Tab switching ─────────────────────────────────────────────────────────────
function switchTab(name, btn) {
    document.querySelectorAll('.imp-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.imp-tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
    // Update URL so page refreshes land on same tab
    history.replaceState(null, '', location.pathname + '?tab=' + name);
}

// ── Color hex sync in sector modal ────────────────────────────────────────────
function syncColor(hex) {
    const colorInput = document.querySelector('input[type="color"][name="sector_color"]');
    if (colorInput && /^#[0-9A-Fa-f]{6}$/.test(hex)) colorInput.value = hex;
}

document.addEventListener('DOMContentLoaded', function () {
    const colorPicker = document.querySelector('input[type="color"][name="sector_color"]');
    if (colorPicker) {
        colorPicker.addEventListener('input', function () {
            const hexField = document.getElementById('sector_color_hex');
            if (hexField) hexField.value = this.value;
        });
    }

    // ── Restore active tab from URL ─────────────────────────────────────────
    const params = new URLSearchParams(location.search);
    const tab = params.get('tab');
    if (tab) {
        const btn = document.querySelector(`.imp-tab-btn[onclick*="'${tab}'"]`);
        if (btn) switchTab(tab, btn);
    }
});
</script>

<?php
renderAdminFooter();
?>