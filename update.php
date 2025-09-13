<?php
$zipUrl = 'https://github.com/apextrack/ApexTrack-Lite/archive/refs/heads/master.zip';
$zipFile = 'update.zip';
$tempDir = 'update_temp/';

if (!class_exists('ZipArchive')) {
    die("Error: PHP ZipArchive class is not available.\n");
}

echo "Memulai proses pembaruan...\n";

if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
    echo "Direktori sementara dibuat.\n";
}

echo "Mengunduh pembaruan dari GitHub...\n";
$zipContent = @file_get_contents($zipUrl);
if ($zipContent === false) {
    die("Error: Gagal mengunduh file ZIP.\n");
}

if (file_put_contents($zipFile, $zipContent) === false) {
    die("Error: Gagal menyimpan file ZIP di server.\n");
}
echo "Berhasil mengunduh file ZIP.\n";

echo "Mengekstrak file...\n";
$zip = new ZipArchive;
if ($zip->open($zipFile) === true) {
    if (!$zip->extractTo($tempDir)) {
        $zip->close();
        unlink($zipFile);
        die("Error: Gagal mengekstrak file ZIP.\n");
    }
    $zip->close();
    
    unlink($zipFile);
    
    echo "Pembaruan berhasil diekstrak!\n";

    echo "Memulai proses pemindahan file...\n";
    $command = 'php -f ' . escapeshellarg('move.php');
    $output = shell_exec($command);

    if (strpos($output, 'success') !== false) {
        echo "Pembaruan berhasil!\n";
    } else {
        echo "Pembaruan gagal. Silakan periksa log server untuk detailnya.\n";
        echo "Output dari move.php: " . $output;
    }

} else {
    unlink($zipFile);
    die("Error: Gagal membuka file ZIP yang diunduh.\n");
}
?>