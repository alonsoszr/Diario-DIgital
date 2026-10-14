{{-- Aplica las preferencias guardadas antes de pintar la página (evita parpadeos). --}}
<script>
    try {
        var a = JSON.parse(localStorage.getItem('accesibilidad.v1') || '{}');
        var r = document.documentElement, t = [100, 112, 125, 150, 175];
        var osc = a.tema === 'oscuro' || ((a.tema || 'auto') === 'auto' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
        if (osc) r.classList.add('tema-oscuro');
        if (a.tamano > 0 && a.tamano < t.length) r.style.fontSize = t[a.tamano] + '%';
        ['contraste', 'fuente', 'espaciado', 'enlaces', 'sinAnimaciones', 'cursor', 'grises'].forEach(function (o) {
            if (a[o]) r.classList.add('a11y-' + o);
        });
    } catch (e) {}
</script>
