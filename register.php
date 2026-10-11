<?php
require_once "includes/db.php";

$message = "";
$alertType = "danger";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    // Server-side validation before storing data.
    if ($full_name != "" && $email != "" && trim($password) != "" && trim($confirm_password) != "")
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $message = "Invalid email address.";
        }
        elseif (strlen($password) < 8)
        {
            $message = "Password must be at least 8 characters.";
        }
        elseif ($password != $confirm_password)
        {
            $message = "Passwords do not match.";
        }
        else
        {
            // Check if the email is already registered using a prepared statement.
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0)
            {
                $message = "Email already registered.";
            }
            else
            {
                // Hash the password before saving it.
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // New accounts are registered as students, not admins.
                $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'student')");
                $stmt->bind_param("sss", $full_name, $email, $hashed_password);

                if ($stmt->execute())
                {
                    $message = "Registration successful!";
                    $alertType = "success";
                }
                else
                {
                    $message = "Registration failed.";
                }
            }
        }
    }
}

require_once "includes/header.php";
?>

<div class="row justify-content-center mt-4">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body p-4">

                <h3 class="text-primary text-center mb-4"><i class="bi bi-person-plus-fill"></i> Create Account</h3>

                <?php if ($message != ""): ?>
                    <div class="alert alert-<?php echo $alertType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="register.php" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control">
                    </div>

                    <div class="mb-5">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-person-check-fill"></i> Register</button>
                </form>

                <p class="text-center mt-3">Already have an account? <a href="login.php">Log in here</a></p>

            </div>
        </div>
    </div>
</div>

<script src="validation.js"></script>

<?php include "includes/footer.php"; ?>