<?php
/**
 * Admin Testimonials Management
 */
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/layout.php';

checkAdminAuth();

$success = '';
$error = '';

// DELETE
if (isset($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->execute([trim($_GET['delete'])]);
        header("Location: testimonials.php"); exit;
    } catch (Exception $e) { $error = 'Failed to delete.'; }
}

// TOGGLE VISIBILITY
if (isset($_GET['toggle_visible'])) {
    try {
        $stmt = $pdo->prepare("UPDATE testimonials SET visible = 1 - visible WHERE id = ?");
        $stmt->execute([trim($_GET['toggle_visible'])]);
        header("Location: testimonials.php"); exit;
    } catch (Exception $e) { $error = 'Failed to update visibility.'; }
}

// SAVE (ADD / EDIT)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_testimonial'])) {
    $id            = trim($_POST['id'] ?? '');
    $name          = trim($_POST['name'] ?? '');
    $role          = trim($_POST['role'] ?? '');
    $quote         = trim($_POST['quote'] ?? '');
    $photo         = trim($_POST['photo'] ?? '');
    $display_order = intval($_POST['display_order'] ?? 0);
    $visible       = isset($_POST['visible']) ? 1 : 0;

    // Handle photo upload
    if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
        require_once __DIR__ . '/upload_helper.php';
        $up = handleAdminUpload('photo_file', 'testimonials');
        if ($up['success']) $photo = $up['url'];
        else $error = $up['error'];
    }

    if (empty($name) || empty($quote)) {
        $error = 'Name and quote are required.';
    }

    if (empty($error)) {
        try {
            if (empty($id)) {
                $stmt = $pdo->prepare("INSERT INTO testimonials (id, name, role, photo, quote, visible, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute(['t-' . time() . '-' . rand(100,999), $name, $role, $photo, $quote, $visible, $display_order]);
                $success = 'Testimonial added successfully.';
            } else {
                $stmt = $pdo->prepare("UPDATE testimonials SET name=?, role=?, photo=?, quote=?, visible=?, display_order=? WHERE id=?");
                $stmt->execute([$name, $role, $photo, $quote, $visible, $display_order, $id]);
                $success = 'Testimonial updated successfully.';
            }
            header("Location: testimonials.php"); exit;
        } catch (Exception $e) { $error = 'Failed to save: ' . $e->getMessage(); }
    }
}

// FETCH EDIT
$editItem = null;
if (isset($_GET['edit'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
        $stmt->execute([trim($_GET['edit'])]);
        $editItem = $stmt->fetch();
    } catch (Exception $e) {}
}

// FETCH ALL
$testimonials = [];
try {
    $stmt = $pdo->query("SELECT * FROM testimonials ORDER BY display_order ASC, created_at DESC");
    $testimonials = $stmt->fetchAll();
} catch (Exception $e) {}

renderAdminHeader('testimonials');
?>

<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="font-serif text-3xl font-bold text-purple-900">Testimonials</h1>
            <p class="text-gray-600 text-sm">Manage quotes from scholars, partners, and community members.</p>
        </div>
        <a href="testimonials.php?add=1" class="bg-purple-900 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-purple-800 transition shadow">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Testimonial
        </a>
    </div>

    <?php if ($success): ?>
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 p-3.5 rounded-xl text-sm font-medium"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 p-3.5 rounded-xl text-sm font-medium"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- LIST -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($testimonials as $t): ?>
        <div class="bg-white rounded-2xl shadow border border-purple-50 p-6 flex flex-col hover:shadow-md transition">
            <div class="text-yellow-400 text-3xl font-serif leading-none mb-3">&ldquo;</div>
            <p class="text-gray-600 text-sm leading-relaxed flex-1 mb-4 line-clamp-4"><?php echo htmlspecialchars($t['quote']); ?></p>
            <div class="flex items-center gap-3 pt-4 border-t border-purple-50 mb-4">
                <?php if (!empty($t['photo'])): ?>
                <img src="<?php echo htmlspecialchars($t['photo']); ?>" class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                <?php else: ?>
                <div class="w-10 h-10 rounded-full bg-yellow-400 flex items-center justify-center text-purple-900 font-bold flex-shrink-0">
                    <?php echo strtoupper(substr($t['name'], 0, 1)); ?>
                </div>
                <?php endif; ?>
                <div>
                    <div class="font-semibold text-purple-900 text-sm"><?php echo htmlspecialchars($t['name']); ?></div>
                    <div class="text-gray-400 text-xs"><?php echo htmlspecialchars($t['role'] ?? ''); ?></div>
                </div>
                <div class="ml-auto flex items-center gap-1">
                    <span class="text-xs px-2 py-0.5 rounded-full <?php echo $t['visible'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'; ?>">
                        <?php echo $t['visible'] ? 'Visible' : 'Hidden'; ?>
                    </span>
                </div>
            </div>
            <div class="flex justify-end gap-2 text-xs font-semibold">
                <a href="testimonials.php?toggle_visible=<?php echo urlencode($t['id']); ?>" class="text-gray-500 hover:bg-gray-50 px-2.5 py-1.5 rounded transition">
                    <i data-lucide="<?php echo $t['visible'] ? 'eye-off' : 'eye'; ?>" class="inline w-3.5 h-3.5"></i>
                </a>
                <a href="testimonials.php?edit=<?php echo urlencode($t['id']); ?>" class="text-purple-700 hover:bg-purple-50 px-2.5 py-1.5 rounded transition">
                    <i data-lucide="edit" class="inline w-3.5 h-3.5 mr-0.5"></i> Edit
                </a>
                <a href="testimonials.php?delete=<?php echo urlencode($t['id']); ?>" onclick="return confirm('Delete this testimonial?');" class="text-red-500 hover:bg-red-50 px-2.5 py-1.5 rounded transition">
                    <i data-lucide="trash-2" class="inline w-3.5 h-3.5 mr-0.5"></i> Delete
                </a>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($testimonials)): ?>
        <div class="col-span-full text-center py-16">
            <i data-lucide="message-square" class="w-16 h-16 mx-auto text-gray-300 mb-4"></i>
            <p class="text-gray-500 font-medium">No testimonials yet.</p>
            <a href="testimonials.php?add=1" class="inline-block mt-4 text-purple-600 hover:text-purple-800">Add your first testimonial →</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ADD / EDIT MODAL -->
<?php if (isset($_GET['add']) || $editItem):
    $t = $editItem ?: ['id'=>'','name'=>'','role'=>'','photo'=>'','quote'=>'','display_order'=>0,'visible'=>1];
?>
<div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl">
        <div class="flex justify-between items-center border-b border-purple-100 pb-4 mb-4">
            <h2 class="font-serif text-xl font-bold text-purple-900"><?php echo $t['id'] ? 'Edit' : 'Add'; ?> Testimonial</h2>
            <a href="testimonials.php" class="text-gray-500 hover:text-gray-700"><i data-lucide="x" class="w-6 h-6"></i></a>
        </div>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="save_testimonial" value="1">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($t['id']); ?>">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" required value="<?php echo htmlspecialchars($t['name']); ?>"
                           class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" placeholder="Full name">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Role / Title</label>
                    <input type="text" name="role" value="<?php echo htmlspecialchars($t['role'] ?? ''); ?>"
                           class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" placeholder="e.g. RVIF Scholar, Rwanda">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Quote *</label>
                <textarea name="quote" required rows="4"
                          class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm resize-none"
                          placeholder="Their testimonial..."><?php echo htmlspecialchars($t['quote']); ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Photo</label>
                <input type="file" name="photo_file" accept="image/*" class="w-full text-sm mb-2">
                <input type="text" name="photo" value="<?php echo htmlspecialchars($t['photo'] ?? ''); ?>"
                       class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" placeholder="Or paste image URL">
                <?php if (!empty($t['photo'])): ?>
                <img src="<?php echo htmlspecialchars($t['photo']); ?>" class="w-12 h-12 rounded-full object-cover mt-2 border">
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Display Order</label>
                    <input type="number" name="display_order" value="<?php echo intval($t['display_order']); ?>"
                           class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
                </div>
                <div class="flex items-end pb-2.5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="visible" value="1" <?php echo $t['visible'] ? 'checked' : ''; ?> class="w-4 h-4 accent-purple-600">
                        <span class="text-sm font-medium text-gray-700">Visible on website</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-purple-100">
                <a href="testimonials.php" class="px-5 py-2.5 rounded-lg text-sm bg-gray-100 text-gray-700 hover:bg-gray-200 transition font-medium">Cancel</a>
                <button type="submit" class="bg-purple-900 text-white px-6 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 hover:bg-purple-800 transition shadow">
                    <i data-lucide="save" class="w-4 h-4"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php renderAdminFooter(); ?>
