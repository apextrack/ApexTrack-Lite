<?php
include 'layout/header.php';
require_once 'config.php';

if (!isset($_SESSION['auth_token'])) {
    header('Location: login.php');
    exit();
}

$settingsFile = 'settings.json';
$uploadsDir = 'uploads/';
$allowedFaviconTypes = ['image/x-icon', 'image/vnd.microsoft.icon'];
$allowedLogoTypes = ['image/jpeg', 'image/png', 'image/svg+xml'];
$maxFileSize = 2 * 1024 * 1024; // 2 MB
$message = '';
$messageType = '';

$repoOwner = 'apextrack';
$repoName = 'ApexTrack-Lite';
$versionFileLocal = 'version.txt';
$versionFileGithub = "https://raw.githubusercontent.com/{$repoOwner}/{$repoName}/master/version.txt";

$currentVersion = '1.2.8'; 
$context = stream_context_create([
    'http' => ['header' => 'User-Agent: PHP-Script']
]);

if (file_exists($versionFileLocal)) {
    $currentVersion = trim(file_get_contents($versionFileLocal));
}

if (!is_dir($uploadsDir)) {
    if (!mkdir($uploadsDir, 0755, true)) {
        die('Gagal membuat direktori uploads.');
    }
}

$siteName = '';
$faviconUrl = '';
$logoUrl = '';
if (file_exists($settingsFile)) {
    $settingsData = file_get_contents($settingsFile);
    $settings = json_decode($settingsData, true);
    if ($settings) {
        $siteName = htmlspecialchars($settings['site_name'] ?? '');
        $faviconUrl = htmlspecialchars($settings['favicon_url'] ?? '');
        $logoUrl = htmlspecialchars($settings['logo_url'] ?? '');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newSiteName = trim($_POST['site_name'] ?? '');
    $uploadSuccess = true;
    $oldFaviconPath = $faviconUrl; // Simpan path lama sebelum diubah
    $oldLogoPath = $logoUrl;     // Simpan path lama sebelum diubah

    if (empty($newSiteName)) {
        $message = 'Nama website tidak boleh kosong.';
        $messageType = 'error';
        $uploadSuccess = false;
    } else {
        $newFaviconUrl = $faviconUrl;
        $newLogoUrl = $logoUrl;

        // Process Favicon Upload
        if (isset($_FILES['favicon_file']) && $_FILES['favicon_file']['error'] == 0) {
            $fileTmpPath = $_FILES['favicon_file']['tmp_name'];
            $fileSize = $_FILES['favicon_file']['size'];
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileType = finfo_file($fileInfo, $fileTmpPath);
            finfo_close($fileInfo);
            $fileExtension = strtolower(pathinfo($_FILES['favicon_file']['name'], PATHINFO_EXTENSION));

            if ($fileSize > $maxFileSize) {
                $message = 'Ukuran file favicon melebihi batas 2MB.';
                $messageType = 'error';
                $uploadSuccess = false;
            } elseif (in_array($fileType, $allowedFaviconTypes) && $fileExtension === 'ico') {
                $newFileName = uniqid('favicon_') . '.' . $fileExtension;
                $destPath = $uploadsDir . $newFileName;
                
                // Hapus favicon lama jika ada
                if (!empty($oldFaviconPath) && file_exists($oldFaviconPath)) {
                    unlink($oldFaviconPath);
                }

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $newFaviconUrl = $destPath;
                } else {
                    $message = 'Gagal memindahkan file favicon yang diunggah.';
                    $messageType = 'error';
                    $uploadSuccess = false;
                }
            } else {
                $message = 'Hanya file .ico yang diperbolehkan untuk favicon.';
                $messageType = 'error';
                $uploadSuccess = false;
            }
        }

        // Process Logo Upload
        if ($uploadSuccess && isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] == 0) {
            $fileTmpPath = $_FILES['logo_file']['tmp_name'];
            $fileSize = $_FILES['logo_file']['size'];
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileType = finfo_file($fileInfo, $fileTmpPath);
            finfo_close($fileInfo);
            $fileExtension = strtolower(pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION));

            if ($fileSize > $maxFileSize) {
                $message = 'Ukuran file logo melebihi batas 2MB.';
                $messageType = 'error';
                $uploadSuccess = false;
            } elseif (in_array($fileType, $allowedLogoTypes)) {
                $newFileName = uniqid('logo_') . '.' . $fileExtension;
                $destPath = $uploadsDir . $newFileName;

                // Hapus logo lama jika ada
                if (!empty($oldLogoPath) && file_exists($oldLogoPath)) {
                    unlink($oldLogoPath);
                }

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $newLogoUrl = $destPath;
                } else {
                    $message = 'Gagal memindahkan file logo yang diunggah.';
                    $messageType = 'error';
                    $uploadSuccess = false;
                }
            } else {
                $message = 'Hanya file PNG, JPG, dan SVG yang diperbolehkan untuk logo.';
                $messageType = 'error';
                $uploadSuccess = false;
            }
        }
        
        if ($uploadSuccess) {
            $settings = [
                'site_name' => $newSiteName,
                'favicon_url' => $newFaviconUrl,
                'versions' => $currentVersion,
                'logo_url' => $newLogoUrl
            ];
            $jsonData = json_encode($settings, JSON_PRETTY_PRINT);

            if ($jsonData === false) {
                $message = 'Gagal mengonversi data ke JSON.';
                $messageType = 'error';
            } elseif (file_put_contents($settingsFile, $jsonData) === false) {
                $message = 'Gagal menyimpan pengaturan.';
                $messageType = 'error';
            } else {
                $message = 'Pengaturan berhasil disimpan!';
                $messageType = 'success';
                $siteName = htmlspecialchars($newSiteName);
                $faviconUrl = htmlspecialchars($newFaviconUrl);
                $logoUrl = htmlspecialchars($newLogoUrl);
            }
        }
    }
}

$latestVersion = null;
$updateAvailable = false;
$updateMessage = '';

$latestVersionData = @file_get_contents($versionFileGithub, false, $context);
if ($latestVersionData !== false) {
    $latestVersion = trim($latestVersionData);
    if (version_compare($latestVersion, $currentVersion, '>')) {
        $updateAvailable = true;
        $updateMessage = "Versi baru ({$latestVersion}) tersedia.";
    }
}
?>

<main class="p-6 md:p-10 lg:p-12 w-full font-sans">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Pengaturan Website</h2>

    <?php if ($message): ?>
        <div class="text-center p-4 mb-4 rounded-lg
            <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="space-y-6 bg-white p-6 shadow-lg">
        <div>
            <label for="site_name" class="block text-sm font-medium text-gray-700">Nama Website</label>
            <input type="text" id="site_name" name="site_name" value="<?php echo $siteName; ?>" required
                class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label for="favicon_file" class="block text-sm font-medium text-gray-700">Unggah Favicon (.ico)</label>
            <input type="file" id="favicon_file" name="favicon_file" accept=".ico"
                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <p class="mt-2 text-xs text-gray-500">Maks. ukuran file 2 MB. Hanya format .ico yang didukung.</p>
            <div class="mt-4 flex items-center space-x-2">
                <p class="text-sm text-gray-500">Favicon saat ini:</p>
                <?php if (!empty($faviconUrl)): ?>
                    <img src="<?php echo $faviconUrl; ?>" alt="Favicon Website Saat Ini" class="w-8 h-8 rounded-full shadow">
                <?php else: ?>
                    <p class="text-sm text-gray-400">Belum ada favicon yang diunggah.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div>
            <label for="logo_file" class="block text-sm font-medium text-gray-700">Unggah Logo (PNG, JPG, SVG)</label>
            <input type="file" id="logo_file" name="logo_file" accept=".png, .jpg, .jpeg, .svg"
                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <p class="mt-2 text-xs text-gray-500">Maks. ukuran file 2 MB. Format yang didukung: PNG, JPG, SVG.</p>
            <div class="mt-4 flex items-center space-x-2">
                <p class="text-sm text-gray-500">Logo saat ini:</p>
                <?php if (!empty($logoUrl)): ?>
                    <img src="<?php echo $logoUrl; ?>" alt="Logo Website Saat Ini" class="max-h-12 shadow">
                <?php else: ?>
                    <p class="text-sm text-gray-400">Belum ada logo yang diunggah.</p>
                <?php endif; ?>
            </div>
        </div>

        <button type="submit"
                class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition duration-300 ease-in-out">
            Simpan Pengaturan
        </button>
    </form>

    <div class="text-center p-4 mb-4 rounded-lg bg-gray-100 text-gray-800">
        <p class="font-semibold">Versi Aplikasi:</p>
        <p>Versi saat ini: <?php echo $currentVersion; ?></p>
        <?php if ($updateAvailable): ?>
            <p class="text-green-600 mt-2 font-bold"><?php echo $updateMessage; ?></p>
            <button id="update-button" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300">
                <i class="fas fa-sync-alt mr-2"></i> Perbarui Sekarang
            </button>
        <?php else: ?>
            <p class="text-gray-500 mt-2">Anda menggunakan versi terbaru.</p>
        <?php endif; ?>
    </div>

    <div id="update-status" class="hidden text-center p-4 mb-4 rounded-lg bg-gray-200 text-gray-800">
        <p class="font-semibold mb-2">Status Pembaruan:</p>
        <div id="status-messages" class="text-left text-sm"></div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const updateButton = document.getElementById('update-button');
    const updateStatusContainer = document.getElementById('update-status');
    const statusMessages = document.getElementById('status-messages');

    if (updateButton) {
        updateButton.addEventListener('click', () => {
            updateButton.disabled = true;
            updateButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memulai...';
            updateStatusContainer.classList.remove('hidden');
            statusMessages.innerHTML = ''; 

            fetch('update.php')
                .then(response => response.text())
                .then(text => {
                    statusMessages.innerHTML = text;
                    if (text.includes("Pembaruan berhasil!")) {
                        setTimeout(() => window.location.reload(), 2000);
                    } else {
                        updateButton.disabled = false;
                        updateButton.innerHTML = '<i class="fas fa-sync-alt mr-2"></i> Perbarui Sekarang';
                    }
                })
                .catch(error => {
                    statusMessages.innerHTML = '<p class="text-red-600">Terjadi kesalahan. Pembaruan gagal.</p>';
                    console.error('Error:', error);
                    updateButton.disabled = false;
                    updateButton.innerHTML = '<i class="fas fa-sync-alt mr-2"></i> Perbarui Sekarang';
                });
        });
    }
});
</script>

<?php
include 'layout/footer.php';
?>