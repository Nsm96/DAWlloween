<?php
require_once __DIR__ . '/VarGlobales.php';

$mensajeError = '';
$mensajeExito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevoUsuario = trim($_POST['usuario'] ?? '');
    $nuevaPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['password_confirm'] ?? '';
    $nuevoEmail = trim($_POST['email'] ?? '');
    $nuevaPregunta = trim($_POST['pregunta'] ?? '');

    
    if (empty($nuevoUsuario) || empty($nuevaPassword) || empty($nuevoEmail) || empty($nuevaPregunta)) {
        $mensajeError = "Todos los campos son obligatorios.";
    } elseif ($nuevaPassword !== $confirmPassword) {
        $mensajeError = "Las contraseñas no coinciden.";
    } else {
        $usuariosActuales = cargarUsuariosJSON();

        $existe = false;
        foreach ($usuariosActuales as $u) {
            if (strtolower($u['usuario']) === strtolower($nuevoUsuario)) {
                $existe = true;
                break;
            }
        }

        if ($existe) {
            $mensajeError = "El usuario '{$nuevoUsuario}' ya está registrado.";
        } else {
            $nuevoRegistro = [
                'usuario'  => $nuevoUsuario,
                'password' => $nuevaPassword,
                'email'    => $nuevoEmail,
                'perfil'   => 'usuario',
                'pregunta' => $nuevaPregunta
            ];

            $usuariosActuales[] = $nuevoRegistro;

            if (guardarUsuariosJSON($usuariosActuales)) {
                $_SESSION['mimatriz'] = $usuariosActuales;
                $mensajeExito = "¡Usuario registrado con éxito! Ya puedes acceder a la plataforma.";
            } else {
                $mensajeError = "Error en el servidor al escribir en el archivo de datos.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de Usuario - DAWlloween</title>
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
        <h2>Alta de Nuevo Usuario</h2>

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

            <form action="altaUsuario.php" method="POST">
                <div class="form-group">
                    <label for="usuario">Nombre de Usuario:</label>
                    <input type="text" id="usuario" name="usuario" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirmar Contraseña:</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>

                <div class="form-group">
                    <label for="pregunta">Pregunta de Seguridad (Monstruo favorito):</label>
                    <input type="text" id="pregunta" name="pregunta" required placeholder="Ej: Vampiro, Hombre Lobo..." autocomplete="off">
                </div>

                <button type="submit" class="btn-submit">Crear Cuenta 🎃</button>
            </form>

        <?php endif; ?>
    </main>
        <footer>
        <p>DAWlloween &copy; - <a href="../aboutus.html">Sobre mí</a></p>
    </footer>

</body>
</html>