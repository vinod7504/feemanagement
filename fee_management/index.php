<?php
require_once 'config/init.php';

// Redirect if already logged in
if(isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$page_title = 'Login';
require_once 'includes/header.php';
?>

<div class="page-container">
    <div class="content-card">
        <h2 class="page-title text-center">Admin Login</h2>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="error">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form action="includes/process_login.php" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 