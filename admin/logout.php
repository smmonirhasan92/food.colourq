<?php
/**
 * Admin Logout script
 * Food Delivery & Real-Time Notification System
 */

session_start();
session_unset();
session_destroy();

header("Location: login.php");
exit;
