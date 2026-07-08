<?php
/**
 * Admin Panel Beneficiaries CRUD
 */
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/upload_helper.php';

$error = '';
$success = '';

// 1. HANDLE DELETION
if (isset($_GET['delete'])) {
    $id = trim($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM beneficiaries WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Scholar successfully deleted.';
    } catch (Exception $e) {
        $error = 'Failed to delete scholar: ' . $e->getMessage();
    }
}

// 2. HANDLE TOGGLES
if (isset($_GET['toggle_visible'])) {
    $id = trim($_GET['toggle_visible']);
    try {
        $stmt = $pdo->prepare("UPDATE beneficiaries SET visible = 1 - visible WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Scholar visibility toggled.';
    } catch (Exception $e) {
        $error = 'Failed to toggle visibility.';
    }
}

if (isset($_GET['toggle_featured'])) {
    $id = trim($_GET['toggle_featured']);
    try {
        $stmt = $pdo->prepare("UPDATE beneficiaries SET featured = 1 - featured WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Scholar featured status toggled.';
    } catch (Exception $e) {
        $error = 'Failed to toggle featured status.';
    }
}

// 3. HANDLE ADD / EDIT SUBMISSION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_scholar'])) {
    $id = isset($_POST['id']) ? trim($_POST['id']) : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $age = isset($_POST['age']) ? trim($_POST['age']) : '';
    $hometown = isset($_POST['hometown']) ? trim($_POST['hometown']) : '';
    $program = isset($_POST['program']) ? trim($_POST['program']) : '';
    $destination = isset($_POST['destination']) ? trim($_POST['destination']) : '';
    $year_sent = isset($_POST['year_sent']) ? trim($_POST['year_sent']) : '';
    $study_field = isset($_POST['study_field']) ? trim($_POST['study_field']) : '';
    $institution = isset($_POST['institution']) ? trim($_POST['institution']) : '';
    $photo = isset($_POST['photo']) ? trim($_POST['photo']) : '';
    $quote = isset($_POST['quote']) ? trim($_POST['quote']) : '';
    $short_story = isset($_POST['short_story']) ? trim($_POST['short_story']) : '';
    $full_story = isset($_POST['full_story']) ? trim($_POST['full_story']) : '';
    $display_order = isset($_POST['display_order']) ? intval($_POST['display_order']) : 0;
    
    $visible = isset($_POST['visible']) ? 1 : 0;
    $featured = isset($_POST['featured']) ? 1 : 0;

    // Handle Upload
    if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
        $uploadRes = handleAdminUpload('photo_file', 'beneficiaries');
        if ($uploadRes['success']) {
            $photo = $uploadRes['url'];
        } else {
            $error = $uploadRes['error'];
        }
    }

    if (empty($name)) {
        $error = 'Name is required.';
    }

    if (empty($error)) {
        try {
            if (empty($id)) {
                // INSERT NEW
                $newId = 'ben-' . time() . '-' . rand(100, 999);
                $stmt = $pdo->prepare("INSERT INTO beneficiaries (id, name, age, hometown, program, destination, year_sent, institution, study_field, photo, short_story, full_story, quote, featured, visible, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$newId, $name, $age, $hometown, $program, $destination, $year_sent, $institution, $study_field, $photo, $short_story, $full_story, $quote, $featured, $visible, $display_order]);
                $success = 'New scholar successfully added.';
            } else {
                // UPDATE EXISTING
                $stmt = $pdo->prepare("UPDATE beneficiaries SET name=?, age=?, hometown=?, program=?, destination=?, year_sent=?, institution=?, study_field=?, photo=?, short_story=?, full_story=?, quote=?, featured=?, visible=?, display_order=? WHERE id=?");
                $stmt->execute([$name, $age, $hometown, $program, $destination, $year_sent, $institution, $study_field, $photo, $short_story, $full_story, $quote, $featured, $visible, $display_order, $id]);
                $success = 'Scholar successfully updated.';
            }
        } catch (Exception $e) {
            $error = 'Database operation failed: ' . $e->getMessage();
        }
    }
}

// 4. FETCH EDIT SCHOLAR IF REQUESTED
$editScholar = null;
if (isset($_GET['edit'])) {
    $editId = trim($_GET['edit']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM beneficiaries WHERE id = ?");
        $stmt->execute([$editId]);
        $editScholar = $stmt->fetch();
    } catch (Exception $e) {
        // Silent fail
    }
}

// 5. FETCH ALL SCHOLARS FOR LISTING
$scholars = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM beneficiaries ORDER BY display_order ASC, name ASC");
    $stmt->execute();
    $scholars = $stmt->fetchAll();
} catch (Exception $e) {
    // Silent fail
}

renderAdminHeader('beneficiaries');
?>

<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="font-serif text-3xl font-bold text-purple-900">Beneficiaries</h1>
            <p class="text-gray-600 text-sm">Manage scholars and their stories.</p>
        </div>
        <a href="<?php echo BASE_PATH; ?>/admin/beneficiaries.php?add=1" class="bg-purple-900 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-purple-800 transition shadow"><i data-lucide="plus" class="w-4 h-4"></i> Add Scholar</a>
    </div>

    <!-- Alert Messages -->
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

    <!-- SCHOLARS LIST -->
    <div class="bg-white rounded-2xl shadow border border-purple-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-purple-50/50 text-purple-900 text-left">
                    <tr>
                        <th class="p-4 font-semibold">Photo</th>
                        <th class="p-4 font-semibold">Name</th>
                        <th class="p-4 font-semibold">Program</th>
                        <th class="p-4 font-semibold">Institution</th>
                        <th class="p-4 font-semibold text-center">Order</th>
                        <th class="p-4 font-semibold text-center">Status</th>
                        <th class="p-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-50">
                    <?php foreach ($scholars as $b): ?>
                        <tr class="hover:bg-purple-50/20 transition">
                            <td class="p-4">
                                <?php if (!empty($b['photo'])): ?>
                                    <img src="<?php echo htmlspecialchars($b['photo']); ?>" alt="" class="w-12 h-12 rounded-full object-cover border" />
                                <?php else: ?>
                                    <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center text-purple-300 border border-purple-100">
                                        <i data-lucide="user" class="w-6 h-6"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-purple-950"><?php echo htmlspecialchars($b['name']); ?></div>
                                <div class="text-xs text-gray-500 font-medium"><?php echo htmlspecialchars($b['hometown']); ?></div>
                            </td>
                            <td class="p-4">
                                <span class="capitalize text-xs px-2.5 py-1 rounded-full bg-purple-50 border border-purple-100 text-purple-900 font-semibold">
                                    <?php echo htmlspecialchars($b['program']); ?>
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-gray-700"><?php echo htmlspecialchars($b['institution']); ?></div>
                                <div class="text-xs text-yellow-600 font-semibold"><?php echo htmlspecialchars($b['study_field']); ?></div>
                            </td>
                            <td class="p-4 text-center font-bold text-gray-600"><?php echo $b['display_order']; ?></td>
                            <td class="p-4 text-center whitespace-nowrap">
                                <!-- Visibility Toggle -->
                                <a href="<?php echo BASE_PATH; ?>/admin/beneficiaries.php?toggle_visible=<?php echo urlencode($b['id']); ?>" class="p-1.5 rounded inline-block transition hover:bg-purple-50 <?php echo $b['visible'] ? 'text-green-600' : 'text-gray-400'; ?>" title="Toggle visibility">
                                    <i data-lucide="<?php echo $b['visible'] ? 'eye' : 'eye-off'; ?>" class="w-4 h-4"></i>
                                </a>
                                <!-- Featured Toggle -->
                                <a href="<?php echo BASE_PATH; ?>/admin/beneficiaries.php?toggle_featured=<?php echo urlencode($b['id']); ?>" class="p-1.5 rounded inline-block transition hover:bg-purple-50 <?php echo $b['featured'] ? 'text-yellow-500' : 'text-gray-400'; ?>" title="Toggle featured status">
                                    <i data-lucide="star" class="w-4 h-4 <?php echo $b['featured'] ? 'fill-yellow-450 fill-yellow-500' : ''; ?>"></i>
                                </a>
                            </td>
                            <td class="p-4 text-right whitespace-nowrap">
                                <a href="<?php echo BASE_PATH; ?>/admin/beneficiaries.php?edit=<?php echo urlencode($b['id']); ?>" class="text-purple-700 hover:text-purple-950 p-2 hover:bg-purple-50 rounded-lg inline-block transition" title="Edit"><i data-lucide="edit" class="w-4 h-4"></i></a>
                                <a href="<?php echo BASE_PATH; ?>/admin/beneficiaries.php?delete=<?php echo urlencode($b['id']); ?>" onclick="return confirm('Are you sure you want to delete this scholar?');" class="text-red-500 hover:text-red-700 p-2 hover:bg-red-55 hover:bg-red-50 rounded-lg inline-block transition" title="Delete"><i data-lucide="trash-2" class="w-4 h-4"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($scholars)): ?>
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-500 font-medium">No scholars found in the system.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ADD/EDIT MODAL OVERLAY (Controlled by PHP query parameters) -->
<?php if (isset($_GET['add']) || $editScholar): 
    $s = $editScholar ?: [
        'id' => '', 'name' => '', 'age' => '', 'hometown' => '', 'program' => 'rwanda',
        'destination' => '', 'year_sent' => '', 'study_field' => '', 'institution' => '',
        'photo' => '', 'quote' => '', 'short_story' => '', 'full_story' => '',
        'display_order' => 0, 'visible' => 1, 'featured' => 0
    ];
?>
<div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-purple-50 pb-3">
            <h2 class="font-serif text-2xl font-bold text-purple-900"><?php echo $s['id'] ? 'Edit' : 'Add'; ?> Beneficiary</h2>
            <a href="<?php echo BASE_PATH; ?>/admin/beneficiaries.php" class="text-gray-500 hover:text-gray-700 p-1"><i data-lucide="x" class="w-6 h-6"></i></a>
        </div>
        
        <form method="POST" action="<?php echo BASE_PATH; ?>/admin/beneficiaries.php" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="save_scholar" value="1" />
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($s['id']); ?>" />
            
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Scholar Name *</label>
                    <input required type="text" name="name" value="<?php echo htmlspecialchars($s['name']); ?>" class="w-full border border-purple-100 p-2.5 rounded-lg focus:ring-1 focus:ring-purple-500 focus:outline-none text-sm" placeholder="Linda N Gbarlea" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Age</label>
                    <input type="text" name="age" value="<?php echo htmlspecialchars($s['age']); ?>" class="w-full border border-purple-100 p-2.5 rounded-lg focus:ring-1 focus:ring-purple-500 focus:outline-none text-sm" placeholder="22" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Hometown</label>
                    <input type="text" name="hometown" value="<?php echo htmlspecialchars($s['hometown']); ?>" class="w-full border border-purple-100 p-2.5 rounded-lg focus:ring-1 focus:ring-purple-500 focus:outline-none text-sm" placeholder="Monrovia, Liberia" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Program</label>
                    <select name="program" class="w-full border border-purple-100 p-2.5 rounded-lg focus:ring-1 focus:ring-purple-500 focus:outline-none text-sm bg-white">
                        <option value="rwanda" <?php echo $s['program'] === 'rwanda' ? 'selected' : ''; ?>>Rwanda</option>
                        <option value="india" <?php echo $s['program'] === 'india' ? 'selected' : ''; ?>>India</option>
                        <option value="liberia" <?php echo $s['program'] === 'liberia' ? 'selected' : ''; ?>>Liberia</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Destination</label>
                    <input type="text" name="destination" value="<?php echo htmlspecialchars($s['destination']); ?>" class="w-full border border-purple-100 p-2.5 rounded-lg focus:ring-1 focus:ring-purple-500 focus:outline-none text-sm" placeholder="Kigali, Rwanda" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Year Sent</label>
                    <input type="text" name="year_sent" value="<?php echo htmlspecialchars($s['year_sent']); ?>" class="w-full border border-purple-100 p-2.5 rounded-lg focus:ring-1 focus:ring-purple-500 focus:outline-none text-sm" placeholder="2024" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Study Field</label>
                    <input type="text" name="study_field" value="<?php echo htmlspecialchars($s['study_field']); ?>" class="w-full border border-purple-100 p-2.5 rounded-lg focus:ring-1 focus:ring-purple-500 focus:outline-none text-sm" placeholder="General Nursing" />
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Institution</label>
                    <input type="text" name="institution" value="<?php echo htmlspecialchars($s['institution']); ?>" class="w-full border border-purple-100 p-2.5 rounded-lg focus:ring-1 focus:ring-purple-500 focus:outline-none text-sm" placeholder="Mount Kigali University" />
                </div>
                
                <div class="sm:col-span-2 space-y-1.5">
                    <label class="block text-xs font-semibold text-gray-600 uppercase">Photo Upload</label>
                    <div class="flex items-center gap-3">
                        <input type="file" name="photo_file" accept="image/*" class="text-xs text-gray-650" />
                        <?php if (!empty($s['photo'])): ?>
                            <img src="<?php echo htmlspecialchars($s['photo']); ?>" alt="" class="w-12 h-12 rounded-full object-cover border" />
                        <?php endif; ?>
                    </div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase">Or Photo URL</label>
                    <input type="text" name="photo" value="<?php echo htmlspecialchars($s['photo']); ?>" class="w-full border border-purple-100 p-2.5 rounded-lg focus:ring-1 focus:ring-purple-500 focus:outline-none text-sm" />
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Quote</label>
                    <input type="text" name="quote" value="<?php echo htmlspecialchars($s['quote']); ?>" class="w-full border border-purple-100 p-2.5 rounded-lg focus:ring-1 focus:ring-purple-500 focus:outline-none text-sm" placeholder="Through RVIF, I am becoming what I dreamed..." />
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Short Story</label>
                    <textarea name="short_story" rows="2" class="w-full border border-purple-100 p-2.5 rounded-lg focus:ring-1 focus:ring-purple-500 focus:outline-none text-sm" placeholder="Summarized quote/story for grids..."><?php echo htmlspecialchars($s['short_story']); ?></textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Full Story</label>
                    <textarea name="full_story" rows="5" class="w-full border border-purple-100 p-2.5 rounded-lg focus:ring-1 focus:ring-purple-500 focus:outline-none text-sm" placeholder="Full story bio log..."><?php echo htmlspecialchars($s['full_story']); ?></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Display Order</label>
                    <input type="number" name="display_order" value="<?php echo intval($s['display_order']); ?>" class="w-full border border-purple-100 p-2.5 rounded-lg focus:ring-1 focus:ring-purple-500 focus:outline-none text-sm" />
                </div>
                
                <div class="flex items-center gap-6 mt-6">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                        <input type="checkbox" name="visible" value="1" <?php echo $s['visible'] ? 'checked' : ''; ?> class="w-4 h-4 accent-purple-800" />
                        <span>Visible</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                        <input type="checkbox" name="featured" value="1" <?php echo $s['featured'] ? 'checked' : ''; ?> class="w-4 h-4 accent-purple-800" />
                        <span>Featured</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-purple-50 mt-4">
                <a href="<?php echo BASE_PATH; ?>/admin/beneficiaries.php" class="px-5 py-2.5 rounded-lg text-sm bg-purple-50 text-purple-900 hover:bg-purple-100 transition font-bold">Cancel</a>
                <button type="submit" class="bg-purple-900 text-white px-6 py-2.5 rounded-lg text-sm font-bold flex items-center gap-2 hover:bg-purple-800 transition shadow"><i data-lucide="save" class="w-4 h-4"></i> Save</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php
renderAdminFooter();
?>
