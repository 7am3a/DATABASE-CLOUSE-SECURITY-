<?php
require_once __DIR__ . '/includes/init.php';

logoutUser();
setFlash('success', 'You have been logged out successfully.');
redirect(baseUrl('login.php'));
