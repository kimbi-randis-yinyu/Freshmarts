<?php
require_once __DIR__ . '/../config/functions.php';
session_unset();
session_destroy();
redirect('admin_login.php');
