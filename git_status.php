<?php
/**
 * Git Status & Clean Tool for cPanel
 * Helps resolve "uncommitted changes exist on the checked-out branch" error
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Git cPanel Deploy Helper</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; color: #1e293b; padding: 2rem; }
        .card { background: white; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 2rem; max-width: 800px; margin: 0 auto; border: 1px solid #e2e8f0; }
        h1 { font-size: 1.5rem; margin-top: 0; color: #0f172a; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.75rem; }
        pre { background: #0f172a; color: #38bdf8; padding: 1rem; border-radius: 6px; overflow-x: auto; font-family: monospace; font-size: 0.9rem; }
        .btn { display: inline-block; background: #3b82f6; color: white; border: none; padding: 0.75rem 1.5rem; font-size: 0.95rem; font-weight: 600; border-radius: 6px; cursor: pointer; text-decoration: none; margin-right: 0.5rem; }
        .btn-danger { background: #ef4444; }
        .btn-success { background: #10b981; }
        .alert { padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; font-weight: 500; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-info { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
    </style>
</head>
<body>

<div class="card">
    <h1>Git cPanel Deploy Helper</h1>
    
    <?php
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    if ($action === 'reset') {
        // Run git reset and clean to wipe any uncommitted files on server
        $outputReset = [];
        $statusReset = 0;
        exec("git reset --hard HEAD 2>&1", $outputReset, $statusReset);
        
        $outputClean = [];
        $statusClean = 0;
        exec("git clean -fd 2>&1", $outputClean, $statusClean);
        
        echo '<div class="alert alert-success">';
        echo '<strong>Success:</strong> Git Reset & Clean commands executed.<br>';
        echo 'Reset Output: ' . htmlspecialchars(implode("\n", $outputReset)) . '<br>';
        echo 'Clean Output: ' . htmlspecialchars(implode("\n", $outputClean));
        echo '</div>';
    } elseif ($action === 'pull') {
        $outputPull = [];
        $statusPull = 0;
        exec("git pull origin main 2>&1", $outputPull, $statusPull);
        
        echo '<div class="alert alert-info">';
        echo '<strong>Git Pull Output:</strong><br><pre>' . htmlspecialchars(implode("\n", $outputPull)) . '</pre>';
        echo '</div>';
    }
    
    // Always show git status
    $outputStatus = [];
    $statusVal = 0;
    exec("git status 2>&1", $outputStatus, $statusVal);
    ?>

    <h3>Current Git Status:</h3>
    <pre><?php echo htmlspecialchars(implode("\n", $outputStatus)); ?></pre>

    <div style="margin-top: 2rem;">
        <a href="?action=reset" class="btn btn-danger" onclick="return confirm('WARNING: This will permanently wipe all uncommitted changes and modified files directly on the server. Continue?')">Reset & Clean Server Repo</a>
        <a href="?action=pull" class="btn btn-success">Run Git Pull</a>
        <a href="?" class="btn">Refresh Status</a>
    </div>
</div>

</body>
</html>
