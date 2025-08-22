<?php
require_once 'includes/db_con.php';
$page_title = "Sports Registration System";
require_once 'templates/header.php';
?>
<style>
    .hero-section {
        background: linear-gradient(to right, #6a11cb, #2575fc);
        color: white;
        padding: 100px 0;
        text-align: center;
    }
    .hero-section h1 {
        font-size: 4rem;
        font-weight: bold;
    }
    .hero-section p {
        font-size: 1.5rem;
        margin-bottom: 30px;
    }
    .features-section {
        padding: 80px 0;
    }
    .feature {
        text-align: center;
        margin-bottom: 40px;
    }
    .feature i {
        font-size: 3rem;
        color: #6a11cb;
        margin-bottom: 20px;
    }
</style>

<div class="hero-section">
    <div class="container">
        <h1>Welcome to the Sports Registration System!</h1>
        <p>Your one-stop solution for managing sports registrations, teams, and players.</p>
        <a class="btn btn-light btn-lg" href="register.php">Register Now</a>
        <a class="btn btn-outline-light btn-lg" href="admin/login.php">Login</a>
    </div>
</div>

<div class="features-section">
    <div class="container">
        <h2 class="text-center mb-5">Features</h2>
        <div class="row">
            <div class="col-md-4 feature">
                <i class="fas fa-user-plus"></i>
                <h3>Player Registration</h3>
                <p>Easily register new players for various sports and teams.</p>
            </div>
            <div class="col-md-4 feature">
                <i class="fas fa-users"></i>
                <h3>Team Management</h3>
                <p>Create and manage teams, assign coaches, and view team rosters.</p>
            </div>
            <div class="col-md-4 feature">
                <i class="fas fa-cogs"></i>
                <h3>Admin Dashboard</h3>
                <p>A powerful dashboard for administrators to manage the entire system.</p>
            </div>
        </div>
    </div>
</div>
<?php require_once 'templates/footer.php'; ?>