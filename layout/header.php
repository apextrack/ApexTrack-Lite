<?php
session_start();

$userRole = $_SESSION['role'] ?? 'guest';

if (!isset($_SESSION['auth_token'])) {
    header('Location: login.php?error=' . urlencode('Anda harus login terlebih dahulu.'));
    exit();
}
$settingsFile = 'settings.json';
$siteName = '';
$faviconUrl = '';

if (file_exists($settingsFile)) {
    $settingsData = file_get_contents($settingsFile);
    $settings = json_decode($settingsData, true);
    if ($settings) {
        $siteName = htmlspecialchars($settings['site_name'] ?? $siteName);
        $faviconUrl = htmlspecialchars($settings['favicon_url'] ?? $faviconUrl);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $siteName; ?></title>
    <link rel="icon" href="<?php echo $faviconUrl; ?>" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        html, body {
            height: 100%;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .dropdown-menu {
            transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
            transform-origin: top right;
            transform: scale(0.95);
            opacity: 0;
        }

        .dropdown-menu.show {
            transform: scale(1);
            opacity: 1;
        }
                .card {
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
        }
        .table-container {
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .table-cell {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            color: #4b5563;
            border: 1px solid #e5e7eb;
        }
        .table-header {
            background-color: #f9fafb;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
        }
        .status-badge {
            display: inline-flex;
            font-size: 0.75rem;
            line-height: 1rem;
            font-weight: 600;
            border-radius: 9999px;
            padding: 0.25rem 0.5rem;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal flex flex-col min-h-screen">

    <nav class="bg-white shadow-md p-4 md:px-10 flex justify-between items-center fixed w-full z-40 relative">
<a href="dashboard.php" class="text-decoration-none">
    <h1 class="text-3xl md:text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-purple-600">
        <?php echo $siteName; ?>
    </h1>
</a>
        
        <div class="hidden md:flex items-center space-x-12">
            <div id="navbar-menu-desktop" class="flex items-center space-x-6">
                <a href="dashboard.php" class="flex items-center text-gray-600 hover:text-gray-900 font-medium transition-colors duration-200">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 mr-1"></i>
                    <span>Dashboard</span>
                </a>
                <a href="generator.php" class="flex items-center text-gray-600 hover:text-gray-900 font-medium transition-colors duration-200">
                    <i data-lucide="link" class="w-5 h-5 mr-1"></i>
                    <span>Generator</span>
                </a>
                
                <?php if ($userRole !== 'user'): ?>
                <a href="offers.php" class="flex items-center text-gray-600 hover:text-gray-900 font-medium transition-colors duration-200">
                    <i data-lucide="trending-up" class="w-5 h-5 mr-1"></i>
                    <span>Offers</span>
                </a>
                <a href="reports.php" class="flex items-center text-gray-600 hover:text-gray-900 font-medium transition-colors duration-200">
                    <i data-lucide="bar-chart" class="w-5 h-5 mr-1"></i>
                    <span>Reports</span>
                </a>
                <a href="user.php" class="flex items-center text-gray-600 hover:text-gray-900 font-medium transition-colors duration-200">
                    <i data-lucide="user" class="w-5 h-5 mr-1"></i>
                    <span>Users</span>
                </a>
                 <a href="domain.php" class="flex items-center text-gray-600 hover:text-gray-900 font-medium transition-colors duration-200">
                    <i data-lucide="globe" class="w-5 h-5 mr-1"></i>
                    <span>Domain</span>
                </a>
                <a href="settings.php" class="flex items-center text-gray-600 hover:text-gray-900 font-medium transition-colors duration-200">
                    <i data-lucide="settings" class="w-5 h-5 mr-1"></i>
                    <span>Settings</span>
                </a>
                <?php endif; ?>
            </div>
            
<div class="relative">
    <button id="profile-dropdown-button" class="flex items-center space-x-2 text-gray-800 focus:outline-none">
        <img src="uploads/user.webp" alt="User Profile" class="w-10 h-10 rounded-full border-2 border-transparent hover:border-blue-500 transition-colors">
    </button>
    <div id="profile-dropdown-menu" class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg hidden">
        <a href="profil.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Profile</a>
        <a href="logout.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Logout</a>
    </div>
</div>

        </div>

        <button id="menu-button" class="md:hidden text-gray-800">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        
        <div id="mobile-menu" class="hidden absolute top-full left-0 w-full bg-white shadow-lg py-2 md:hidden">
            <a href="dashboard.php" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 transition-colors duration-200">
                <i data-lucide="layout-dashboard" class="w-5 h-5 mr-2"></i>
                <span>Dashboard</span>
            </a>
            <a href="generator.php" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 transition-colors duration-200">
                <i data-lucide="link" class="w-5 h-5 mr-2"></i>
                <span>Generator</span>
            </a>
            <?php if ($userRole !== 'user'): ?>
            <a href="offers.php" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 transition-colors duration-200">
                <i data-lucide="trending-up" class="w-5 h-5 mr-2"></i>
                <span>Offers</span>
            </a>
            <a href="reports.php" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 transition-colors duration-200">
                <i data-lucide="bar-chart" class="w-5 h-5 mr-2"></i>
                <span>Reports</span>
            </a>
            <a href="user.php" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 transition-colors duration-200">
                <i data-lucide="user" class="w-5 h-5 mr-2"></i>
                <span>Users</span>
            </a>
             <a href="domain.php" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 transition-colors duration-200">
                <i data-lucide="globe" class="w-5 h-5 mr-2"></i>
                <span>Domain</span>
            </a>
            <a href="settings.php" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 transition-colors duration-200">
                <i data-lucide="settings" class="w-5 h-5 mr-2"></i>
                <span>Settings</span>
            </a>
            <?php endif; ?>
            <hr class="my-2 border-gray-200">
            <a href="profil.php" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 transition-colors duration-200">
                <i data-lucide="user" class="w-5 h-5 mr-2"></i>
                <span>Profile</span>
            </a>
            <a href="logout.php" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 transition-colors duration-200">
                <i data-lucide="log-out" class="w-5 h-5 mr-2"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>



    <script>
        lucide.createIcons();

        const menuButton = document.getElementById('menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        menuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        const profileButton = document.getElementById('profile-dropdown-button');
        const profileMenu = document.getElementById('profile-dropdown-menu');

        profileButton.addEventListener('click', (event) => {
            event.stopPropagation(); 
            profileMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', (event) => {
            const isClickInside = profileButton.contains(event.target) || profileMenu.contains(event.target);
            if (!isClickInside && !profileMenu.classList.contains('hidden')) {
                profileMenu.classList.add('hidden');
            }
        });
    </script>
            <main class="flex-grow pt-0 md:pt-0 p-0 md:p-6 lg:p-0">

