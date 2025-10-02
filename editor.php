<?php
// layout/header.php - Pastikan ini memuat Tailwind CSS dan Font Awesome
include('layout/header.php');
?>

<main class="flex-grow p-6 md:p-10 lg:p-12 min-h-screen bg-gray-100">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Editor Thumbnail</h2>

    <div class=" mx-auto bg-white p-6 md:p-10 shadow-2xl rounded-xl border border-gray-200">

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] === 'success'): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> Foto berhasil diedit dan disimpan!
                    <button type="button" class="absolute top-0 right-0 mt-2 mr-4 text-green-700" onclick="this.parentElement.style.display='none';">&times;</button>
                </div>
            <?php elseif ($_GET['status'] === 'error'): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Terjadi kesalahan: <?php echo htmlspecialchars($_GET['message']); ?>
                    <button type="button" class="absolute top-0 right-0 mt-2 mr-4 text-red-700" onclick="this.parentElement.style.display='none';">&times;</button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Container Grid untuk Kontrol dan Preview (Dua Kolom di Layar Besar) -->
        <div class="lg:grid lg:grid-cols-3 lg:gap-8">

            <!-- Kolom Kontrol (Kiri) -->
            <div class="lg:col-span-1 space-y-6">

                <!-- 1. Pilih Gambar -->
                <div class="upload-section">
                    <label for="imageUpload" class="inline-block w-full text-center cursor-pointer bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 shadow-md hover:shadow-lg">
                        <i class="fas fa-upload mr-2"></i> Unggah Gambar (.jpg, .png)
                    </label>
                    <input type="file" id="imageUpload" accept="image/*" class="hidden">
                </div>

                <!-- 2. Jumlah Views (Opsional) -->
                <div class="view-section">
                    <label for="viewCount" class="text-md font-medium text-gray-700 block mb-2">Jumlah Views:</label>
                    <input type="number" id="viewCount" class="w-full border-2 border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-4 focus:ring-indigo-200 focus:border-indigo-500 transition duration-150" value="" placeholder="Kosongkan untuk menyembunyikan views" min="0">
                </div>

                <!-- 3. Toggle Buttons & New Profile Name Input -->
                <div class="toggle-section space-y-3">
                    <label class="text-md font-medium text-gray-700 block mb-2">Overlay:</label>
                    
                    <!-- NEW PROFILE NAME INPUT -->
                    <div class="profile-name-section mb-4">
                        <label for="profileNameInput" class="text-sm font-medium text-gray-700 block mb-1">artis LIVE:</label>
                        <input type="text" id="profileNameInput" class="w-full border border-gray-300 p-2 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500" value="Nama Live Anda" placeholder="Masukkan nama profil Anda">
                    </div>

                    <button id="addPlayButton" class="w-full flex items-center justify-center bg-gray-700 hover:bg-gray-800 text-white font-bold py-3 px-4 rounded-lg transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed shadow-md" disabled>
                        <i class="fas fa-play mr-3 text-lg"></i> (Play)
                    </button>
                    
                    <button id="addLiveIndicator" class="w-full flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed shadow-md" disabled>
                        <i class="fas fa-video mr-3 text-lg"></i> (LIVE)
                    </button>
                </div>
            </div>

            <!-- Kolom Canvas/Preview (Kanan) -->
            <div class="lg:col-span-2 mt-8 lg:mt-0">
                <!-- Flex container untuk menahan canvas agar rapi -->
                <div class="relative flex justify-center items-center bg-gray-200 rounded-lg overflow-hidden shadow-inner p-4">
                    <canvas id="myCanvas" class="border-4 border-gray-400 shadow-xl rounded-lg w-full h-auto max-w-full"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Pemisah -->
        <hr class="my-10 border-gray-300">

        <!-- Bagian Generate & Hasil (Di bawah Grid) -->
        <div class="text-center">
            <button id="generateButton" class="w-full md:w-1/3 bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed text-xl shadow-lg hover:shadow-xl" disabled>
                <i class="fas fa-magic mr-2"></i> GENERATE
            </button>
        </div>

        <div class="result-section mt-10">
            <div id="resultContainer" class="hidden text-center p-6 border border-green-200 bg-green-50 rounded-lg">
                <h5 class="text-2xl font-semibold mb-6 text-green-800">🎉 Hasil Akhir Siap Diunduh!</h5>
                <img id="editedImagePreview" src="" alt="Pratinjau Hasil" class="mx-auto max-w-full h-auto border-4 border-green-500 shadow-2xl rounded-lg mb-6">
                <a id="downloadLink" href="#" download="edited_image.png" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg transition duration-300 text-lg shadow-xl hidden">
                    <i class="fas fa-download mr-2"></i> Unduh Gambar
                </a>
            </div>
        </div>
    </div>
</main>
<script>
    const canvas = document.getElementById('myCanvas');
    const ctx = canvas.getContext('2d');
    const imageUpload = document.getElementById('imageUpload');
    const viewCountInput = document.getElementById('viewCount');
    const profileNameInput = document.getElementById('profileNameInput'); 
    const addPlayButton = document.getElementById('addPlayButton');
    const addLiveIndicator = document.getElementById('addLiveIndicator');
    const generateButton = document.getElementById('generateButton');
    const resultContainer = document.getElementById('resultContainer');
    const editedImagePreview = document.getElementById('editedImagePreview');
    const downloadLink = document.getElementById('downloadLink');

    let currentImage = null;
    const overlayElements = {
        play: false,
        live: false,
        views: '',
        liveProfileName: 'Nama Live Anda' 
    };
    
    // [BARU] Daftar lengkap komentar dan hadiah dalam Bahasa Inggris
    const FULL_COMMENT_LIST = [
        // Regular Comments
        { type: 'comment', user: 'CoolDude_99', text: 'You look amazing tonight! ❤️' },
        { type: 'comment', user: 'GlobalFan', text: 'I love your stream! 😍' },
        { type: 'comment', user: 'NightOwl', text: 'Where are you streaming from?' },
        { type: 'comment', user: 'SexyBae', text: 'OMG! So hot 🔥🔥🔥' },
        { type: 'comment', user: 'JP_Fan', text: 'So cute in that outfit! 👍' },
        { type: 'comment', user: 'Anon55', text: 'Nice stream!' },
        { type: 'comment', user: 'StreamLover', text: 'Keep up the good work!' },
        { type: 'comment', user: 'LateComer', text: 'Did I miss anything exciting?' },
        { type: 'comment', user: 'DailyViewer', text: 'The background music is perfect.' },
        { type: 'comment', user: 'RandomUser', text: 'Awesome vibes here!' },
        { type: 'comment', user: 'Geek_23', text: 'What game are you playing later?' },
        { type: 'comment', user: 'FashionGuru', text: 'That shirt really suits you!' },
        { type: 'comment', user: 'Newbie', text: 'First time watching, love it!' },
        { type: 'comment', user: 'BigFan_xoxo', text: 'My favorite streamer is on! 🎉' },
        { type: 'comment', user: 'Chill_Guy', text: 'Super relaxing stream, thanks!' },
        { type: 'comment', user: 'TrollHunter', text: 'Ignore the haters, you\'re doing great!' },
        { type: 'comment', user: 'ZenMaster', text: 'Just watching and vibing. 🙏' },
        { type: 'comment', user: 'CoffeeLover', text: 'Fueling up for the rest of the stream!' },
        { type: 'comment', user: 'A_Random', text: 'This stream is fire!' },
        { type: 'comment', user: 'TheAuditor', text: 'The resolution is fantastic.' },
        
        // Gift Comments (all English)
        { type: 'gift', user: 'MoneyMan', text: 'sent 🎁 5 Roses!', color: '#FCD34D' }, 
        { type: 'gift', user: 'StarGazer', text: 'sent 💖 10 Hearts!', color: '#F87171' },
        { type: 'gift', user: 'VIP_Member', text: 'sent 💎 1 Diamond!', color: '#93C5FD' },
        { type: 'gift', user: 'GenerousOne', text: 'sent ✨ 3 Stars!', color: '#FFE0B2' }, 
        { type: 'gift', user: 'TheBoss', text: 'sent 👑 1 Crown!', color: '#FFD700' }, 
        { type: 'gift', user: 'Supporter', text: 'sent 🎈 5 Balloons!', color: '#E0BBE4' },
        { type: 'gift', user: 'MegaDonor', text: 'sent 🚀 1 Rocket!', color: '#FF6F61' },
        { type: 'gift', user: 'AngelInvestor', text: 'sent 💰 100 Coins!', color: '#77DD77' },
        { type: 'gift', user: 'LoyaltyFan', text: 'sent 🏅 1 Gold Medal!', color: '#D4AF37' },
        { type: 'gift', user: 'TheGiver', text: 'sent 💐 1 Bouquet!', color: '#A3EBB1' },
    ];
    
    // [BARU] Konstanta untuk membatasi jumlah komentar yang ditampilkan
    const MAX_DISPLAYED_COMMENTS = 12; 

    // [BARU] Variabel untuk menyimpan subset komentar yang telah dipilih secara acak
    let displayedComments = []; 

    // [BARU] Fungsi utilitas untuk mengacak array (Fisher-Yates)
    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
    }


    // Fungsi untuk menggambar ikon Font Awesome
    function drawFontAwesomeIcon(context, iconUnicode, x, y, size, color) {
        // Pastikan Font Awesome 5 Free terload dengan benar
        context.font = `900 ${size}px "Font Awesome 5 Free"`;
        context.fillStyle = color;
        context.textAlign = 'center';
        context.textBaseline = 'middle';
        context.fillText(iconUnicode, x, y);
    }

    // Fungsi utama untuk menggambar ulang seluruh canvas
    function redrawCanvas() {
        // Bersihkan canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Gambar latar belakang gambar jika ada
        if (currentImage) {
            ctx.drawImage(currentImage, 0, 0, canvas.width, canvas.height);
        }

        // --- Pengaturan untuk elemen overlay ---
        const margin = canvas.height * 0.05; // Margin dari tepi atas canvas
        const padding = 10;
        const gap = 10;
        const elementHeight = 30;
        const iconSize = 16;
        const bottomOffset = 15; // Jarak dari bawah canvas (Untuk progress bar/text)
        const textBarHeight = 35; // Tinggi total area bawah untuk teks/bar

        let currentX = margin; 

        // --- Menggambar tombol Play di tengah (jika aktif) ---
        if (overlayElements.play) {
            const playSize = canvas.width * 0.2;
            const playX = canvas.width / 2;
            const playY = canvas.height / 2;

            ctx.shadowColor = 'rgba(0,0,0,0.8)';
            ctx.shadowBlur = 10;
            ctx.shadowOffsetX = 4;
            ctx.shadowOffsetY = 4;

            drawFontAwesomeIcon(ctx, '\uf04b', playX, playY, playSize, '#FFFFFF'); // fa-play

            // Reset shadow
            ctx.shadowBlur = 0;
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = 0;
        }

        // --- Menggambar indikator Live di pojok kiri atas ---
        if (overlayElements.live) {
            const liveText = 'LIVE';
            ctx.font = `700 ${iconSize}px Arial`; 
            const textMetrics = ctx.measureText(liveText);
            const liveBoxWidth = textMetrics.width + padding * 2;
            const liveX = currentX;
            const liveY = margin;

            // Gambar background kotak LIVE
            ctx.fillStyle = '#e74c3c'; // Warna merah
            ctx.beginPath();
            ctx.roundRect(liveX, liveY, liveBoxWidth, elementHeight, 5); 
            ctx.fill();

            // Gambar teks LIVE
            ctx.fillStyle = '#FFFFFF';
            ctx.textAlign = 'left';
            ctx.fillText(liveText, liveX + padding, liveY + elementHeight / 2);
            ctx.textAlign = 'center';

            currentX += liveBoxWidth + gap;
        }

        // --- Menggambar indikator Views (Sebelah Live), HANYA JIKA ADA VIEWS ---
        if (overlayElements.views && currentImage) {
            const viewsText = overlayElements.views;
            ctx.font = `700 ${iconSize}px Arial`;
            const viewsTextMetrics = ctx.measureText(viewsText);
            const viewsIconWidth = iconSize + 5;
            
            const viewsBoxWidth = padding + viewsIconWidth + viewsTextMetrics.width + padding;
            
            const viewsX = currentX;
            const viewsY = margin;

            // Gambar background kotak Views
            ctx.fillStyle = 'rgba(0,0,0,0.6)'; // Hitam transparan
            ctx.beginPath();
            ctx.roundRect(viewsX, viewsY, viewsBoxWidth, elementHeight, 5);
            ctx.fill();

            // Gambar ikon mata (fa-eye)
            const iconX = viewsX + padding + viewsIconWidth / 2;
            const iconY = viewsY + elementHeight / 2;
            drawFontAwesomeIcon(ctx, '\uf06e', iconX, iconY, iconSize, '#FFFFFF'); // fa-eye

            // Gambar teks views
            ctx.fillStyle = '#FFFFFF';
            ctx.textAlign = 'left'; 
            const textX = viewsX + padding + viewsIconWidth;
            const textY = viewsY + elementHeight / 2;
            ctx.fillText(viewsText, textX, textY);
            ctx.textAlign = 'center';
            
            currentX += viewsBoxWidth + gap;
        }

        // --- Menggambar Profil LIVE di pojok kanan atas (TANPA BULATAN) ---
        if (overlayElements.live && currentImage) {
            const profileMargin = margin * 0.75; // Margin dari tepi kanan
            const profileY = margin + elementHeight / 2; // Vertical center line

            // Atur shadow
            ctx.shadowColor = 'rgba(0,0,0,0.8)';
            ctx.shadowBlur = 5;
            ctx.shadowOffsetX = 2;
            ctx.shadowOffsetY = 2;

            // 1. Gambar Nama Profil dan Icon Panah (Caret Down)
            const profileName = overlayElements.liveProfileName || 'Nama Live Anda';
            const nameFontSize = 16;
            const caretIcon = '\uf0d7'; // fa-caret-down
            const caretSize = 12;
            const textGap = 5; // Gap between name and caret

            // Tentukan posisi Anchor (Posisi paling kanan elemen teks/icon)
            // Mengatur Anchor agar tidak terlalu mepet ke kanan
            const rightAnchorX = canvas.width - profileMargin; 
            const centerLineY = profileY;
            
            // 1. Gambar Icon Panah (fa-caret-down)
            // Posisikan center caret sedikit ke kiri dari rightAnchor
            const caretX = rightAnchorX - caretSize/2; 
            drawFontAwesomeIcon(ctx, caretIcon, caretX, centerLineY, caretSize, '#FFFFFF');

            // 2. Gambar Nama Profil
            // Posisikan ujung kanan teks (right-aligned) ke kiri caret
            const nameEndX = caretX - caretSize/2 - textGap; 
            
            ctx.font = `700 ${nameFontSize}px Arial`;
            ctx.fillStyle = '#FFFFFF';
            ctx.textAlign = 'right'; // Menggunakan right alignment
            ctx.textBaseline = 'middle';
            
            ctx.fillText(profileName, nameEndX, centerLineY);

            // 3. Reset shadow and alignment
            ctx.shadowBlur = 0;
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = 0;
            ctx.textAlign = 'center';
        }
        
        // --- Menggambar Komentar LIVE (Jika LIVE aktif) ---
        if (currentImage && overlayElements.live) {
            const commentFontSize = 14;
            const lineSpacing = commentFontSize + 8;
            const commentX = margin;
            // Posisikan komentar di atas area bottom bar
            const commentYStart = canvas.height - textBarHeight - 100; 
            
            let currentCommentY = commentYStart; 

            // Atur shadow untuk teks komentar
            ctx.shadowColor = 'rgba(0,0,0,0.8)';
            ctx.shadowBlur = 5;

            // Menggunakan displayedComments yang sudah diacak dan dibatasi
            displayedComments.forEach(comment => { 
                const userText = comment.user;
                const msgText = comment.text;
                let finalMessage = '';
                
                if (comment.type === 'gift') {
                    // Teks hadiah: Font tebal dan warna kustom
                    finalMessage = `${userText} ${msgText}`;

                    ctx.font = `700 ${commentFontSize}px Arial`; 
                    ctx.fillStyle = comment.color; 
                    ctx.textAlign = 'left';
                    
                    // Draw transparent background for gift
                    ctx.shadowBlur = 0; // Temporarily disable text shadow for box
                    const textWidth = ctx.measureText(finalMessage).width;
                    const boxX = commentX - 5;
                    const boxY = currentCommentY - commentFontSize / 2 - 4; 
                    const boxHeight = lineSpacing - 4;

                    ctx.fillStyle = 'rgba(0, 0, 0, 0.5)'; // Dark transparent background
                    ctx.beginPath();
                    ctx.roundRect(boxX, boxY, textWidth + 10, boxHeight, 5);
                    ctx.fill();
                    ctx.shadowBlur = 5; // Re-enable text shadow

                    ctx.fillText(finalMessage, commentX, currentCommentY);

                } else {
                    // Komentar Standar
                    
                    // User (Tebal, Biru Muda)
                    ctx.font = `700 ${commentFontSize}px Arial`;
                    ctx.fillStyle = '#ADD8E6'; 
                    ctx.textAlign = 'left';
                    const userWidth = ctx.measureText(userText).width;
                    ctx.fillText(userText, commentX, currentCommentY);
                    
                    // Message (Reguler, Putih)
                    ctx.font = `400 ${commentFontSize}px Arial`;
                    ctx.fillStyle = '#FFFFFF';
                    const msgX = commentX + userWidth + 5; // Jarak setelah username
                    ctx.fillText(`: ${msgText}`, msgX, currentCommentY); 
                }
                
                currentCommentY -= lineSpacing; // Pindah ke baris di atasnya
                
                // Stop if comments go out of bounds
                if (currentCommentY < margin + elementHeight) return; 
            });
            
            ctx.shadowBlur = 0; // Reset shadow
            ctx.textAlign = 'center'; // Reset alignment
        }

        // --- Menggambar Durasi Video/Label di pojok kanan bawah ---
        if (currentImage) {
            let bottomText;
            const fontSize = 18;
            const textMargin = 15;
            const textY = canvas.height - (textBarHeight / 2); // Posisikan di tengah area bottom bar
            
            // LOGIKA DURASI:
            if (overlayElements.live) {
                // Jika LIVE aktif
                bottomText = '01:15:30'; 
            } else {
                // Jika LIVE non-aktif
                bottomText = '0:12 / 4:30'; 
            }

            ctx.font = `700 ${fontSize}px Arial`;
            ctx.fillStyle = '#FFFFFF';
            ctx.textAlign = 'right';

            ctx.shadowColor = 'rgba(0,0,0,0.8)';
            ctx.shadowBlur = 5;
            ctx.shadowOffsetX = 2;
            ctx.shadowOffsetY = 2;
            
            const textX = canvas.width - textMargin; 
            ctx.fillText(bottomText, textX, textY);

            // Reset shadow
            ctx.shadowBlur = 0;
            ctx.shadowOffsetX = 0;
            ctx.shadowOffsetY = 0;
            ctx.textAlign = 'center';
        }
        
        // --- Menggambar Bilah di Bagian Bawah Canvas ---
        if (currentImage) {
            const barHeight = 6;
            const barY = canvas.height - barHeight - bottomOffset;

            if (overlayElements.live) {
                // Mode LIVE: Bar Solid Merah (Indikator Aktif)
                // Latar belakang hitam penuh di belakang area teks
                ctx.fillStyle = 'rgba(0, 0, 0, 0.6)';
                ctx.fillRect(0, canvas.height - textBarHeight, canvas.width, textBarHeight);
                
                // Garis indikator merah tipis di bagian atas bar hitam
                ctx.fillStyle = '#FF0000'; 
                ctx.fillRect(0, canvas.height - textBarHeight, canvas.width, 3);
            
            } else {
                // Mode Video Biasa (dengan progress dan dot)
                
                // 1. Latar belakang (Hitam transparan)
                ctx.fillStyle = 'rgba(0, 0, 0, 0.4)';
                ctx.fillRect(0, barY, canvas.width, barHeight);

                // 2. Progres (Merah cerah)
                const progressWidth = canvas.width * 0.25; 
                ctx.fillStyle = '#FF0000';
                ctx.fillRect(0, barY, progressWidth, barHeight);
                
                // 3. Titik (Bulatan) Progres (Putih)
                const dotRadius = 5;
                const dotX = progressWidth;
                const dotY = barY + barHeight / 2;
                
                ctx.beginPath();
                ctx.arc(dotX, dotY, dotRadius, 0, Math.PI * 2);
                ctx.fillStyle = '#FFFFFF';
                ctx.fill();
            }
        }
    }

    // Event listener untuk upload gambar
    imageUpload.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(event) {
            const img = new Image();
            img.onload = function() {
                canvas.width = img.width;
                canvas.height = img.height;
                currentImage = img;

                overlayElements.play = false;
                overlayElements.live = false;
                displayedComments = []; // Reset komentar saat gambar baru diunggah
                
                addPlayButton.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
                addPlayButton.classList.add('bg-gray-700', 'hover:bg-gray-800');
                addLiveIndicator.classList.remove('bg-green-600', 'hover:bg-green-700');
                addLiveIndicator.classList.add('bg-red-600', 'hover:bg-red-700');

                viewCountInput.value = '';
                overlayElements.views = ''; 

                redrawCanvas();

                resultContainer.classList.add('hidden');
                downloadLink.classList.add('hidden');
                addPlayButton.disabled = false;
                addLiveIndicator.disabled = false;
                generateButton.disabled = false;
            };
            img.src = event.target.result;
        };
        reader.readAsDataURL(file);
    });

    // Event listener untuk tombol Play
    addPlayButton.addEventListener('click', function() {
        overlayElements.play = !overlayElements.play;
        if (overlayElements.play) {
            addPlayButton.classList.remove('bg-gray-700', 'hover:bg-gray-800');
            addPlayButton.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
        } else {
            addPlayButton.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
            addPlayButton.classList.add('bg-gray-700', 'hover:bg-gray-800');
        }
        redrawCanvas();
    });

    // Event listener untuk tombol Live Indicator
    addLiveIndicator.addEventListener('click', function() {
        overlayElements.live = !overlayElements.live;
        if (overlayElements.live) {
            addLiveIndicator.classList.remove('bg-red-600', 'hover:bg-red-700');
            addLiveIndicator.classList.add('bg-green-600', 'hover:bg-green-700');
            
            // [LOGIKA BARU] Saat LIVE dihidupkan: acak dan potong daftar komentar
            let shuffled = [...FULL_COMMENT_LIST]; // Buat salinan
            shuffleArray(shuffled);
            displayedComments = shuffled.slice(0, MAX_DISPLAYED_COMMENTS);

        } else {
            addLiveIndicator.classList.remove('bg-green-600', 'hover:bg-green-700');
            addLiveIndicator.classList.add('bg-red-600', 'hover:bg-red-700');
            displayedComments = []; // Kosongkan komentar saat LIVE dimatikan
        }
        redrawCanvas();
    });

    // Event listener untuk input jumlah views
    viewCountInput.addEventListener('input', function() {
        const views = parseInt(this.value, 10);
        if (this.value === '' || isNaN(views) || views < 0) {
            overlayElements.views = '';
        } else {
            overlayElements.views = `${views.toLocaleString()}`;
        }
        redrawCanvas();
    });

    // Event listener untuk input nama profil LIVE 
    profileNameInput.addEventListener('input', function() {
        overlayElements.liveProfileName = this.value || 'Nama Live Anda';
        redrawCanvas();
    });

    // Event listener untuk tombol Generate
    generateButton.addEventListener('click', function() {
        if (!currentImage) {
            console.error('Peringatan: Silakan unggah gambar terlebih dahulu sebelum menggenerate.'); 
            return;
        }
        const dataURL = canvas.toDataURL('image/png');
        editedImagePreview.src = dataURL;
        downloadLink.href = dataURL;
        const timestamp = new Date().toISOString().replace(/[-:.]/g, '');
        downloadLink.download = `edited_image_${timestamp}.png`;

        resultContainer.classList.remove('hidden');
        downloadLink.classList.remove('hidden');
    });

    // Polyfill untuk CanvasRenderingContext2D.prototype.roundRect
    if (!CanvasRenderingContext2D.prototype.roundRect) {
        CanvasRenderingContext2D.prototype.roundRect = function (x, y, w, h, r) {
            if (w < 2 * r) r = w / 2;
            if (h < 2 * r) r = h / 2;
            this.beginPath();
            this.moveTo(x + r, y);
            this.arcTo(x + w, y, x + w, y + h, r);
            this.arcTo(x + w, y + h, x, y + h, r);
            this.arcTo(x, y + h, x, y, r);
            this.arcTo(x, y, x + w, y, r);
            this.closePath();
        }
    }
</script>

<?php
// layout/footer.php
include('layout/footer.php');
?>
