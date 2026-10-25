<?php
/**
 * Simple RSVP Viewer
 * View all RSVP submissions in a formatted table
 * 
 * SECURITY WARNING: This page has no authentication!
 * Add password protection or move to a secure admin area before using in production.
 */

// Simple password protection (CHANGE THIS PASSWORD!)
$admin_password = 'calix2026'; // CHANGE THIS!

session_start();

// Check if password is submitted
if (isset($_POST['password'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['authenticated'] = true;
    } else {
        $error = 'Incorrect password';
    }
}

// Check if logout is requested
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: view_rsvps.php');
    exit();
}

// If not authenticated, show login form
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>RSVP Admin Login</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
            }
            .login-container {
                background: white;
                padding: 40px;
                border-radius: 10px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.3);
                max-width: 400px;
                width: 90%;
            }
            h1 {
                margin: 0 0 20px;
                color: #333;
                text-align: center;
            }
            input[type="password"] {
                width: 100%;
                padding: 12px;
                border: 2px solid #ddd;
                border-radius: 5px;
                font-size: 16px;
                box-sizing: border-box;
            }
            button {
                width: 100%;
                padding: 12px;
                background: #667eea;
                color: white;
                border: none;
                border-radius: 5px;
                font-size: 16px;
                cursor: pointer;
                margin-top: 10px;
            }
            button:hover {
                background: #5568d3;
            }
            .error {
                color: #e74c3c;
                margin-top: 10px;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h1>🔒 RSVP Admin</h1>
            <form method="POST">
                <input type="password" name="password" placeholder="Enter admin password" required autofocus>
                <button type="submit">Login</button>
            </form>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Authenticated - Show RSVPs
$file = __DIR__ . '/data/rsvps.json';
$rsvps = [];

if (file_exists($file)) {
    $content = file_get_contents($file);
    $rsvps = json_decode($content, true) ?: [];
}

// Calculate statistics
$totalRsvps = count($rsvps);
$totalGuests = array_sum(array_column($rsvps, 'guests'));

// Sort by timestamp (newest first)
usort($rsvps, function($a, $b) {
    return strtotime($b['timestamp']) - strtotime($a['timestamp']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSVP List - Calix's Baptism</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        header {
            background: linear-gradient(135deg, #a3c4e5 0%, #7ba7d1 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 2px solid #e0e0e0;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
        }
        .stat-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .controls {
            padding: 20px;
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        .btn-danger:hover {
            background: #c0392b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e0e0e0;
            position: sticky;
            top: 0;
        }
        td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        .message-cell {
            max-width: 300px;
            word-wrap: break-word;
        }
        .timestamp-cell {
            white-space: nowrap;
            color: #666;
            font-size: 0.9rem;
        }
        .id-cell {
            font-family: monospace;
            font-size: 0.8rem;
            color: #999;
        }
        @media (max-width: 768px) {
            .stats {
                flex-direction: column;
                gap: 20px;
            }
            .controls {
                flex-direction: column;
                gap: 10px;
                align-items: stretch;
            }
            table {
                font-size: 0.9rem;
            }
            th, td {
                padding: 10px;
            }
            .message-cell {
                max-width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📋 RSVP List</h1>
            <p>Baptism of Calix</p>
        </header>

        <div class="stats">
            <div class="stat-item">
                <div class="stat-number"><?php echo $totalRsvps; ?></div>
                <div class="stat-label">Total RSVPs</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $totalGuests; ?></div>
                <div class="stat-label">Total Guests</div>
            </div>
        </div>

        <div class="controls">
            <div>
                <button class="btn btn-primary" onclick="window.print()">🖨️ Print List</button>
                <button class="btn btn-primary" onclick="downloadCSV()">📥 Download CSV</button>
            </div>
            <a href="?logout=1" class="btn btn-danger">🚪 Logout</a>
        </div>

        <?php if (empty($rsvps)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <h2>No RSVPs Yet</h2>
                <p>RSVPs will appear here when guests submit the form.</p>
            </div>
        <?php else: ?>
            <table id="rsvpTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Guests</th>
                        <th>Message</th>
                        <th>Date & Time</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rsvps as $index => $rsvp): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><strong><?php echo htmlspecialchars($rsvp['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($rsvp['guests']); ?></td>
                            <td class="message-cell"><?php echo htmlspecialchars($rsvp['message']) ?: '<em>No message</em>'; ?></td>
                            <td class="timestamp-cell"><?php echo htmlspecialchars($rsvp['timestamp']); ?></td>
                            <td class="id-cell"><?php echo htmlspecialchars($rsvp['ip'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script>
        function downloadCSV() {
            <?php if (empty($rsvps)): ?>
                alert('No RSVPs to download');
                return;
            <?php endif; ?>

            const data = <?php echo json_encode($rsvps); ?>;
            
            // Create CSV content
            let csv = 'Name,Guests,Message,Timestamp,IP Address\n';
            data.forEach(rsvp => {
                const name = `"${rsvp.name.replace(/"/g, '""')}"`;
                const guests = rsvp.guests;
                const message = `"${(rsvp.message || '').replace(/"/g, '""')}"`;
                const timestamp = rsvp.timestamp;
                const ip = rsvp.ip || 'N/A';
                csv += `${name},${guests},${message},${timestamp},${ip}\n`;
            });

            // Create download link
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'calix_baptism_rsvps_' + new Date().toISOString().split('T')[0] + '.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>
