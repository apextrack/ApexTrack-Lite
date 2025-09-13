<?php
include 'layout/header.php';

require_once 'config.php';

if (!isset($_SESSION['auth_token'])) {
    header('Location: login.php');
    exit();
}

$token = $_SESSION['auth_token'];
$offer = null;
$error = null;

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $error = 'ID Offers tidak ditemukan. Silakan kembali ke halaman offers.';
} else {
    $offerId = htmlspecialchars($_GET['id']);

    /**
     * Mengambil data penawaran spesifik dari API
     *
     * @param string $endpoint URL endpoint API
     * @param string $token Token otentikasi
     * @return array Data yang diambil dari API
     * @throws Exception Jika terjadi kesalahan saat mengambil data
     */
    function fetchDataById($endpoint, $token)
    {
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            "Authorization: Bearer {$token}"
        ]);

        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new Exception("Kesalahan saat mengambil data dari {$endpoint}: " . $curlError);
        }
        
        if ($httpStatus === 401) {
            session_destroy();
            header('Location: login.php?error=' . urlencode('Token Anda tidak valid atau kedaluwarsa. Silakan login kembali.'));
            exit();
        }
        
        $responseData = json_decode($response, true);

        if ($httpStatus !== 200) {
            $errorMessage = $responseData['message'] ?? "Gagal memuat data. Status: {$httpStatus}.";
            throw new Exception("Gagal memuat data dari {$endpoint}. Respons: {$errorMessage}");
        }
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Respons API tidak valid: " . json_last_error_msg());
        }

        return $responseData;
    }

    try {
        $offer = fetchDataById(BASE_API_URL . '/offers/' . $offerId, $token);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<main class="p-6 md:p-10 lg:p-12 w-full font-sans">
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Edit Offers</h2>

    <div id="message-container" class="mb-4 hidden">
        <div id="message-box" class="px-4 py-3 rounded-lg border relative" role="alert">
            <strong id="message-title" class="font-bold"></strong>
            <span id="message-text" class="block sm:inline"></span>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-4" role="alert">
            <strong class="font-bold">Error:</strong>
            <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
        </div>
        <a href="offers.php" class="bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-md hover:bg-gray-400 transition-colors shadow-md">Kembali ke Offers</a>
    <?php else: ?>
        <div class="card p-6 shadow-xl bg-white">
            <form id="edit-offer-form">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($offer['id'] ?? ''); ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="name" class="block text-gray-700 text-sm font-semibold mb-2">Nama Offers</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($offer['name'] ?? ''); ?>" required class="form-input w-full px-4 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="form-group">
                        <label for="url" class="block text-gray-700 text-sm font-semibold mb-2">URL</label>
                        <input type="url" id="url" name="url" value="<?php echo htmlspecialchars($offer['url'] ?? ''); ?>" required class="form-input w-full px-4 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="form-group">
                        <label for="status" class="block text-gray-700 text-sm font-semibold mb-2">Status</label>
                        <select id="status" name="status" required class="form-select w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="active" <?php echo ($offer['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="paused" <?php echo ($offer['status'] ?? '') === 'paused' ? 'selected' : ''; ?>>Paused</option>
                            <option value="pending" <?php echo ($offer['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="country" class="block text-gray-700 text-sm font-semibold mb-2">Negara</label>
                        <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($offer['country'] ?? ''); ?>" class="form-input w-full px-4 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="form-group">
                        <label for="device" class="block text-gray-700 text-sm font-semibold mb-2">Perangkat</label>
                        <input type="text" id="device" name="device" value="<?php echo htmlspecialchars($offer['device'] ?? ''); ?>" class="form-input w-full px-4 py-2 border focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="form-group flex items-center gap-2 mt-4">
                        <input type="checkbox" id="can_show_to_proxy" name="can_show_to_proxy" class="form-checkbox text-blue-600 rounded" <?php echo ($offer['can_show_to_proxy'] ?? false) ? 'checked' : ''; ?>>
                        <label for="can_show_to_proxy" class="text-gray-700 text-sm font-semibold">Tampilkan ke Proxy</label>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-4">
                    <a href="offers.php" class="bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-md hover:bg-gray-400 transition-colors">Batal</a>
                    <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-md hover:bg-blue-700 transition-colors shadow-md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</main>

<script>
    const API_URL = '<?php echo BASE_API_URL; ?>';
    const TOKEN = '<?php echo $token; ?>';
    const OFFER_ID = '<?php echo htmlspecialchars($offerId ?? ''); ?>';
    
    const form = document.getElementById('edit-offer-form');
    const messageContainer = document.getElementById('message-container');
    const messageBox = document.getElementById('message-box');
    const messageTitle = document.getElementById('message-title');
    const messageText = document.getElementById('message-text');

    function showMessage(type, title, message) {
        messageContainer.classList.remove('hidden');
        messageBox.className = 'px-4 py-3 rounded-lg border relative';
        if (type === 'success') {
            messageBox.classList.add('bg-green-100', 'border-green-400', 'text-green-700');
        } else {
            messageBox.classList.add('bg-red-100', 'border-red-400', 'text-red-700');
        }
        messageTitle.innerText = title;
        messageText.innerText = message;
        setTimeout(() => {
            messageContainer.classList.add('hidden');
        }, 5000);
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        data['can_show_to_proxy'] = formData.has('can_show_to_proxy');

        try {
            const response = await fetch(`${API_URL}/offers/${OFFER_ID}`, {
                method: 'PUT', 
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${TOKEN}`
                },
                body: JSON.stringify(data)
            });

            const responseData = await response.json();

            if (response.ok) {
                showMessage('success', 'Berhasil', responseData.message);
                setTimeout(() => {
                    window.location.href = 'offers.php?success=' + encodeURIComponent('Offers berhasil diperbarui.');
                }, 1500);
            } else {
                let errorMessage = responseData.message || 'Gagal memperbarui offers.';
                if (responseData.errors) {
                    errorMessage += ': ' + Object.values(responseData.errors).flat().join(', ');
                }
                showMessage('error', 'Error', errorMessage);
            }
        } catch (error) {
            showMessage('error', 'Error', 'Terjadi kesalahan jaringan.');
        }
    });
</script>

<?php
include 'layout/footer.php';
?>
