<?php
/**
 * Admin Founder Management
 */
require_once __DIR__ . '/layout.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            $photoPath = $_POST['current_photo'] ?? '';
            
            // Handle file upload
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileInfo = pathinfo($_FILES['photo']['name']);
                $extension = strtolower($fileInfo['extension']);
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($extension, $allowedExtensions)) {
                    $fileName = 'founder_' . time() . '.' . $extension;
                    $uploadPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
                        $photoPath = BASE_PATH . '/uploads/' . $fileName;
                        
                        // Delete old photo if it exists and is different
                        if (!empty($_POST['current_photo']) && $_POST['current_photo'] !== $photoPath) {
                            $oldPhotoPath = __DIR__ . '/..' . str_replace(BASE_PATH, '', $_POST['current_photo']);
                            if (file_exists($oldPhotoPath)) {
                                unlink($oldPhotoPath);
                            }
                        }
                    } else {
                        throw new Exception("Failed to upload photo");
                    }
                } else {
                    throw new Exception("Invalid file type. Please upload JPG, PNG, GIF, or WebP images only.");
                }
            }
            
            switch ($_POST['action']) {
                case 'update':
                    $stmt = $pdo->prepare("
                        UPDATE founder_info SET 
                        name = ?, title = ?, profession = ?, photo = ?, 
                        short_bio = ?, full_story = ?, quote = ?, achievements = ?, visible = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $_POST['name'], $_POST['title'], $_POST['profession'], $photoPath,
                        $_POST['short_bio'], $_POST['full_story'], $_POST['quote'], $_POST['achievements'],
                        isset($_POST['visible']) ? 1 : 0, $_POST['id']
                    ]);
                    $success = "Founder information updated successfully!";
                    break;
                    
                case 'create':
                    $id = 'founder-' . time();
                    $stmt = $pdo->prepare("
                        INSERT INTO founder_info (id, name, title, profession, photo, short_bio, full_story, quote, achievements, visible)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $id, $_POST['name'], $_POST['title'], $_POST['profession'], $photoPath,
                        $_POST['short_bio'], $_POST['full_story'], $_POST['quote'], $_POST['achievements'],
                        isset($_POST['visible']) ? 1 : 0
                    ]);
                    $success = "Founder information created successfully!";
                    break;
            }
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Get founder information
try {
    $stmt = $pdo->prepare("SELECT * FROM founder_info ORDER BY updated_at DESC LIMIT 1");
    $stmt->execute();
    $founder = $stmt->fetch();
} catch (Exception $e) {
    $founder = null;
}

renderAdminHeader('founder');
?>

<div style="padding:32px;max-width:1200px;margin:0 auto;">
    <!-- Header -->
    <div style="margin-bottom:32px;">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:8px;">
            <div style="width:48px;height:48px;border-radius:16px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;">
                <i data-lucide="crown" style="width:24px;height:24px;color:#fff;"></i>
            </div>
            <div>
                <h1 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:2.2rem;font-weight:700;color:#3b0764;margin:0;line-height:1.1;">Founder Management</h1>
                <p style="font-family:'Outfit',sans-serif;color:#6b7280;margin:4px 0 0;font-size:0.9rem;">Manage founder information and story</p>
            </div>
        </div>
    </div>

    <?php if (isset($success)): ?>
    <div style="background:linear-gradient(135deg,#d1fae5,#a7f3d0);border:1px solid #34d399;border-radius:12px;padding:16px;margin-bottom:24px;display:flex;align-items:center;gap:12px;">
        <i data-lucide="check-circle" style="width:20px;height:20px;color:#059669;"></i>
        <span style="color:#065f46;font-weight:500;"><?php echo htmlspecialchars($success); ?></span>
    </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
    <div style="background:linear-gradient(135deg,#fecaca,#fca5a5);border:1px solid #f87171;border-radius:12px;padding:16px;margin-bottom:24px;display:flex;align-items:center;gap:12px;">
        <i data-lucide="alert-circle" style="width:20px;height:20px;color:#dc2626;"></i>
        <span style="color:#991b1b;font-weight:500;"><?php echo htmlspecialchars($error); ?></span>
    </div>
    <?php endif; ?>

    <!-- Founder Form -->
    <div style="background:#fff;border-radius:20px;box-shadow:0 4px 20px rgba(88,28,135,0.08);border:1px solid #f3e8ff;overflow:hidden;">
        <div style="background:linear-gradient(135deg,#f3e8ff,#ede9fe);padding:24px;border-bottom:1px solid #e9d5ff;">
            <h2 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:1.5rem;font-weight:700;color:#3b0764;margin:0;display:flex;align-items:center;gap:12px;">
                <i data-lucide="user" style="width:20px;height:20px;color:#7e22ce;"></i>
                <?php echo $founder ? 'Edit Founder Information' : 'Add Founder Information'; ?>
            </h2>
        </div>

        <form method="POST" enctype="multipart/form-data" style="padding:32px;">
            <input type="hidden" name="action" value="<?php echo $founder ? 'update' : 'create'; ?>">
            <?php if ($founder): ?>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($founder['id']); ?>">
            <input type="hidden" name="current_photo" value="<?php echo htmlspecialchars($founder['photo'] ?? ''); ?>">
            <?php endif; ?>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">
                <div>
                    <label style="display:block;font-family:'Outfit',sans-serif;font-weight:600;color:#374151;margin-bottom:8px;font-size:0.9rem;">Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($founder['name'] ?? ''); ?>" required
                           style="width:100%;padding:12px 16px;border:2px solid #e5e7eb;border-radius:12px;font-family:'Outfit',sans-serif;font-size:0.9rem;transition:all 0.2s;background:#fff;"
                           onfocus="this.style.borderColor='#7e22ce';this.style.boxShadow='0 0 0 3px rgba(126,34,206,0.1)'"
                           onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
                </div>
                <div>
                    <label style="display:block;font-family:'Outfit',sans-serif;font-weight:600;color:#374151;margin-bottom:8px;font-size:0.9rem;">Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($founder['title'] ?? ''); ?>"
                           style="width:100%;padding:12px 16px;border:2px solid #e5e7eb;border-radius:12px;font-family:'Outfit',sans-serif;font-size:0.9rem;transition:all 0.2s;background:#fff;"
                           onfocus="this.style.borderColor='#7e22ce';this.style.boxShadow='0 0 0 3px rgba(126,34,206,0.1)'"
                           onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">
                <div>
                    <label style="display:block;font-family:'Outfit',sans-serif;font-weight:600;color:#374151;margin-bottom:8px;font-size:0.9rem;">Profession</label>
                    <input type="text" name="profession" value="<?php echo htmlspecialchars($founder['profession'] ?? ''); ?>"
                           style="width:100%;padding:12px 16px;border:2px solid #e5e7eb;border-radius:12px;font-family:'Outfit',sans-serif;font-size:0.9rem;transition:all 0.2s;background:#fff;"
                           onfocus="this.style.borderColor='#7e22ce';this.style.boxShadow='0 0 0 3px rgba(126,34,206,0.1)'"
                           onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
                </div>
                <div>
                    <label style="display:block;font-family:'Outfit',sans-serif;font-weight:600;color:#374151;margin-bottom:8px;font-size:0.9rem;">Photo Upload</label>
                    <div style="position:relative;">
                        <input type="file" name="photo" accept="image/*" id="photo-upload"
                               style="width:100%;padding:12px 16px;border:2px solid #e5e7eb;border-radius:12px;font-family:'Outfit',sans-serif;font-size:0.9rem;transition:all 0.2s;background:#fff;cursor:pointer;"
                               onfocus="this.style.borderColor='#7e22ce';this.style.boxShadow='0 0 0 3px rgba(126,34,206,0.1)'"
                               onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                               onchange="previewImage(this)">
                        <div style="font-size:0.75rem;color:#6b7280;margin-top:4px;">
                            <i data-lucide="info" style="width:12px;height:12px;display:inline;margin-right:4px;"></i>
                            Supported: JPG, PNG, GIF, WebP (Max 5MB)
                        </div>
                    </div>
                    
                    <?php if ($founder && $founder['photo']): ?>
                    <div style="margin-top:12px;">
                        <div style="font-size:0.8rem;color:#6b7280;margin-bottom:6px;">Current Photo:</div>
                        <img src="<?php echo htmlspecialchars($founder['photo']); ?>" alt="Current founder photo" 
                             style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid #e5e7eb;">
                    </div>
                    <?php endif; ?>
                    
                    <!-- Preview area for new upload -->
                    <div id="photo-preview" style="margin-top:12px;display:none;">
                        <div style="font-size:0.8rem;color:#6b7280;margin-bottom:6px;">New Photo Preview:</div>
                        <img id="preview-img" style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid #7e22ce;">
                    </div>
                </div>
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block;font-family:'Outfit',sans-serif;font-weight:600;color:#374151;margin-bottom:8px;font-size:0.9rem;">Short Bio</label>
                <textarea name="short_bio" rows="3"
                          style="width:100%;padding:12px 16px;border:2px solid #e5e7eb;border-radius:12px;font-family:'Outfit',sans-serif;font-size:0.9rem;transition:all 0.2s;background:#fff;resize:vertical;"
                          onfocus="this.style.borderColor='#7e22ce';this.style.boxShadow='0 0 0 3px rgba(126,34,206,0.1)'"
                          onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"><?php echo htmlspecialchars($founder['short_bio'] ?? ''); ?></textarea>
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block;font-family:'Outfit',sans-serif;font-weight:600;color:#374151;margin-bottom:8px;font-size:0.9rem;">Full Story</label>
                <textarea name="full_story" rows="8"
                          style="width:100%;padding:12px 16px;border:2px solid #e5e7eb;border-radius:12px;font-family:'Outfit',sans-serif;font-size:0.9rem;transition:all 0.2s;background:#fff;resize:vertical;"
                          onfocus="this.style.borderColor='#7e22ce';this.style.boxShadow='0 0 0 3px rgba(126,34,206,0.1)'"
                          onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"><?php echo htmlspecialchars($founder['full_story'] ?? ''); ?></textarea>
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block;font-family:'Outfit',sans-serif;font-weight:600;color:#374151;margin-bottom:8px;font-size:0.9rem;">Inspirational Quote</label>
                <textarea name="quote" rows="2"
                          style="width:100%;padding:12px 16px;border:2px solid #e5e7eb;border-radius:12px;font-family:'Outfit',sans-serif;font-size:0.9rem;transition:all 0.2s;background:#fff;resize:vertical;"
                          onfocus="this.style.borderColor='#7e22ce';this.style.boxShadow='0 0 0 3px rgba(126,34,206,0.1)'"
                          onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"><?php echo htmlspecialchars($founder['quote'] ?? ''); ?></textarea>
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block;font-family:'Outfit',sans-serif;font-weight:600;color:#374151;margin-bottom:8px;font-size:0.9rem;">Key Achievements</label>
                <textarea name="achievements" rows="4"
                          style="width:100%;padding:12px 16px;border:2px solid #e5e7eb;border-radius:12px;font-family:'Outfit',sans-serif;font-size:0.9rem;transition:all 0.2s;background:#fff;resize:vertical;"
                          onfocus="this.style.borderColor='#7e22ce';this.style.boxShadow='0 0 0 3px rgba(126,34,206,0.1)'"
                          onblur="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'"
                          placeholder="List key achievements, one per line"><?php echo htmlspecialchars($founder['achievements'] ?? ''); ?></textarea>
            </div>

            <div style="margin-bottom:32px;">
                <label style="display:flex;align-items:center;gap:12px;cursor:pointer;">
                    <input type="checkbox" name="visible" <?php echo ($founder['visible'] ?? 1) ? 'checked' : ''; ?>
                           style="width:18px;height:18px;accent-color:#7e22ce;">
                    <span style="font-family:'Outfit',sans-serif;font-weight:500;color:#374151;font-size:0.9rem;">Show founder section on website</span>
                </label>
            </div>

            <div style="display:flex;gap:16px;justify-content:flex-end;">
                <button type="submit"
                        style="background:linear-gradient(135deg,#7e22ce,#581c87);color:#fff;border:none;padding:12px 24px;border-radius:12px;font-family:'Outfit',sans-serif;font-weight:600;font-size:0.9rem;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all 0.2s;box-shadow:0 4px 12px rgba(126,34,206,0.3);"
                        onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 20px rgba(126,34,206,0.4)'"
                        onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(126,34,206,0.3)'">
                    <i data-lucide="save" style="width:16px;height:16px;"></i>
                    <?php echo $founder ? 'Update Founder' : 'Create Founder'; ?>
                </button>
            </div>
        </form>
    </div>

    <?php if ($founder): ?>
    <!-- Preview -->
    <div style="background:#fff;border-radius:20px;box-shadow:0 4px 20px rgba(88,28,135,0.08);border:1px solid #f3e8ff;overflow:hidden;margin-top:32px;">
        <div style="background:linear-gradient(135deg,#fef3c7,#fde68a);padding:24px;border-bottom:1px solid #f59e0b;">
            <h2 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:1.5rem;font-weight:700;color:#92400e;margin:0;display:flex;align-items:center;gap:12px;">
                <i data-lucide="eye" style="width:20px;height:20px;color:#d97706;"></i>
                Preview
            </h2>
        </div>

        <div style="padding:32px;">
            <div style="display:grid;grid-template-columns:300px 1fr;gap:32px;align-items:start;">
                <?php if ($founder['photo']): ?>
                <div>
                    <img src="<?php echo htmlspecialchars($founder['photo']); ?>" alt="<?php echo htmlspecialchars($founder['name']); ?>"
                         style="width:100%;height:300px;object-fit:cover;border-radius:16px;box-shadow:0 8px 24px rgba(0,0,0,0.1);">
                </div>
                <?php endif; ?>
                
                <div>
                    <h3 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:1.8rem;font-weight:700;color:#3b0764;margin:0 0 8px;"><?php echo htmlspecialchars($founder['name']); ?></h3>
                    <p style="font-family:'Outfit',sans-serif;font-size:1rem;color:#7e22ce;font-weight:600;margin:0 0 4px;"><?php echo htmlspecialchars($founder['title']); ?></p>
                    <p style="font-family:'Outfit',sans-serif;font-size:0.9rem;color:#6b7280;margin:0 0 20px;"><?php echo htmlspecialchars($founder['profession']); ?></p>
                    
                    <?php if ($founder['short_bio']): ?>
                    <p style="font-family:'Outfit',sans-serif;font-size:0.95rem;color:#4b5563;line-height:1.7;margin-bottom:20px;"><?php echo nl2br(htmlspecialchars($founder['short_bio'])); ?></p>
                    <?php endif; ?>
                    
                    <?php if ($founder['quote']): ?>
                    <blockquote style="border-left:4px solid #f59e0b;padding-left:16px;margin:20px 0;font-family:'Cormorant Garamond',Georgia,serif;font-size:1.1rem;font-style:italic;color:#7e22ce;">
                        "<?php echo htmlspecialchars($founder['quote']); ?>"
                    </blockquote>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function previewImage(input) {
    const previewDiv = document.getElementById('photo-preview');
    const previewImg = document.getElementById('preview-img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewDiv.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        previewDiv.style.display = 'none';
    }
}
</script>

<?php renderAdminFooter(); ?>