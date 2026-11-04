<?php
require_once __DIR__ . '/VarGlobales.php';

$mensajeError = '';
$mensajeExito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioInput = trim($_POST['usuario'] ?? '');
    $respuestaSeguridad = trim($_POST['pregunta'] ?? '');
    $nuevaPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['password_confirm'] ?? '';

    if (empty($usuarioInput) || empty($respuestaSeguridad) || empty($nuevaPassword) || empty($confirmPassword)) {
        $mensajeError = "Todos los campos son obligatorios.";
    } elseif ($nuevaPassword !== $confirmPassword) {
        $mensajeError = "Las nuevas contraseñas no coinciden.";
    } else {
        $usuarios = cargarUsuariosJSON();
        $estadoValidacion = false;

        foreach ($usuarios as $index => $u) {
            $coincideUsuario = strtolower($u['usuario']) === strtolower($usuarioInput) || strtolower($u['email']) === strtolower($usuarioInput);
            
            if ($coincideUsuario) {
                if (strtolower(trim($u['pregunta'])) === strtolower($respuestaSeguridad)) {
                    $usuarios[$index]['password'] = $nuevaPassword;
                    $estadoValidacion = 'exito';
                    break;
                } else {
                    $estadoValidacion = 'error_pregunta';
                    break;
                }
            }
        }

        if ($estadoValidacion === 'exito') {
            if (guardarUsuariosJSON($usuarios)) {
                $_SESSION['mimatriz'] = $usuarios;
                $mensajeExito = "¡Contraseña modificada correctamente! Ya puedes acceder con tus nuevas credenciales.";
            } else {
                $mensajeError = "Error al actualizar el archivo de datos del servidor.";
            }
        } elseif ($estadoValidacion === 'error_pregunta') {
            $mensajeError = "La respuesta a la pregunta de seguridad es incorrecta.";
        } else {
            $mensajeError = "No se ha encontrado ningún usuario con esos datos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio de Contraseña - DAWlloween</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

    <header>
        <nav>
            <ul>
                <li><a href="../index.html">Inicio</a></li>
                <li><a href="login.php">Acceso</a></li>
            </ul>
        </nav>
    </header>

    <main class="form-container">
        <h2>Cambiar Contraseña</h2>

        <?php if (!empty($mensajeError)): ?>
            <div class="alert alert-error">⚠️ <?php echo htmlspecialchars($mensajeError); ?></div>
        <?php endif; ?>

        <?php if (!empty($mensajeExito)): ?>
            <div class="alert alert-success">
                ✅ <?php echo htmlspecialchars($mensajeExito); ?>
                <p style="margin-top: 15px;">
                    <a href="login.php" class="btn-link">Ir al Login 🔑</a>
                </p>
            </div>
        <?php else: ?>

            <form action="cambioPass.php" method="POST">
                <div class="form-group">
                    <label for="usuario">Usuario o Correo Electrónico:</label>
                    <input type="text" id="usuario" name="usuario" required autocomplete="off" value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="pregunta">Pregunta de Seguridad (Monstruo favorito):</label>
                    <input type="text" id="pregunta" name="pregunta" required placeholder="Ej: Vampiro, Hombre Lobo..." autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="password">Nueva Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirmar Nueva Contraseña:</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>

                <button type="submit" class="btn-submit">Actualizar Contraseña 🧙‍♂️</button>
            </form>

        <?php endif; ?>
    </main>

    <script src="../js/main.js"></script>
</body>
</html>