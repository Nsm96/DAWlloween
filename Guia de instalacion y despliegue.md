# 🎃 DAWlloween - Guía Técnico-Administrativa de Instalación y Despliegue

Este documento detalla los pasos de configuración de servidor, entorno PHP, permisos del sistema de archivos y servicio de correo necesarios para el despliegue del proyecto **DAWlloween** sobre un servidor web Apache.

---

## 1. Requisitos del Sistema y Entorno

- **Servidor Web:** Apache 2.4+ (con módulo `mod_rewrite` activo).
- **Intérprete:** PHP 7.4 u 8.x con extensiones activas (`json`, `mbstring`, `openssl`).
- **Navegadores soportados:** Google Chrome, Mozilla Firefox, Safari, Edge (Compatibilidad con HTML5, CSS Flexbox/Grid y JavaScript ES6+).

---

## 2. Configuración de Apache (`httpd.conf` / `000-default.conf`)

Asegúrate de que la raíz de tu DocumentRoot apunte al directorio principal del proyecto o que la estructura en `var/www/html/` respete la arquitectura de carpetas:

```apache
<VirtualHost *:80>
    ServerName dawlloween.local
    DocumentRoot /var/www/html/dawlloween

    <Directory /var/www/html/dawlloween>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/dawlloween_error.log
    CustomLog ${APACHE_LOG_DIR}/dawlloween_access.log combined
</VirtualHost>
```

---

## 3. Estructura de Directorios del Proyecto

```
dawlloween/
├── aboutus.html          # Portada principal y presentación del creador
├── recetas.html          # Listado de recetas (zona privada)
├── juego.html            # Minijuego interactivo (zona privada)
├── correojack.html       # Formulario para enviar mensajes a Jack (zona privada)
├── css/
│   └── styles.css        # Estilos consolidados (temática neón/dark + responsive)
├── js/
│   └── main.js           # Lógica central cliente, inyección badge y comprobaciones
├── img/
│   └── fotoNico.jpg      # Fotografía de perfil para Sobre Mí
└── php/
    ├── login.php         # Procesamiento de autenticación y sincronización localStorage
    ├── logout.php        # Destrucción de sesión PHP y limpieza de localStorage
    ├── altaUsuario.php   # Registro e incorporación a la estructura de usuarios
    ├── correo.php        # Gestor del envío de mensajes a Jack
    ├── VarGlobales.php   # Variables globales y estructura temporal de datos
    └── mostrar_usuarios.php # Endpoint JSON para verificación de estado de sesión
```

---

## 4. Permisos del Sistema de Archivos (Linux / macOS)

Para garantizar la persistencia de usuarios e impedir bloqueos de escritura durante el registro desde la web, aplica los siguientes permisos desde la terminal en el servidor:

```bash
# Asignar propietario al usuario de Apache (www-data en Debian/Ubuntu)
sudo chown -R www-data:www-data /var/www/html/dawlloween

# Otorgar permisos de lectura/escritura/ejecución de carpetas
sudo find /var/www/html/dawlloween -type d -exec chmod 755 {} \;

# Otorgar permisos de lectura/escritura de archivos
sudo find /var/www/html/dawlloween -type f -exec chmod 644 {} \;

# Asegurar permisos de escritura si la matriz guarda datos en un archivo JSON/txt local
chmod 777 /var/www/html/dawlloween/php/
```

---

## 5. Configuración del Servicio de Correo (`php/correo.php`)

El envío de correo de la web (**El Correo de Jack**) procesa las peticiones enviadas desde `correojack.html` mediante `fetch` (FormData).

### A. Funcionamiento Interno
1. `correojack.html` envía los datos vía `POST` al script `php/correo.php`.
2. `php/correo.php` recibe las variables Sanitizadas (`nombre`, `email`, `mensaje`).
3. Retorna un estado HTTP 200 con respuesta plana o JSON en caso de éxito.

### B. Configuración de `php.ini` para correo local/SMTP
Para que la función nativa `mail()` de PHP procese los correos salientes correctamente:

```ini
; Para Linux (usando sendmail o postfix instalado localmente):
sendmail_path = /usr/sbin/sendmail -t -i

; Para Windows (usando servidor SMTP externo o XAMPP):
SMTP = smtp.tuservidor.com
smtp_port = 587
sendmail_from = no-reply@dawlloween.com
```

---

## 6. Sincronización de Sesión (PHP + LocalStorage)

1. **Sesión en Servidor:** Gestionada por `session_start()` y la matriz `$_SESSION['mimatriz']` / `$_SESSION['currentUser']`.
2. **Sincronización en Cliente:** En `login.php` y `altaUsuario.php`, se ejecuta un snippet de JavaScript al validar credenciales:
   ```javascript
   localStorage.setItem('usuario', nombreUsuario);
   ```
3. **Cierre de Sesión:** `php/logout.php` ejecuta `session_destroy()` en el servidor y `localStorage.removeItem('usuario')` en el navegador antes de redirigir a `aboutus.html`.