<?php
session_start();
require_once 'db_connect.php';  // Loads $host, $username, etc.

// Authentication check: Only logged-in admins can access
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_auth.php');
    exit();
}

// Create MySQLi connection
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    $_SESSION['error'] = 'Database connection failed. Please try again.';
    header('Location: admin_dashboard.php');
    exit();
}

// Disable strict errors to handle manually
mysqli_report(MYSQLI_REPORT_OFF);

// Handle approval or rejection actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id'] ?? 0);
    $action = trim(strip_tags($_POST['action'] ?? ''));

    if ($user_id > 0 && in_array($action, ['approve', 'reject'], true)) {
        if ($action === 'approve') {
            $stmt = $conn->prepare("UPDATE admin_users SET is_active = 1 WHERE id = ? AND is_active = 0");
            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = "User  ID $user_id approved successfully.";
                } else {
                    $_SESSION['error'] = 'Approval failed. Please try again.';
                }
                $stmt->close();
            }
        } else if ($action === 'reject') {
            // Delete user on rejection (with safety check)
            $stmt = $conn->prepare("DELETE FROM admin_users WHERE id = ? AND is_active = 0");
            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = "User  ID $user_id rejected and deleted.";
                } else {
                    $_SESSION['error'] = 'Rejection failed. Please try again.';
                }
                $stmt->close();
            }
        }
        $conn->close();
        header('Location: admin_approve_users.php');
        exit();
    } else {
        $_SESSION['error'] = 'Invalid action or user ID.';
    }
}

// Fetch all pending users (inactive admins)
$stmt = $conn->prepare("SELECT id, name, email, created_at FROM admin_users WHERE is_active = 0 ORDER BY created_at ASC");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    $pending_users = [];
    while ($row = $result->fetch_assoc()) {
        $pending_users[] = $row;
    }
    $stmt->close();
} else {
    $pending_users = [];
    $_SESSION['error'] = 'Failed to fetch pending users.';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin - Approve Users</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fff;
            color: #333;
            margin: 2rem;
            line-height: 1.5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #FF8800;
        }
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success {
            background-color: #4CAF50;
            color: #fff;
        }
        .error {
            background-color: #f44336;
            color: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 15px;
            overflow: hidden;
        }
        thead {
            background-color: #F8F8F8;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ccc;
            font-size: 1rem;
        }
        th {
            font-weight: 600;
            color: #FF8800;
        }
        tbody tr:hover {
            background-color: #F8F8F8;
            transition: background-color 0.15s ease-in-out;
        }
        form {
            display: inline;
        }
        .btn {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            min-height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin: 0 0.25rem;
        }
        .btn-approve {
            background-color: #4CAF50;
            color: #fff;
        }
        .btn-approve:hover {
            background-color: #43a047;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        .btn-reject {
            background-color: #f44336;
            color: #fff;
        }
        .btn-reject:hover {
            background-color: #d32f2f;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        .back-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: #FF8800;
            text-decoration: none;
            font-weight: 500;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            body { margin: 1rem; }
            table, thead, tbody, th, td, tr {
                display: block;
            }
            thead tr { display: none; }
            tbody tr {
                margin-bottom: 2rem;
                border: 1px solid #ccc;
                border-radius: 15px;
                padding: 1rem;
            }
            tbody td {
                padding-left: 50%;
                position: relative;
                text-align: right;
                font-size: 0.875rem;
            }
            tbody td::before {
                content: attr(data-label);
                position: absolute;
                left: 1rem;
                width: 45%;
                padding-left: 0.5rem;
                font-weight: 600;
                text-align: left;
                color: #FF8800;
            }
            .btn {
                width: 100%;
                margin-top: 0.5rem;
                margin: 0.5rem 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin_dashboard.php" class="back-link">&larr; Back to Dashboard</a>
        <h1>Pending User Approvals</h1>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="message success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="message error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (empty($pending_users)): ?>
            <p>No users pending approval.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        
                        <th>Registered At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_users as $user): ?>
                        <tr>
                            <td data-label="ID"><?= htmlspecialchars($user['id']) ?></td>
                            <td data-label="Name"><?= htmlspecialchars($user['name']) ?></td>
                            <td data-label="Email"><?= htmlspecialchars($user['email']) ?></td>
                    
                            <td data-label="Registered At"><?= htmlspecialchars($user['created_at']) ?></td>
                            <td data-label="Actions">
                                <form method="POST" onsubmit="return confirm('Approve this user?');" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']) ?>" />
                                    <button type="submit" name="action" value="approve" class="btn btn-approve">Approve</button>
                                </form>
                                <form method="POST" onsubmit="return confirm('Reject and delete this user?');" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']) ?>" />
                                    <button type="submit" name="action" value="reject" class="btn btn-reject">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>