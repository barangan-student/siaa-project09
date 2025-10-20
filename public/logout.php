<?php
session_start();
require_once '../auth.php';

init('../database/database.db');

logoutUser();
header('Location: login.php');
exit();