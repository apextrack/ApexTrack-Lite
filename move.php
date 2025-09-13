<?php
$sourceDir = 'update_temp/ApexTrack-Lite-master/';

if (!is_dir($sourceDir)) {
    die("Error: Direktori sumber tidak ditemukan. Pembaruan gagal.\n");
}

function recursiveMove($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                recursiveMove($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
                unlink($src . '/' . $file);
            }
        }
    }
    closedir($dir);
    rmdir($src);
}

recursiveMove($sourceDir, './');

function rrmdir($dir) {
    if (is_dir($dir)) {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? rrmdir("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}
rrmdir('update_temp/');

echo "success";
?>