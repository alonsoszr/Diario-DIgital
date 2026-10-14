<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Datos iniciales: administrador, categorías y noticias de ejemplo.
     */
    public function run(): void
    {
        //administrador del blog (credenciales para el informe y las pruebas)
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@blog.test',
            'password' => 'Admin12345',
            'is_admin' => true,
        ]);

        $categorias = [];
        foreach ([
            ['Política', 'Gobierno, elecciones y decisiones públicas.', 'rojo'],
            ['Economía', 'Mercados, empleo, empresas y finanzas personales.', 'verde'],
            ['Deportes', 'Fútbol, vóley, atletismo y más.', 'azul'],
            ['Tecnología', 'Innovación, internet, ciencia y educación digital.', 'morado'],
            ['Cultura', 'Arte, música, literatura y tradiciones.', 'ambar'],
            ['Salud', 'Bienestar, prevención y avances médicos.', 'gris'],
        ] as [$nombre, $descripcion, $color]) {
            $categorias[$nombre] = Category::create([
                'name' => $nombre, 'description' => $descripcion, 'color' => $color,
            ]);
        }

        //[categoría, título, resumen, cuerpo, estado, días atrás, vistas]
        $noticias = [
            ['Tecnología', 'Colegios públicos ampliarán el acceso a internet en zonas rurales',
                'Un nuevo programa conectará a más de mil instituciones educativas con internet satelital durante el próximo año.',
                "El Ministerio de Educación anunció un plan para llevar conectividad a escuelas rurales que hoy no cuentan con internet.\n\nLa iniciativa contempla la instalación de antenas satelitales y la capacitación de docentes para integrar herramientas digitales en sus clases.\n\nSegún las autoridades, la primera etapa beneficiará a cerca de veinte mil estudiantes y se evaluará cada seis meses para medir su impacto en el aprendizaje.",
                'published', 1, 420],
            ['Economía', 'El empleo formal crece por tercer trimestre consecutivo',
                'Los datos más recientes muestran un aumento sostenido de puestos de trabajo en el sector servicios y comercio.',
                "El empleo formal registró un nuevo crecimiento durante el último trimestre, impulsado por el comercio y los servicios.\n\nAnalistas señalan que la tendencia podría mantenerse si continúa la inversión privada, aunque advierten que el empleo juvenil todavía requiere atención.\n\nLas pequeñas empresas explicaron que la reducción de trámites ayudó a contratar personal con contrato.",
                'published', 2, 310],
            ['Deportes', 'La selección juvenil de vóley clasifica a la final del torneo sudamericano',
                'El equipo venció en cinco sets y jugará por el título este domingo.',
                "Tras un partido de cinco sets, la selección juvenil de vóley aseguró su lugar en la final del campeonato sudamericano.\n\nLa capitana destacó el trabajo colectivo y la preparación física de las últimas semanas. El entrenador pidió el apoyo del público para la última jornada.\n\nLa final se jugará el domingo por la tarde y será transmitida por televisión abierta.",
                'published', 3, 560],
            ['Salud', 'Campaña de vacunación llegará a más de cien distritos este mes',
                'Brigadas móviles aplicarán dosis a niños y adultos mayores en centros de salud y plazas públicas.',
                "El sector salud inició una campaña de vacunación que recorrerá más de cien distritos del país durante este mes.\n\nLas brigadas atenderán en centros de salud, colegios y plazas, y recomiendan acudir con el carné de vacunación.\n\nLos especialistas recordaron que mantener el esquema completo es la mejor forma de prevenir enfermedades.",
                'published', 4, 280],
            ['Cultura', 'Festival de música andina reunirá a veinte agrupaciones en la plaza central',
                'Habrá conciertos gratuitos, feria de artesanías y talleres de danza durante tres días.',
                "La ciudad celebrará un festival de música andina con la participación de veinte agrupaciones de distintas regiones.\n\nAdemás de los conciertos, habrá una feria de artesanías y talleres de danza y quechua abiertos al público.\n\nLos organizadores esperan recibir a más de cinco mil visitantes y recomiendan llegar temprano.",
                'published', 5, 190],
            ['Política', 'El Congreso debatirá la reforma del sistema de transporte público',
                'La propuesta busca renovar flotas, integrar tarifas y mejorar la seguridad de los usuarios.',
                "El pleno del Congreso debatirá esta semana un proyecto para reformar el transporte público urbano.\n\nEntre los puntos principales figuran la renovación de la flota, el pago unificado con tarjeta y la creación de carriles exclusivos.\n\nAlgunos legisladores pidieron revisar el financiamiento antes de la votación final.",
                'published', 6, 340],
            ['Tecnología', 'Universidades lanzan un laboratorio abierto de inteligencia artificial',
                'Estudiantes y docentes podrán desarrollar proyectos con herramientas y datos compartidos.',
                "Tres universidades presentaron un laboratorio abierto de inteligencia artificial para fomentar la investigación aplicada.\n\nEl espacio contará con equipos de cómputo, asesoría de especialistas y convocatorias para proyectos de impacto social.\n\nLas inscripciones estarán abiertas hasta fin de mes en la página de cada universidad.",
                'published', 7, 650],
            ['Economía', 'Consejos para ordenar las finanzas personales antes de fin de año',
                'Especialistas recomiendan elaborar un presupuesto, reducir deudas y crear un fondo de emergencia.',
                "Antes de que termine el año, los expertos sugieren revisar los ingresos y gastos del hogar y fijar metas de ahorro realistas.\n\nTambién aconsejan priorizar el pago de deudas con intereses altos y reservar un monto mensual para imprevistos.\n\nUn presupuesto simple, actualizado cada semana, puede evitar muchos problemas a largo plazo.",
                'published', 9, 220],
            ['Deportes', 'Maratón de la ciudad abre inscripciones con nuevas categorías',
                'Habrá recorridos de 5, 10 y 21 kilómetros, y una categoría adaptada para personas con discapacidad.',
                "Los organizadores de la maratón anunciaron las inscripciones para la próxima edición, que incluye por primera vez una categoría adaptada.\n\nLos recorridos serán de 5, 10 y 21 kilómetros, con puntos de hidratación y atención médica.\n\nLa inscripción puede hacerse en línea o en los puntos autorizados.",
                'published', 10, 150],
            ['Salud', 'Cinco hábitos sencillos para dormir mejor',
                'Mantener horarios regulares y reducir las pantallas antes de dormir mejora la calidad del sueño.',
                "Dormir bien es esencial para la salud física y mental. Los especialistas recomiendan mantener horarios regulares y evitar la cafeína por la tarde.\n\nTambién sugieren reducir el uso de pantallas antes de acostarse y mantener el dormitorio oscuro y silencioso.\n\nSi los problemas de sueño persisten, conviene consultar a un profesional.",
                'published', 12, 480],
            ['Cultura', 'Biblioteca municipal digitaliza su archivo histórico',
                'Más de diez mil documentos estarán disponibles gratis en línea para estudiantes e investigadores.',
                "La biblioteca municipal inició la digitalización de su archivo histórico, con mapas, periódicos y fotografías de más de un siglo.\n\nLos documentos estarán disponibles en línea y se podrán buscar por tema, fecha o autor.\n\nEl proyecto busca preservar el patrimonio y facilitar su consulta desde cualquier lugar.",
                'published', 14, 130],
            ['Política', 'Municipalidad presenta su plan de ciudad más accesible',
                'Se instalarán rampas, semáforos sonoros y señalización en braille en los principales espacios públicos.',
                "La municipalidad presentó un plan para mejorar la accesibilidad en calles, parques y edificios públicos.\n\nSe construirán rampas, se instalarán semáforos sonoros y se colocará señalización en braille en puntos clave.\n\nLas asociaciones de personas con discapacidad participaron en el diseño de las obras.",
                'published', 16, 240],
            //borradores y publicación programada (no se ven en público)
            ['Tecnología', 'Nuevo curso gratuito de programación para jóvenes (borrador)',
                'Borrador pendiente de revisión antes de publicarse.',
                "Texto en preparación. Falta confirmar fechas, requisitos y enlace de inscripción.",
                'draft', 0, 0],
            ['Economía', 'Resumen semanal de precios en los mercados (borrador)',
                'Borrador con datos aún por verificar.',
                "Se actualizará con los precios del fin de semana antes de publicarse.",
                'draft', 0, 0],
        ];

        foreach ($noticias as [$cat, $titulo, $resumen, $cuerpo, $estado, $dias, $vistas]) {
            $post = Post::create([
                'category_id' => $categorias[$cat]->id,
                'user_id' => $admin->id,
                'title' => $titulo,
                'excerpt' => $resumen,
                'body' => $cuerpo,
                'status' => $estado,
                'published_at' => $estado === 'published' ? now()->subDays($dias)->subHours(2) : null,
            ]);
            $post->forceFill(['views' => $vistas])->save();
        }

        //comentarios de ejemplo: unos aprobados y otros pendientes de moderación
        $primera = Post::where('status', 'published')->orderByDesc('views')->first();
        foreach ([
            ['Lucía Ramos', 'lucia@correo.test', 'Muy buena iniciativa, ojalá llegue pronto a todas las regiones.', true],
            ['Marco Salas', 'marco@correo.test', 'Gracias por la información, estaré atento a las próximas novedades.', true],
            ['Visitante', 'visita@correo.test', 'Quisiera saber dónde puedo inscribirme.', false],
        ] as [$nombre, $correo, $texto, $aprobado]) {
            Comment::create([
                'post_id' => $primera->id, 'name' => $nombre, 'email' => $correo,
                'body' => $texto, 'approved' => $aprobado,
            ]);
        }
    }
}
