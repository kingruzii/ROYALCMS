<?php
/**
 * Admin Panel Partner Organizations CRUD
 */
require_once __DIR__ . '/layout.php';
$success = '';

// 1. HANDLE DELETION
if (isset($_GET['delete'])) {
    $id = trim($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM partners WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Partner successfully deleted.';
    } catch (Exception $e) {
        $error = 'Failed to delete partner: ' . $e->getMessage();
    }
}

// 2. HANDLE SAVE SUBMISSION (ADD / EDIT)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_partner'])) {
    $id = isset($_POST['id']) ? trim($_POST['id']) : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $country = isset($_POST['country']) ? trim($_POST['country']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';
    $icon = isset($_POST['icon']) ? trim($_POST['icon']) : 'graduation-cap';
    $color = isset($_POST['color']) ? trim($_POST['color']) : '#7e22ce';
    $display_order = isset($_POST['display_order']) ? intval($_POST['display_order']) : 0;

    if (empty($name) || empty($country) || empty($category)) {
        $error = 'Name, Country, and Category are required.';
    }

    if (empty($error)) {
        try {
            if (empty($id)) {
                // INSERT NEW
                $newId = 'p-' . time() . '-' . rand(100, 999);
                $stmt = $pdo->prepare("INSERT INTO partners (id, name, country, category, icon, color, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$newId, $name, $country, $category, $icon, $color, $display_order]);
                $success = 'New partner successfully added.';
            } else {
                // UPDATE EXISTING
                $stmt = $pdo->prepare("UPDATE partners SET name=?, country=?, category=?, icon=?, color=?, display_order=? WHERE id=?");
                $stmt->execute([$name, $country, $category, $icon, $color, $display_order, $id]);
                $success = 'Partner successfully updated.';
            }
        } catch (Exception $e) {
            $error = 'Database operation failed: ' . $e->getMessage();
        }
    }
}

// 3. FETCH EDIT DETAILS IF REQUESTED
$editPartner = null;
if (isset($_GET['edit'])) {
    $editId = trim($_GET['edit']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM partners WHERE id = ?");
        $stmt->execute([$editId]);
        $editPartner = $stmt->fetch();
    } catch (Exception $e) {
        // Silent fail
    }
}

// 4. FETCH ALL PARTNERS FOR LISTING
$partners = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM partners ORDER BY display_order ASC, name ASC");
    $stmt->execute();
    $partners = $stmt->fetchAll();
} catch (Exception $e) {
    // Silent fail
}

renderAdminHeader('partners');
?>

<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="font-serif text-3xl font-bold text-purple-900">Partner Organizations</h1>
            <p class="text-gray-600 text-sm">Manage RVIF support networks, institutions, and governments.</p>
        </div>
        <a href="<?php echo BASE_PATH; ?>/admin/partners.php?add=1" class="bg-purple-900 text-white px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-purple-800 transition shadow"><i data-lucide="plus" class="w-4 h-4"></i> Add Partner</a>
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

    <!-- PARTNERS GRID -->
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($partners as $p): ?>
            <div class="bg-white rounded-2xl shadow p-5 border border-purple-50 flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex justify-between items-start mb-3">
                        <div style="width:50px;height:50px;border-radius:12px;background:<?php echo htmlspecialchars($p['color']); ?>1a;display:flex;align-items:center;justify-content:center;border:1.5px solid <?php echo htmlspecialchars($p['color']); ?>22;">
                            <i data-lucide="<?php echo htmlspecialchars($p['icon']); ?>" style="width:22px;height:22px;color:<?php echo htmlspecialchars($p['color']); ?>;"></i>
                        </div>
                        <div class="text-xs font-bold text-purple-600 uppercase tracking-wide bg-purple-50 px-2.5 py-1 rounded-full"><?php echo htmlspecialchars($p['category']); ?></div>
                    </div>
                    
                    <div class="font-serif font-bold text-purple-900 text-lg mb-1"><?php echo htmlspecialchars($p['name']); ?></div>
                    <div class="text-gray-500 text-xs font-semibold flex items-center gap-1"><i data-lucide="map-pin" class="w-3.5 h-3.5 text-yellow-600"></i> <?php echo htmlspecialchars($p['country']); ?></div>
                </div>
                
                <div class="flex gap-3 justify-end border-t border-purple-50 pt-3 mt-4 text-xs font-semibold">
                    <span class="mr-auto text-gray-400 self-center">Order: <?php echo $p['display_order']; ?></span>
                    <a href="<?php echo BASE_PATH; ?>/admin/partners.php?edit=<?php echo urlencode($p['id']); ?>" class="text-purple-700 hover:bg-purple-50 px-2.5 py-1.5 rounded transition"><i data-lucide="edit" class="inline w-3.5 h-3.5 mr-0.5"></i> Edit</a>
                    <a href="<?php echo BASE_PATH; ?>/admin/partners.php?delete=<?php echo urlencode($p['id']); ?>" onclick="return confirm('Are you sure you want to delete this partner?');" class="text-red-500 hover:bg-red-50 px-2.5 py-1.5 rounded transition"><i data-lucide="trash-2" class="inline w-3.5 h-3.5 mr-0.5"></i> Delete</a>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($partners)): ?>
            <p class="text-center text-gray-500 col-span-full py-8">No partners added yet.</p>
        <?php endif; ?>
    </div>
</div>

<!-- ADD/EDIT DIALOG MODAL -->
<?php if (isset($_GET['add']) || $editPartner): 
    $p = $editPartner ?: [
        'id' => '', 'name' => '', 'country' => '', 'category' => '', 'icon' => 'graduation-cap', 'color' => '#7e22ce', 'display_order' => 0
    ];
?>
<div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-xl p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b border-purple-50 pb-3">
            <h2 class="font-serif text-xl font-bold text-purple-900"><?php echo $p['id'] ? 'Edit' : 'Add'; ?> Partner Organization</h2>
            <a href="<?php echo BASE_PATH; ?>/admin/partners.php" class="text-gray-500 hover:text-gray-700"><i data-lucide="x" class="w-6 h-6"></i></a>
        </div>
        
        <form method="POST" action="<?php echo BASE_PATH; ?>/admin/partners.php" class="space-y-4">
            <input type="hidden" name="save_partner" value="1" />
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($p['id']); ?>" />
            
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Partner Name *</label>
                    <input required type="text" name="name" value="<?php echo htmlspecialchars($p['name']); ?>" class="w-full border border-purple-100 p-2.5 rounded-lg focus:ring-1 focus:ring-purple-500 focus:outline-none text-sm" placeholder="Mount Kigali University" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Country *</label>
                        <input required type="text" name="country" value="<?php echo htmlspecialchars($p['country']); ?>" class="w-full border border-purple-100 p-2.5 rounded-lg focus:ring-1 focus:ring-purple-500 focus:outline-none text-sm" placeholder="Rwanda" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Category *</label>
                        <input required type="text" name="category" value="<?php echo htmlspecialchars($p['category']); ?>" class="w-full border border-purple-100 p-2.5 rounded-lg focus:ring-1 focus:ring-purple-500 focus:outline-none text-sm" placeholder="University" />
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Lucide Icon</label>
                        <input type="text" name="icon" value="<?php echo htmlspecialchars($p['icon']); ?>" class="w-full border border-purple-100 p-2.5 rounded-lg focus:ring-1 focus:ring-purple-500 focus:outline-none text-sm" placeholder="graduation-cap" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Hex Color</label>
                        <input type="text" name="color" value="<?php echo htmlspecialchars($p['color']); ?>" class="w-full border border-purple-100 p-2.5 rounded-lg focus:ring-1 focus:ring-purple-500 focus:outline-none text-sm" placeholder="#7e22ce" />
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Display Order</label>
                    <input type="number" name="display_order" value="<?php echo intval($p['display_order']); ?>" class="w-full border border-purple-100 p-2.5 rounded-lg focus:ring-1 focus:ring-purple-500 focus:outline-none text-sm" />
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-purple-50 mt-4">
                <a href="<?php echo BASE_PATH; ?>/admin/partners.php" class="px-5 py-2.5 rounded-lg text-sm bg-purple-50 text-purple-900 hover:bg-purple-100 transition font-bold">Cancel</a>
                <button type="submit" class="bg-purple-900 text-white px-6 py-2.5 rounded-lg text-sm font-bold flex items-center gap-2 hover:bg-purple-800 transition shadow"><i data-lucide="save" class="w-4 h-4"></i> Save</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php
renderAdminFooter();
?>
