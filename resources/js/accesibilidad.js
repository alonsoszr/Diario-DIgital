/*
 * Menú de accesibilidad: guarda las preferencias de cada persona en su navegador
 * (localStorage) y las aplica como clases sobre <html>.
 */
const CLAVE = 'accesibilidad.v1';
const TAMANOS = [100, 112, 125, 150, 175];
const OPCIONES = ['contraste', 'fuente', 'espaciado', 'enlaces', 'sinAnimaciones', 'cursor', 'grises'];

const raiz = document.documentElement;
let estado = { tamano: 0, tema: 'auto', ...Object.fromEntries(OPCIONES.map((o) => [o, false])) };

function leer() {
    try {
        estado = { ...estado, ...JSON.parse(localStorage.getItem(CLAVE) || '{}') };
    } catch (e) { /* sin almacenamiento: se usan los valores por defecto */ }
}

function guardar() {
    try { localStorage.setItem(CLAVE, JSON.stringify(estado)); } catch (e) { /* ignorar */ }
}

const oscuroSistema = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

function esOscuro() {
    return estado.tema === 'oscuro' || (estado.tema === 'auto' && !!oscuroSistema && oscuroSistema.matches);
}

function aplicar() {
    raiz.classList.toggle('tema-oscuro', esOscuro());
    document.querySelectorAll('[data-tema]').forEach((btn) => {
        btn.setAttribute('aria-pressed', String(btn.dataset.tema === estado.tema));
    });
    document.querySelectorAll('[data-tema-toggle]').forEach((btn) => {
        btn.setAttribute('aria-pressed', String(esOscuro()));
        btn.querySelector('[data-tema-texto]').textContent = esOscuro() ? 'Modo claro' : 'Modo oscuro';
    });
    raiz.style.fontSize = TAMANOS[estado.tamano] + '%';
    OPCIONES.forEach((o) => raiz.classList.toggle('a11y-' + o, !!estado[o]));

    document.querySelectorAll('[data-a11y-toggle]').forEach((btn) => {
        btn.setAttribute('aria-pressed', String(!!estado[btn.dataset.a11yToggle]));
    });
    const nivel = document.getElementById('a11y-nivel');
    if (nivel) nivel.textContent = TAMANOS[estado.tamano] + '%';
    const menos = document.querySelector('[data-a11y-size="menos"]');
    const mas = document.querySelector('[data-a11y-size="mas"]');
    if (menos) menos.disabled = estado.tamano === 0;
    if (mas) mas.disabled = estado.tamano === TAMANOS.length - 1;
}

function anunciar(texto) {
    const zona = document.getElementById('a11y-anuncio');
    if (zona) zona.textContent = texto;
}

/* ---------- lectura en voz alta (SpeechSynthesis del navegador) ---------- */
let leyendo = false;
function detenerVoz() {
    if ('speechSynthesis' in window) window.speechSynthesis.cancel();
    leyendo = false;
    const b = document.querySelector('[data-a11y-leer]');
    if (b) b.setAttribute('aria-pressed', 'false');
}

function alternarVoz() {
    if (!('speechSynthesis' in window)) {
        anunciar('Tu navegador no admite lectura en voz alta.');
        return;
    }
    if (leyendo) { detenerVoz(); anunciar('Lectura detenida.'); return; }

    const seleccion = String(window.getSelection() || '').trim();
    const principal = document.getElementById('contenido');
    const texto = seleccion || (principal ? principal.innerText : document.body.innerText);
    const voz = new SpeechSynthesisUtterance(texto);
    voz.lang = document.documentElement.lang || 'es';
    voz.onend = detenerVoz;
    leyendo = true;
    document.querySelector('[data-a11y-leer]')?.setAttribute('aria-pressed', 'true');
    window.speechSynthesis.speak(voz);
}

/* ---------- inicialización ---------- */
function iniciar() {
    leer();
    estado.tamano = Math.min(Math.max(estado.tamano | 0, 0), TAMANOS.length - 1);
    aplicar();

    //botones de tema (en el menú y en la cabecera)
    document.querySelectorAll('[data-tema]').forEach((btn) => {
        btn.addEventListener('click', () => {
            estado.tema = btn.dataset.tema;
            guardar(); aplicar();
            anunciar('Tema ' + btn.dataset.nombre + ' seleccionado.');
        });
    });
    document.querySelectorAll('[data-tema-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            estado.tema = esOscuro() ? 'claro' : 'oscuro';
            guardar(); aplicar();
            anunciar(esOscuro() ? 'Modo oscuro activado.' : 'Modo claro activado.');
        });
    });
    if (oscuroSistema) oscuroSistema.addEventListener('change', aplicar);

    const abrir = document.getElementById('a11y-abrir');
    const panel = document.getElementById('a11y-panel');
    if (!abrir || !panel) return;

    const cerrar = (devolverFoco = true) => {
        panel.hidden = true;
        abrir.setAttribute('aria-expanded', 'false');
        if (devolverFoco) abrir.focus();
    };
    abrir.addEventListener('click', () => {
        const abierto = panel.hidden;
        panel.hidden = !abierto;
        abrir.setAttribute('aria-expanded', String(abierto));
        if (abierto) panel.querySelector('button')?.focus();
    });
    panel.addEventListener('keydown', (e) => { if (e.key === 'Escape') cerrar(); });
    document.getElementById('a11y-cerrar')?.addEventListener('click', () => cerrar());

    document.querySelectorAll('[data-a11y-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const clave = btn.dataset.a11yToggle;
            estado[clave] = !estado[clave];
            guardar(); aplicar();
            anunciar(btn.dataset.nombre + (estado[clave] ? ' activado.' : ' desactivado.'));
        });
    });

    document.querySelectorAll('[data-a11y-size]').forEach((btn) => {
        btn.addEventListener('click', () => {
            estado.tamano += btn.dataset.a11ySize === 'mas' ? 1 : -1;
            estado.tamano = Math.min(Math.max(estado.tamano, 0), TAMANOS.length - 1);
            guardar(); aplicar();
            anunciar('Tamaño del texto ' + TAMANOS[estado.tamano] + ' por ciento.');
        });
    });

    document.querySelector('[data-a11y-leer]')?.addEventListener('click', alternarVoz);

    document.getElementById('a11y-reset')?.addEventListener('click', () => {
        detenerVoz();
        estado = { tamano: 0, tema: 'auto', ...Object.fromEntries(OPCIONES.map((o) => [o, false])) };
        guardar(); aplicar();
        anunciar('Preferencias de accesibilidad restablecidas.');
    });

    window.addEventListener('beforeunload', detenerVoz);
}

document.addEventListener('DOMContentLoaded', iniciar);
