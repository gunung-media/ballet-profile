<?php
declare(strict_types=1);
require dirname(__DIR__) . '/app/requirements.php';
require dirname(__DIR__) . '/app/bootstrap.php';

if (!is_post()) {
    abort(405, 'Metode tidak diizinkan.');
}
csrf_verify();
logout();
flash('info', 'Anda telah keluar.');
redirect('login.php');
