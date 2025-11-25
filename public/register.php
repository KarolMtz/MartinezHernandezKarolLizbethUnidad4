<?php
ob_start();
session_start();
require_once('db_conexion.php');

// Redirect logged-in users away from register page
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: index.php');
    exit();
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

$errors = [];

// TUS NUEVAS CLAVES reCAPTCHA v2
$recaptcha_site_key = "6Le8dhcsAAAAAH7chpkcaB-9hDdRTHzXcKEAR5Bt";
$recaptcha_secret_key = "6Le8dhcsAAAAADkrUalJKTG20iUUQ1_B90Tk-dT0";

if (isset($_POST['register'])) {
    // CSRF token validation
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = "Error de validación del formulario.";
    }

    $name = trim($_POST['name'] ?? '');
    $secondname = trim($_POST['secondname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pssword = $_POST['pssword'] ?? '';
    $captcha = $_POST['g-recaptcha-response'] ?? '';

    // Input validation
    if (empty($name) || strlen($name) > 50) {
        $errors[] = "Nombre inválido.";
    }
    if (empty($secondname) || strlen($secondname) > 50) {
        $errors[] = "Segundo nombre inválido.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Correo electrónico inválido.";
    }
    if (empty($pssword) || strlen($pssword) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres.";
    }
    if (!preg_match('/[A-Z]/', $pssword)) {
        $errors[] = "La contraseña debe contener al menos una letra mayúscula.";
    }
    if (!preg_match('/[^a-zA-Z0-9]/', $pssword)) {
        $errors[] = "La contraseña debe contener al menos un carácter especial.";
    }

    // Verify CAPTCHA v2
    if (empty($captcha)) {
        $errors[] = "Por favor verifica que no eres un robot.";
    } else {
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret_key}&response={$captcha}");
        $responseKeys = json_decode($response, true);
        
        if (!$responseKeys["success"]) {
            $errors[] = "Error en la verificación reCAPTCHA. Por favor, inténtalo de nuevo.";
        }
    }

    if (empty($errors)) {
        // Check if email already exists
        $checkEmail = $cnnPDO->prepare("SELECT id FROM register WHERE email = :email");
        $checkEmail->bindParam(':email', $email);
        $checkEmail->execute();
        if ($checkEmail->fetch()) {
            $errors[] = "El correo electrónico ya está registrado.";
        } else {
            // Hash password
            $hashedPassword = password_hash($pssword, PASSWORD_DEFAULT);
            
            // Valores para los campos adicionales
            $recovery_code = '';
            $recovery_expiration = NULL;
            $session_token = NULL;
            $role = 'usuario';

            // Insert into database
            $sql = $cnnPDO->prepare("INSERT INTO register (name, secondname, email, pssword, recovery_code, recovery_expiration, session_token, role) 
                                   VALUES (:name, :secondname, :email, :pssword, :recovery_code, :recovery_expiration, :session_token, :role)");
            
            $sql->bindParam(':name', $name);
            $sql->bindParam(':secondname', $secondname);
            $sql->bindParam(':email', $email);
            $sql->bindParam(':pssword', $hashedPassword);
            $sql->bindParam(':recovery_code', $recovery_code);
            $sql->bindParam(':recovery_expiration', $recovery_expiration);
            $sql->bindParam(':session_token', $session_token);
            $sql->bindParam(':role', $role);

            if ($sql->execute()) {
                header("Location: login.php");
                exit();
            } else {
                $errors[] = "Error al registrar el usuario.";
            }
        }
    }
}

$csrfToken = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=FONT_NAME:weights&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/index.css" />
    <link rel="stylesheet" href="css/custom.css" />
    <link rel="stylesheet" href="css/contact.css" />
    <title>Registro</title>
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
      <a href="contact.php" tabindex="-1">Contact</a>
    </nav>
  </header>


    <div class="container-fluid py-5 mx-auto">
        <div class="contact-form-container">

            <h4>Crear Cuenta</h4>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                <div class="row">
                    <div class="col-12">
                        <label for="name">Name</label>
                        <input class="form-control" type="text" name="name" id="name" placeholder="Enter your name"
                            required maxlength="50" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label for="secondname">Second Name</label>
                        <input class="form-control" type="text" name="secondname" id="secondname"
                            placeholder="Enter your second name" required maxlength="50" value="<?php echo htmlspecialchars($_POST['secondname'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label for="email">Email Address</label>
                        <input class="form-control" type="email" name="email" id="email"
                            placeholder="Enter a valid email address" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label for="pssword">Password</label>
                        <input class="form-control" type="password" name="pssword" id="pssword"
                            placeholder="Enter password" required minlength="8">
                    </div>
                    
                    <!-- reCAPTCHA v2 VISIBLE -->
                    <div class="col-12">
                        <div class="g-recaptcha" data-sitekey="6Le8dhcsAAAAAH7chpkcaB-9hDdRTHzXcKEAR5Bt"></div>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" name="register" class="btn">Crear Cuenta</button>
                    </div>
                </div>
            </form>
            <div class="row mb-4">
                <small class="font-weight-bold">Already have an account? <a href="login.php"
                        class="text-danger">Login</a></small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <!-- reCAPTCHA v2 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>

</html>