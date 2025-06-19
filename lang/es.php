<?php 
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       1.0
 *  Author:        Yamiru <yamiru@yamiru.com>
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 *  Repository    https://github.com/Yamiru/LitebansU/
 * ============================================================================
 */

return [
    'site' => [
        'name' => 'LiteBans',
        'title' => '{page} - LiteBans',
        'description' => 'Interfaz pública para ver sanciones y baneos del servidor'
    ],
    
    'nav' => [
        'home' => 'Inicio',
        'bans' => 'Baneos',
        'mutes' => 'Silencios',
        'warnings' => 'Advertencias',
        'kicks' => 'Expulsiones'
    ],
    
    'home' => [
        'welcome' => 'Sanciones del Servidor',
        'description' => 'Busca sanciones de jugadores y muestra la actividad reciente',
        'recent_activity' => 'Actividad Reciente',
        'recent_bans' => 'Baneos Recientes',
        'recent_mutes' => 'Silencios Recientes',
        'no_recent_bans' => 'No hay baneos recientes',
        'no_recent_mutes' => 'No hay silencios recientes',
        'view_all_bans' => 'Ver todos los baneos',
        'view_all_mutes' => 'Ver todos los silencios'
    ],
    
    'search' => [
        'title' => 'Buscar Jugadores',
        'placeholder' => 'Introduce el nombre del jugador o UUID...',
        'help' => 'Puedes buscar por nombre de jugador o UUID completo',
        'button' => 'Buscar',
        'no_results' => 'No se encontraron sanciones para este jugador',
        'error' => 'Ocurrió un error durante la búsqueda'
    ],
    
    'stats' => [
        'title' => 'Estadísticas del Servidor',
        'active_bans' => 'Baneos Activos',
        'active_mutes' => 'Silencios Activos',
        'total_warnings' => 'Advertencias Totales',
        'total_kicks' => 'Expulsiones Totales',
        'total_of' => 'de',
        'all_time' => 'en total'
    ],
    
    'table' => [
        'player' => 'Jugador',
        'reason' => 'Razón',
        'staff' => 'Moderador',
        'date' => 'Fecha',
        'expires' => 'Expira',
        'status' => 'Estado'
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
    
    'time' => [
        'days' => '{count} días',
        'hours' => '{count} horas',
        'minutes' => '{count} minutos'
    ],
    
    'pagination' => [
        'label' => 'Navegación de páginas',
        'previous' => 'Anterior',
        'next' => 'Siguiente',
        'page_info' => 'Página {current} de {total}'
    ],
    
    'footer' => [
        'rights' => 'Todos los derechos reservados.',
        'powered_by' => 'Desarrollado con',
        'license' => 'Licenciado bajo'
    ],
    
    'error' => [
        'not_found' => 'Página no encontrada',
        'server_error' => 'Error del servidor',
        'invalid_request' => 'Solicitud no válida'
    ]
];
