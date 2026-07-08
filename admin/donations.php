<?php
/**
 * Admin Panel Donations Management
 */
require_once __DIR__ . '/../db.php';
checkAdminAuth();

// Handle deletion
$error = '';
$success = '';
if (isset($_GET['delete'])) {
    $id = trim($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM donations WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Donation record deleted.';
    } catch (Exception $e) {
        $error = 'Failed to delete donation record.';
    }
}

// Fetch all filtered & searched donations
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = [];
$params = [];

if ($filter !== 'all') {
    $where[] = "status = ?";
    $params[] = $filter;
}

if (!empty($search)) {
    $where[] = "(donor_name LIKE ? OR donor_email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query = "SELECT * FROM donations";
if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}
$query .= " ORDER BY created_at DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $donations = $stmt->fetchAll();
} catch (Exception $e) {
    $donations = [];
}

// HANDLE CSV EXPORT
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=rvif-donations-' . time() . '.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Date', 'Status', 'Donor Name', 'Email', 'Amount (USD)', 'Message', 'Stripe Session ID']);
    
    foreach ($donations as $d) {
        fputcsv($output, [
            $d['created_at'],
            $d['status'],
            $d['donor_name'] ?: 'Anonymous',
            $d['donor_email'] ?: '—',
            number_format($d['amount_cents'] / 100, 2),
            $d['message'] ?: '',
            $d['stripe_session_id']
        ]);
    }
    fclose($output);
    exit;
}

// Calculate Dashboard Stats
$stats = ['total' => 0, 'this_month' => 0, 'count' => 0, 'unique_donors' => 0];
try {
    // Total raised
    $stats['total'] = $pdo->query("SELECT SUM(amount_cents) FROM donations WHERE status = 'completed'")->fetchColumn() ?: 0;
    
    // This month raised
    $stats['this_month'] = $pdo->query("SELECT SUM(amount_cents) FROM donations WHERE status = 'completed' AND MONTH(COALESCE(completed_at, created_at)) = MONTH(CURRENT_DATE()) AND YEAR(COALESCE(completed_at, created_at)) = YEAR(CURRENT_DATE())")->fetchColumn() ?: 0;
    
    // Completed count
    $stats['count'] = $pdo->query("SELECT COUNT(*) FROM donations WHERE status = 'completed'")->fetchColumn() ?: 0;
    
    // Unique donors
    $stats['unique_donors'] = $pdo->query("SELECT COUNT(DISTINCT donor_email) FROM donations WHERE status = 'completed' AND donor_email IS NOT NULL AND donor_email != ''")->fetchColumn() ?: 0;
} catch (Exception $e) {
    // Fail silently
}

function fmt($cents) {
    return '$' . number_format($cents / 100, 2);
}

require_once __DIR__ . '/layout.php';
renderAdminHeader('donations');
?>

<div class="p-8">
    <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
        <div>
            <h1 class="font-serif text-3xl font-bold text-purple-900">Donations</h1>
            <p class="text-gray-600 text-sm">Track all donations received through Stripe.</p>
        </div>
        <!-- Export link -->
        <a href="<?php echo BASE_PATH; ?>/admin/donations.php?export=csv&filter=<?php echo urlencode($filter); ?>&search=<?php echo urlencode($search); ?>" class="bg-purple-900 text-white px-4 py-2.5 rounded-lg flex items-center gap-2 hover:bg-purple-800 transition shadow">
            <i data-lucide="download" class="w-4 h-4"></i> Export CSV
        </a>
    </div>

    <!-- Feedback messages -->
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

    <!-- STATS BLOCKS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 text-white rounded-2xl p-5 shadow">
            <i data-lucide="dollar-sign" class="w-7 h-7 opacity-80"></i>
            <div class="text-3xl font-serif font-bold mt-2"><?php echo fmt($stats['total']); ?></div>
            <div class="text-xs opacity-80 mt-1">Total Raised</div>
        </div>
        <div class="bg-gradient-to-br from-purple-700 to-purple-900 text-white rounded-2xl p-5 shadow">
            <i data-lucide="trending-up" class="w-7 h-7 opacity-80"></i>
            <div class="text-3xl font-serif font-bold mt-2"><?php echo fmt($stats['this_month']); ?></div>
            <div class="text-xs opacity-80 mt-1">This Month</div>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-700 text-white rounded-2xl p-5 shadow">
            <i data-lucide="check-circle-2" class="w-7 h-7 opacity-80"></i>
            <div class="text-3xl font-serif font-bold mt-2"><?php echo $stats['count']; ?></div>
            <div class="text-xs opacity-80 mt-1">Completed Gifts</div>
        </div>
        <div class="bg-gradient-to-br from-pink-600 to-purple-700 text-white rounded-2xl p-5 shadow">
            <i data-lucide="users" class="w-7 h-7 opacity-80"></i>
            <div class="text-3xl font-serif font-bold mt-2"><?php echo $stats['unique_donors']; ?></div>
            <div class="text-xs opacity-80 mt-1">Unique Donors</div>
        </div>
    </div>

    <!-- FILTER & SEARCH PANEL -->
    <div class="bg-white rounded-2xl shadow border border-purple-50">
        <div class="p-4 border-b border-purple-50 flex flex-wrap items-center gap-3 justify-between">
            <!-- Filter buttons -->
            <div class="flex gap-2 flex-wrap">
                <?php
                $filters = ['all', 'completed', 'pending', 'cancelled'];
                foreach ($filters as $f):
                    $isActive = ($filter === $f);
                    $btnClass = $isActive
                        ? 'bg-purple-900 text-white shadow px-3 py-1.5 rounded-full text-xs font-semibold capitalize transition'
                        : 'bg-purple-50 text-purple-900 hover:bg-purple-100 px-3 py-1.5 rounded-full text-xs font-semibold capitalize transition';
                ?>
                    <a href="<?php echo BASE_PATH; ?>/admin/donations.php?filter=<?php echo $f; ?>&search=<?php echo urlencode($search); ?>" class="<?php echo $btnClass; ?>"><?php echo $f; ?></a>
                <?php endforeach; ?>
            </div>
            <!-- Search Form -->
            <form method="GET" action="<?php echo BASE_PATH; ?>/admin/donations.php" class="relative">
                <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>" />
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search donor..." class="pl-9 pr-3 py-2 border border-purple-200 rounded-full text-sm focus:outline-none focus:border-purple-500 shadow-sm w-56" />
            </form>
        </div>

        <!-- DONATIONS TABLE -->
        <?php if (empty($donations)): ?>
            <div class="p-10 text-center text-gray-500">No donations found.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-purple-50/50 text-purple-900 text-left">
                        <tr>
                            <th class="p-4 font-semibold">Date</th>
                            <th class="p-4 font-semibold">Donor</th>
                            <th class="p-4 font-semibold">Amount</th>
                            <th class="p-4 font-semibold">Status</th>
                            <th class="p-4 font-semibold">Message</th>
                            <th class="p-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-purple-50">
                        <?php foreach ($donations as $d): ?>
                            <tr class="hover:bg-purple-50/20 transition">
                                <td class="p-4 text-xs text-gray-600 whitespace-nowrap"><?php echo date('M j, Y, g:i a', strtotime($d['created_at'])); ?></td>
                                <td class="p-4">
                                    <div class="font-semibold text-purple-900"><?php echo htmlspecialchars($d['donor_name'] ?: 'Anonymous'); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo htmlspecialchars($d['donor_email'] ?: '—'); ?></div>
                                </td>
                                <td class="p-4 font-bold text-emerald-700"><?php echo fmt($d['amount_cents']); ?></td>
                                <td class="p-4">
                                    <?php
                                    $status = $d['status'];
                                    $badge = 'bg-gray-150 text-gray-700';
                                    $icon = 'clock';
                                    if ($status === 'completed') {
                                        $badge = 'bg-green-50 text-green-700 border border-green-200';
                                        $icon = 'check-circle-2';
                                    } elseif ($status === 'cancelled') {
                                        $badge = 'bg-red-50 text-red-700 border border-red-200';
                                        $icon = 'x-circle';
                                    }
                                    ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold capitalize <?php echo $badge; ?>">
                                        <i data-lucide="<?php echo $icon; ?>" class="w-3.5 h-3.5"></i> <?php echo $status; ?>
                                    </span>
                                </td>
                                <td class="p-4 text-xs text-gray-600 max-w-[240px] truncate" title="<?php echo htmlspecialchars($d['message'] ?: ''); ?>">
                                    <?php echo htmlspecialchars($d['message'] ?: '—'); ?>
                                </td>
                                <td class="p-4 text-right">
                                    <a href="<?php echo BASE_PATH; ?>/admin/donations.php?delete=<?php echo urlencode($d['id']); ?>&filter=<?php echo urlencode($filter); ?>&search=<?php echo urlencode($search); ?>" onclick="return confirm('Are you sure you want to delete this donation record?');" class="text-red-500 hover:text-red-700 p-1.5 hover:bg-red-50 rounded-lg inline-block transition" title="Delete record">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
renderAdminFooter();
?>
