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
        'description' => 'Публичный интерфейс для просмотра наказаний и банов сервера'
    ],
    
    'nav' => [
        'home' => 'Главная',
        'bans' => 'Баны',
        'mutes' => 'Муты',
        'warnings' => 'Предупреждения',
        'kicks' => 'Кики'
    ],
    
    'home' => [
        'welcome' => 'Наказания на сервере',
        'description' => 'Поиск наказаний игроков и просмотр недавней активности',
        'recent_activity' => 'Недавняя активность',
        'recent_bans' => 'Недавние баны',
        'recent_mutes' => 'Недавние муты',
        'no_recent_bans' => 'Нет недавних банов',
        'no_recent_mutes' => 'Нет недавних мутов',
        'view_all_bans' => 'Просмотреть все баны',
        'view_all_mutes' => 'Просмотреть все муты'
    ],
    
    'search' => [
        'title' => 'Поиск игроков',
        'placeholder' => 'Введите имя игрока или UUID...',
        'help' => 'Вы можете искать по нику или полному UUID',
        'button' => 'Искать',
        'no_results' => 'Для этого игрока не найдено наказаний',
        'error' => 'Произошла ошибка при поиске'
    ],
    
    'stats' => [
        'title' => 'Статистика сервера',
        'active_bans' => 'Активные баны',
        'active_mutes' => 'Активные муты',
        'total_warnings' => 'Всего предупреждений',
        'total_kicks' => 'Всего киков',
        'total_of' => 'из',
        'all_time' => 'всего'
    ],
    
    'table' => [
        'player' => 'Игрок',
        'reason' => 'Причина',
        'staff' => 'Администратор',
        'date' => 'Дата',
        'expires' => 'Истекает',
        'status' => 'Статус'
    ],
    
    'status' => [
        'active' => 'Активный',
        'inactive' => 'Неактивный',
        'expired' => 'Истёкший',
        'removed' => 'Удалённый',
        'completed' => 'Завершённый',
        'removed_by' => 'Удалено пользователем'
    ],
    
    'punishment' => [
        'permanent' => 'Постоянное',
        'expired' => 'Истёкшее'
    ],
    
    'punishments' => [
        'no_data' => 'Наказания не найдены',
        'no_data_desc' => 'На данный момент нет наказаний для отображения'
    ],
    
    'time' => [
        'days' => '{count} дн.',
        'hours' => '{count} ч.',
        'minutes' => '{count} мин.'
    ],
    
    'pagination' => [
        'label' => 'Навигация по страницам',
        'previous' => 'Предыдущая',
        'next' => 'Следующая',
        'page_info' => 'Страница {current} из {total}'
    ],
    
    'footer' => [
        'rights' => 'Все права защищены.',
        'powered_by' => 'Работает на',
        'license' => 'Лицензировано под'
    ],
    
    'error' => [
        'not_found' => 'Страница не найдена',
        'server_error' => 'Ошибка сервера',
        'invalid_request' => 'Неверный запрос'
    ]
];
