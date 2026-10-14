<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Casos de prueba de la parte pública (CP-01 a CP-14).
 */
class PublicBlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_cp01_la_portada_lista_noticias_publicadas(): void
    {
        $post = Post::factory()->create(['title' => 'Noticia visible de prueba']);

        $this->get('/')->assertOk()->assertSee('Noticia visible de prueba')->assertSee('Más leídas');
    }

    public function test_cp02_la_portada_vacia_muestra_mensaje(): void
    {
        $this->get('/')->assertOk()->assertSee('Todavía no hay noticias publicadas');
    }

    public function test_cp03_los_borradores_no_aparecen_en_portada(): void
    {
        Post::factory()->draft()->create(['title' => 'Borrador secreto']);

        $this->get('/')->assertOk()->assertDontSee('Borrador secreto');
    }

    public function test_cp04_un_borrador_devuelve_404(): void
    {
        $post = Post::factory()->draft()->create();

        $this->get(route('posts.show', $post))->assertNotFound();
    }

    public function test_cp05_una_noticia_programada_a_futuro_no_es_visible(): void
    {
        $post = Post::factory()->create(['published_at' => now()->addDays(3)]);

        $this->get(route('posts.show', $post))->assertNotFound();
        $this->get('/')->assertDontSee($post->title);
    }

    public function test_cp06_el_detalle_muestra_contenido_y_categoria(): void
    {
        $post = Post::factory()->create(['body' => "Primer párrafo del cuerpo.\n\nSegundo párrafo del cuerpo."]);

        $this->get(route('posts.show', $post))
            ->assertOk()
            ->assertSee($post->title)
            ->assertSee($post->category->name)
            ->assertSee('Segundo párrafo del cuerpo.');
    }

    public function test_cp07_el_contador_de_vistas_suma_una_vez_por_sesion(): void
    {
        $post = Post::factory()->create();

        $this->get(route('posts.show', $post));
        $this->get(route('posts.show', $post));
        $this->assertSame(1, $post->fresh()->views);

        //otra sesión (otro visitante) suma una vista más
        $this->flushSession();
        $this->get(route('posts.show', $post));
        $this->assertSame(2, $post->fresh()->views);
    }

    public function test_cp08_la_categoria_solo_lista_sus_noticias(): void
    {
        $deportes = Category::factory()->create(['name' => 'Deportes']);
        Post::factory()->for($deportes)->create(['title' => 'Gol en el último minuto']);
        Post::factory()->create(['title' => 'Otra categoría distinta']);

        $r = $this->get(route('categories.show', $deportes))->assertOk()->assertSee('Gol en el último minuto');
        //la barra lateral lista otras noticias; se comprueba el listado principal
        $this->assertSame(['Gol en el último minuto'], $r->viewData('posts')->pluck('title')->all());
    }

    public function test_cp09_la_busqueda_encuentra_por_titulo_y_contenido(): void
    {
        Post::factory()->create(['title' => 'Lluvias intensas en la sierra']);
        Post::factory()->create(['title' => 'Otra', 'body' => str_repeat('texto ', 10) . 'volcán activo']);
        Post::factory()->create(['title' => 'Ajena al tema']);

        $r = $this->get('/buscar?q=lluvias')->assertOk()->assertSee('Lluvias intensas');
        $this->assertSame(['Lluvias intensas en la sierra'], $r->viewData('posts')->pluck('title')->all());
        $this->get('/buscar?q=volcán')->assertOk()->assertSee('1 resultado');
    }

    public function test_cp10_la_busqueda_sin_resultados_informa_al_usuario(): void
    {
        $this->get('/buscar?q=inexistente')->assertOk()->assertSee('No encontramos noticias');
    }

    public function test_cp11_la_busqueda_vacia_pide_una_palabra(): void
    {
        $this->get('/buscar')->assertOk()->assertSee('Escribe una palabra');
    }

    public function test_cp12_la_busqueda_no_ejecuta_html_inyectado(): void
    {
        $this->get('/buscar?q=' . urlencode('<script>alert(1)</script>'))
            ->assertOk()->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_cp13_la_portada_se_pagina(): void
    {
        Post::factory()->count(15)->create();

        //1 destacada + 9 en la primera página, 5 en la segunda
        $this->get('/')->assertOk()->assertSee('Siguiente');
        $this->get('/?page=2')->assertOk()->assertSee('Anterior');
    }

    public function test_cp14_mas_leidas_ordena_por_vistas(): void
    {
        Post::factory()->create(['title' => 'Poco leída', 'views' => 3]);
        Post::factory()->create(['title' => 'Muy leída', 'views' => 900]);

        $this->get('/')->assertSeeInOrder(['Más leídas', 'Muy leída', 'Poco leída']);
    }
}
