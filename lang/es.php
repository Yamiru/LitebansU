<?php

return [
    'site' => [
        'name' => 'LiteBans',
        'title' => '{page} - LiteBans',
        'description' => 'Interfaz pública para ver las sanciones y baneos del servidor'
    ],
    
    'nav' => [
        'home' => 'Inicio',
        'bans' => 'Baneos',
        'mutes' => 'Silencios',
        'warnings' => 'Advertencias',
        'kicks' => 'Expulsiones',
        'statistics' => 'Estadísticas',
        'language' => 'Idioma',
        'theme' => 'Tema',
        'admin' => 'Admin',
        'protest' => 'Apelar Baneo',
    ],
    
    'home' => [
        'welcome' => 'Sanciones del Servidor',
        'description' => 'Busca sanciones de jugadores y mira la actividad reciente',
        'recent_activity' => 'Actividad Reciente',
        'recent_bans' => 'Baneos Recientes',
        'recent_mutes' => 'Silencios Recientes',
        'no_recent_bans' => 'No se encontraron baneos recientes',
        'no_recent_mutes' => 'No se encontraron silencios recientes',
        'view_all_bans' => 'Ver todos los baneos',
        'view_all_mutes' => 'Ver todos los silencios'
    ],
    
    'search' => [
        'title' => 'Búsqueda de Jugadores',
        'placeholder' => 'Introduce el nombre del jugador o UUID...',
        'help' => 'Puedes buscar por nombre de jugador o UUID completo',
        'button' => 'Buscar',
        'no_results' => 'No se encontraron sanciones para este jugador',
        'error' => 'Ocurrió un error en la búsqueda',
        'network_error' => 'Ocurrió un error de red. Por favor, inténtalo de nuevo.'
    ],
    
    'stats' => [
        'title' => 'Estadísticas del Servidor',
        'active_bans' => 'Baneos Activos',
        'active_mutes' => 'Silencios Activos',
        'total_warnings' => 'Total de Advertencias',
        'total_kicks' => 'Total de Expulsiones',
        'total_of' => 'de',
        'all_time' => 'histórico',
        'most_banned_players' => 'Jugadores más baneados',
        'most_active_staff' => 'Staff más activo',
        'top_ban_reasons' => 'Razones de baneo más comunes',
        'recent_activity_overview' => 'Resumen de Actividad Reciente',
        'activity_by_day' => 'Actividad por Día',
        'cache_cleared' => 'La caché de estadísticas se ha limpiado correctamente',
        'cache_clear_failed' => 'Error al limpiar la caché de estadísticas',
        'clear_cache' => 'Limpiar Caché',
        'last_24h' => 'Últimas 24 horas',
        'last_7d' => 'Últimos 7 días',
        'last_30d' => 'Últimos 30 días'
    ],
    
    'table' => [
        'player' => 'Jugador',
        'reason' => 'Razón',
        'staff' => 'Staff',
        'date' => 'Fecha',
        'expires' => 'Expira',
        'status' => 'Estado',
        'actions' => 'Acciones',
        'type' => 'Tipo',
        'view' => 'Ver',
        'total' => 'Total',
        'active' => 'Activo',
        'last_ban' => 'Último Baneo',
        'last_action' => 'Última Acción',
        'server' => 'Servidor',
    ],
    
    'status' => [
        'active' => 'Activo',
        'inactive' => 'Inactivo',
        'expired' => 'Expirado',
        'removed' => 'Eliminado',
        'completed' => 'Completado',
        'removed_by' => 'Eliminado por'
    ],
    
    'punishment' => [
        'permanent' => 'Permanente',
        'expired' => 'Expirado'
    ],
    
    'punishments' => [
        'no_data' => 'No se encontraron sanciones',
        'no_data_desc' => 'Actualmente no hay sanciones para mostrar'
    ],
    
    'detail' => [
        'duration' => 'Duración',
        'time_left' => 'Tiempo restante',
        'progress' => 'Progreso',
        'removed_by' => 'Eliminado Por',
        'removed_date' => 'Fecha de Eliminación',
        'flags' => 'Indicadores',
        'other_punishments' => 'Otras Sanciones'
    ],
    
    'time' => [
        'days' => '{count} días',
        'hours' => '{count} horas',
        'minutes' => '{count} minutos'
    ],
    
    'pagination' => [
        'label' => 'Navegación de página',
        'previous' => 'Anterior',
        'next' => 'Siguiente',
        'page_info' => 'Página {current} de {total}'
    ],
    
    'footer' => [
        'rights' => 'Todos los derechos reservados.',
        'powered_by' => 'Funciona con',
        'license' => 'Bajo la licencia de'
    ],
    
    'error' => [
        'not_found' => 'Página no encontrada',
        'server_error' => 'Ocurrió un error en el servidor',
        'invalid_request' => 'Solicitud no válida',
        'punishment_not_found' => 'No se pudo encontrar la sanción solicitada.',
        'loading_failed' => 'Error al cargar los detalles de la sanción.'
    ],
    
    'protest' => [
        'title' => 'Apelar Baneo',
        'description' => 'Si crees que tu baneo fue un error, puedes enviar una apelación para que sea revisada.',
        'how_to_title' => 'Cómo Enviar una Apelación de Baneo',
        'how_to_subtitle' => 'Sigue estos pasos para solicitar un desbaneo:',
        'step1_title' => '1. Reúne tu Información',
        'step1_desc' => 'Antes de enviar una apelación, asegúrate de tener:',
        'step1_items' => [
            'Tu nombre de usuario de Minecraft',
            'La fecha y hora de tu baneo',
            'La razón dada para tu baneo',
            'Cualquier evidencia que apoye tu caso'
        ],
        'step2_title' => '2. Métodos de Contacto',
        'step2_desc' => 'Puedes enviar tu apelación de baneo a través de uno de los siguientes métodos:',
        'discord_title' => 'Discord (Recomendado)',
        'discord_desc' => 'Únete a nuestro servidor de Discord y crea un ticket en el canal #apelaciones-baneo',
        'discord_button' => 'Unirse a Discord',
        'email_title' => 'Correo Electrónico',
        'email_desc' => 'Envía un correo electrónico detallado con tu apelación a:',
        'forum_title' => 'Foro',
        'forum_desc' => 'Crea una nueva publicación en la sección de Apelaciones de Baneo de nuestro foro.',
        'forum_button' => 'Visitar Foro',
        'step3_title' => '3. Qué Incluir',
        'step3_desc' => 'Tu apelación debe incluir: Tu apodo - Qué pasó - Por qué estás apelando - Qué quieres que suceda - ID del sitio (ej. ban&id=181) - Opcional: Captura de pantalla como prueba.',
        'step3_items' => [
            'Tu nombre de usuario de Minecraft',
            'La fecha y hora aproximada del baneo',
            'El miembro del staff que te baneó (si lo sabes)',
            'Una explicación detallada de por qué crees que el baneo fue injusto',
            'Cualquier captura de pantalla o evidencia que apoye tu caso',
            'Un relato honesto de lo que sucedió'
        ],
        'step4_title' => '4. Espera la Revisión',
        'step4_desc' => 'Nuestro equipo de staff revisará tu apelación en un plazo de 48 a 72 horas. Por favor, sé paciente y no envíes múltiples apelaciones por el mismo baneo.',
        'guidelines_title' => 'Directrices Importantes',
        'guidelines_items' => [
            'Sé honesto y respetuoso en tu apelación',
            'No mientas ni proporciones información falsa',
            'No hagas spam ni envíes múltiples apelaciones',
            'Acepta la decisión final del equipo de staff',
            'La evasión de baneo resultará en un baneo permanente'
        ],
        'warning_title' => 'Advertencia',
        'warning_desc' => 'Enviar información falsa o intentar enganar al staff resultará en el rechazo de tu apelación y puede llevar a una sanción extendida.',
        'form_not_available' => 'El envío directo de apelaciones no está disponible en este momento. Por favor, utiliza uno de los métodos de contacto anteriores.'
    ],
    
    'admin' => [
        'dashboard' => 'Panel de Administración',
        'login' => 'Inicio de Sesión de Admin',
        'logout' => 'Cerrar Sesión',
        'password' => 'Contrasena',
        'export_data' => 'Exportar Datos',
        'export_desc' => 'Exportar datos de sanciones en varios formatos',
        'import_data' => 'Importar Datos',
        'import_desc' => 'Importar datos de sanciones desde archivos JSON o XML',
        'data_type' => 'Tipo de Datos',
        'all_punishments' => 'Todas las Sanciones',
        'select_file' => 'Seleccionar Archivo',
        'import' => 'Importar',
        'settings' => 'Ajustes',
        'show_player_uuid' => 'Mostrar UUID del Jugador',
        'footer_site_name' => 'Nombre del Sitio en el Pie de Página',
        'footer_site_name_desc' => 'Nombre del sitio mostrado en el texto de copyright del pie de página',
    ],
];