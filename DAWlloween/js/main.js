document.addEventListener('DOMContentLoaded', () => {


    const paginasProtegidas = ['recetas.html', 'juego.html', 'correojack.html'];

    let paginaActual = window.location.pathname.split('/').pop();
    if (!paginaActual || paginaActual === '') {
        paginaActual = 'aboutus.html';
    }


    fetch('php/mostrar_usuarios.php')
        .then(response => response.json())
        .then(data => {
            const usuarioAutenticado = data && data.usuario;

            if (usuarioAutenticado) {

                localStorage.setItem('usuario', data.usuario);

                pintarHeaderUsuario(data.usuario);

                gestionarVisibilidad(['recetas', 'recetas.html', 'juego.html', 'correojack.html', '.solo-login'], true);
                gestionarVisibilidad(['login.php', '.solo-invitado'], false);

                document.body.classList.add('usuario-logueado');

            } else {

                localStorage.removeItem('usuario');

                const badgeExistente = document.querySelector('.header-user');
                if (badgeExistente) badgeExistente.remove();

                gestionarVisibilidad(['recetas.html', 'juego.html', 'correojack.html', '.solo-login'], false);
                gestionarVisibilidad(['login.php', '.solo-invitado'], true);

                if (paginasProtegidas.includes(paginaActual)) {
                    alert('🎃 ¡¡Boof!! Necesitas iniciar sesión para acceder a esta sección.');
                    window.location.href = 'php/login.php';
                }
            }
        })
        .catch(err => {
            console.error('Error al comprobar la sesión:', err);

            if (paginasProtegidas.includes(paginaActual)) {
                window.location.href = 'php/login.php';
            }
        });

    const formJack = document.getElementById('form-correo-jack');
    const alertaRespuesta = document.getElementById('mensaje-correo');

    if (formJack) {
        formJack.addEventListener('submit', (e) => {
            e.preventDefault();

            const formData = new FormData(formJack);
            const btnEnviar = formJack.querySelector('button[type="submit"]');
            if (btnEnviar) btnEnviar.disabled = true;

            fetch('php/correo.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.text();
                })
                .then(data => {
                    alertaRespuesta.className = 'alert alert-success';
                    alertaRespuesta.textContent = `¡Mensaje enviado con éxito a Jack! Te responderá antes de medianoche. 🎃`;
                    alertaRespuesta.style.display = 'block';
                    formJack.reset();
                })
                .catch(error => {
                    console.error('Error al enviar:', error);
                    alertaRespuesta.className = 'alert alert-error';
                    alertaRespuesta.textContent = 'Hubo un error al entregar el mensaje. Inténtalo de nuevo.';
                    alertaRespuesta.style.display = 'block';
                })
                .finally(() => {
                    if (btnEnviar) btnEnviar.disabled = false;
                });
        });
    }
});


function pintarHeaderUsuario(nombreUsuario) {
    let container = document.getElementById('user-badge-container');
    const header = document.querySelector('header');

    if (!container && header) {
        container = document.createElement('div');
        container.className = 'header-user solo-login';
        container.id = 'user-badge-container';
        header.appendChild(container);
    }

    if (container) {
        container.style.display = 'inline-block';
        container.innerHTML = `
            <span class="user-badge">
                🦇 <strong>${nombreUsuario.toUpperCase()}</strong> 🦇
                <span style="opacity: 0.5; margin: 0 4px;">|</span>
                <a href="php/logout.php" class="btn-logout-badge" title="Cerrar sesión">Huir 🚪</a>
            </span>
        `;
    }
}

function gestionarVisibilidad(elementos, mostrar) {
    const estadoDisplay = mostrar ? '' : 'none';

    elementos.forEach(clave => {
        if (clave.startsWith('.')) {
            document.querySelectorAll(clave).forEach(el => {
                el.style.display = estadoDisplay;
            });
            return;
        }

        document.querySelectorAll('a').forEach(enlace => {
            const texto = enlace.textContent.trim().toLowerCase();
            const href = (enlace.getAttribute('href') || '').toLowerCase();

            if (texto.includes(clave) || href.includes(clave)) {
                const liPadre = enlace.closest('li');
                const objetivo = liPadre || enlace;
                objetivo.style.display = estadoDisplay;
            }
        });
    });
}