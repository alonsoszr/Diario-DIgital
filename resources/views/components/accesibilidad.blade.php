{{-- Menú de accesibilidad (se incluye en todos los layouts). Lógica en resources/js/accesibilidad.js --}}
<div class="a11y-widget print:hidden">
    <button type="button" id="a11y-abrir" class="a11y-fab" aria-expanded="false" aria-controls="a11y-panel">
        <svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="4" r="2"/><path d="M5 8h14v2h-5v4l2 7h-2l-2-5-2 5H8l2-7v-4H5z"/></svg>
        <span>Accesibilidad</span>
    </button>

    <section id="a11y-panel" class="a11y-panel" hidden aria-labelledby="a11y-titulo">
        <div class="mb-3 flex items-center justify-between">
            <h2 id="a11y-titulo" class="text-lg font-bold">Opciones de accesibilidad</h2>
            <button type="button" id="a11y-cerrar" class="a11y-btn !min-h-[36px] !px-3">Cerrar</button>
        </div>

        <div class="mb-4">
            <p class="mb-1 font-semibold" id="a11y-tamano-label">Tamaño del texto: <span id="a11y-nivel">100%</span></p>
            <div class="flex gap-2" role="group" aria-labelledby="a11y-tamano-label">
                <button type="button" class="a11y-btn flex-1" data-a11y-size="menos" aria-label="Reducir tamaño del texto">A−</button>
                <button type="button" class="a11y-btn flex-1" data-a11y-size="mas" aria-label="Aumentar tamaño del texto">A+</button>
            </div>
        </div>

        <div class="mb-4">
            <p class="mb-1 font-semibold" id="a11y-tema-label">Tema de color</p>
            <div class="grid grid-cols-3 gap-2" role="group" aria-labelledby="a11y-tema-label">
                @foreach ([['claro', 'Claro'], ['oscuro', 'Oscuro'], ['auto', 'Automático']] as [$valor, $texto])
                    <button type="button" class="a11y-btn a11y-opt a11y-tema justify-center" data-tema="{{ $valor }}" data-nombre="{{ $texto }}" aria-pressed="false">{{ $texto }}</button>
                @endforeach
            </div>
            <p class="mt-1 text-sm">«Automático» sigue la configuración de tu dispositivo.</p>
        </div>

        <ul class="grid gap-2">
            @foreach ([
                ['contraste', 'Alto contraste', 'Fondo negro y letras claras (baja visión)'],
                ['grises', 'Escala de grises', 'Quita los colores del sitio'],
                ['fuente', 'Fuente legible', 'Tipografía simple y clara (dislexia)'],
                ['espaciado', 'Más espacio entre letras y líneas', 'Facilita la lectura'],
                ['enlaces', 'Resaltar enlaces', 'Subrayado grueso y recuadro'],
                ['sinAnimaciones', 'Detener animaciones', 'Sin transiciones ni desplazamientos suaves'],
                ['cursor', 'Cursor grande', 'Puntero más fácil de ubicar'],
            ] as [$clave, $nombre, $ayuda])
                <li>
                    <button type="button" class="a11y-btn a11y-opt w-full" data-a11y-toggle="{{ $clave }}" data-nombre="{{ $nombre }}" aria-pressed="false">
                        <span class="a11y-check" aria-hidden="true"></span>
                        <span class="text-left"><span class="block font-semibold">{{ $nombre }}</span><span class="block text-sm">{{ $ayuda }}</span></span>
                    </button>
                </li>
            @endforeach
            <li>
                <button type="button" class="a11y-btn a11y-opt w-full" data-a11y-leer aria-pressed="false">
                    <span class="a11y-check" aria-hidden="true"></span>
                    <span class="text-left"><span class="block font-semibold">Leer en voz alta</span><span class="block text-sm">Lee el texto seleccionado o la página (personas ciegas o con baja visión)</span></span>
                </button>
            </li>
        </ul>

        <button type="button" id="a11y-reset" class="a11y-btn mt-4 w-full">Restablecer todo</button>
        <p id="a11y-anuncio" class="sr-only" role="status" aria-live="polite"></p>
    </section>
</div>
