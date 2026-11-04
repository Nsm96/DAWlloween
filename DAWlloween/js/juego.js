const simbolos = [
    'img/calabaza.png',
    'img/fantasma.png',
    'img/sombrero.png',
    'img/pocion.png',
    'img/gato.png',
    'img/calavera.png'
];

let cartas = [];
let primeraCarta = null;
let segundaCarta = null;
let bloqueoTablero = false;
let intentos = 0;
let parejasEncontradas = 0;

let tiempo = 0;
let intervaloTiempo = null;
let comodinUsado = false;

function formatearTiempo(totalSegundos) {
    if (totalSegundos < 60) {
        return `${totalSegundos}s`;
    }
    const mins = Math.floor(totalSegundos / 60);
    const segs = totalSegundos % 60;
    
    const minTexto = mins === 1 ? '1 minuto' : `${mins} minutos`;
    const segTexto = segs === 1 ? '1 segundo' : `${segs} segundos`;
    
    return segs === 0 ? minTexto : `${minTexto} y ${segTexto}`;
}

function iniciarJuego() {
    const tablero = document.getElementById('tablero-memoria');
    const mensajeWin = document.getElementById('mensaje-victoria');
    const btnComodin = document.getElementById('btn-comodin');

    if (!tablero) return;

    tablero.innerHTML = '';
    if (mensajeWin) mensajeWin.style.display = 'none';
    intentos = 0;
    parejasEncontradas = 0;
    tiempo = 0;
    comodinUsado = false;

    const elIntentos = document.getElementById('contador-intentos');
    const elTiempo = document.getElementById('tiempo');
    if (elIntentos) elIntentos.textContent = intentos;
    if (elTiempo) elTiempo.textContent = formatearTiempo(tiempo);

    if (btnComodin) {
        btnComodin.disabled = false;
        btnComodin.textContent = '👁️ Ojo de Bruja (+3 intentos)';
    }

    clearInterval(intervaloTiempo);
    intervaloTiempo = setInterval(() => {
        tiempo++;
        if (elTiempo) elTiempo.textContent = formatearTiempo(tiempo);
    }, 1000);

    cartas = [...simbolos, ...simbolos];
    cartas.sort(() => Math.random() - 0.5);

    cartas.forEach((rutaImagen) => {
        const card = document.createElement('div');
        card.classList.add('card');
        card.dataset.value = rutaImagen;
        card.innerHTML = '❓';
        card.addEventListener('click', voltearCarta);
        tablero.appendChild(card);
    });
}

function voltearCarta() {
    if (bloqueoTablero || this === primeraCarta || this.classList.contains('matched')) return;

    this.classList.add('flipped');
    this.innerHTML = `<img src="${this.dataset.value}" alt="Carta">`;

    if (!primeraCarta) {
        primeraCarta = this;
        return;
    }

    segundaCarta = this;
    intentos++;
    const elIntentos = document.getElementById('contador-intentos');
    if (elIntentos) elIntentos.textContent = intentos;

    comprobarPareja();
}

function comprobarPareja() {
    const esIgual = primeraCarta.dataset.value === segundaCarta.dataset.value;

    if (esIgual) {
        marcarEmparejadas();
    } else {
        desvoltearCartas();
    }
}

function marcarEmparejadas() {
    primeraCarta.classList.add('matched');
    segundaCarta.classList.add('matched');
    parejasEncontradas++;

    resetearSeleccion();

    if (parejasEncontradas === simbolos.length) {
        clearInterval(intervaloTiempo);
        const mensajeWin = document.getElementById('mensaje-victoria');
        if (mensajeWin) {
            mensajeWin.textContent = `¡Felicidades! Has completado el ritual en ${intentos} intentos y un tiempo total de ${formatearTiempo(tiempo)}.`;
            mensajeWin.style.display = 'block';
        }
    }
}

function desvoltearCartas() {
    bloqueoTablero = true;
    primeraCarta.classList.add('error');
    segundaCarta.classList.add('error');

    setTimeout(() => {
        primeraCarta.innerHTML = '❓';
        segundaCarta.innerHTML = '❓';
        primeraCarta.classList.remove('flipped', 'error');
        segundaCarta.classList.remove('flipped', 'error');
        resetearSeleccion();
    }, 800);
}

function resetearSeleccion() {
    [primeraCarta, segundaCarta] = [null, null];
    bloqueoTablero = false;
}

function usarComodin() {
    if (comodinUsado || bloqueoTablero) return;

    comodinUsado = true;
    bloqueoTablero = true;

    const btnComodin = document.getElementById('btn-comodin');
    if (btnComodin) {
        btnComodin.disabled = true;
        btnComodin.textContent = '👁️ Hechizo Agotado';
    }

    intentos += 3;
    const elIntentos = document.getElementById('contador-intentos');
    if (elIntentos) elIntentos.textContent = intentos;

    const ocultas = document.querySelectorAll('.card:not(.matched)');
    ocultas.forEach(card => {
        card.innerHTML = `<img src="${card.dataset.value}" alt="Carta">`;
        card.classList.add('flipped');
    });

    setTimeout(() => {
        ocultas.forEach(card => {
            if (card !== primeraCarta) {
                card.innerHTML = '❓';
                card.classList.remove('flipped');
            }
        });
        bloqueoTablero = false;
    }, 1200);
}

window.onload = iniciarJuego;