<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

logout();
start_session();
set_flash('notice', 'You have been logged out.');
redirect('login.php');
