<?php
/**
 * Admin Panel — Banner & Hero Slides Management
 * Manages: Slider Backgrounds, Tags, Overlay Headlines
 */
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/layout.php';

checkAdminAuth();

$error   = '';
$success = '';

// ─── Load current settings from DB ───────────────────────────────────────────
$defaults = [
    'heroTitle'    => "Empowering Africa's Future Through Education",
    'heroSubtitle' => "Royal Village International Foundation provides scholarships, vocational training, and community support.",
    'heroSlides'   => [],
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
            if (!isset($settings['heroSlides']) || !is_array($settings['heroSlides'])) {
                $settings['heroSlides'] = [];
            }
        }
    }
} catch (Exception $e) {
    $error = 'Could not load settings: ' . $e->getMessage();
}

// ─── Seed default slides if empty ────────────────────────────────────────────
if (empty($settings['heroSlides'])) {
    $settings['heroSlides'] = [
        ['img' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=1920&q=80', 'tag' => 'Education First'],
        ['img' => 'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?w=1920&q=80', 'tag' => 'Empowering Scholars'],
        ['img' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=1920&q=80', 'tag' => 'Community Growth'],
        ['img' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=1920&q=80', 'tag' => 'Brighter Futures'],
    ];
}

// ─── Handle POST actions (MUST be before any output) ─────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['banner_action'])) {
    $action = $_POST['banner_action'];
    $shouldRedirect = true;

    // ── 1. Save Overlay Headlines ─────────────────────────────────────────────
    if ($action === 'save_overlays') {
        $settings['heroTitle']    = trim($_POST['heroTitle'] ?? '');
        $settings['heroSubtitle'] = trim($_POST['heroSubtitle'] ?? '');
        $success = 'Overlay headlines saved successfully.';
    }

    // ── 2. Add / Edit Slide ───────────────────────────────────────────────────
    elseif ($action === 'save_slide') {
        $slideIdx  = isset($_POST['slide_idx']) ? intval($_POST['slide_idx']) : -1;
        $slideData = [
            'tag' => trim($_POST['slide_tag'] ?? ''),
            'img' => trim($_POST['slide_img'] ?? ''),
        ];

        // Process slide background image upload
        if (isset($_FILES['slide_file']) && $_FILES['slide_file']['error'] === UPLOAD_ERR_OK) {
            require_once __DIR__ . '/upload_helper.php';
            $upRes = handleAdminUpload('slide_file', 'hero');
            if ($upRes['success']) {
                $slideData['img'] = $upRes['url'];
            } else {
                $error .= $upRes['error'] . ' ';
            }
        }

        if (empty($slideData['img'])) {
            $error = 'A slide background picture is required (either upload an image or paste a URL).';
        } elseif (empty($slideData['tag'])) {
            $error = 'A slide tag caption is required.';
        } else {
            if ($slideIdx >= 0 && isset($settings['heroSlides'][$slideIdx])) {
                $settings['heroSlides'][$slideIdx] = $slideData;
                $success = 'Slide updated successfully.';
            } else {
                $settings['heroSlides'][] = $slideData;
                $success = 'New slide added successfully.';
            }
        }
    }

    // ── 3. Delete Slide ───────────────────────────────────────────────────────
    elseif ($action === 'delete_slide') {
        $idx = intval($_POST['slide_idx'] ?? -1);
        if (isset($settings['heroSlides'][$idx])) {
            array_splice($settings['heroSlides'], $idx, 1);
            $settings['heroSlides'] = array_values($settings['heroSlides']);
            $success = 'Slide deleted successfully.';
        }
    }

    // ── Persist to DB securely ────────────────────────────────────────────────
    if (empty($error)) {
        try {
            // Load latest state from DB to guarantee other custom blocks are not overwritten
            $stmt = $pdo->prepare("SELECT value FROM site_settings WHERE id = 'main' LIMIT 1");
            $stmt->execute();
            $dbRow = $stmt->fetch();
            $fullSettings = [];
            if ($dbRow && !empty($dbRow['value'])) {
                $fullSettings = json_decode($dbRow['value'], true);
            }
            if (!is_array($fullSettings)) {
                $fullSettings = [];
            }

            // Merge our updated values
            $fullSettings = array_merge($fullSettings, $settings);

            $jsonValue = json_encode($fullSettings, JSON_UNESCAPED_UNICODE);
            $now = date('Y-m-d H:i:s');
            $stmt = $pdo->prepare("INSERT INTO site_settings (id, value, updated_at) VALUES ('main', ?, ?) ON DUPLICATE KEY UPDATE value = ?, updated_at = ?");
            $stmt->execute([$jsonValue, $now, $jsonValue, $now]);
            
            // Refresh local state
            $settings = $fullSettings;
            
            // Redirect to prevent form resubmission
            header("Location: banners.php");
            exit();
        } catch (Exception $e) {
            $error = 'Failed to save changes: ' . $e->getMessage();
        }
    }
}

// ─── Edit states from GET params ──────────────────────────────────────────────
$editSlideIdx = isset($_GET['edit_slide']) ? intval($_GET['edit_slide']) : -1;
$showAddSlide = isset($_GET['add_slide']);

$editSlide = ($editSlideIdx >= 0 && isset($settings['heroSlides'][$editSlideIdx])) ? $settings['heroSlides'][$editSlideIdx] : null;

// Render the header (ONCE!) - This is now safe because any redirects have already happened
renderAdminHeader('banners');
?>

<style>
/* ── Page Specific Styling ────────────────────────────────────── */
.banner-card {
    background: #fff;
    border: 1px solid #ede9fe;
    border-radius: 20px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 12px rgba(124,58,237,0.03);
    transition: transform 0.2s, box-shadow 0.2s;
}
.banner-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(124,58,237,0.08);
}
.banner-thumb {
    height: 180px;
    background-size: cover;
    background-position: center;
    position: relative;
    display: flex;
    align-items: flex-end;
    padding: 16px;
}
.banner-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(30,10,64,0.7) 0%, transparent 65%);
    z-index: 1;
}
.tag-pill {
    position: relative;
    z-index: 2;
    background: rgba(245,158,11,0.92);
    color: #3b0764;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 5px 14px;
    border-radius: 50px;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    box-shadow: 0 4px 10px rgba(245,158,11,0.25);
}

.action-btn {
    padding: 6px 14px; 
    border-radius: 8px;
    font-size: 0.75rem; 
    font-weight: 600;
    cursor: pointer; 
    border: none;
    display: inline-flex; 
    align-items: center; 
    gap: 5px;
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
    position: fixed; 
    inset: 0;
    background: rgba(15,5,35,0.6);
    backdrop-filter: blur(4px);
    z-index: 200;
    display: flex; 
    align-items: center; 
    justify-content: center; 
    padding: 20px;
}
.modal-box {
    background: #fff; 
    border-radius: 24px;
    width: 100%; 
    max-width: 520px;
    padding: 32px; 
    box-shadow: 0 24px 80px rgba(0,0,0,0.2);
    max-height: 90vh; 
    overflow-y: auto;
}

.form-label { 
    font-size: 0.72rem; 
    font-weight: 700; 
    color: #6b21a8; 
    text-transform: uppercase; 
    letter-spacing: 0.06em; 
    display: block; 
    margin-bottom: 5px; 
}
.form-input {
    width: 100%; 
    padding: 10px 14px;
    border: 1.5px solid #ede9fe; 
    border-radius: 10px;
    font-family: 'Outfit', sans-serif; 
    font-size: 0.87rem; 
    color: #1e0a40;
    outline: none; 
    transition: border-color 0.2s, box-shadow 0.2s;
}
.form-input:focus { 
    border-color: #7c3aed; 
    box-shadow: 0 0 0 3px rgba(124,58,237,0.12); 
}
</style>

<div style="padding: 36px 40px; max-width: 1100px;">

    <!-- ── Header ──────────────────────────────────────────────────────── -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 32px; flex-wrap:wrap; gap:16px;">
        <div>
            <h1 style="font-family:'Cormorant Garamond',Georgia,serif; font-size:2rem; font-weight:700; color:#1e0a40; margin:0 0 4px;">Banner & Hero Slider</h1>
            <p style="font-size:0.85rem; color:#9ca3af; margin:0;">Configure the landing page overlays and background slide images rotating on the homepage.</p>
        </div>
        <a href="<?php echo BASE_PATH; ?>/index.php" target="_blank" class="action-btn btn-edit" style="font-size:0.8rem;">
            <i data-lucide="external-link" style="width:14px;height:14px;"></i> Preview Homepage
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

    <!-- ════════════════════════════════════════════════════════════════════
         SECTION 1: OVERLAY HEADLINES
    ═══════════════════════════════════════════════════════════════════════ -->
    <div style="background:#fff; border:1px solid #ede9fe; border-radius:24px; padding:28px; margin-bottom:36px; box-shadow: 0 4px 18px rgba(124,58,237,0.02);">
        <h2 style="font-family:'Cormorant Garamond',Georgia,serif; font-size:1.4rem; font-weight:700; color:#1e0a40; margin:0 0 6px; display:flex; align-items:center; gap:8px;">
            <i data-lucide="type" style="width:20px;height:20px;color:#7c3aed;"></i> Text Overlay Headlines
        </h2>
        <p style="font-size:0.8rem; color:#9ca3af; margin:0 24px 20px 0;">Customize the main headline title and subtitle text layered over the sliding banners.</p>
        
        <form method="POST" action="banners.php" class="space-y-4">
            <input type="hidden" name="banner_action" value="save_overlays">
            
            <div style="margin-bottom:14px;">
                <label class="form-label" for="heroTitle">Hero Main Headline</label>
                <input type="text" id="heroTitle" name="heroTitle" value="<?php echo htmlspecialchars($settings['heroTitle']); ?>" required class="form-input" style="font-size:0.92rem;" placeholder="Empowering Africa's Future...">
            </div>
            
            <div style="margin-bottom:18px;">
                <label class="form-label" for="heroSubtitle">Hero Sub-Headline Description</label>
                <textarea id="heroSubtitle" name="heroSubtitle" rows="3" required class="form-input" placeholder="Providing scholarships and community support..."><?php echo htmlspecialchars($settings['heroSubtitle']); ?></textarea>
            </div>
            
            <div style="display:flex; justify-content:flex-end;">
                <button type="submit" class="action-btn btn-primary" style="padding:10px 24px;">
                    <i data-lucide="save" style="width:15px;height:15px;"></i> Save Overlay text
                </button>
            </div>
        </form>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════
         SECTION 2: SLIDE BANNER ROSTER
    ═══════════════════════════════════════════════════════════════════════ -->
    <div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <div>
                <h2 style="font-family:'Cormorant Garamond',Georgia,serif; font-size:1.4rem; font-weight:700; color:#1e0a40; margin:0 0 4px; display:flex; align-items:center; gap:8px;">
                    <i data-lucide="images" style="width:20px;height:20px;color:#f59e0b;"></i> Slider Background Cards
                </h2>
                <p style="font-size:0.8rem; color:#9ca3af; margin:0;"><?php echo count($settings['heroSlides']); ?> slide image<?php echo count($settings['heroSlides']) !== 1 ? 's' : ''; ?> currently rotating on the landing slide.</p>
            </div>
            <a href="banners.php?add_slide=1" class="action-btn btn-primary">
                <i data-lucide="plus" style="width:15px;height:15px;"></i> Add Slide Card
            </a>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:24px;">
            <?php foreach ($settings['heroSlides'] as $idx => $slide): ?>
            <div class="banner-card">
                <!-- Thumbnail with tag -->
                <div class="banner-thumb" style="background-image: url('<?php echo htmlspecialchars($slide['img']); ?>');">
                    <div class="banner-overlay"></div>
                    <span class="tag-pill">Slide <?php echo ($idx + 1); ?>: <?php echo htmlspecialchars($slide['tag']); ?></span>
                </div>
                <!-- Card footer action bar -->
                <div style="padding:16px 20px; background:#fff; border-top:1px solid #f5f3ff; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:0.75rem; color:#9ca3af; text-overflow:ellipsis; overflow:hidden; white-space:nowrap; max-width:160px;" title="<?php echo htmlspecialchars($slide['img']); ?>">
                        <?php echo htmlspecialchars(basename($slide['img'])); ?>
                    </span>
                    <div style="display:flex; gap:8px;">
                        <a href="banners.php?edit_slide=<?php echo $idx; ?>" class="action-btn btn-edit" style="padding:5px 10px;">
                            <i data-lucide="edit-2" style="width:13px;height:13px;"></i> Edit
                        </a>
                        <form method="POST" action="banners.php" style="margin:0;" onsubmit="return confirm('Delete this banner slide card?');">
                            <input type="hidden" name="banner_action" value="delete_slide">
                            <input type="hidden" name="slide_idx" value="<?php echo $idx; ?>">
                            <button type="submit" class="action-btn btn-delete" style="padding:5px 10px;">
                                <i data-lucide="trash-2" style="width:13px;height:13px;"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($settings['heroSlides'])): ?>
            <div style="grid-column: 1/-1; text-align:center; padding:64px; color:#c4b5fd; font-size:0.9rem;">
                No slides created yet. <a href="banners.php?add_slide=1" style="color:#7c3aed; font-weight:600; text-decoration:none;">Add the first slide card →</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════════════════
     MODAL: Add / Edit Banner Slide
═════════════════════════════════════════════════════════════════════════ -->
<?php if ($showAddSlide || $editSlide):
    $slide = $editSlide ?: ['tag' => '', 'img' => ''];
    $slideIdx = $editSlideIdx;
?>
<div class="modal-backdrop">
    <div class="modal-box">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
            <h2 style="font-family:'Cormorant Garamond',Georgia,serif; font-size:1.5rem; font-weight:700; color:#1e0a40; margin:0;">
                <?php echo $editSlide ? 'Edit Banner Slide' : 'Add Banner Slide'; ?>
            </h2>
            <a href="banners.php" style="color:#9ca3af; transition:color 0.2s;" onmouseover="this.style.color='#1e0a40'" onmouseout="this.style.color='#9ca3af'">
                <i data-lucide="x" style="width:22px;height:22px;"></i>
            </a>
        </div>

        <form method="POST" action="banners.php" enctype="multipart/form-data">
            <input type="hidden" name="banner_action" value="save_slide">
            <input type="hidden" name="slide_idx" value="<?php echo $slideIdx; ?>">

            <div style="margin-bottom:16px;">
                <label class="form-label" for="slide_tag">Slide Caption / Tag Label *</label>
                <input type="text" id="slide_tag" name="slide_tag" value="<?php echo htmlspecialchars($slide['tag']); ?>" required class="form-input" placeholder="e.g. Education First">
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label" for="slide_img">Background Image URL</label>
                <input type="text" id="slide_img" name="slide_img" value="<?php echo htmlspecialchars($slide['img']); ?>" class="form-input" placeholder="https://images.unsplash.com/...">
                <p style="font-size:0.7rem; color:#9ca3af; margin-top:4px;">Enter a direct image URL (JPG, PNG, WebP)</p>
            </div>

            <div style="margin-bottom:24px;">
                <label class="form-label">Or Upload Local Image file</label>
                <input type="file" name="slide_file" accept="image/*" style="font-size:0.82rem; color:#6b7280;">
                <p style="font-size:0.7rem; color:#9ca3af; margin-top:4px;">Upload JPG, PNG, or GIF (Max 5MB)</p>
            </div>

            <?php if (!empty($slide['img'])): ?>
            <div style="margin-bottom:20px;">
                <label class="form-label">Current Background Image Preview</label>
                <div style="width:100%; height:120px; border-radius:12px; border:2px solid #ede9fe; background-image: url('<?php echo htmlspecialchars($slide['img']); ?>'); background-size: cover; background-position: center;"></div>
            </div>
            <?php endif; ?>

            <div style="display:flex; justify-content:flex-end; gap:10px; padding-top:16px; border-top:1px solid #f3e8ff;">
                <a href="banners.php" class="action-btn btn-edit" style="padding:10px 22px;">Cancel</a>
                <button type="submit" class="action-btn btn-primary" style="padding:10px 26px;">
                    <i data-lucide="save" style="width:15px;height:15px;"></i> Save Slide
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php
renderAdminFooter();
?>