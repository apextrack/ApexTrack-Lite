<?php
include 'layout/header.php';

require_once 'config.php';

if (!isset($_SESSION['auth_token'])) {
    header('Location: login.php');
    exit();
}

$token = $_SESSION['auth_token'];

/**
 * Mengambil data dari endpoint API dengan otentikasi bearer token.
 *
 * @param string $endpoint URL endpoint API
 * @param string $token Token otentikasi
 * @return array Data yang diambil dari API
 * @throws Exception Jika terjadi kesalahan saat mengambil data
 */
function fetchData($endpoint, $token)
{
    if (!$token) {
        throw new Exception('Autentikasi token tidak ditemukan. Silakan login kembali.');
    }

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

    return $responseData['data'] ?? $responseData; 
}

$offers = [];
$error = null;

try {
    $offers = fetchData(BASE_API_URL . '/offers', $token);
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>


<main class="p-6 md:p-10 lg:p-12 w-full font-sans">
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Offers Management</h2>

    <div id="message-container" class="mb-4 hidden">
        <div id="message-box" class="px-4 py-3 rounded-lg border relative" role="alert">
            <strong id="message-title" class="font-bold"></strong>
            <span id="message-text" class="block sm:inline"></span>
        </div>
    </div>
    
    <div class="card p-6 shadow-xl bg-white">
        <div class="mb-6 flex justify-between items-center">
            <h3 class="text-xl font-semibold">Daftar Offers</h3>
            <a href="create-offers.php" class="bg-blue-600 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-700 transition-colors shadow-md">
                Add Offers
            </a>
        </div>
        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-4" role="alert">
                <strong class="font-bold">Error:</strong>
                <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <div class="table-container overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-300 overflow-hidden" id="offers-table">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-4 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">Nama</th>
                        <th class="py-3 px-4 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">URL</th>
                        <th class="py-3 px-4 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">Negara</th>
                        <th class="py-3 px-4 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">Perangkat</th>
                        <th class="py-3 px-4 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">Check Proxy</th>
                        <th class="py-3 px-4 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($offers) && is_array($offers)): ?>
                        <?php foreach ($offers as $offer): ?>
                            <tr data-id="<?php echo htmlspecialchars($offer['id'] ?? ''); ?>" class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4 whitespace-nowrap text-sm font-medium text-gray-900 offer-name"><?php echo htmlspecialchars($offer['name'] ?? 'N/A'); ?></td>
                                <td class="py-3 px-4 whitespace-nowrap text-sm text-blue-600 offer-url"><a href="<?php echo htmlspecialchars($offer['url'] ?? '#'); ?>" target="_blank" class="hover:underline">Link</a></td>
                                <td class="py-3 px-4 whitespace-nowrap text-sm offer-status">
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold leading-tight
                                        <?php 
                                            switch($offer['status'] ?? '') {
                                                case 'active': echo 'bg-green-100 text-green-800'; break;
                                                case 'paused': echo 'bg-red-100 text-red-800'; break;
                                                case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                                default: echo 'bg-gray-100 text-gray-800'; break;
                                            }
                                        ?>
                                    ">
                                        <?php echo htmlspecialchars(ucfirst($offer['status'] ?? 'N/A')); ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-sm text-gray-500 offer-country"><?php echo htmlspecialchars($offer['country'] ?? 'N/A'); ?></td>
                                <td class="py-3 px-4 whitespace-nowrap text-sm text-gray-500 offer-device"><?php echo htmlspecialchars($offer['device'] ?? 'N/A'); ?></td>
                                <td class="py-3 px-4 whitespace-nowrap text-sm text-gray-500 offer-proxy">
                                    <?php echo ($offer['can_show_to_proxy'] ?? false) ? 'Ya' : 'Tidak'; ?>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-right text-sm font-medium flex gap-2">
                                    <a href="edit-offers.php?id=<?php echo htmlspecialchars($offer['id'] ?? ''); ?>" class="text-indigo-600 hover:text-indigo-900 transition-colors" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button onclick='confirmDelete(<?php echo htmlspecialchars(json_encode($offer['id']), ENT_QUOTES, 'UTF-8'); ?>)' class="text-red-600 hover:text-red-900 transition-colors" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">
                                <?php echo $error ? 'Gagal memuat data offers: ' . htmlspecialchars($error) : 'Tidak ada offers yang ditemukan.'; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div id="delete-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden flex items-center justify-center">
    <div class="relative p-8 bg-white w-96 max-w-md m-auto flex-col flex rounded-lg shadow-lg text-center">
        <h3 class="text-lg font-bold mb-4">Konfirmasi Penghapusan</h3>
        <p class="text-gray-700 mb-6">Apakah Anda yakin ingin menghapus Offers ini?</p>
        <div class="flex justify-center gap-4">
            <button id="cancel-delete" class="bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-md hover:bg-gray-400 transition-colors">Batal</button>
            <button id="confirm-delete" class="bg-red-600 text-white font-bold py-2 px-4 rounded-md hover:bg-red-700 transition-colors">Hapus</button>
        </div>
    </div>
</div>

<script>
    const API_URL = '<?php echo BASE_API_URL; ?>';
    const TOKEN = '<?php echo $token; ?>';
    
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

    function confirmDelete(id) {
        document.getElementById('delete-modal').classList.remove('hidden');
        document.getElementById('confirm-delete').onclick = () => deleteOffer(id);
    }

    async function deleteOffer(id) {
        document.getElementById('delete-modal').classList.add('hidden');
        try {
            const response = await fetch(`${API_URL}/offers/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${TOKEN}`
                }
            });
            const data = await response.json();

            if (response.ok) {
                showMessage('success', 'Berhasil', data.message);
                
                const row = document.querySelector(`tr[data-id='${id}']`);
                if (row) {
                    row.remove();
                }
                
                if (document.querySelector('#offers-table tbody').children.length === 0) {
                    const noOffersRow = document.createElement('tr');
                    noOffersRow.innerHTML = `<td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada offers yang ditemukan.</td>`;
                    document.querySelector('#offers-table tbody').appendChild(noOffersRow);
                }

            } else {
                showMessage('error', 'Error', data.message || 'Gagal menghapus Offers.');
            }
        } catch (error) {
            showMessage('error', 'Error', 'Terjadi kesalahan jaringan.');
        }
    }

    document.getElementById('cancel-delete').addEventListener('click', () => {
        document.getElementById('delete-modal').classList.add('hidden');
    });

    document.getElementById('delete-modal').addEventListener('click', (e) => {
        if (e.target === e.currentTarget) {
            document.getElementById('delete-modal').classList.add('hidden');
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const successMessage = urlParams.get('success');
        if (successMessage) {
            showMessage('success', 'Berhasil', successMessage);
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
</script>

<?php
include 'layout/footer.php';
?>
