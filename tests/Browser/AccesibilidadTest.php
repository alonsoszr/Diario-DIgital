<?php

namespace Tests\Browser;

use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Pruebas E2E del menú de accesibilidad y de la navegación por teclado (CP-60 a CP-65).
 */
class AccesibilidadTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Post::factory()->create(['title' => 'Noticia de accesibilidad']);
    }

    public function test_cp60_el_menu_se_abre_y_se_cierra_con_escape(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/')
                ->assertAttribute('#a11y-abrir', 'aria-expanded', 'false')
                ->assertMissing('#a11y-panel')
                ->click('#a11y-abrir')
                ->waitFor('#a11y-panel')
                ->assertAttribute('#a11y-abrir', 'aria-expanded', 'true')
                ->screenshot('a11y-menu-abierto')
                ->keys('#a11y-panel', ['{escape}'])
                ->waitUntilMissing('#a11y-panel')
                ->assertAttribute('#a11y-abrir', 'aria-expanded', 'false');
        });
    }

    public function test_cp61_aumentar_el_texto_y_que_se_conserve_al_recargar(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/')->click('#a11y-abrir')->waitFor('#a11y-panel')
                ->click('[data-a11y-size="mas"]')
                ->assertSeeIn('#a11y-nivel', '112%');

            $this->assertSame('112%', $b->script('return document.documentElement.style.fontSize')[0]);

            $b->refresh();
            $this->assertSame('112%', $b->script('return document.documentElement.style.fontSize')[0]);
            $b->click('#a11y-abrir')->waitFor('#a11y-panel')->click('#a11y-reset');
            $this->assertSame('100%', $b->script('return document.documentElement.style.fontSize')[0]);
        });
    }

    public function test_cp62_alto_contraste_cambia_los_colores_y_se_recuerda(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/')->click('#a11y-abrir')->waitFor('#a11y-panel')
                ->click('[data-a11y-toggle="contraste"]')
                ->assertAttribute('[data-a11y-toggle="contraste"]', 'aria-pressed', 'true')
                ->screenshot('a11y-alto-contraste');

            $fondo = $b->script("return getComputedStyle(document.body).backgroundColor")[0];
            $this->assertSame('rgb(0, 0, 0)', $fondo);

            $b->visit('/noticias/' . Post::first()->slug);
            $this->assertSame('rgb(0, 0, 0)', $b->script("return getComputedStyle(document.body).backgroundColor")[0]);
            $b->click('#a11y-abrir')->waitFor('#a11y-panel')->click('#a11y-reset');
        });
    }

    public function test_cp63_cada_opcion_activa_su_clase_en_html(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/')->click('#a11y-abrir')->waitFor('#a11y-panel');
            foreach (['grises', 'fuente', 'espaciado', 'enlaces', 'sinAnimaciones', 'cursor'] as $opcion) {
                $b->click('[data-a11y-toggle="' . $opcion . '"]');
                $this->assertTrue(
                    $b->script("return document.documentElement.classList.contains('a11y-$opcion')")[0],
                    "La opción $opcion no aplicó su clase"
                );
            }
            $b->click('#a11y-reset');
            $this->assertFalse($b->script("return document.documentElement.className.includes('a11y-')")[0]);
        });
    }

    public function test_cp64_el_enlace_saltar_al_contenido_es_el_primer_foco(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/')->keys('body', ['{tab}']);
            $this->assertSame('skip-link', $b->script('return document.activeElement.className')[0]);
            $b->keys('.skip-link', ['{enter}']);
            $this->assertSame('contenido', $b->script('return location.hash.substring(1)')[0]);
        });
    }

    public function test_cp65_el_menu_tambien_esta_en_el_login(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/login')->assertPresent('#a11y-abrir');
        });
    }
}
