<?php
/**
 * Admin Panel Programs Management
 */
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/layout.php';

checkAdminAuth();

$error = '';
$success = '';

// 1. HANDLE DELETION
if (isset($_GET['delete'])) {
    $id = trim($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM programs WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Program successfully deleted.';
        header("Location: programs.php");
        exit();
    } catch (Exception $e) {
        $error = 'Failed to delete program.';
    }
}

// 2. HANDLE VISIBILITY TOGGLE
if (isset($_GET['toggle_visible'])) {
    $id = trim($_GET['toggle_visible']);
    try {
        $stmt = $pdo->prepare("UPDATE programs SET visible = 1 - visible WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Program visibility status updated.';
        header("Location: programs.php");
        exit();
    } catch (Exception $e) {
        $error = 'Failed to update visibility.';
    }
}

// 3. HANDLE SAVE SUBMISSION (ADD / EDIT)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_program'])) {
    $id = isset($_POST['id']) ? trim($_POST['id']) : '';
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $icon = isset($_POST['icon']) ? trim($_POST['icon']) : 'Heart';
    $image = isset($_POST['image']) ? trim($_POST['image']) : '';
    $display_order = isset($_POST['display_order']) ? intval($_POST['display_order']) : 0;
    $visible = isset($_POST['visible']) ? 1 : 0;

    // Handle Upload
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        require_once __DIR__ . '/upload_helper.php';
        $uploadRes = handleAdminUpload('image_file', 'programs');
        if ($uploadRes['success']) {
            $image = $uploadRes['url'];
        } else {
            $error = $uploadRes['error'];
        }
    }

    if (empty($title)) {
        $error = 'Title is required.';
    }

    if (empty($error)) {
        try {
            if (empty($id)) {
                // INSERT NEW
                $newId = 'p-' . time() . '-' . rand(100, 999);
                $stmt = $pdo->prepare("INSERT INTO programs (id, title, description, icon, image, display_order, visible) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$newId, $title, $description, $icon, $image, $display_order, $visible]);
                $success = 'New program successfully added.';
                header("Location: programs.php");
                exit();
            } else {
                // UPDATE EXISTING
                $stmt = $pdo->prepare("UPDATE programs SET title=?, description=?, icon=?, image=?, display_order=?, visible=? WHERE id=?");
                $stmt->execute([$title, $description, $icon, $image, $display_order, $visible, $id]);
                $success = 'Program details updated.';
                header("Location: programs.php");
                exit();
            }
        } catch (Exception $e) {
            $error = 'Failed to save program details: ' . $e->getMessage();
        }
    }
}

// 4. FETCH EDIT DETAILS IF REQUESTED
$editProgram = null;
if (isset($_GET['edit'])) {
    $editId = trim($_GET['edit']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM programs WHERE id = ?");
        $stmt->execute([$editId]);
        $editProgram = $stmt->fetch();
    } catch (Exception $e) {
        // Silent fail
    }
}

// 5. FETCH ALL PROGRAMS FOR LISTING
$programs = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM programs ORDER BY display_order ASC, title ASC");
    $stmt->execute();
    $programs = $stmt->fetchAll();
} catch (Exception $e) {
    // Silent fail
}

// Helper function to map icon names to Lucide icon names
function mapIconName($icon) {
    $lowIcon = strtolower($icon);
    if ($lowIcon === 'graduationcap') return 'graduation-cap';
    if ($lowIcon === 'sparkles') return 'sparkles';
    if ($lowIcon === 'wrench') return 'wrench';
    if ($lowIcon === 'users') return 'users';
    if ($lowIcon === 'globe') return 'globe';
    if ($lowIcon === 'award') return 'award';
    if ($lowIcon === 'heart') return 'heart';
    return 'heart';
}

// Render the header
renderAdminHeader('programs');
?>

<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="font-serif text-3xl font-bold text-purple-900">Programs</h1>
            <p class="text-gray-600 text-sm">Manage educational & support programs.</p>
        </div>
        <a href="programs.php?add=1" class="bg-purple-900 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-purple-800 transition shadow">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Program
        </a>
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

    <!-- PROGRAMS GRID -->
    <div class="grid md:grid-cols-2 gap-6">
        <?php foreach ($programs as $p): ?>
            <div class="bg-white rounded-2xl shadow overflow-hidden border border-purple-50 flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <?php if (!empty($p['image'])): ?>
                        <div class="relative h-44 w-full">
                            <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" class="w-full h-full object-cover" />
                            <div class="absolute bottom-3 left-3 w-10 h-10 rounded-full bg-yellow-400 text-purple-950 flex items-center justify-center shadow">
                                <i data-lucide="<?php echo mapIconName($p['icon']); ?>" class="w-5 h-5"></i>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="relative h-32 bg-gradient-to-r from-purple-100 to-pink-100 flex items-center justify-center">
                            <div class="w-16 h-16 rounded-full bg-yellow-400 text-purple-950 flex items-center justify-center shadow">
                                <i data-lucide="<?php echo mapIconName($p['icon']); ?>" class="w-8 h-8"></i>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="p-5">
                        <div class="flex justify-between items-center mb-1">
                            <h3 class="font-bold text-purple-900 text-lg"><?php echo htmlspecialchars($p['title']); ?></h3>
                            <a href="programs.php?toggle_visible=<?php echo urlencode($p['id']); ?>" class="p-1 rounded inline-block transition hover:bg-purple-50 <?php echo $p['visible'] ? 'text-green-600' : 'text-gray-400'; ?>" title="Toggle visibility">
                                <i data-lucide="<?php echo $p['visible'] ? 'eye' : 'eye-off'; ?>" class="w-4 h-4"></i>
                            </a>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed line-clamp-3 mt-1"><?php echo htmlspecialchars($p['description']); ?></p>
                    </div>
                </div>
                
                <div class="p-4 bg-purple-50/20 border-t border-purple-50 flex justify-end gap-3 text-xs font-semibold">
                    <span class="mr-auto text-gray-400 self-center">Order: <?php echo $p['display_order']; ?></span>
                    <a href="programs.php?edit=<?php echo urlencode($p['id']); ?>" class="text-purple-700 hover:bg-purple-50 px-2.5 py-1.5 rounded transition">
                        <i data-lucide="edit" class="inline w-3.5 h-3.5 mr-0.5"></i> Edit
                    </a>
                    <a href="programs.php?delete=<?php echo urlencode($p['id']); ?>" onclick="return confirm('Are you sure you want to delete this program?');" class="text-red-500 hover:bg-red-50 px-2.5 py-1.5 rounded transition">
                        <i data-lucide="trash-2" class="inline w-3.5 h-3.5 mr-0.5"></i> Delete
                    </a>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($programs)): ?>
            <div class="text-center py-12 col-span-full">
                <i data-lucide="briefcase" class="w-16 h-16 mx-auto text-gray-300 mb-4"></i>
                <p class="text-gray-500 font-medium">No programs added yet.</p>
                <a href="programs.php?add=1" class="inline-block mt-4 text-purple-600 hover:text-purple-800">Add your first program →</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ADD/EDIT MODAL -->
<?php if (isset($_GET['add']) || $editProgram): 
    $p = $editProgram ?: [
        'id' => '', 'title' => '', 'description' => '', 'icon' => 'Heart', 'image' => '', 'display_order' => 0, 'visible' => 1
    ];
?>
<div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl w-full max-w-xl p-6 shadow-2xl">
        <div class="flex justify-between items-center border-b border-purple-100 pb-4 mb-4 sticky top-0 bg-white">
            <h2 class="font-serif text-xl font-bold text-purple-900">
                <?php echo $p['id'] ? 'Edit' : 'Add'; ?> Program
            </h2>
            <a href="programs.php" class="text-gray-500 hover:text-gray-700 transition">
                <i data-lucide="x" class="w-6 h-6"></i>
            </a>
        </div>
        
        <form method="POST" action="programs.php" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="save_program" value="1" />
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($p['id']); ?>" />
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title *</label>
                <input type="text" name="title" required value="<?php echo htmlspecialchars($p['title']); ?>" 
                       class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                       placeholder="e.g., Vocational Training" />
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="4" 
                          class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                          placeholder="Describe the program..."><?php echo htmlspecialchars($p['description']); ?></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Icon Style</label>
                <select name="icon" class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white">
                    <option value="Heart" <?php echo $p['icon'] === 'Heart' ? 'selected' : ''; ?>>❤️ Heart</option>
                    <option value="GraduationCap" <?php echo $p['icon'] === 'GraduationCap' ? 'selected' : ''; ?>>🎓 Graduation Cap</option>
                    <option value="Wrench" <?php echo $p['icon'] === 'Wrench' ? 'selected' : ''; ?>>🔧 Wrench</option>
                    <option value="Sparkles" <?php echo $p['icon'] === 'Sparkles' ? 'selected' : ''; ?>>✨ Sparkles</option>
                    <option value="Users" <?php echo $p['icon'] === 'Users' ? 'selected' : ''; ?>>👥 Users</option>
                    <option value="Globe" <?php echo $p['icon'] === 'Globe' ? 'selected' : ''; ?>>🌍 Globe</option>
                    <option value="Award" <?php echo $p['icon'] === 'Award' ? 'selected' : ''; ?>>🏆 Award</option>
                </select>
            </div>
            
            <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Program Image</label>
                
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Upload Image</label>
                    <input type="file" name="image_file" accept="image/*" class="w-full text-sm" />
                </div>
                
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Or Image URL</label>
                    <input type="text" name="image" value="<?php echo htmlspecialchars($p['image']); ?>" 
                           class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" 
                           placeholder="https://example.com/image.jpg" />
                </div>
                
                <?php if (!empty($p['image'])): ?>
                    <div class="mt-2">
                        <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="Current image" class="w-24 h-24 object-cover rounded-lg border" />
                    </div>
                <?php endif; ?>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Display Order</label>
                <input type="number" name="display_order" value="<?php echo intval($p['display_order']); ?>" 
                       class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                       placeholder="0" />
                <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
            </div>
            
            <div class="flex items-center gap-2">
                <input type="checkbox" name="visible" value="1" <?php echo $p['visible'] ? 'checked' : ''; ?> class="w-4 h-4 accent-purple-600" />
                <label class="text-sm font-medium text-gray-700">Visible on website</label>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-purple-100 mt-4">
                <a href="programs.php" class="px-5 py-2.5 rounded-lg text-sm bg-gray-100 text-gray-700 hover:bg-gray-200 transition font-medium">Cancel</a>
                <button type="submit" class="bg-purple-900 text-white px-6 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 hover:bg-purple-800 transition shadow">
                    <i data-lucide="save" class="w-4 h-4"></i> Save Program
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php
renderAdminFooter();
?>