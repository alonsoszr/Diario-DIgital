<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Casos de prueba de comentarios y moderación (CP-15 a CP-24).
 */
class CommentsTest extends TestCase
{
    use RefreshDatabase;

    private array $valido = ['name' => 'Ana Torres', 'email' => 'ana@correo.test', 'body' => 'Excelente nota, gracias.'];

    public function test_cp15_un_comentario_nuevo_queda_pendiente(): void
    {
        $post = Post::factory()->create();

        $this->post(route('comments.store', $post), $this->valido)
            ->assertRedirect(route('posts.show', $post) . '#comentarios')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('comments', ['post_id' => $post->id, 'name' => 'Ana Torres', 'approved' => false]);
    }

    public function test_cp16_un_comentario_pendiente_no_se_muestra(): void
    {
        $post = Post::factory()->create();
        Comment::factory()->for($post)->create(['body' => 'Texto aún pendiente']);

        $this->get(route('posts.show', $post))->assertDontSee('Texto aún pendiente');
    }

    public function test_cp17_un_comentario_aprobado_se_muestra(): void
    {
        $post = Post::factory()->create();
        Comment::factory()->for($post)->approved()->create(['body' => 'Comentario ya aprobado']);

        $this->get(route('posts.show', $post))->assertSee('Comentario ya aprobado')->assertSee('Comentarios (1)');
    }

    public function test_cp18_campos_obligatorios_del_comentario(): void
    {
        $post = Post::factory()->create();

        $this->post(route('comments.store', $post), [])
            ->assertSessionHasErrors(['name', 'email', 'body']);
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_cp19_correo_invalido_y_texto_demasiado_largo(): void
    {
        $post = Post::factory()->create();

        $this->post(route('comments.store', $post), ['name' => 'Ana', 'email' => 'no-es-correo', 'body' => str_repeat('a', 1001)])
            ->assertSessionHasErrors(['email', 'body']);
    }

    public function test_cp20_no_se_puede_comentar_un_borrador(): void
    {
        $post = Post::factory()->draft()->create();

        $this->post(route('comments.store', $post), $this->valido)->assertNotFound();
    }

    public function test_cp21_el_comentario_escapa_html(): void
    {
        $post = Post::factory()->create();
        Comment::factory()->for($post)->approved()->create(['body' => '<script>alert("xss")</script>']);

        $this->get(route('posts.show', $post))->assertDontSee('<script>alert("xss")</script>', false);
    }

    public function test_cp22_limite_de_comentarios_por_minuto(): void
    {
        $post = Post::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('comments.store', $post), $this->valido)->assertRedirect();
        }
        $this->post(route('comments.store', $post), $this->valido)->assertStatus(429);
    }

    public function test_cp23_el_admin_aprueba_un_comentario(): void
    {
        $admin = User::factory()->admin()->create();
        $comment = Comment::factory()->create();

        $this->actingAs($admin)->patch(route('admin.comments.approve', $comment))->assertSessionHas('status');

        $this->assertTrue($comment->fresh()->approved);
    }

    public function test_cp24_el_admin_elimina_un_comentario(): void
    {
        $admin = User::factory()->admin()->create();
        $comment = Comment::factory()->create();

        $this->actingAs($admin)->delete(route('admin.comments.destroy', $comment))->assertSessionHas('status');

        $this->assertModelMissing($comment);
    }
}
