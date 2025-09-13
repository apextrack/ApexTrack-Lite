<?php
include 'layout/header.php';
require_once 'config.php';

if (!isset($_SESSION['auth_token'])) {
    header('Location: login.php');
    exit();
}
?>

<main class="p-6 md:p-10 lg:p-12 w-full font-sans">
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Create New Offer</h2>
    
    <div id="message-container" class="mb-4 hidden">
        <div id="message-box" class="px-4 py-3 rounded-lg border relative" role="alert">
            <strong id="message-title" class="font-bold"></strong>
            <span id="message-text" class="block sm:inline"></span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <div class="card p-6 shadow-xl bg-white">
                <form id="offer-form">
                    <input type="hidden" id="offer-id" name="id">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Offers</label>
                            <input type="text" name="name" id="name" class="mt-1 block w-full px-4 py-2 border shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Contoh: Smartlink Dating" required>
                        </div>
                        <div>
                            <label for="url" class="block text-sm font-medium text-gray-700">URL</label>
                            <input type="url" name="url" id="url" class="mt-1 block w-full px-4 py-2 border shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="https://your-offer-domain.com/base?clickid={clickid}&subid={tracking}" required>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full px-4 py-2 border shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                <option value="active">Active</option>
                                <option value="paused">Paused</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700">Negara</label>
                            <input type="text" name="country" id="country" class="mt-1 block w-full px-4 py-2 border shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Contoh: US, ID, ALL" required>
                        </div>
                        <div>
                            <label for="device" class="block text-sm font-medium text-gray-700">Perangkat</label>
                            <input type="text" name="device" id="device" class="mt-1 block w-full px-4 py-2 border shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Contoh: Mobile, Desktop, ALL">
                        </div>
                        <div class="flex items-center">
                            <input id="can_show_to_proxy" name="can_show_to_proxy" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="can_show_to_proxy" class="ml-2 block text-sm text-gray-900">Check Proxy</label>
                        </div>
                    </div>
                    
                    <div class="mt-8 flex justify-end gap-4">
                        <a href="offers.php" class="bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-md hover:bg-gray-400 transition-colors shadow-md">Batal</a>
                        <button type="submit" id="submit-btn" class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 py-2 px-6 font-bold text-white shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Simpan Offers
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div>
            <div class="card p-6 shadow-xl bg-white mb-6">
                <h4 class="text-xl font-semibold mb-3">Example Offer URL</h4>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="p-3 border border-gray-300 text-left">Network</th>
                                <th class="p-3 border border-gray-300 text-left">Click ID Param</th>
                                <th class="p-3 border border-gray-300 text-left">Sub ID Param</th>
                                <th class="p-3 border border-gray-300 text-left">Example URL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-3 border border-gray-300 font-bold">Our Platform</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">{clickid}</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">{tracking}</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://your-offer-domain.com/base?clickid={clickid}&subid={tracking}</code></td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td class="p-3 border border-gray-300">NOVA</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">{sub2}</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">{sub3}</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://your-offer-domain.com/base?sub2={sub2}&sub3={tracking}</code></td>
                            </tr>

                            <tr class="bg-gray-50">
                                <td class="p-3 border border-gray-300">AdsEmpire</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">{clickid}</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">{subid2}</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://your-offer-domain.com/base?clickid={clickid}&subid2={tracking}</code></td>
                            </tr>
                            <tr>
                                <td class="p-3 border border-gray-300">OliMob</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">{sub2}</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">{sub1}</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://your-offer-domain.com/base?sub2={sub2}&sub1={tracking}</code></td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td class="p-3 border border-gray-300">iMonetizeIt</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">{click_id}</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">{token1}</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://your-offer-domain.com/base?click_id={click_id}&token1={tracking}</code></td>
                            </tr>
                            <tr>
                                <td class="p-3 border border-gray-300">LosPollos</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">{cid}</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">{tracker}</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://your-offer-domain.com/base?cid={cid}&tracker={tracking}</code></td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td class="p-3 border border-gray-300">ClickDealer</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">#s2#</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">#s1#</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://your-offer-domain.com/base?s2=#s2#&s1={tracking}</code></td>
                            </tr>
                            <tr>
                                <td class="p-3 border border-gray-300">Torazzo</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">{p1}</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">{p2}</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://your-offer-domain.com/base?p1={p1}&p2={tracking}</code></td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td class="p-3 border border-gray-300">Trafee</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">{ext_click_id}</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">{track}</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://your-offer-domain.com/base?ext_click_id={ext_click_id}&track={tracking}</code></td>
                            </tr>
                            <tr>
                                <td class="p-3 border border-gray-300">Adverten</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">{s2}</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs text-blue-600">{tracker}</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://your-offer-domain.com/base?s2={s2}&tracker={tracking}</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card p-6 shadow-xl bg-white">
                <h4 class="text-xl font-semibold mb-3">S2S Postback URL Examples</h4>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="p-3 border border-gray-300 text-left">Network</th>
                                <th class="p-3 border border-gray-300 text-left">Postback URL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-3 border border-gray-300 font-bold">General</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://www3.apextrack.site/api/postback/conversion?clickid=Di_Isi_Click_ID&payout=Di_Isi_Payout</code></td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td class="p-3 border border-gray-300">NOVA</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://www3.apextrack.site/api/postback/conversion?clickid={sub2}&payout={payout_amount}</code></td>
                            </tr>

                            <tr class="bg-gray-50">
                                <td class="p-3 border border-gray-300">AdsEmpire</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://www3.apextrack.site/api/postback/conversion?clickid={clickid}&payout={payout}</code></td>
                            </tr>
                            <tr>
                                <td class="p-3 border border-gray-300">OliMob</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://www3.apextrack.site/api/postback/conversion?clickid={sub2}&payout={revenue}</code></td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td class="p-3 border border-gray-300">iMonetizeIt</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://www3.apextrack.site/api/postback/conversion?clickid=<click_id>&payout=<payout></code></td>
                            </tr>
                            <tr>
                                <td class="p-3 border border-gray-300">LosPollos</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://www3.apextrack.site/api/postback/conversion?clickid={cid}&payout={sum}</code></td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td class="p-3 border border-gray-300">Adverten</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://www3.apextrack.site/api/postback/conversion?clickid={s2}&payout={amount}</code></td>
                            </tr>
                            <tr>
                                <td class="p-3 border border-gray-300">ClickDealer</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://www3.apextrack.site/api/postback/conversion?clickid=#s2#&payout=#price_usd#</code></td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td class="p-3 border border-gray-300">Trafee</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://www3.apextrack.site/api/postback/conversion?clickid={ext_click_id}&payout={sum}</code></td>
                            </tr>
                            <tr>
                                <td class="p-3 border border-gray-300">Torazzo</td>
                                <td class="p-3 border border-gray-300 font-mono text-xs"><code>https://www3.apextrack.site/api/postback/conversion?clickid={p1}&payout={payout}</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    const API_URL = '<?php echo BASE_API_URL; ?>';
    const TOKEN = '<?php echo $_SESSION['auth_token']; ?>';
    
    const offerForm = document.getElementById('offer-form');
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
    
    offerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(offerForm);
        const offerData = Object.fromEntries(formData.entries());
        offerData.can_show_to_proxy = offerForm.elements.can_show_to_proxy.checked ? 1 : 0;
        
        try {
            const response = await fetch(`${API_URL}/offers`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${TOKEN}`
                },
                body: JSON.stringify(offerData)
            });

            const data = await response.json();

            if (response.ok) {
                showMessage('success', 'Berhasil', data.message);
                offerForm.reset();
                setTimeout(() => {
                    window.location.href = 'offers.php';
                }, 1000);
            } else {
                let errorMessage = data.message || 'Gagal menyimpan Offers.';
                if (data.errors) {
                    errorMessage += ': ' + Object.values(data.errors).flat().join(' ');
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
