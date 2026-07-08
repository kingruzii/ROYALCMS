<?php
/**
 * Admin Panel Team Members CRUD
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
        $stmt = $pdo->prepare("DELETE FROM team_members WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Team member successfully deleted.';
        header("Location: team.php");
        exit();
    } catch (Exception $e) {
        $error = 'Failed to delete team member: ' . $e->getMessage();
    }
}

// 2. HANDLE VISIBILITY TOGGLE
if (isset($_GET['toggle_visible'])) {
    $id = trim($_GET['toggle_visible']);
    try {
        $stmt = $pdo->prepare("UPDATE team_members SET visible = 1 - visible WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Team member visibility toggled.';
        header("Location: team.php");
        exit();
    } catch (Exception $e) {
        $error = 'Failed to toggle visibility.';
    }
}

// 3. HANDLE SAVE SUBMISSION (ADD / EDIT)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_team_member'])) {
    $id = isset($_POST['id']) ? trim($_POST['id']) : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';
    $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';
    $photo = isset($_POST['photo']) ? trim($_POST['photo']) : '';
    $display_order = isset($_POST['display_order']) ? intval($_POST['display_order']) : 0;
    $visible = isset($_POST['visible']) ? 1 : 0;

    // Handle Upload
    if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
        require_once __DIR__ . '/upload_helper.php';
        $uploadRes = handleAdminUpload('photo_file', 'team');
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
                $newId = 't-' . time() . '-' . rand(100, 999);
                $stmt = $pdo->prepare("INSERT INTO team_members (id, name, role, bio, photo, display_order, visible) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$newId, $name, $role, $bio, $photo, $display_order, $visible]);
                $success = 'New team member successfully added.';
                header("Location: team.php");
                exit();
            } else {
                // UPDATE EXISTING
                $stmt = $pdo->prepare("UPDATE team_members SET name=?, role=?, bio=?, photo=?, display_order=?, visible=? WHERE id=?");
                $stmt->execute([$name, $role, $bio, $photo, $display_order, $visible, $id]);
                $success = 'Team member successfully updated.';
                header("Location: team.php");
                exit();
            }
        } catch (Exception $e) {
            $error = 'Database operation failed: ' . $e->getMessage();
        }
    }
}

// 4. FETCH EDIT DETAILS IF REQUESTED
$editMember = null;
if (isset($_GET['edit'])) {
    $editId = trim($_GET['edit']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM team_members WHERE id = ?");
        $stmt->execute([$editId]);
        $editMember = $stmt->fetch();
    } catch (Exception $e) {
        // Silent fail
    }
}

// 5. FETCH ALL TEAM MEMBERS FOR LISTING
$team = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM team_members ORDER BY display_order ASC, name ASC");
    $stmt->execute();
    $team = $stmt->fetchAll();
} catch (Exception $e) {
    // Silent fail
}

// Render the header
renderAdminHeader('team');
?>

<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="font-serif text-3xl font-bold text-purple-900">Team Members</h1>
            <p class="text-gray-600 text-sm">Manage organization team members.</p>
        </div>
        <a href="team.php?add=1" class="bg-purple-900 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-purple-800 transition shadow">
            <i data-lucide="plus" class="w-4 h-4"></i> Add Member
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

    <!-- TEAM MEMBERS GRID -->
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($team as $m): ?>
            <div class="bg-white rounded-2xl shadow p-5 border border-purple-50 flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex justify-between items-start mb-3">
                        <?php if (!empty($m['photo'])): ?>
                            <img src="<?php echo htmlspecialchars($m['photo']); ?>" alt="" class="w-20 h-20 rounded-full object-cover border" />
                        <?php else: ?>
                            <div class="w-20 h-20 rounded-full bg-purple-50 flex items-center justify-center text-purple-300 border">
                                <i data-lucide="user" class="w-10 h-10"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="flex gap-1.5">
                            <!-- Visibility Toggle -->
                            <a href="team.php?toggle_visible=<?php echo urlencode($m['id']); ?>" class="p-1 rounded transition hover:bg-purple-50 <?php echo $m['visible'] ? 'text-green-600' : 'text-gray-400'; ?>" title="Toggle visibility">
                                <i data-lucide="<?php echo $m['visible'] ? 'eye' : 'eye-off'; ?>" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="font-serif font-bold text-purple-900 text-lg"><?php echo htmlspecialchars($m['name']); ?></div>
                    <div class="text-yellow-600 text-sm font-semibold mb-2"><?php echo htmlspecialchars($m['role']); ?></div>
                    <p class="text-xs text-gray-600 leading-relaxed line-clamp-4 border-t border-purple-50 pt-2"><?php echo htmlspecialchars($m['bio']); ?></p>
                </div>
                
                <div class="flex gap-3 justify-end border-t border-purple-50 pt-3 mt-4 text-xs font-semibold">
                    <span class="mr-auto text-gray-400 self-center">Order: <?php echo $m['display_order']; ?></span>
                    <a href="team.php?edit=<?php echo urlencode($m['id']); ?>" class="text-purple-700 hover:bg-purple-50 px-2.5 py-1.5 rounded transition">
                        <i data-lucide="edit" class="inline w-3.5 h-3.5 mr-0.5"></i> Edit
                    </a>
                    <a href="team.php?delete=<?php echo urlencode($m['id']); ?>" onclick="return confirm('Are you sure you want to delete this team member?');" class="text-red-500 hover:bg-red-50 px-2.5 py-1.5 rounded transition">
                        <i data-lucide="trash-2" class="inline w-3.5 h-3.5 mr-0.5"></i> Delete
                    </a>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($team)): ?>
            <div class="text-center py-12 col-span-full">
                <i data-lucide="users" class="w-16 h-16 mx-auto text-gray-300 mb-4"></i>
                <p class="text-gray-500 font-medium">No team members added yet.</p>
                <a href="team.php?add=1" class="inline-block mt-4 text-purple-600 hover:text-purple-800">Add your first team member →</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ADD/EDIT DIALOG MODAL -->
<?php if (isset($_GET['add']) || $editMember): 
    $m = $editMember ?: [
        'id' => '', 'name' => '', 'role' => '', 'bio' => '', 'photo' => '', 'display_order' => 0, 'visible' => 1
    ];
?>
<div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl w-full max-w-xl p-6 shadow-2xl">
        <div class="flex justify-between items-center border-b border-purple-100 pb-4 mb-4 sticky top-0 bg-white">
            <h2 class="font-serif text-xl font-bold text-purple-900">
                <?php echo $m['id'] ? 'Edit' : 'Add'; ?> Team Member
            </h2>
            <a href="team.php" class="text-gray-500 hover:text-gray-700 transition">
                <i data-lucide="x" class="w-6 h-6"></i>
            </a>
        </div>
        
        <form method="POST" action="team.php" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="save_team_member" value="1" />
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($m['id']); ?>" />
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Name *</label>
                <input type="text" name="name" required value="<?php echo htmlspecialchars($m['name']); ?>" 
                       class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                       placeholder="Full name" />
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Role / Position</label>
                <input type="text" name="role" value="<?php echo htmlspecialchars($m['role']); ?>" 
                       class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                       placeholder="e.g., Chief Executive Officer" />
            </div>
            
            <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Profile Photo</label>
                
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Upload Image</label>
                    <input type="file" name="photo_file" accept="image/*" class="w-full text-sm" />
                </div>
                
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Or Photo URL</label>
                    <input type="text" name="photo" value="<?php echo htmlspecialchars($m['photo']); ?>" 
                           class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" 
                           placeholder="https://example.com/photo.jpg" />
                </div>
                
                <?php if (!empty($m['photo'])): ?>
                    <div class="mt-2">
                        <img src="<?php echo htmlspecialchars($m['photo']); ?>" alt="Current photo" class="w-16 h-16 rounded-full object-cover border" />
                    </div>
                <?php endif; ?>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Bio / Description</label>
                <textarea name="bio" rows="4" 
                          class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                          placeholder="Enter biographical information..."><?php echo htmlspecialchars($m['bio']); ?></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Display Order</label>
                <input type="number" name="display_order" value="<?php echo intval($m['display_order']); ?>" 
                       class="w-full border border-purple-200 p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                       placeholder="0" />
                <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
            </div>
            
            <div class="flex items-center gap-2">
                <input type="checkbox" name="visible" value="1" <?php echo $m['visible'] ? 'checked' : ''; ?> class="w-4 h-4 accent-purple-600" />
                <label class="text-sm font-medium text-gray-700">Visible on website</label>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-purple-100 mt-4">
                <a href="team.php" class="px-5 py-2.5 rounded-lg text-sm bg-gray-100 text-gray-700 hover:bg-gray-200 transition font-medium">Cancel</a>
                <button type="submit" class="bg-purple-900 text-white px-6 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2 hover:bg-purple-800 transition shadow">
                    <i data-lucide="save" class="w-4 h-4"></i> Save Member
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php
renderAdminFooter();
?>