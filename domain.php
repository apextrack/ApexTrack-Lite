<?php

include 'layout/header.php';
require_once 'config.php';

if (!isset($_SESSION['auth_token'])) {
    header('Location: login.php');
    exit();
}

$token = $_SESSION['auth_token'];
$domains = [];
$editingDomain = null;
$error = null;
$message = null;


/**
 * Mengambil data dari endpoint API dengan otentikasi bearer token.
 *
 * @param string $endpoint URL endpoint API
 * @param string $method Metode HTTP (GET, POST, PUT, DELETE)
 * @param array $data Data yang dikirim dalam request body (untuk POST/PUT)
 * @return array Respons dari API
 * @throws Exception Jika terjadi kesalahan saat mengambil data
 */
function callApi($endpoint, $method = 'GET', $data = [])
{
    global $token;

    if (!$token) {
        throw new Exception('Autentikasi token tidak ditemukan. Silakan login kembali.');
    }

    $ch = curl_init(BASE_API_URL . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        "Authorization: Bearer {$token}"
    ]);

    switch ($method) {
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case 'PUT':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case 'DELETE':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            break;
    }

    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        throw new Exception("Kesalahan saat mengambil data: " . $curlError);
    }
    
    if ($httpStatus === 401) {
        session_destroy();
        header('Location: login.php?error=' . urlencode('Token Anda tidak valid atau kedaluwarsa. Silakan login kembali.'));
        exit();
    }
    
    $responseData = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Respons API tidak valid: " . json_last_error_msg());
    }
    
    if ($httpStatus >= 400) {
        $errorMessage = $responseData['message'] ?? "Gagal memuat data. Status: {$httpStatus}.";
        throw new Exception($errorMessage);
    }

    return $responseData['data'] ?? $responseData;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    try {
        $result = callApi('/domains', 'POST', [
            'domain_name' => $_POST['domain_name'],
            'is_active' => isset($_POST['is_active']),
            'notes' => $_POST['notes']
        ]);
        $message = ['type' => 'success', 'text' => $result['message'] ?? 'Domain berhasil ditambahkan!'];
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    try {
        $domainId = $_POST['domain_id'];
        $result = callApi('/domains/' . $domainId, 'PUT', [
            'domain_name' => $_POST['domain_name'],
            'is_active' => isset($_POST['is_active']),
            'notes' => $_POST['notes']
        ]);
        $message = ['type' => 'success', 'text' => $result['message'] ?? 'Domain berhasil diperbarui!'];
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        $domainId = $_POST['domain_id'];
        $result = callApi('/domains/' . $domainId, 'DELETE');
        $message = ['type' => 'success', 'text' => $result['message'] ?? 'Domain berhasil dihapus!'];
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

if (isset($_GET['edit'])) {
    try {
        $editingDomain = callApi('/domains/' . $_GET['edit']);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

try {
    $domains = callApi('/domains');
} catch (Exception $e) {
    $error = $e->getMessage();
}

?>

<main class="p-6 md:p-10 lg:p-12 w-full font-sans">
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Manajemen Domain</h2>

    <?php if ($message): ?>
        <div class="p-4 mb-4 text-sm rounded-lg <?= $message['type'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>" role="alert">
            <?= htmlspecialchars($message['text']) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-4" role="alert">
            <strong class="font-bold">Error:</strong>
            <span class="block sm:inline"><?= htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>
    
    <div class="md:flex md:space-x-8">

        <div class="md:w-1/2">
            <div class="card p-6 shadow-xl bg-white mb-8">
                <h3 class="text-xl font-semibold mb-4 text-gray-700">
                    <?= $editingDomain ? 'Edit Domain' : 'Tambah Domain Baru' ?>
                </h3>
                <form action="domain.php" method="POST">
                    <input type="hidden" name="action" value="<?= $editingDomain ? 'update' : 'create' ?>">
                    <?php if ($editingDomain): ?>
                        <input type="hidden" name="domain_id" value="<?= htmlspecialchars(isset($editingDomain['id']) ? $editingDomain['id'] : '') ?>">
                    <?php endif; ?>

                    <div class="mb-4">
                        <label for="domain_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Domain</label>
                        <input type="text" id="domain_name" name="domain_name" value="<?= $editingDomain ? htmlspecialchars(isset($editingDomain['domain_name']) ? $editingDomain['domain_name'] : '') : '' ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2 border">
                    </div>
                    
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2 border"><?= $editingDomain ? htmlspecialchars(isset($editingDomain['notes']) ? $editingDomain['notes'] : '') : '' ?></textarea>
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-md transition ease-in-out duration-150">
                            <?= $editingDomain ? 'Perbarui Domain' : 'Tambah Domain' ?>
                        </button>
                        <?php if ($editingDomain): ?>
                            <a href="domain.php" class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 transition ease-in-out duration-150 text-center">
                                Batal
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <div class="card p-6 shadow-xl bg-white mb-8">
                <h3 class="text-xl font-semibold mb-4 text-gray-700">Daftar Domain</h3>
                <div class="table-container overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-300 overflow-hidden" id="domains-table">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="py-3 px-4 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">Nama Domain</th>
                                <th class="py-3 px-4 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="py-3 px-4 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">Catatan</th>
                                <th class="py-3 px-4 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (!empty($domains) && is_array($domains)): ?>
                                <?php foreach ($domains as $domain): ?>
                                    <tr data-id="<?= htmlspecialchars(isset($domain['id']) ? $domain['id'] : '') ?>" class="hover:bg-gray-50 transition-colors">
                                        <td class="py-3 px-4 whitespace-nowrap text-sm font-medium text-gray-900 domain-name"><?= htmlspecialchars(isset($domain['domain_name']) ? $domain['domain_name'] : 'N/A') ?></td>
                                        <td class="py-3 px-4 whitespace-nowrap text-sm domain-status">
                                            <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold leading-tight
                                                <?php echo (isset($domain['is_active']) && $domain['is_active']) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                                <?php echo (isset($domain['is_active']) && $domain['is_active']) ? 'Aktif' : 'Tidak Aktif'; ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 whitespace-nowrap text-sm text-gray-500 domain-notes"><?= htmlspecialchars(isset($domain['notes']) ? $domain['notes'] : 'N/A') ?></td>
                                        <td class="py-3 px-4 whitespace-nowrap text-right text-sm font-medium flex gap-2 justify-end">
                                            <a href="?edit=<?= htmlspecialchars(isset($domain['id']) ? $domain['id'] : '') ?>" class="text-blue-600 hover:text-blue-900 transition-colors" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>

                                            </a>
                                            <form action="domain.php" method="POST" class="inline-block" title="Hapus">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="domain_id" value="<?= htmlspecialchars(isset($domain['id']) ? $domain['id'] : '') ?>">
                                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus domain ini?')" class="text-red-600 hover:text-red-900">
                                        <i class="fa-solid fa-trash-can"></i>

                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">
                                        <?php echo $error ? 'Gagal memuat data domain: ' . htmlspecialchars($error) : 'Tidak ada domain yang ditemukan.'; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="md:w-1/2">
            <div class="card p-6 shadow-xl bg-white mb-8">
                <h3 class="text-xl font-semibold mb-4 text-gray-700">Petunjuk Pengaturan DNS (Khusus Cloudflare)</h3>
                <p class="text-gray-600 mb-4">Untuk mengarahkan domain Anda ke livedate.sbs, ikuti langkah-langkah berikut di dashboard Cloudflare Anda:</p>
                <ol class="list-decimal list-inside space-y-4 text-gray-700">
                    <li>
                        Tambahkan dua CNAME record baru di tab DNS:
                        <ul class="list-disc list-inside ml-6 mt-2 text-gray-600">
                            <li>Satu untuk nama `@` (akar domain) dengan nilai target `livedate.sbs`.</li>
                            <li>Satu lagi untuk nama `*` (wildcard) dengan nilai target `livedate.sbs`.</li>
                        </ul>
                    </li>
                    <li>Pastikan status proxy untuk kedua record tersebut diatur ke DNS only (ikon awan akan berwarna abu-abu).</li>
                    <li>
                        Buka tab SSL/TLS lalu pilih opsi Flexible pada bagian SSL/TLS encryption mode
                    </li>
                </ol>
                <div class="mt-6 flex justify-center">
                    <img src="https://apextrack.site/public/images/cname.png" alt="Contoh Pengaturan DNS Cloudflare" class="rounded-lg shadow-md max-w-full h-auto">
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include 'layout/footer.php';
?>
