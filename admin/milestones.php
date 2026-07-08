<?php
/**
 * Admin Panel - Milestones Management
 */
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/layout.php';

checkAdminAuth();

$error = '';
$success = '';

// Handle delete
if (isset($_GET['delete'])) {
    $id = trim($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM milestones WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Milestone deleted successfully.';
        header("Location: milestones.php");
        exit();
    } catch (Exception $e) {
        $error = 'Failed to delete milestone.';
    }
}

// Handle visibility toggle
if (isset($_GET['toggle_visible'])) {
    $id = trim($_GET['toggle_visible']);
    try {
        $stmt = $pdo->prepare("UPDATE milestones SET visible = 1 - visible WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Milestone visibility toggled.';
        header("Location: milestones.php");
        exit();
    } catch (Exception $e) {
        $error = 'Failed to toggle visibility.';
    }
}

// Handle save/add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_milestone'])) {
    $id = isset($_POST['id']) ? trim($_POST['id']) : '';
    $year = trim($_POST['year']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $display_order = intval($_POST['display_order']);
    $visible = isset($_POST['visible']) ? 1 : 0;

    if (empty($year) || empty($title) || empty($description)) {
        $error = 'Year, Title, and Description are required.';
    }

    if (empty($error)) {
        try {
            if (empty($id)) {
                // Insert new
                $newId = 'm-' . time() . '-' . rand(100, 999);
                $stmt = $pdo->prepare("INSERT INTO milestones (id, year, title, description, display_order, visible) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$newId, $year, $title, $description, $display_order, $visible]);
                $success = 'New milestone added successfully.';
            } else {
                // Update existing
                $stmt = $pdo->prepare("UPDATE milestones SET year=?, title=?, description=?, display_order=?, visible=? WHERE id=?");
                $stmt->execute([$year, $title, $description, $display_order, $visible, $id]);
                $success = 'Milestone updated successfully.';
            }
            header("Location: milestones.php");
            exit();
        } catch (Exception $e) {
            $error = 'Failed to save milestone: ' . $e->getMessage();
        }
    }
}

// Fetch all milestones
$milestones = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM milestones ORDER BY display_order ASC, year ASC");
    $stmt->execute();
    $milestones = $stmt->fetchAll();
} catch (Exception $e) {
    // Table might not exist - create it
    $createTable = "
    CREATE TABLE IF NOT EXISTS `milestones` (
      `id` varchar(50) NOT NULL,
      `year` varchar(10) NOT NULL,
      `title` varchar(255) NOT NULL,
      `description` text NOT NULL,
      `display_order` int(11) NOT NULL DEFAULT 0,
      `visible` tinyint(1) NOT NULL DEFAULT 1,
      `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $pdo->exec($createTable);
}

// Get edit milestone if requested
$editMilestone = null;
if (isset($_GET['edit'])) {
    $editId = trim($_GET['edit']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM milestones WHERE id = ?");
        $stmt->execute([$editId]);
        $editMilestone = $stmt->fetch();
    } catch (Exception $e) {}
}

renderAdminHeader('milestones');
?>

<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="font-serif text-3xl font-bold text-purple-900">Milestones</h1>
            <p class="text-gray-600 text-sm">Manage the timeline milestones displayed on the About page.</p>
        </div>
        <a href="milestones.php?add=1" class="bg-purple-900 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-purple-800 transition">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Milestone
        </a>
    </div>

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

    <!-- Milestones Table -->
    <div class="bg-white rounded-2xl shadow border border-purple-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-purple-50/50 text-purple-900 text-left">
                    <tr>
                        <th class="p-4 font-semibold">Order</th>
                        <th class="p-4 font-semibold">Year</th>
                        <th class="p-4 font-semibold">Title</th>
                        <th class="p-4 font-semibold">Description</th>
                        <th class="p-4 font-semibold text-center">Visible</th>
                        <th class="p-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-purple-50">
                    <?php foreach ($milestones as $m): ?>
                        <tr class="hover:bg-purple-50/20 transition">
                            <td class="p-4 font-bold text-gray-600"><?php echo $m['display_order']; ?></td>
                            <td class="p-4 font-semibold text-purple-900"><?php echo htmlspecialchars($m['year']); ?></td>
                            <td class="p-4 font-semibold text-gray-800"><?php echo htmlspecialchars($m['title']); ?></td>
                            <td class="p-4 text-gray-600 max-w-md"><?php echo htmlspecialchars(substr($m['description'], 0, 100)); ?>...</td>
                            <td class="p-4 text-center">
                                <a href="milestones.php?toggle_visible=<?php echo urlencode($m['id']); ?>" class="inline-block">
                                    <i data-lucide="<?php echo $m['visible'] ? 'eye' : 'eye-off'; ?>" class="w-4 h-4 <?php echo $m['visible'] ? 'text-green-600' : 'text-gray-400'; ?>"></i>
                                </a>
                            </td>
                            <td class="p-4 text-right whitespace-nowrap">
                                <a href="milestones.php?edit=<?php echo urlencode($m['id']); ?>" class="text-purple-700 hover:text-purple-950 p-2 hover:bg-purple-50 rounded-lg inline-block transition" title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                <a href="milestones.php?delete=<?php echo urlencode($m['id']); ?>" onclick="return confirm('Are you sure you want to delete this milestone?');" class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-lg inline-block transition" title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </a>
                             </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($milestones)): ?>
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">No milestones found. Add your first milestone!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
             </table>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<?php if (isset($_GET['add']) || $editMilestone):
    $m = $editMilestone ?: ['id' => '', 'year' => '', 'title' => '', 'description' => '', 'display_order' => 0, 'visible' => 1];
?>
<div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl">
        <div class="flex justify-between items-center border-b border-purple-100 pb-4 mb-4">
            <h2 class="font-serif text-xl font-bold text-purple-900">
                <?php echo $m['id'] ? 'Edit' : 'Add'; ?> Milestone
            </h2>
            <a href="milestones.php" class="text-gray-500 hover:text-gray-700 transition">
                <i data-lucide="x" class="w-6 h-6"></i>
            </a>
        </div>

        <form method="POST" action="milestones.php">
            <input type="hidden" name="save_milestone" value="1">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($m['id']); ?>">

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Year *</label>
                    <input type="text" name="year" required value="<?php echo htmlspecialchars($m['year']); ?>" class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="e.g., 2018">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Title *</label>
                    <input type="text" name="title" required value="<?php echo htmlspecialchars($m['title']); ?>" class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="e.g., Foundation Established">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description *</label>
                    <textarea name="description" rows="3" required class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Describe this milestone..."><?php echo htmlspecialchars($m['description']); ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Display Order</label>
                    <input type="number" name="display_order" value="<?php echo intval($m['display_order']); ?>" class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="0">
                    <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="visible" value="1" <?php echo $m['visible'] ? 'checked' : ''; ?> class="w-4 h-4 accent-purple-600">
                    <label class="text-sm font-medium text-gray-700">Visible on website</label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-purple-100 mt-4">
                    <a href="milestones.php" class="px-5 py-2.5 rounded-lg text-sm bg-gray-100 text-gray-700 hover:bg-gray-200 transition font-medium">Cancel</a>
                    <button type="submit" class="bg-purple-900 text-white px-6 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 hover:bg-purple-800 transition">
                        <i data-lucide="save" class="w-4 h-4"></i> Save Milestone
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php renderAdminFooter(); ?>