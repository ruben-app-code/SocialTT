# SocialTikTok

Plataforma web para **creadores** y seguidores: perfiles públicos, enlaces y redes sociales (con foco en **TikTok**), encuestas, agenda, mensajes y un panel de administración (usuarios, roles, permisos con Spatie, categorías, temas, traducciones).

Los creadores pueden mantener una lista **«Siguiendo»** con cuentas TikTok externas o con otros creadores de la plataforma, con histórico de métricas cuando se sincroniza el perfil.

## Stack

- **PHP 8.2+**, **Laravel 12**
- **Jetstream** (Livewire), **Laravel Sanctum**, sesiones web
- **Tailwind CSS** y **Vite** para el frontend
- **MySQL/MariaDB** (u otro driver compatible con Laravel)

## Puesta en marcha (resumen)

1. Copiar `.env.example` a `.env`, configurar `APP_URL`, base de datos y `APP_KEY` (`php artisan key:generate`).
2. `composer install` y `npm install`
3. Migraciones y datos iniciales según tu entorno: `php artisan migrate` y seeders que uses (por ejemplo usuario administrador).
4. `npm run build` o `npm run dev` para assets.
5. En local (p. ej. XAMPP / vhost `.test`), alinear `APP_URL` con la URL real del proyecto para evitar problemas de sesión, CSRF y Livewire.

Para detalles de variables opcionales (tokens de seed, dominios Sanctum, etc.) revisa los comentarios en `.env.example`.
