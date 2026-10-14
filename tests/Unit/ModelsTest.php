<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Post;
use PHPUnit\Framework\TestCase;

/**
 * Pruebas unitarias de lógica de modelos sin base de datos (CP-43 a CP-45).
 */
class ModelsTest extends TestCase
{
    public function test_cp43_tiempo_de_lectura_minimo_un_minuto(): void
    {
        $post = new Post(['body' => 'Texto corto.']);

        $this->assertSame(1, $post->reading_time);
    }

    public function test_cp44_tiempo_de_lectura_usa_200_palabras_por_minuto(): void
    {
        $post = new Post(['body' => str_repeat('palabra ', 450)]);

        $this->assertSame(3, $post->reading_time);
    }

    public function test_cp45_cada_color_de_categoria_tiene_clases_propias(): void
    {
        $clases = [];
        foreach (Category::COLORES as $color) {
            $clases[] = (new Category(['color' => $color]))->badge_classes;
        }

        $this->assertCount(count(Category::COLORES), array_unique($clases));
    }
}
