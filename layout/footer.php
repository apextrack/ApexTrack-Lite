</main>
<footer class="bg-white text-gray-600 p-4 shadow-md">
    <div class="flex flex-row justify-between items-center mx-auto"> <p class="text-sm" id="dynamic-footer-text">
            Copyright &copy; 2024 ApexTrack. All rights reserved.
        </p>
        <p class="text-xs text-gray-400" id="app-version">Loading...</p>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const currentYear = new Date().getFullYear();
        const footerTextElement = document.getElementById('dynamic-footer-text');
        const versionElement = document.getElementById('app-version');

        fetch('settings.json')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal memuat file settings.json');
                }
                return response.json();
            })
            .then(settings => {
                if (footerTextElement && settings.site_name) {
                    footerTextElement.textContent = `Copyright © ${currentYear} ${settings.site_name}. All rights reserved.`;
                }
                
                if (versionElement && settings.versions) {
                    versionElement.textContent = `Version ${settings.versions}`;
                }
            })
            .catch(error => {
                console.error('Error fetching settings:', error);
                if (footerTextElement) {
                    footerTextElement.textContent = `Copyright © ${currentYear} ApexTrack. All rights reserved.`;
                }
                if (versionElement) {
                    versionElement.textContent = 'Version N/A';
                }
            });

        lucide.createIcons();
    });
</script>