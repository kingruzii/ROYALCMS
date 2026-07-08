<?php
/**
 * Admin Panel System Settings
 */
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/layout.php';

checkAdminAuth();

$error = '';
$success = '';

// Load current settings from database
$settings = [
    'logo' => '',
    'siteName' => "Royal Village Int'l",
    'tagline' => '',
    'backgroundMusic' => '',
    'heroTitle' => '',
    'heroSubtitle' => '',
    'heroImage' => '',
    'aboutTitle' => '',
    'aboutText' => '',
    'contactEmail' => '',
    'contactPhone' => '',
    'address' => '',
    'facebook' => '',
    'twitter' => '',
    'instagram' => ''
];

try {
    $stmt = $pdo->prepare("SELECT value FROM site_settings WHERE id = 'main' LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch();
    if ($row && !empty($row['value'])) {
        $dbSettings = json_decode($row['value'], true);
        if (is_array($dbSettings)) {
            $settings = array_merge($settings, $dbSettings);
        }
    }
} catch (Exception $e) {
    $error = 'Could not load settings: ' . $e->getMessage();
}

// Handle save settings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch current settings fields from POST
    $updatedSettings = [
        'logo' => isset($_POST['logo']) ? trim($_POST['logo']) : '',
        'siteName' => isset($_POST['siteName']) ? trim($_POST['siteName']) : '',
        'tagline' => isset($_POST['tagline']) ? trim($_POST['tagline']) : '',
        'backgroundMusic' => isset($_POST['backgroundMusic']) ? trim($_POST['backgroundMusic']) : '',
        'heroTitle' => isset($_POST['heroTitle']) ? trim($_POST['heroTitle']) : '',
        'heroSubtitle' => isset($_POST['heroSubtitle']) ? trim($_POST['heroSubtitle']) : '',
        'heroImage' => isset($_POST['heroImage']) ? trim($_POST['heroImage']) : '',
        'aboutTitle' => isset($_POST['aboutTitle']) ? trim($_POST['aboutTitle']) : '',
        'aboutText' => isset($_POST['aboutText']) ? trim($_POST['aboutText']) : '',
        'contactEmail' => isset($_POST['contactEmail']) ? trim($_POST['contactEmail']) : '',
        'contactPhone' => isset($_POST['contactPhone']) ? trim($_POST['contactPhone']) : '',
        'address' => isset($_POST['address']) ? trim($_POST['address']) : '',
        'facebook' => isset($_POST['facebook']) ? trim($_POST['facebook']) : '',
        'twitter' => isset($_POST['twitter']) ? trim($_POST['twitter']) : '',
        'instagram' => isset($_POST['instagram']) ? trim($_POST['instagram']) : ''
    ];

    // Handle Uploads
    // 1. Logo
    if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
        require_once __DIR__ . '/upload_helper.php';
        $uploadRes = handleAdminUpload('logo_file', 'logo');
        if ($uploadRes['success']) {
            $updatedSettings['logo'] = $uploadRes['url'];
        } else {
            $error .= $uploadRes['error'] . ' ';
        }
    }

    // 2. Background Music
    if (isset($_FILES['music_file']) && $_FILES['music_file']['error'] === UPLOAD_ERR_OK) {
        require_once __DIR__ . '/upload_helper.php';
        $uploadRes = handleAdminUpload('music_file', 'music');
        if ($uploadRes['success']) {
            $updatedSettings['backgroundMusic'] = $uploadRes['url'];
        } else {
            $error .= $uploadRes['error'] . ' ';
        }
    }

    // 3. Hero Image
    if (isset($_FILES['hero_file']) && $_FILES['hero_file']['error'] === UPLOAD_ERR_OK) {
        require_once __DIR__ . '/upload_helper.php';
        $uploadRes = handleAdminUpload('hero_file', 'hero');
        if ($uploadRes['success']) {
            $updatedSettings['heroImage'] = $uploadRes['url'];
        } else {
            $error .= $uploadRes['error'] . ' ';
        }
    }

    if (empty($error)) {
        try {
            // Merge updated settings into the fully loaded settings to preserve other blocks (like sectors and testimonials)
            $fullSettings = array_merge($settings, $updatedSettings);
            
            // Encode fully merged settings as JSON string
            $jsonValue = json_encode($fullSettings, JSON_UNESCAPED_UNICODE);
            $now = date('Y-m-d H:i:s');
            
            // Upsert into site_settings
            $stmt = $pdo->prepare("INSERT INTO site_settings (id, value, updated_at) VALUES ('main', ?, ?) ON DUPLICATE KEY UPDATE value = ?, updated_at = ?");
            $stmt->execute([$jsonValue, $now, $jsonValue, $now]);
            
            // Refresh local $settings object
            $settings = $fullSettings;
            $success = 'Settings successfully saved!';
            
            // Redirect to prevent form resubmission
            header("Location: settings.php");
            exit();
        } catch (Exception $e) {
            $error = 'Failed to save settings: ' . $e->getMessage();
        }
    }
}

renderAdminHeader('settings');
?>

<div class="p-8 max-w-4xl">
    <form method="POST" action="settings.php" enctype="multipart/form-data" class="space-y-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="font-serif text-3xl font-bold text-purple-900">System Settings</h1>
                <p class="text-gray-600 text-sm">Manage logo, background music, branding, copy, and metrics.</p>
            </div>
            <button type="submit" class="bg-purple-900 text-white px-5 py-2.5 rounded-lg flex items-center gap-2 hover:bg-purple-800 transition shadow">
                <i data-lucide="save" class="w-4 h-4"></i> Save All
            </button>
        </div>

        <!-- Alert messages -->
        <?php if (!empty($success)): ?>
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 p-3.5 rounded-xl text-sm font-medium">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 p-3.5 rounded-xl text-sm font-medium">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- BRANDING SECTION -->
        <section class="bg-white rounded-2xl shadow p-6 border border-purple-50">
            <h2 class="font-bold text-purple-900 mb-4 flex items-center gap-2 text-lg">
                <i data-lucide="image" class="w-5 h-5 text-purple-600"></i> Branding
            </h2>
            <div class="flex flex-wrap items-center gap-4 mb-4">
                <?php if (!empty($settings['logo'])): ?>
                    <img src="<?php echo htmlspecialchars($settings['logo']); ?>" alt="logo" class="w-20 h-20 object-contain rounded-xl border p-1" />
                <?php else: ?>
                    <div class="w-20 h-20 rounded-xl bg-purple-50 border border-purple-200 flex items-center justify-center">
                        <i data-lucide="image" class="w-8 h-8 text-purple-300"></i>
                    </div>
                <?php endif; ?>
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-500 uppercase">Logo Upload</label>
                    <input type="file" name="logo_file" accept="image/*" class="text-sm text-gray-600" />
                    <p class="text-xs text-gray-400">Recommended: PNG or JPG, max 2MB</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Logo URL</label>
                    <input type="text" name="logo" value="<?php echo htmlspecialchars($settings['logo']); ?>" class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" placeholder="https://example.com/logo.png" />
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Site Name</label>
                        <input type="text" name="siteName" value="<?php echo htmlspecialchars($settings['siteName']); ?>" class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Tagline</label>
                        <input type="text" name="tagline" value="<?php echo htmlspecialchars($settings['tagline']); ?>" class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" placeholder="Empowering Africa's Future" />
                    </div>
                </div>
            </div>
        </section>

        <!-- BACKGROUND MUSIC SECTION -->
        <section class="bg-white rounded-2xl shadow p-6 border border-purple-50">
            <h2 class="font-bold text-purple-900 mb-4 flex items-center gap-2 text-lg">
                <i data-lucide="music" class="w-5 h-5 text-purple-600"></i> Background Music
            </h2>
            <p class="text-xs text-gray-500 mb-3">Upload an audio file (MP3, WAV). A floating play button will appear in the bottom-right corner of the site.</p>
            <div class="flex flex-wrap items-center gap-3 mb-3">
                <input type="file" name="music_file" accept="audio/*" class="text-sm text-gray-600" />
                <?php if (!empty($settings['backgroundMusic'])): ?>
                    <audio controls src="<?php echo htmlspecialchars($settings['backgroundMusic']); ?>" class="h-8 max-w-full"></audio>
                    <button type="button" onclick="document.getElementById('music-url-field').value=''; this.style.display='none';" class="text-red-600 text-xs font-semibold hover:underline">Clear URL</button>
                <?php endif; ?>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Or paste audio URL</label>
                <input type="text" id="music-url-field" name="backgroundMusic" value="<?php echo htmlspecialchars($settings['backgroundMusic']); ?>" class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" placeholder="https://example.com/music.mp3" />
            </div>
        </section>

        <!-- HERO SECTION -->
        <section class="bg-white rounded-2xl shadow p-6 border border-purple-50">
            <h2 class="font-bold text-purple-900 mb-4 text-lg flex items-center gap-2">
                <i data-lucide="layout-dashboard" class="w-5 h-5 text-purple-600"></i> Hero Section
            </h2>
            <div class="grid sm:grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Hero Title</label>
                    <input type="text" name="heroTitle" value="<?php echo htmlspecialchars($settings['heroTitle']); ?>" class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Hero Subtitle</label>
                    <input type="text" name="heroSubtitle" value="<?php echo htmlspecialchars($settings['heroSubtitle']); ?>" class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" />
                </div>
            </div>
            <div class="mt-3 space-y-3">
                <div class="flex flex-wrap items-center gap-3">
                    <?php if (!empty($settings['heroImage'])): ?>
                        <img src="<?php echo htmlspecialchars($settings['heroImage']); ?>" alt="Hero" class="w-32 h-20 object-cover rounded-lg border" />
                    <?php else: ?>
                        <div class="w-32 h-20 rounded-lg bg-purple-50 border border-purple-200 flex items-center justify-center">
                            <i data-lucide="image" class="w-8 h-8 text-purple-300"></i>
                        </div>
                    <?php endif; ?>
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-gray-500 uppercase">Upload Hero Image</label>
                        <input type="file" name="hero_file" accept="image/*" class="text-sm text-gray-600" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Hero Image URL</label>
                    <input type="text" name="heroImage" value="<?php echo htmlspecialchars($settings['heroImage']); ?>" class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" placeholder="https://example.com/hero.jpg" />
                </div>
            </div>
        </section>

        <!-- ABOUT SECTION -->
        <section class="bg-white rounded-2xl shadow p-6 border border-purple-50">
            <h2 class="font-bold text-purple-900 mb-4 text-lg flex items-center gap-2">
                <i data-lucide="info" class="w-5 h-5 text-purple-600"></i> About Section
            </h2>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">About Title</label>
                <input type="text" name="aboutTitle" value="<?php echo htmlspecialchars($settings['aboutTitle']); ?>" class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm mb-3" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">About Text</label>
                <textarea name="aboutText" rows="6" class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"><?php echo htmlspecialchars($settings['aboutText']); ?></textarea>
                <p class="text-xs text-gray-400 mt-1">HTML formatting supported</p>
            </div>
        </section>

        <!-- Link to Impact Page for Stats -->
        <section class="bg-gradient-to-r from-purple-50 to-amber-50 rounded-2xl shadow p-6 border border-purple-100">
            <h2 class="font-bold text-purple-900 mb-2 text-lg flex items-center gap-2">
                <i data-lucide="bar-chart-2" class="w-5 h-5 text-purple-600"></i> Impact Data
            </h2>
            <p class="text-sm text-gray-600 mb-4">Impact statistics, sector cards, and testimonials are now managed on the dedicated Impact page.</p>
            <a href="impact.php" class="inline-flex items-center gap-2 bg-purple-900 text-white px-4 py-2.5 rounded-lg text-sm font-semibold hover:bg-purple-800 transition">
                <i data-lucide="external-link" class="w-4 h-4"></i> Manage Impact Data
            </a>
        </section>

        <!-- CONTACT & SOCIAL -->
        <section class="bg-white rounded-2xl shadow p-6 border border-purple-50">
            <h2 class="font-bold text-purple-900 mb-4 text-lg flex items-center gap-2">
                <i data-lucide="mail" class="w-5 h-5 text-purple-600"></i> Contact & Social
            </h2>
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Contact Email</label>
                    <input type="email" name="contactEmail" value="<?php echo htmlspecialchars($settings['contactEmail']); ?>" class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" placeholder="info@royalvillage.org" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Contact Phone</label>
                    <input type="text" name="contactPhone" value="<?php echo htmlspecialchars($settings['contactPhone']); ?>" class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" placeholder="+1 234 567 8900" />
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Address</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars($settings['address']); ?>" class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" placeholder="123 Main Street, City, Country" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Facebook URL</label>
                    <input type="url" name="facebook" value="<?php echo htmlspecialchars($settings['facebook'] ?? ''); ?>" class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" placeholder="https://facebook.com/yourpage" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Twitter URL</label>
                    <input type="url" name="twitter" value="<?php echo htmlspecialchars($settings['twitter'] ?? ''); ?>" class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" placeholder="https://twitter.com/yourhandle" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Instagram URL</label>
                    <input type="url" name="instagram" value="<?php echo htmlspecialchars($settings['instagram'] ?? ''); ?>" class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" placeholder="https://instagram.com/yourpage" />
                </div>
            </div>
        </section>

        <!-- Save button -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-purple-900 text-white px-7 py-3 rounded-xl flex items-center gap-2 hover:bg-purple-800 transition shadow-lg font-bold">
                <i data-lucide="save" class="w-5 h-5"></i> Save Settings
            </button>
        </div>
    </form>
</div>

<?php
renderAdminFooter();
?>