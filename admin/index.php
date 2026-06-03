<?php
/**
 * Admin Index Redirect
 * Prevents 403 Forbidden Directory Listing
 */
header("Location: dashboard.php");
exit;
