<?php
include 'layout/header.php';
include 'config.php';

$authToken = $_SESSION['auth_token'] ?? null;
$baseApiUrl = BASE_API_URL ?? '';
?>

<main class="flex-grow pt-20 md:pt-6 p-0 md:p-6 lg:p-10">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Edit Profil Anda</h2>
    <div class="w-full mx-auto bg-white p-8 shadow-xl">

        <div id="profile-message" class="hidden mb-6"></div>

        <form id="profile-form" class="space-y-6">
            
            <h2 class="text-2xl font-semibold text-gray-700">Informasi Dasar</h2>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                <input type="text" name="name" id="name" required autofocus
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div class="flex justify-end">
                <button type="submit" id="profile-submit-button"
                        class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Simpan Perubahan
                </button>
            </div>
        </form>

        <hr class="my-10 border-gray-200">

        <form id="password-form" class="space-y-6">
            
            <h2 class="text-2xl font-semibold text-gray-700">Ganti Kata Sandi</h2>

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">Kata Sandi Saat Ini</label>
                <input type="password" name="current_password" id="current_password" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi Baru</label>
                <input type="password" name="password" id="password" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div class="flex justify-end">
                <button type="submit" id="password-submit-button"
                        class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Ganti Kata Sandi
                </button>
            </div>
        </form>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const authToken = "<?php echo htmlspecialchars($authToken, ENT_QUOTES, 'UTF-8'); ?>";
        const API_BASE_URL = "<?php echo htmlspecialchars($baseApiUrl, ENT_QUOTES, 'UTF-8'); ?>";

        const profileForm = document.getElementById('profile-form');
        const passwordForm = document.getElementById('password-form');
        const profileMessage = document.getElementById('profile-message');
        
        const profileSubmitBtn = document.getElementById('profile-submit-button');
        const passwordSubmitBtn = document.getElementById('password-submit-button');

        const showMessage = (type, message) => {
            profileMessage.innerHTML = `<strong class="font-bold">${type === 'success' ? 'Berhasil!' : 'Error!'}</strong> <span class="block sm:inline">${message}</span>`;
            profileMessage.className = `bg-${type === 'success' ? 'green' : 'red'}-100 border border-${type === 'success' ? 'green' : 'red'}-400 text-${type === 'success' ? 'green' : 'red'}-700 px-4 py-3 rounded relative mb-6`;
            profileMessage.classList.remove('hidden');
            setTimeout(() => {
                profileMessage.classList.add('hidden');
            }, 5000);
        };

        const toggleButtonState = (button, state, text) => {
            button.disabled = state;
            button.textContent = state ? 'Loading...' : text;
        };

        const fetchProfileData = async () => {
            if (!authToken) return;
            try {
                const response = await fetch(`${API_BASE_URL}/profile`, {
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to fetch profile data.');
                }

                const data = await response.json();
                document.getElementById('name').value = data.user.name;
                document.getElementById('email').value = data.user.email;
            } catch (error) {
                showMessage('error', error.message);
            }
        };
        
        fetchProfileData();

        profileForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            toggleButtonState(profileSubmitBtn, true, 'Simpan Perubahan');
            
            const formData = new FormData(profileForm);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(`${API_BASE_URL}/profile`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const responseData = await response.json();

                if (!response.ok) {
                    const errorMessage = responseData.message || 'Gagal memperbarui profil.';
                    showMessage('error', errorMessage);
                } else {
                    showMessage('success', 'Profil Anda telah diperbarui.');
                }
            } catch (error) {
                showMessage('error', 'Terjadi kesalahan saat memperbarui profil.');
            } finally {
                toggleButtonState(profileSubmitBtn, false, 'Simpan Perubahan');
            }
        });

        passwordForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            toggleButtonState(passwordSubmitBtn, true, 'Ganti Kata Sandi');
            
            const formData = new FormData(passwordForm);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(`${API_BASE_URL}/password`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const responseData = await response.json();

                if (!response.ok) {
                    const errorMessage = responseData.message || 'Gagal mengganti kata sandi.';
                    showMessage('error', errorMessage);
                } else {
                    showMessage('success', 'Kata sandi Anda telah diperbarui.');
                    passwordForm.reset();
                }
            } catch (error) {
                showMessage('error', 'Terjadi kesalahan saat mengganti kata sandi.');
            } finally {
                toggleButtonState(passwordSubmitBtn, false, 'Ganti Kata Sandi');
            }
        });
    });
</script>
    
<?php include 'layout/footer.php'; ?>