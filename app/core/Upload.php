<?php
declare(strict_types=1);

/**
 * Upload foto: validasi isi file (bukan hanya ekstensi), nama acak,
 * diperkecil & dikonversi ke WebP agar website tetap ringan di HP.
 * Mengembalikan path relatif, mis. "assets/uploads/2026/10/ab-1a2b3c.webp".
 */
function upload_image(array $file): string
{
    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
        throw new RuntimeException('Ukuran foto melebihi batas server (' . ini_get('upload_max_filesize') . ').');
    }
    if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Foto gagal diunggah. Coba lagi.');
    }
    if ($file['size'] > config('upload.max_bytes')) {
        throw new RuntimeException('Ukuran foto maksimal ' . round(config('upload.max_bytes') / 1048576) . ' MB.');
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $size = @getimagesize($file['tmp_name']);
    if (!isset($allowed[$mime]) || !$size) {
        throw new RuntimeException('Format foto harus JPG, PNG, atau WebP.');
    }
    if ($size[0] * $size[1] > 40_000_000) {
        throw new RuntimeException('Resolusi foto terlalu besar.');
    }

    $subdir = date('Y/m');
    $dir = config('upload.dir') . '/' . $subdir;
    if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
        throw new RuntimeException('Folder upload tidak dapat dibuat. Periksa izin folder assets/uploads.');
    }

    $base = 'ab-' . bin2hex(random_bytes(8));

    if (function_exists('imagewebp')) {
        $name = $base . '.webp';
        optimize_image($file['tmp_name'], $mime, $dir . '/' . $name);
    } else {
        $name = $base . '.' . $allowed[$mime];
        if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
            throw new RuntimeException('Foto gagal disimpan.');
        }
    }

    return config('upload.url') . '/' . $subdir . '/' . $name;
}

function optimize_image(string $source, string $mime, string $target): void
{
    $img = match ($mime) {
        'image/jpeg' => @imagecreatefromjpeg($source),
        'image/png'  => @imagecreatefrompng($source),
        'image/webp' => @imagecreatefromwebp($source),
    };
    if (!$img) {
        throw new RuntimeException('Foto tidak dapat dibaca. Coba simpan ulang fotonya.');
    }

    // Foto dari HP sering tersimpan miring; luruskan berdasarkan data EXIF
    if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
        $orientation = @exif_read_data($source)['Orientation'] ?? 1;
        $img = match ((int) $orientation) {
            3 => imagerotate($img, 180, 0),
            6 => imagerotate($img, -90, 0),
            8 => imagerotate($img, 90, 0),
            default => $img,
        };
    }

    $w = imagesx($img);
    $h = imagesy($img);
    $max = config('upload.max_width');
    if ($w > $max || $h > $max) {
        $ratio = min($max / $w, $max / $h);
        $nw = (int) round($w * $ratio);
        $nh = (int) round($h * $ratio);
        $resized = imagecreatetruecolor($nw, $nh);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagedestroy($img);
        $img = $resized;
    } elseif (!imageistruecolor($img)) {
        imagepalettetotruecolor($img);
    }

    imagesavealpha($img, true);
    $ok = imagewebp($img, $target, config('upload.quality'));
    imagedestroy($img);
    if (!$ok) {
        throw new RuntimeException('Foto gagal disimpan.');
    }
}

/** Hapus file upload lama. Hanya file di dalam folder uploads yang boleh dihapus. */
function delete_upload(?string $path): void
{
    $prefix = config('upload.url') . '/';
    if (!$path || !str_starts_with($path, $prefix) || str_contains($path, '..')) {
        return;
    }
    $full = config('upload.dir') . '/' . substr($path, strlen($prefix));
    if (is_file($full)) {
        @unlink($full);
    }
}
