<?php
session_start();

require_once "includes/db.php";

if (isset($_SESSION["user_id"]) && isset($_SESSION["role"]))
{
    if ($_SESSION["role"] == "admin" || $_SESSION["role"] == "student")
    {
        header("Location: index.php");
        exit();
    }
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // Server-side validation for login credentials.
    if ($email != "" && trim($password) != "")
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $message = "Please enter a valid email address.";
        }
        else
        {
            // Use a prepared statement to retrieve the user securely.
            $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            // Verify the hashed password and user role before allowing login.
            if ($user && password_verify($password, $user["password"]) && ($user["role"] == "admin" || $user["role"] == "student"))
            {
                // Regenerate the session ID to prevent session fixation attacks.
                session_regenerate_id(true);

                // Store user details and role in the session for access control across pages.
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["full_name"] = $user["full_name"];
                $_SESSION["role"] = $user["role"];
                $_SESSION["login_success"] = true;

                header("Location: index.php");
                exit();
            }
            else
            {
                $message = "Invalid password.";
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

                <h3 class="text-primary text-center mb-4"><i class="bi bi-box-arrow-in-right"></i> Login</h3>

                <?php if ($message != ""): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control">
                    </div>

                    <div class="mb-5">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right"></i> Login</button>
                </form>

                <p class="text-center mt-3">Don't have an account? <a href="register.php">Register here</a>
                </p>

            </div>
        </div>
    </div>
</div>

<script src="validation.js"></script>

<?php include "includes/footer.php"; ?>