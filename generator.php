<?php
include 'layout/header.php';
require_once 'config.php';

if (!isset($_SESSION['auth_token'])) {
    header('Location: login.php');
    exit();
}

$authToken = $_SESSION['auth_token'];

// Token App untuk scraping, ditaruh di sini agar tidak terlihat oleh pengguna
$appAccessToken = '1308899767242947|HVu-8GkDtyPmpAR2SQOAx2BT2bg';

?>

<main class="p-6 md:p-10 lg:p-12 w-full font-sans">
    <h2 class="text-3xl font-bold text-gray-900 mb-6">Smartlink Generator</h2>
    <div class="mx-auto bg-white p-8 shadow-lg">
        <div id="status-message" class="hidden mb-4 p-4 text-center rounded-lg"></div>

        <div id="loading-overlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-blue-500 border-solid"></div>
        </div>

        <form id="generator-form" enctype="multipart/form-data" class="space-y-6">
            <div class="border border-gray-300 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="offer" class="block text-sm font-medium text-gray-700">Offers (Opsional)</label>
                        <select id="offer" name="offer" class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></select>
                    </div>
                    <div>
                        <label for="shared_domain" class="block text-sm font-medium text-gray-700">Domain</label>
                        <select id="shared_domain" name="shared_domain" required class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></select>
                    </div>
                    <div>
                        <label for="redirect_type" class="block text-sm font-medium text-gray-700">Redirect</label>
                        <select id="redirect_type" name="redirect_type" required class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></select>
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Smartlink Type</label>
                        <select id="type" name="type" required class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></select>
                    </div>
                    <div>
                        <label for="generation_mode" class="block text-sm font-medium text-gray-700">Mode</label>
                        <select id="generation_mode" name="generation_mode" required class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></select>
                    </div>
                    <div id="shortener-choice-container" class="hidden">
                        <label for="shortener_choice" class="block text-sm font-medium text-gray-700">Shortner</label>
                        <select id="shortener_choice" name="shortener_choice" class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></select>
                    </div>
                    
                    <div id="lp-container">
                        <label for="lp" class="block text-sm font-medium text-gray-700">Landing (Opsional)</label>
                        <select id="lp" name="lp" class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Random</option>
                            <option value="1">FlirtGPT</option>
                            <option value="2">NearYou</option>
                            <option value="3">SnapChat</option>
                            <option value="4">BioLink</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="border border-gray-300 p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Meta Tags (Opsional)</h3>
                <div class="space-y-4">
                    <div>
                        <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta Title</label>
                        <input type="text" id="meta_title" name="meta_title" class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                        <textarea id="meta_description" name="meta_description" rows="3" class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                    </div>
                    <div>
                        <label for="og_image_file" class="block text-sm font-medium text-gray-700">Unggah Gambar (Open Graph)</label>
                        <input type="file" id="og_image_file" name="og_image_file" accept="image/*" class="mt-1 block w-full text-sm text-gray-500">
                    </div>
                    <div>
                        <label for="favicon_file" class="block text-sm font-medium text-gray-700">Unggah Favicon</label>
                        <input type="file" id="favicon_file" name="favicon_file" accept=".ico, .png, .jpg, .jpeg, .gif, .svg" class="mt-1 block w-full text-sm text-gray-500">
                    </div>
                </div>
            </div>
            <button type="submit" id="generate-button" class="w-full py-3 px-4 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-300 ease-in-out flex items-center justify-center">
                <span id="generate-button-text">Generate Smartlink</span>
                <svg id="spinner" class="hidden animate-spin h-5 w-5 text-white ml-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 8l2.938-3.709z"></path>
                </svg>
            </button>
        </form>

        <div id="result-section" class="hidden mt-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Hasil</h3>
            <div class="space-y-4">
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm font-medium text-gray-700">URL Final:</p>
                    <a id="final-url-link" href="#" target="_blank" class="text-blue-600 font-semibold break-all hover:underline"></a>
                </div>
                <div id="domain-url-container" class="hidden bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm font-medium text-gray-700">URL Domain:</p>
                    <a id="domain-url-link" href="#" target="_blank" class="text-blue-600 font-semibold break-all hover:underline"></a>
                </div>
                <div id="first-shortened-url-container" class="hidden bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm font-medium text-gray-700">Url Smartlink:</p>
                    <a id="first-shortened-url-link" href="#" target="_blank" class="text-blue-600 font-semibold break-all hover:underline"></a>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    const API_URL = '<?php echo BASE_API_URL; ?>';
    const AUTH_TOKEN = '<?php echo $authToken; ?>';
    const FACEBOOK_ACCESS_TOKEN = '<?php echo $appAccessToken; ?>';

    const form = document.getElementById('generator-form');
    const statusMessage = document.getElementById('status-message');
    const loadingOverlay = document.getElementById('loading-overlay'); 
    const resultSection = document.getElementById('result-section');
    const finalUrlLink = document.getElementById('final-url-link');
    const domainUrlContainer = document.getElementById('domain-url-container');
    const domainUrlLink = document.getElementById('domain-url-link');
    const firstShortenedUrlContainer = document.getElementById('first-shortened-url-container');
    const firstShortenedUrlLink = document.getElementById('first-shortened-url-link');
    const shortenerChoiceContainer = document.getElementById('shortener-choice-container');
    const generateButton = document.getElementById('generate-button');
    const generateButtonText = document.getElementById('generate-button-text');
    const spinner = document.getElementById('spinner');  
    const lpContainer = document.getElementById('lp-container');
    const lpSelect = document.getElementById('lp');
    const typeSelect = document.getElementById('type');

    function showStatus(message, type) {
        statusMessage.innerHTML = message;
        statusMessage.className = 'mb-4 p-4 text-center rounded-lg';
        if (type === 'success') {
            statusMessage.classList.add('bg-green-100', 'text-green-700');
        } else if (type === 'error') {
            statusMessage.classList.add('bg-red-100', 'text-red-700');
        } else if (type === 'info') {
            statusMessage.classList.add('bg-blue-100', 'text-blue-700');
        }
        statusMessage.classList.remove('hidden');
    }

    function showButtonLoadingState(isLoading) {
        if (isLoading) {
            generateButtonText.classList.add('hidden');
            spinner.classList.remove('hidden');
            generateButton.disabled = true;
        } else {
            generateButtonText.classList.remove('hidden');
            spinner.classList.add('hidden');
            generateButton.disabled = false;
        }
    }

    async function fetchFormData() {
        loadingOverlay.classList.remove('hidden');
        try {
            const response = await fetch(`${API_URL}/generator-data`, {
                headers: { 'Authorization': `Bearer ${AUTH_TOKEN}` }
            });
            const data = await response.json();

            if (!response.ok) {
                let errorMessage = data.message || 'Gagal mengambil data.';
                if (response.status === 403) {
                    errorMessage = 'Unauthorized action. Mohon login ulang.';
                }
                throw new Error(errorMessage);
            }

            populateSelect('offer', data.offers, 'Offers', 'id', 'name');
            populateSelect('shared_domain', data.domains, 'Domain', null, null);
            populateSelect('redirect_type', data.redirect_types, 'Redirect', null, null);
            populateSelectWithOptions('type', data.types, 'Smartlink', { 'render': 'Landing Pages', 'redirect': 'Redirect Offer' });
            populateSelectWithOptions('generation_mode', data.generation_modes, 'Mode', { 'smartlink_external_self': 'Double Shortener', 'smartlink_self': 'Single Shortener' });

            const generationModeSelect = document.getElementById('generation_mode');
            generationModeSelect.addEventListener('change', (e) => {
                shortenerChoiceContainer.style.display = e.target.value === 'smartlink_external_self' ? 'block' : 'none';
            });
            generationModeSelect.dispatchEvent(new Event('change'));

            populateSelect('shortener_choice', data.shortener_choices, 'Shortner', null, null);
        } catch (error) {
            console.error('Kesalahan saat mengambil data :', error);
            showStatus(`Gagal mengambil data: ${error.message}`, 'error');
        } finally {
            loadingOverlay.classList.add('hidden'); 
        }
    }

    function populateSelect(selectId, data, placeholder, valueKey, textKey) {
        const select = document.getElementById(selectId);
        select.innerHTML = '';
        const defaultOption = document.createElement('option');
        defaultOption.textContent = placeholder;
        defaultOption.value = '';
        select.appendChild(defaultOption);
        if (data) {
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = valueKey && textKey ? item[valueKey] : item;
                option.textContent = valueKey && textKey ? item[textKey] : item;
                select.appendChild(option);
            });
        }
    }

    function populateSelectWithOptions(selectId, data, placeholder, mapping) {
        const select = document.getElementById(selectId);
        select.innerHTML = '';
        const defaultOption = document.createElement('option');
        defaultOption.textContent = placeholder;
        defaultOption.value = '';
        select.appendChild(defaultOption);
        if (data) {
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item;
                option.textContent = mapping[item] || item;
                select.appendChild(option);
            });
        }
    }

    function toggleLpContainer() {
        if (typeSelect.value === 'render') {
            lpContainer.classList.remove('hidden');
        } else {
            lpContainer.classList.add('hidden');
            lpSelect.value = '';
        }
    }

    async function performScraping(url) {
        try {
            const scrapingData = new FormData();
            scrapingData.append('url', url);
            scrapingData.append('access_token', FACEBOOK_ACCESS_TOKEN);

            const scrapeResponse = await fetch('scrape_proxy.php', {
                method: 'POST',
                body: scrapingData
            });

            const scrapeResult = await scrapeResponse.json();

            if (scrapeResult.error) {
                console.error('Scraping Gagal:', scrapeResult.error.message);
                showStatus('Scraping Gagal: ' + scrapeResult.error.message, 'error');
            } else {
                showStatus('Proses berhasil! Semua URL telah dibuat dan meta tags telah diperbarui.', 'success');
                setTimeout(() => { statusMessage.classList.add('hidden'); }, 5000);
            }
        } catch (scrapeError) {
            console.error('Kesalahan saat memicu scraping:', scrapeError);
            showStatus('Gagal memicu scraping: ' + scrapeError.message, 'error');
        }
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        showButtonLoadingState(true);
        resultSection.classList.add('hidden');
        statusMessage.classList.add('hidden');

        const formData = new FormData(form);
        const generationMode = formData.get('generation_mode');
        if (generationMode !== 'smartlink_external_self') {
            formData.delete('shortener_choice');
        }
        
        const typeValue = formData.get('type');
        if (typeValue !== 'render') {
            formData.delete('lp');
        } else if (lpSelect.value === '') { 
            formData.delete('lp');
        }

        try {
            const response = await fetch(`${API_URL}/generate-smartlink`, {
                method: 'POST',
                body: formData,
                headers: {
                    'Authorization': `Bearer ${AUTH_TOKEN}`
                }
            });

            const data = await response.json();
            if (!response.ok) {
                let errorMessage = data.message || 'Terjadi kesalahan tidak terduga.';
                if (data.errors) {
                    errorMessage += '<br>' + Object.values(data.errors).flat().join('<br>');
                }
                throw new Error(errorMessage);
            }

            resultSection.classList.remove('hidden');

            const finalUrl = data.final_shared_url;
            finalUrlLink.href = finalUrl;
            finalUrlLink.textContent = finalUrl;

            const url = new URL(finalUrl);
            const domain = url.hostname;
            const subdomainParts = domain.split('.');
            let finalDomainUrl = '';
            let finalUrlCode = '';

            if (subdomainParts.length > 2) {
                finalUrlCode = subdomainParts[0];
                finalDomainUrl = `https://${subdomainParts.slice(1).join('.')}`;
            } else {
                finalDomainUrl = finalUrl;
            }

            if (finalDomainUrl && finalUrlCode) {
                domainUrlContainer.classList.remove('hidden');
                domainUrlLink.href = `${finalDomainUrl}/${finalUrlCode}`;
                domainUrlLink.textContent = `${finalDomainUrl}/${finalUrlCode}`;
            } else {
                domainUrlContainer.classList.add('hidden');
            }

            if (data.smartlink_url_after_first_shortening) {
                firstShortenedUrlContainer.classList.remove('hidden');
                firstShortenedUrlLink.href = data.smartlink_url_after_first_shortening;
                firstShortenedUrlLink.textContent = data.smartlink_url_after_first_shortening;
            } else {
                firstShortenedUrlContainer.classList.add('hidden');
            }

            await performScraping(data.final_shared_url);

        } catch (error) {
            console.error('Kesalahan saat membuat URL:', error);
            showStatus(`Gagal membuat URL: ${error.message}`, 'error');
            resultSection.classList.add('hidden');
        } finally {
            showButtonLoadingState(false);
        }
    });

    typeSelect.addEventListener('change', toggleLpContainer);

    toggleLpContainer();

    fetchFormData();
</script>

<?php
include 'layout/footer.php';
?>