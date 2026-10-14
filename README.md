# Diario Digital – Blog de noticias autoadministrable

Producto Académico N.º 2 – Pruebas y Calidad de Software (Universidad Continental).
Aplicación web en **PHP 8.2 / Laravel 12 / Breeze (Blade) / Tailwind CSS / MySQL**.

## Funcionalidades
- **Público:** portada con noticia destacada y paginación, detalle de noticia, categorías, búsqueda, "Más leídas", comentarios (con moderación) y noticias relacionadas.
- **Administración (`/admin`):** resumen con métricas, CRUD de noticias (imagen con texto alternativo, borrador/publicado), CRUD de categorías, moderación de comentarios.
- **Accesibilidad (WCAG 2.1 AA):** enlace "Saltar al contenido", landmarks, foco visible, navegación por teclado, textos alternativos, contraste, estados que no dependen solo del color, `aria-live`, `prefers-reduced-motion`.
- **Eficiencia:** assets minificados con Vite, imágenes `loading="lazy"`, eager loading, paginación, caché de la barra lateral e índices en la BD.

## Requisitos
PHP 8.2+ (extensiones mbstring, pdo_mysql, fileinfo), Composer 2, Node 18+ y MySQL/MariaDB (XAMPP sirve).

## Instalación
```bash
composer install
npm install && npm run build
cp .env.example .env          # en Windows: copy .env.example .env
php artisan key:generate
```
1. Inicia MySQL y crea la base de datos `blog_noticias` (cotejamiento `utf8mb4_unicode_ci`).
   Ajusta `DB_*` en `.env` si tu usuario/clave no son `root` sin clave.
2. Crea las tablas y datos de ejemplo (o importa `database/blog_noticias.sql`):
```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve             # http://127.0.0.1:8000
```

**Administrador de ejemplo:** `admin@blog.test` / `Admin12345` (entrar por `/login`).

## Pruebas
```bash
php artisan test                         # PHPUnit (Feature + Unit), SQLite en memoria
php artisan dusk                         # E2E en Chrome (usa .env.dusk.local y SQLite aparte)
```
Los casos de prueba se documentan en el informe (Plan de Pruebas, formato IEEE 829).

## Estructura relevante
- `app/Http/Controllers` (público) y `app/Http/Controllers/Admin` (panel)
- `app/Models` (Post, Category, Comment, User), `app/Http/Requests/PostRequest.php`
- `app/Http/Middleware/EnsureUserIsAdmin.php`
- `resources/views/blog`, `resources/views/admin`, `resources/views/layouts`
- `database/migrations`, `database/seeders/BlogSeeder.php`
- `tests/Feature`, `tests/Unit`, `tests/Browser`
