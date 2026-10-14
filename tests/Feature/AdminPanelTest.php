<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Casos de prueba de seguridad y del panel de administración (CP-25 a CP-42).
 */
class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    private function datos(array $extra = []): array
    {
        return $extra + [
            'category_id' => Category::factory()->create()->id,
            'title' => 'Una noticia nueva de prueba',
            'excerpt' => 'Resumen de la noticia de prueba.',
            'body' => 'Contenido completo de la noticia de prueba con suficiente longitud.',
            'status' => 'published',
        ];
    }

    /* ---- control de acceso ---- */

    public function test_cp25_un_invitado_es_enviado_al_login(): void
    {
        foreach (['/admin', '/admin/noticias', '/admin/categorias', '/admin/comentarios'] as $ruta) {
            $this->get($ruta)->assertRedirect('/login');
        }
    }

    public function test_cp26_un_usuario_sin_rol_admin_recibe_403(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }

    public function test_cp27_el_admin_ve_el_resumen(): void
    {
        Post::factory()->count(2)->create();
        Post::factory()->draft()->create();

        $this->actingAs($this->admin())->get('/admin')
            ->assertOk()->assertSee('Resumen del blog')->assertSee('Noticias publicadas');
    }

    public function test_cp28_el_registro_publico_esta_desactivado(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_cp29_login_correcto_lleva_al_panel(): void
    {
        $user = User::factory()->admin()->create(['password' => 'Secreta123']);

        $this->post('/login', ['email' => $user->email, 'password' => 'Secreta123'])->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_cp30_login_con_clave_incorrecta_falla(): void
    {
        $user = User::factory()->admin()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'incorrecta'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /* ---- CRUD de noticias ---- */

    public function test_cp31_crear_noticia_con_imagen(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post(route('admin.posts.store'), $this->datos([
            'image' => $this->imagenFalsa('portada.png'),
            'image_alt' => 'Vista general del estadio',
        ]))->assertRedirect(route('admin.posts.index'));

        $post = Post::firstWhere('title', 'Una noticia nueva de prueba');
        $this->assertNotNull($post);
        $this->assertSame('una-noticia-nueva-de-prueba', $post->slug);
        $this->assertNotNull($post->published_at);
        Storage::disk('public')->assertExists($post->image);
    }

    public function test_cp32_la_imagen_exige_texto_alternativo(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post(route('admin.posts.store'), $this->datos([
            'image' => $this->imagenFalsa('portada.png'),
        ]))->assertSessionHasErrors('image_alt');
    }

    public function test_cp33_rechaza_archivos_que_no_son_imagen_o_pesan_mucho(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.posts.store'), $this->datos([
            'image' => UploadedFile::fake()->create('virus.pdf', 100, 'application/pdf'), 'image_alt' => 'x',
        ]))->assertSessionHasErrors('image');

        $this->actingAs($admin)->post(route('admin.posts.store'), $this->datos([
            'image' => $this->imagenFalsa('enorme.png', 3000), 'image_alt' => 'x',
        ]))->assertSessionHasErrors('image');
    }

    public function test_cp34_validacion_de_campos_obligatorios_de_noticia(): void
    {
        $this->actingAs($this->admin())->post(route('admin.posts.store'), [])
            ->assertSessionHasErrors(['category_id', 'title', 'excerpt', 'body', 'status']);
    }

    public function test_cp35_un_borrador_no_recibe_fecha_de_publicacion(): void
    {
        $this->actingAs($this->admin())->post(route('admin.posts.store'), $this->datos(['status' => 'draft']));

        $this->assertNull(Post::first()->published_at);
    }

    public function test_cp36_editar_noticia_y_publicarla(): void
    {
        $post = Post::factory()->draft()->create();

        $this->actingAs($this->admin())->put(route('admin.posts.update', $post), $this->datos([
            'category_id' => $post->category_id, 'title' => 'Titulo actualizado ya', 'status' => 'published',
        ]))->assertRedirect(route('admin.posts.index'));

        $post->refresh();
        $this->assertSame('Titulo actualizado ya', $post->title);
        $this->assertSame('titulo-actualizado-ya', $post->slug);
        $this->assertNotNull($post->published_at);
        $this->get(route('posts.show', $post))->assertOk();
    }

    public function test_cp37_eliminar_noticia_borra_tambien_su_imagen(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.posts.store'), $this->datos([
            'image' => $this->imagenFalsa('a.png'), 'image_alt' => 'Imagen de prueba',
        ]));
        $post = Post::first();
        $ruta = $post->image;

        $this->actingAs($admin)->delete(route('admin.posts.destroy', $post))->assertRedirect();

        $this->assertModelMissing($post);
        Storage::disk('public')->assertMissing($ruta);
    }

    public function test_cp38_slug_unico_cuando_se_repite_el_titulo(): void
    {
        $a = Post::factory()->create(['title' => 'Título repetido']);
        $b = Post::factory()->create(['title' => 'Título repetido']);

        $this->assertSame('titulo-repetido', $a->slug);
        $this->assertSame('titulo-repetido-2', $b->slug);
    }

    /* ---- categorías ---- */

    public function test_cp39_crear_categoria_genera_slug(): void
    {
        $this->actingAs($this->admin())->post(route('admin.categories.store'), [
            'name' => 'Medio Ambiente', 'description' => 'Naturaleza', 'color' => 'verde',
        ])->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['name' => 'Medio Ambiente', 'slug' => 'medio-ambiente']);
    }

    public function test_cp40_nombre_de_categoria_unico_y_color_valido(): void
    {
        Category::factory()->create(['name' => 'Cultura']);

        $this->actingAs($this->admin())->post(route('admin.categories.store'), ['name' => 'Cultura', 'color' => 'rosa'])
            ->assertSessionHasErrors(['name', 'color']);
    }

    public function test_cp41_no_se_elimina_una_categoria_con_noticias(): void
    {
        $post = Post::factory()->create();

        $this->actingAs($this->admin())->delete(route('admin.categories.destroy', $post->category))
            ->assertSessionHas('error');

        $this->assertModelExists($post->category);
    }

    public function test_cp42_se_elimina_una_categoria_vacia(): void
    {
        $categoria = Category::factory()->create();

        $this->actingAs($this->admin())->delete(route('admin.categories.destroy', $categoria))
            ->assertSessionHas('status');

        $this->assertModelMissing($categoria);
    }
}
