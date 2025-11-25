<?php
ob_start();
session_start();
require_once('db_conexion.php');

// Redirigir si ya está logueado (Esto es una validación, pero la dejamos)
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: index.php');
    exit();
}
$errors = [];

if (isset($_POST['login'])) {
    

    $email = trim($_POST['email'] ?? '');
    $pssword = $_POST['pssword'] ?? '';


    $query = $cnnPDO->prepare('SELECT * FROM register WHERE email=:email');
    $query->bindParam(':email', $email);
    $query->execute();
    $campo = $query->fetch();

    if ($campo && password_verify($pssword, $campo['pssword'])) {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        // Generate unique session token
        $sessionToken = bin2hex(random_bytes(32));

        // Store session token in database
        $updateTokenQuery = $cnnPDO->prepare('UPDATE register SET session_token = :sessionToken WHERE id = :userId');
        $updateTokenQuery->bindParam(':sessionToken', $sessionToken);
        $updateTokenQuery->bindParam(':userId', $campo['id']);
        $updateTokenQuery->execute();

        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $campo['id'] ?? null;
        $_SESSION['user_name'] = $campo['name'] ?? '';
        $_SESSION['user_role'] = $campo['role'] ?? 'usuario'; // default role if not set
        $_SESSION['session_token'] = $sessionToken;

        // Role-based redirection
        switch ($_SESSION['user_role']) {
            case 'administrador':
                header("Location: admin_dashboard.php");
                break;
            case 'editor':
                header("Location: editor_dashboard.php");
                break;
            case 'usuario':
            default:
                header("Location: index.php");
                break;
        }
        exit();
    } else {
        // Si la autenticación falla, este es el único error posible ahora.
        $errors[] = "Correo o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=FONT_NAME:weights&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/index.css" />
    <link rel="stylesheet" href="css/custom.css" />
    <link rel="stylesheet" href="css/contact.css" />
</head>

<body>

          <header>
    <div class="nav-container container" role="navigation" aria-label="Main navigation">
      <button aria-label="Abrir menú" aria-expanded="false" aria-controls="mobile-menu"
        class="hamburger-btn material-icons" id="hamburger-btn">
        menu
      </button>
      <div class="logo" aria-label="1004 Cake Boutique">
      <a href="index.php"><img src="img/toram.png" alt="" width="70" height="70">
        1004<span>Cake Boutique</span>
      </div></a>
      <nav class="desktop-nav" aria-label="Navegación principal">

      </nav>
    </div>

    <nav id="mobile-menu" class="mobile-nav" aria-label="Menú móvil">
      <a href="instore.php" tabindex="-1">In Store</a>
      <?php if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true): ?>
        <a href="register.php" tabindex="-1">Register</a>
        <a href="login.php" tabindex="-1">Login</a>
      <?php else: ?>
        <a href="logout.php" tabindex="-1">Logout</a> <!-- Logout link -->
      <?php endif; ?>
      <a href="contact.php" tabindex="-1">Contacto</a>
    </nav>
  </header>

    <div id="main-wrapper" class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="contact-form-container">
                    <div class="p-5">
                        <div class="mb-5">
                            <h3 class="h4 font-weight-bold text-theme">Login</h3>
                        </div>

                        <h6 class="h5 mb-0">Enter your email address and password to access admin panel.</h6>

                        <?php if (!empty($errors)) : ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php foreach ($errors as $error) : ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="post" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>" />
                            <div class="form-group">
                                <label for="exampleInputEmail1">Email address</label>
                                <input type="email" name="email" class="form-control" id="exampleInputEmail1" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />
                            </div>
                            <div class="form-group mb-5">
                                <label for="exampleInputPassword1">Password</label>
                                <input type="password" name="pssword" class="form-control" id="exampleInputPassword1" required minlength="8" pattern="(?=.*[A-Z])(?=.*\W).{8,}" />
                                <small class="form-text text-muted">Password must be at least 8 characters, include 1 uppercase letter and 1 special character.</small>
                            </div>
                            <button type="submit" name="login" class="btn">Login</button>
                            <div>
                                <a href="recuperar.php" class="forgot-link float-right text-primary">Forgot password?</a>
                            </div>
                        </form>
                    </div>
                </div>

                <p class="text-muted text-center mt-3 mb-0">Don't have an account? <a href="register.php" class="text-primary ml-1">register</a></p>
            </div>
        </div>
    </div>
</body>

</html>
