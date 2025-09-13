<?php
$sourceDir = 'update_temp/ApexTrack-Lite-master/';
$excludedFile = 'settings.json'; // File yang dikecualikan

if (!is_dir($sourceDir)) {
    die("Error: Direktori sumber tidak ditemukan. Pembaruan gagal.\n");
}

function recursiveMove($src, $dst, $exclude = '') {
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            // Periksa apakah file ini adalah file yang dikecualikan
            if ($file === $exclude && is_file($src . '/' . $file)) {
                echo "Mengabaikan file yang dikecualikan: " . $dst . '/' . $file . "\n";
                continue; // Lewati file ini
            }

            if (is_dir($src . '/' . $file)) {
                recursiveMove($src . '/' . $file, $dst . '/' . $file, $exclude);
            } else {
                // Pastikan kita tidak menimpa file yang dikecualikan di tujuan
                if (file_exists($dst . '/' . $file) && $file === $exclude) {
                    echo "File tujuan yang dikecualikan sudah ada, mengabaikan: " . $dst . '/' . $file . "\n";
                } else {
                    // Hapus file tujuan jika ada sebelum menyalin agar tidak ada sisa file lama
                    if (file_exists($dst . '/' . $file)) {
                        unlink($dst . '/' . $file);
                    }
                    if (copy($src . '/' . $file, $dst . '/' . $file)) {
                        echo "Menyalin: " . $src . '/' . $file . " ke " . $dst . '/' . $file . "\n";
                        unlink($src . '/' . $file);
                    } else {
                        echo "Gagal menyalin: " . $src . '/' . $file . "\n";
                    }
                }
            }
        }
    }
    closedir($dir);
    // Hanya hapus direktori sumber jika kosong setelah memindahkan semua file
    // atau jika itu adalah direktori yang tidak dikecualikan
    if (empty(scandir($src))) {
        rmdir($src);
    }
}

recursiveMove($sourceDir, './', $excludedFile);

function rrmdir($dir) {
    if (is_dir($dir)) {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? rrmdir("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}

// Hapus direktori update_temp jika sudah kosong atau hanya berisi file yang tidak dipindahkan
if (is_dir('update_temp/')) {
    // Periksa apakah direktori 'update_temp/ApexTrack-Lite-master/' sudah benar-benar kosong atau dihapus
    if (!is_dir('update_temp/ApexTrack-Lite-master/')) {
        rrmdir('update_temp/');
        echo "Direktori sementara pembaruan dibersihkan.\n";
    } else {
        echo "Peringatan: Direktori pembaruan sementara mungkin masih berisi file yang tidak dipindahkan.\n";
    }
}

echo "success";
?>