<?php
session_start();

require_once 'config.php';
$settingsFile = 'settings.json';
$siteName = '';
$faviconUrl = '';

if (file_exists($settingsFile)) {
    $settingsData = file_get_contents($settingsFile);
    $settings = json_decode($settingsData, true);
    if ($settings) {
        $siteName = htmlspecialchars($settings['site_name'] ?? 'Default Site Name');
        $faviconUrl = htmlspecialchars($settings['favicon_url'] ?? '');
        $logoUrl = htmlspecialchars($settings['logo_url'] ?? ''); 

    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['email']) || !isset($_POST['password'])) {
        $errorMessage = 'Email dan kata sandi harus diisi.';
    } else {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $baseApiUrl = BASE_API_URL;

        $loginData = json_encode([
            'email' => $email,
            'password' => $password,
        ]);

        $ch = curl_init("{$baseApiUrl}/auth/login");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $loginData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); 

        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $errorMessage = "Kesalahan koneksi API: " . curl_error($ch);
        } else {
            $responseData = json_decode($response, true);

            if ($httpStatus === 200 && isset($responseData['token'])) {
                $_SESSION['auth_token'] = $responseData['token'];
                $_SESSION['user_id'] = $responseData['user']['id'];
                $_SESSION['role'] = $responseData['user']['role'];
                
                header('Location: dashboard.php');
                exit();
            } else {
                $errorMessage = $responseData['message'] ?? 'Login gagal. Silakan periksa kredensial Anda.';
            }
        }
        curl_close($ch);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $siteName; ?></title>
    <link rel="icon" type="image/x-icon" href="<?php echo $faviconUrl; ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: sans-serif; }
        .loading-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }
        .loading-overlay.hidden {
            display: none;
        }
        .spinner {
            animation: spin 1s linear infinite;
            border-radius: 50%;
            width: 64px; 
            height: 64px; 
            border-color: #3b82f6; 
            border-style: solid;
            border-width: 4px;
            border-top-color: transparent; 
            border-left-color: transparent;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div id="loading-overlay" class="loading-overlay hidden">
        <div class="spinner"></div>
    </div>

    <div class="bg-white p-8 shadow-xl w-full max-w-md">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-6">
            <?php if (!empty($logoUrl)): ?>
                <img src="<?php echo $logoUrl; ?>" alt="<?php echo $siteName; ?>" class="mx-auto h-26"> <?php else: ?>
                <?php echo $siteName; ?>
            <?php endif; ?>
        </h2>
        <?php 
        if (isset($errorMessage)): ?>
            <p class="text-red-500 text-sm text-center mb-4"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php elseif (isset($_GET['error'])): ?>
            <p class="text-red-500 text-sm text-center mb-4"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['message'])): ?>
            <p class="text-green-500 text-sm text-center mb-4"><?php echo htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>
        <form id="login-form" action="" method="POST">
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Kata Sandi</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <button type="submit" id="login-button" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Login
            </button>
        </form>
    </div>

    <script>
        const loginForm = document.getElementById('login-form');
        const loginButton = document.getElementById('login-button');
        const loadingOverlay = document.getElementById('loading-overlay');

        loginForm.addEventListener('submit', () => {
            loadingOverlay.classList.remove('hidden');
            loginButton.disabled = true; 
        });
    </script>
</body>
</html>