<?php
require_once __DIR__ . '/config.php';

unset($_SESSION['user_id']);
unset($_SESSION['username']);
unset($_SESSION['full_name']);

header('Location: index.php');
exit;

