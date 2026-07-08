<?php
/**
 * Admin Portal Login Page - Premium Enhanced
 */
require_once __DIR__ . '/../config.php';

startAdminSession();

// Logout handler
if (isset($_GET['logout'])) {
    unset($_SESSION['rvif_admin']);
    session_destroy();
    header('Location: ' . BASE_PATH . '/admin/login.php');
    exit;
}

// If already logged in, redirect to dashboard
if (isset($_SESSION['rvif_admin']) && $_SESSION['rvif_admin'] === true) {
    header('Location: ' . BASE_PATH . '/admin/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($username === ADMIN_USER && $password === ADMIN_PASS) {
        $_SESSION['rvif_admin'] = true;
        header('Location: ' . BASE_PATH . '/admin/dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal — RVIF CMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Outfit', sans-serif;
            background: #0f0523;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* ── Animated background ── */
        .bg-layer {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, #0f0523 0%, #1e0a40 40%, #2e1065 70%, #1a0535 100%);
            z-index: 0;
        }

        /* Animated mesh gradient orbs */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            z-index: 0;
            animation: floatOrb 12s ease-in-out infinite;
        }
        .orb-1 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, #7c3aed, transparent);
            top: -100px; left: -100px;
            animation-delay: 0s;
        }
        .orb-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, #f59e0b, transparent);
            bottom: -80px; right: -80px;
            animation-delay: -4s;
        }
        .orb-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, #db2777, transparent);
            top: 40%; left: 60%;
            animation-delay: -8s;
        }

        @keyframes floatOrb {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%       { transform: translate(30px, -20px) scale(1.05); }
            66%       { transform: translate(-20px, 30px) scale(0.95); }
        }

        /* Particle dots */
        .particle {
            position: fixed;
            width: 3px; height: 3px;
            border-radius: 50%;
            background: rgba(245, 158, 11, 0.6);
            z-index: 0;
            animation: floatParticle linear infinite;
        }

        @keyframes floatParticle {
            0%   { transform: translateY(100vh) scale(0); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { transform: translateY(-20vh) scale(1); opacity: 0; }
        }

        /* ── Card ── */
        .login-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 28px;
            padding: 48px 40px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.05) inset;
            animation: slideUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px) scale(0.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Logo badge */
        .logo-ring {
            width: 80px; height: 80px;
            border-radius: 24px;
            background: linear-gradient(135deg, #7c3aed, #f59e0b);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 16px 40px rgba(124,58,237,0.5);
            animation: pulse-glow 3s ease-in-out infinite;
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 16px 40px rgba(124,58,237,0.5); }
            50%       { box-shadow: 0 20px 60px rgba(124,58,237,0.8), 0 0 0 8px rgba(124,58,237,0.1); }
        }

        /* Form elements */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(233, 213, 255, 0.6);
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px; height: 16px;
            color: rgba(168, 85, 247, 0.7);
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            background: rgba(255,255,255,0.06);
            border: 1.5px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 14px 14px 14px 44px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.92rem;
            color: #fff;
            outline: none;
            transition: border-color 0.3s, background 0.3s, box-shadow 0.3s;
        }

        .form-input::placeholder { color: rgba(255,255,255,0.3); }

        .form-input:focus {
            border-color: rgba(168, 85, 247, 0.7);
            background: rgba(255,255,255,0.09);
            box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.12);
        }

        /* Error banner */
        .error-banner {
            background: rgba(220,38,38,0.15);
            border: 1px solid rgba(220,38,38,0.3);
            border-radius: 12px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            animation: shake 0.4s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%       { transform: translateX(-6px); }
            40%       { transform: translateX(6px); }
            60%       { transform: translateX(-4px); }
            80%       { transform: translateX(4px); }
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: 16px;
            border-radius: 14px;
            border: none;
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            color: #fff;
            font-family: 'Outfit', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(124,58,237,0.4);
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -60%;
            width: 200%;
            height: 200%;
            background: linear-gradient(115deg, transparent 40%, rgba(255,255,255,0.15) 50%, transparent 60%);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .btn-submit:hover::before { transform: translateX(100%); }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(124,58,237,0.6); }
        .btn-submit:active { transform: translateY(0); }

        /* Back link */
        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 24px;
            font-size: 0.78rem;
            color: rgba(233,213,255,0.5);
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-link:hover { color: rgba(233,213,255,0.85); }

        /* Grid lines overlay */
        .grid-overlay {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.015) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: 1;
            pointer-events: none;
        }

        ::-webkit-scrollbar { display: none; }
    </style>
</head>
<body>
    <div class="bg-layer"></div>
    <div class="grid-overlay"></div>

    <!-- Animated orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- Floating particles -->
    <div id="particles"></div>

    <!-- Login Card -->
    <div class="login-card">

        <!-- Logo -->
        <div style="text-align:center;margin-bottom:32px;">
            <div class="logo-ring">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <h1 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:1.8rem;font-weight:700;color:#fff;line-height:1.2;margin-bottom:6px;">
                Admin Portal
            </h1>
            <p style="font-size:0.8rem;color:rgba(233,213,255,0.5);letter-spacing:0.06em;">
                RVIF Content Management System
            </p>
        </div>

        <!-- Error Banner -->
        <?php if (!empty($error)): ?>
        <div class="error-banner">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span style="font-size:0.83rem;color:#fca5a5;font-weight:500;"><?php echo htmlspecialchars($error); ?></span>
        </div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" action="<?php echo BASE_PATH; ?>/admin/login.php" id="login-form">

            <!-- Username -->
            <div class="form-group">
                <label class="form-label">Username</label>
                <div class="input-wrap">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <input required type="text" name="username" class="form-input" placeholder="Enter username" autocomplete="username" />
                </div>
            </div>

            <!-- Password -->
            <div class="form-group" style="margin-bottom:28px;">
                <label class="form-label">Password</label>
                <div class="input-wrap">
                    <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <input required type="password" name="password" id="pass-input" class="form-input" placeholder="••••••••" autocomplete="current-password" />
                    <!-- Toggle visibility -->
                    <button type="button" id="toggle-pass" onclick="togglePassword()"
                            style="position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:rgba(168,85,247,0.6);padding:0;display:flex;">
                        <svg id="eye-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-submit" id="submit-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                    <polyline points="10 17 15 12 10 7"></polyline>
                    <line x1="15" y1="12" x2="3" y2="12"></line>
                </svg>
                Sign in to Dashboard
            </button>
        </form>

        <!-- Hint & Back link -->
        <?php if (!$_is_live): ?>
        <div style="margin-top:20px;padding:12px 16px;background:rgba(245,158,11,0.07);border:1px solid rgba(245,158,11,0.15);border-radius:10px;text-align:center;">
            <span style="font-size:0.72rem;color:rgba(253,230,138,0.6);">Default credentials: </span>
            <span style="font-size:0.72rem;color:rgba(253,230,138,0.9);font-weight:700;">admin</span>
            <span style="font-size:0.72rem;color:rgba(253,230,138,0.6);"> / </span>
            <span style="font-size:0.72rem;color:rgba(253,230,138,0.9);font-weight:700;">1234</span>
        </div>
        <?php endif; ?>

        <a href="<?php echo BASE_PATH; ?>/index.php" class="back-link">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to public website
        </a>
    </div>

    <script>
        // Generate floating particles
        const container = document.getElementById('particles');
        for (let i = 0; i < 22; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            p.style.left = Math.random() * 100 + 'vw';
            p.style.width = p.style.height = (Math.random() * 3 + 1) + 'px';
            p.style.animationDuration = (Math.random() * 12 + 8) + 's';
            p.style.animationDelay = -(Math.random() * 20) + 's';
            p.style.opacity = Math.random() * 0.6 + 0.2;
            if (Math.random() > 0.7) {
                p.style.background = 'rgba(168, 85, 247, 0.7)';
            }
            container.appendChild(p);
        }

        // Password toggle
        function togglePassword() {
            const input = document.getElementById('pass-input');
            const icon  = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }

        // Loading state on submit
        document.getElementById('login-form').addEventListener('submit', function() {
            const btn = document.getElementById('submit-btn');
            btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation:spin 1s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"></path></svg> Signing in...';
            btn.style.opacity = '0.85';
        });
    </script>
    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</body>
</html>
