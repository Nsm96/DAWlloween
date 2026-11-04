<?php
require_once 'VarGlobales.php';


if (empty($_SESSION['currentUser'])) {
    header("Location: login.php");
    exit();
}

$mensajeFeedback = "";
$errorFeedback = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asunto = trim($_POST['asunto'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if (!empty($asunto) && !empty($mensaje)) {
        
        $emailRemitente = "";
        if (isset($_SESSION['mimatriz']) && is_array($_SESSION['mimatriz'])) {
            foreach ($_SESSION['mimatriz'] as $user) {
                if ($user['usuario'] === $_SESSION['currentUser']) {
                    $emailRemitente = $user['email'];
                    break;
                }
            }
        }

        if (empty($emailRemitente)) {
            $emailRemitente = "no-reply@dawlloween.dev";
        }

        $destinatario = "jack.skellington@dawlloween.dev"; 
        $asuntoCorreo = "DAWlloween - " . htmlspecialchars($asunto);
        
        $cuerpoMensaje = "Contenido del correo:\n" . htmlspecialchars($mensaje);

        
        $headers = "From: " . $emailRemitente . "\r\n" .
                   "Reply-To: " . $emailRemitente . "\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        
        if (@mail($destinatario, $asuntoCorreo, $cuerpoMensaje, $headers)) {
            $mensajeFeedback = "¡Mensaje enviado con éxito a Jack! Te responderá antes de medianoche. 🎃";
        } else {
            $errorFeedback = "El mensaje no pudo enviarse. Revisa la configuración de Sendmail/SMTP.";
        }

    } else {
        $errorFeedback = "Por favor, completa todos los campos del formulario.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Correo de Jack - DAWlloween</title>
    <link rel="preconnect" href="https:
    <link rel="preconnect" href="https:
    <link href="https:
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

    <header>
        <nav>
            <ul>
                <li><a href="../aboutus.html">Inicio</a></li>
                <li><a href="../recetas.html">Recetas</a></li>
                <li><a href="../juego.html">Minijuego</a></li>
                <li><a href="correo.php">Correo de Jack</a></li>
                <li><a href="login.php">Acceso</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>El Correo de Jack</h1>
        <p style="text-align: center; color: var(--color-texto-mutado); margin-bottom: 2rem;">
            Envía una lechuza digital a Jack con tus peores pesadillas. (Remitente actual: <strong><?php echo htmlspecialchars($_SESSION['currentUser']); ?></strong>)
        </p>

        <?php if (!empty($mensajeFeedback)): ?>
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                <?php echo htmlspecialchars($mensajeFeedback); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorFeedback)): ?>
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                <?php echo htmlspecialchars($errorFeedback); ?>
            </div>
        <?php endif; ?>

        <form action="correo.php" method="POST">
            <div class="form-group">
                <label for="asunto">Asunto</label>
                <input type="text" id="asunto" name="asunto" placeholder="Ej. Pesadillas en la granja de calabazas" required>
            </div>

            <div class="form-group">
                <label for="mensaje">Mensaje</label>
                <textarea id="mensaje" name="mensaje" rows="5" placeholder="Escribe tus pesadillas aquí..." required></textarea>
            </div>

            <button type="submit" class="btn">Enviar a Jack 🎃</button>
        </form>
    </main>

    <footer>
        <p>DAWlloween &copy; - <a href="../aboutus.html">Sobre mí</a></p>
    </footer>

</body>
</html>