<?php
include "config.php";
session_start();

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $stmt = $connection->prepare("SELECT driver_id FROM driver WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($driver_id);
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        $stmt->close();

        if ($new_password === $confirm_password) {
            if (strlen($new_password) < 6) {
                echo "<script>alert('Password must be at least 6 characters.'); window.location.href='forgot_password.php';</script>";
                exit;
            }

            $hashed_password = md5($new_password);
            
            $update_stmt = $connection->prepare("UPDATE driver SET password = ? WHERE driver_id = ?");
            $update_stmt->bind_param("si", $hashed_password, $driver_id);
            
            if ($update_stmt->execute()) {
                $_SESSION['driver_id'] = $driver_id;
                echo "<script>alert('Password successfully reset.'); window.location.href='index.php';</script>";
            } else {
                echo "<script>alert('Error updating password.'); window.location.href='forgot_password.php';</script>";
            }
            $update_stmt->close();
        } else {
            echo "<script>alert('Password and confirmation do not match.'); window.location.href='forgot_password.php';</script>";
        }
    } else {
        echo "<script>alert('Username not found.'); window.location.href='forgot_password.php';</script>";
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #000000;
            --secondary-color: #333333;
            --background-color: #ffffff;
            --text-color: #000000;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        body {
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Helvetica Neue', Arial, sans-serif;
            color: var(--text-color);
        }

        .reset-password-container {
            background: #ffffff;
            padding: 2.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 450px;
            position: relative;
            border: 1px solid #e0e0e0;
        }

        .reset-password-container h2 {
            color: var(--text-color);
            font-weight: 600;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 1.8rem;
            letter-spacing: 0.5px;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: var(--border-radius);
            padding: 12px 16px;
            transition: all 0.3s ease;
            font-size: 1rem;
            background: #ffffff;
            color: var(--text-color);
        }

        .form-control:focus {
            border-color: var(--text-color);
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }

        .btn-custom {
            background: var(--primary-color);
            color: #ffffff;
            border: none;
            border-radius: var(--border-radius);
            padding: 12px 24px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-custom:hover {
            background: var(--secondary-color);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .btn-back {
            color: var(--text-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            margin-top: 1.5rem;
            transition: all 0.3s ease;
            padding: 8px 0;
        }

        .btn-back:hover {
            color: var(--secondary-color);
            transform: translateX(-5px);
        }

        .password-info {
            font-size: 0.85rem;
            color: var(--text-color);
            margin-top: 0.5rem;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-color);
            cursor: pointer;
            z-index: 10;
        }

        .input-icon:hover {
            color: var(--secondary-color);
        }

        .alert {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            display: none;
        }

        .form-divider {
            height: 1px;
            background: #e0e0e0;
            margin: 1.5rem 0;
        }

        @media (max-width: 576px) {
            .reset-password-container {
                margin: 1rem;
                padding: 1.5rem;
            }
        }

        .btn-custom.loading {
            position: relative;
            color: transparent;
        }

        .btn-custom.loading::after {
            content: "";
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin: -10px 0 0 -10px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #ffffff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<div class="reset-password-container">
    <h2>Reset Password</h2>
    <div class="alert" id="errorAlert"></div>
    
    <form action="forgot_password.php" method="POST" onsubmit="return validateForm()">
        <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <div class="input-group">
                <input type="text" name="username" id="username" class="form-control" 
                       placeholder="Enter your username" required>
                <span class="input-icon">
                    <i class="fas fa-user"></i>
                </span>
            </div>
        </div>

        <div class="form-divider"></div>

        <div class="form-group">
            <label for="new_password" class="form-label">New Password</label>
            <div class="input-group">
                <input type="password" name="new_password" id="new_password" 
                       class="form-control" placeholder="Enter new password" required>
                <span class="input-icon" onclick="togglePassword('new_password')">
                    <i class="fas fa-eye" id="new_password_toggle"></i>
                </span>
            </div>
            <div class="password-info">Password must be at least 6 characters</div>
        </div>

        <div class="form-group">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <div class="input-group">
                <input type="password" name="confirm_password" id="confirm_password" 
                       class="form-control" placeholder="Confirm new password" required>
                <span class="input-icon" onclick="togglePassword('confirm_password')">
                    <i class="fas fa-eye" id="confirm_password_toggle"></i>
                </span>
            </div>
        </div>

        <button type="submit" name="submit" class="btn btn-custom" id="submitBtn">
            <i class="fas fa-lock me-2"></i>Reset Password
        </button>

        <a href="index.php" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Home</span>
        </a>
    </form>
</div>

<script>
function validateForm() {
    const password = document.getElementById("new_password").value;
    const confirm = document.getElementById("confirm_password").value;
    const submitBtn = document.getElementById("submitBtn");
    
    if (password.length < 6) {
        showError("Password must be at least 6 characters!");
        return false;
    }
    
    if (password !== confirm) {
        showError("Password and confirmation do not match!");
        return false;
    }
    
    submitBtn.classList.add("loading");
    return true;
}

function showError(message) {
    const errorAlert = document.getElementById("errorAlert");
    errorAlert.textContent = message;
    errorAlert.style.display = "block";
    
    setTimeout(() => {
        errorAlert.style.display = "none";
    }, 3000);
}

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(`${inputId}_toggle`);
    
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>

</body>
</html>