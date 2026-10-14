<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Pruebas E2E de los flujos principales (CP-70 a CP-74).
 */
class FlujoBlogTest extends DuskTestCase
{
    use DatabaseMigrations;

    private function entrar(Browser $b): void
    {
        User::factory()->admin()->create(['email' => 'admin@prueba.test', 'password' => 'Secreta123']);
        $b->visit('/login')
            ->type('email', 'admin@prueba.test')->type('password', 'Secreta123')
            ->click('form button[type=submit]')
            ->waitForText('Resumen del blog');
    }

    public function test_cp70_login_crear_noticia_y_verla_en_la_portada(): void
    {
        Category::factory()->create(['name' => 'Ciencia']);

        $this->browse(function (Browser $b) {
            $this->entrar($b);
            $b->screenshot('admin-resumen')
                ->visit('/admin/noticias/create')
                ->type('title', 'Hallan agua en un cráter lunar')
                ->select('category_id')
                ->type('excerpt', 'Un nuevo estudio confirma la presencia de hielo.')
                ->type('body', 'Investigadores confirmaron la presencia de hielo en una zona sombreada de la Luna.')
                ->select('status', 'published')
                ->screenshot('admin-formulario-noticia')
                ->click('form[enctype] button[type=submit]')
                ->waitForText('Noticia creada correctamente.')
                ->visit('/')
                ->assertSee('Hallan agua en un cráter lunar');
        });
    }

    public function test_cp71_formulario_vacio_muestra_errores_accesibles(): void
    {
        $this->browse(function (Browser $b) {
            $this->entrar($b);
            $b->visit('/admin/noticias/create')
                ->click('form[enctype] button[type=submit]')
                ->waitFor('[role=alert]')
                ->assertSee('No se pudo guardar')
                ->screenshot('admin-errores');
        });
    }

    public function test_cp72_comentar_aprobar_y_ver_publicado(): void
    {
        $post = Post::factory()->create(['title' => 'Noticia con comentarios']);

        $this->browse(function (Browser $b) use ($post) {
            $b->visit('/noticias/' . $post->slug)
                ->type('name', 'Rosa Quispe')->type('email', 'rosa@correo.test')
                ->type('body', 'Muy buena información, gracias.')
                ->press('Enviar comentario')
                ->waitForText('Se publicará cuando sea revisado')
                ->assertDontSee('Muy buena información, gracias.')
                ->screenshot('comentario-pendiente');

            $this->entrar($b);
            $b->visit('/admin/comentarios')
                ->assertSee('Muy buena información, gracias.')
                ->screenshot('admin-moderacion')
                ->press('Aprobar')
                ->waitForText('Comentario aprobado.')
                ->visit('/noticias/' . $post->slug)
                ->assertSee('Muy buena información, gracias.');
        });
    }

    public function test_cp73_busqueda_desde_el_buscador(): void
    {
        Post::factory()->create(['title' => 'Festival gastronómico de otoño']);
        Post::factory()->create(['title' => 'Cierre de la bolsa de valores']);

        $this->browse(function (Browser $b) {
            $b->visit('/')->type('q', 'gastronómico')->press('Buscar')
                ->waitForText('Resultados de búsqueda')
                ->assertSee('1 resultado')
                ->assertSee('Festival gastronómico de otoño')
                ->screenshot('busqueda');
        });
    }

    public function test_cp74_un_borrador_no_se_ve_y_al_publicar_si(): void
    {
        $borrador = Post::factory()->draft()->create(['title' => 'Texto en preparación']);

        $this->browse(function (Browser $b) use ($borrador) {
            $b->visit('/noticias/' . $borrador->slug)->assertSee('404');
            $this->entrar($b);
            $b->visit('/admin/noticias/' . $borrador->slug . '/edit')
                ->select('status', 'published')
                ->click('form[enctype] button[type=submit]')
                ->waitForText('Noticia actualizada.')
                ->visit('/noticias/' . $borrador->fresh()->slug)
                ->assertSee('Texto en preparación');
        });
    }
}
