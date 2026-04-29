REGLAS DEL PROYECTO - NUESTROMURO

STACK
- Laravel + Jetstream + Tailwind
- NO usar Bootstrap
- NO instalar librerías sin permiso

ARQUITECTURA

- User = base del sistema
- SocialAccount = redes sociales
- SocialAccountEvent = historial (registro, verificación, eliminado, robado)
- Schedule = horarios de publicación
- FollowerSnapshot = historial de seguidores
- Poll / PollOption / PollVote = encuestas
- LiveAnnouncement = próximos lives
- Subscription = monetización
- Ad / AdAssignment = anuncios
- Message = contacto (whatsapp proxy)

REGLAS IMPORTANTES

- NO modificar estructura de base de datos
- NO agregar campos nuevos
- NO refactorizar modelos existentes
- SOLO trabajar en lo que se pide

VISTAS

- layouts en: resources/views/layouts
- creator en: resources/views/creator
- public en: resources/views/public
- admin en: resources/views/admin
- componentes en: resources/views/components

BLADE

- usar Blade SIEMPRE
- usar @extends layouts
- usar Tailwind existente
- reutilizar componentes

TIMEZONE

- usar timezone string (ej: America/Tijuana)
- NO usar offsets manuales

FOLLOWERS

- SIEMPRE usar FollowerSnapshot
- NO guardar followers en users

SOCIAL ACCOUNTS

- estado actual en SocialAccount
- historial en SocialAccountEvent

ENCUESTAS

- expires_at null = indefinida
- expires_at con fecha = limitada

FLUJO

1. El usuario pide una tarea
2. SOLO hacer esa tarea
3. NO agregar extras
4. NO modificar otras partes

OBJETIVO

Sistema de creadores con:
- perfiles públicos
- horarios globales
- redes sociales verificables
- historial de crecimiento
- interacción (encuestas, comentarios)
- monetización (ads, pro, donaciones)