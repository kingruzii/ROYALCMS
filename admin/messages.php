<?php
/**
 * Admin Panel Messages
 */
require_once __DIR__ . '/layout.php';

// Handle deletion
$error = '';
$success = '';
if (isset($_GET['delete'])) {
    $id = trim($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Message successfully deleted.';
    } catch (Exception $e) {
        $error = 'Failed to delete message.';
    }
}

// Fetch all messages
$msgs = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM contact_messages ORDER BY created_at DESC");
    $stmt->execute();
    $msgs = $stmt->fetchAll();
} catch (Exception $e) {
    // Fail silently
}

renderAdminHeader('messages');
?>

<div class="p-8 max-w-5xl">
    <h1 class="font-serif text-3xl font-bold text-purple-900 mb-2">Messages</h1>
    <p class="text-gray-600 mb-6">Read and manage contact form submissions sent from the public website.</p>

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

    <div class="space-y-4">
        <?php foreach ($msgs as $m): ?>
            <div class="bg-white rounded-xl shadow p-5 border border-purple-100/50 hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 font-bold text-purple-900 text-base">
                            <i data-lucide="mail" class="w-4 h-4 shrink-0 text-purple-600"></i>
                            <span><?php echo htmlspecialchars($m['subject'] ?: '(No Subject)'); ?></span>
                        </div>
                        <div class="text-sm text-gray-650 font-medium mt-1">
                            From: <strong class="text-purple-800"><?php echo htmlspecialchars($m['name']); ?></strong> 
                            &lt;<?php echo htmlspecialchars($m['email']); ?>&gt;
                        </div>
                        <div class="text-[11px] text-gray-400 font-semibold">
                            <i class="inline-block align-middle" data-lucide="calendar" style="width:12px; height:12px;"></i> 
                            <?php echo date('F j, Y, g:i a', strtotime($m['created_at'])); ?>
                        </div>
                        <p class="text-gray-750 mt-4 whitespace-pre-line text-sm border-t border-purple-50 pt-3 leading-relaxed"><?php echo htmlspecialchars($m['message']); ?></p>
                    </div>
                    <a href="<?php echo BASE_PATH; ?>/admin/messages.php?delete=<?php echo urlencode($m['id']); ?>" onclick="return confirm('Are you sure you want to delete this message?');" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition" title="Delete message">
                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($msgs)): ?>
            <div class="bg-white rounded-2xl p-10 text-center border border-purple-50">
                <i data-lucide="mail-open" class="w-12 h-12 text-gray-300 mx-auto mb-2"></i>
                <p class="text-gray-500 text-sm">No messages received yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
renderAdminFooter();
?>
