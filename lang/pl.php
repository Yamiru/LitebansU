<?php

return [
    'site' => [
        'name' => 'LiteBans',
        'title' => '{page} - LiteBans',
        'description' => 'Publiczny interfejs do przeglądania kar i banów na serwerze'
    ],
    
    'nav' => [
        'home' => 'Strona główna',
        'bans' => 'Bany',
        'mutes' => 'Wyciszenia',
        'warnings' => 'Ostrzeżenia',
        'kicks' => 'Wyrzucenia',
        'statistics' => 'Statystyki',
        'language' => 'Język',
        'theme' => 'Motyw',
        'admin' => 'Admin',
        'protest' => 'Odwołanie od bana',
    ],
    
    'home' => [
        'welcome' => 'Kary na serwerze',
        'description' => 'Wyszukaj kary graczy i zobacz ostatnią aktywność',
        'recent_activity' => 'Ostatnia aktywność',
        'recent_bans' => 'Ostatnie bany',
        'recent_mutes' => 'Ostatnie wyciszenia',
        'no_recent_bans' => 'Nie znaleziono żadnych ostatnich banów',
        'no_recent_mutes' => 'Nie znaleziono żadnych ostatnich wyciszeń',
        'view_all_bans' => 'Zobacz wszystkie bany',
        'view_all_mutes' => 'Zobacz wszystkie wyciszenia'
    ],
    
    'search' => [
        'title' => 'Wyszukiwarka graczy',
        'placeholder' => 'Wpisz nazwę gracza lub UUID...',
        'help' => 'Możesz wyszukiwać po nazwie gracza lub pełnym UUID',
        'button' => 'Szukaj',
        'no_results' => 'Nie znaleziono kar dla tego gracza',
        'error' => 'Wystąpił błąd podczas wyszukiwania',
        'network_error' => 'Wystąpił błąd sieci. Spróbuj ponownie.'
    ],
    
    'stats' => [
        'title' => 'Statystyki serwera',
        'active_bans' => 'Aktywne bany',
        'active_mutes' => 'Aktywne wyciszenia',
        'total_warnings' => 'Liczba ostrzeżeń',
        'total_kicks' => 'Liczba wyrzuceń',
        'total_of' => 'z',
        'all_time' => 'całkowita',
        'most_banned_players' => 'Najczęściej banowani gracze',
        'most_active_staff' => 'Najaktywniejsza administracja',
        'top_ban_reasons' => 'Najczęstsze powody banów',
        'recent_activity_overview' => 'Przegląd ostatniej aktywności',
        'activity_by_day' => 'Aktywność według dni',
        'cache_cleared' => 'Pamięć podręczna statystyk została pomyślnie wyczyszczona',
        'cache_clear_failed' => 'Nie udało się wyczyścić pamięci podręcznej statystyk',
        'clear_cache' => 'Wyczyść pamięć podręczną',
        'last_24h' => 'Ostatnie 24 godziny',
        'last_7d' => 'Ostatnie 7 dni',
        'last_30d' => 'Ostatnie 30 dni'
    ],
    
    'table' => [
        'player' => 'Gracz',
        'reason' => 'Powód',
        'staff' => 'Administrator',
        'date' => 'Data',
        'expires' => 'Wygasa',
        'status' => 'Status',
        'actions' => 'Akcje',
        'type' => 'Typ',
        'view' => 'Zobacz',
        'total' => 'Łącznie',
        'active' => 'Aktywny',
        'last_ban' => 'Ostatni ban',
        'last_action' => 'Ostatnia akcja',
        'server' => 'Serwer',
    ],
    
    'status' => [
        'active' => 'Aktywny',
        'inactive' => 'Nieaktywny',
        'expired' => 'Wygasł',
        'removed' => 'Zdjęty',
        'completed' => 'Ukończony',
        'removed_by' => 'Zdjęty przez'
    ],
    
    'punishment' => [
        'permanent' => 'Na stałe',
        'expired' => 'Wygasł'
    ],
    
    'punishments' => [
        'no_data' => 'Nie znaleziono żadnych kar',
        'no_data_desc' => 'Obecnie nie ma żadnych kar do wyświetlenia'
    ],
    
    'detail' => [
        'duration' => 'Czas trwania',
        'time_left' => 'Pozostały czas',
        'progress' => 'Postęp',
        'removed_by' => 'Zdjęty przez',
        'removed_date' => 'Data zdjęcia',
        'flags' => 'Flagi',
        'other_punishments' => 'Inne kary'
    ],
    
    'time' => [
        'days' => '{count} dni',
        'hours' => '{count} godzin',
        'minutes' => '{count} minut'
    ],
    
    'pagination' => [
        'label' => 'Nawigacja strony',
        'previous' => 'Poprzednia',
        'next' => 'Następna',
        'page_info' => 'Strona {current} z {total}'
    ],
    
    'footer' => [
        'rights' => 'Wszystkie prawa zastrzeżone.',
        'powered_by' => 'Napędzane przez',
        'license' => 'Na licencji'
    ],
    
    'error' => [
        'not_found' => 'Strona nie znaleziona',
        'server_error' => 'Wystąpił błąd serwera',
        'invalid_request' => 'Nieprawidłowe żądanie',
        'punishment_not_found' => 'Żądana kara nie została znaleziona.',
        'loading_failed' => 'Nie udało się załadować szczegółów kary.'
    ],
    
    'protest' => [
        'title' => 'Odwołanie od bana',
        'description' => 'Jeśli uważasz, że Twój ban został nałożony przez pomyłkę, możesz złożyć odwołanie do rozpatrzenia.',
        'how_to_title' => 'Jak złożyć odwołanie od bana',
        'how_to_subtitle' => 'Postępuj zgodnie z tymi krokami, aby poprosić o odbanowanie:',
        'step1_title' => '1. Zbierz informacje',
        'step1_desc' => 'Przed złożeniem odwołania upewnij się, że masz:',
        'step1_items' => [
            'Twoją nazwę użytkownika w Minecraft',
            'Datę i godzinę nałożenia bana',
            'Podany powód bana',
            'Wszelkie dowody, które wspierają Twoją sprawę'
        ],
        'step2_title' => '2. Metody kontaktu',
        'step2_desc' => 'Możesz złożyć odwołanie od bana za pomocą jednej z następujących metod:',
        'discord_title' => 'Discord (Zalecane)',
        'discord_desc' => 'Dołącz do naszego serwera Discord i utwórz zgłoszenie na kanale #odwołania-banów',
        'discord_button' => 'Dołącz do Discorda',
        'email_title' => 'E-mail',
        'email_desc' => 'Wyślij szczegółową wiadomość e-mail z odwołaniem na adres:',
        'forum_title' => 'Forum',
        'forum_desc' => 'Utwórz nowy post w sekcji Odwołania od banów na naszym forum.',
        'forum_button' => 'Odwiedź forum',
        'step3_title' => '3. Co zawrzeć w odwołaniu',
        'step3_desc' => 'Twoje odwołanie powinno zawierać: Twój nick - Co się stało - Dlaczego się odwołujesz - Czego oczekujesz - ID ze strony (np. ban&id=181) - Opcjonalnie: Zrzut ekranu jako dowód.',
        'step3_items' => [
            'Twoją nazwę użytkownika w Minecraft',
            'Datę i przybliżoną godzinę nałożenia bana',
            'Administratora, który nałożył bana (jeśli jest znany)',
            'Szczegółowe wyjaśnienie, dlaczego uważasz, że ban był niesprawiedliwy',
            'Wszelkie zrzuty ekranu lub dowody, które wspierają Twoją sprawę',
            'Uczciwy opis tego, co się stało'
        ],
        'step4_title' => '4. Poczekaj na rozpatrzenie',
        'step4_desc' => 'Nasz zespół rozpatrzy Twoje odwołanie w ciągu 48-72 godzin. Prosimy o cierpliwość i nie składanie wielu odwołań dotyczących tego samego bana.',
        'guidelines_title' => 'Ważne wytyczne',
        'guidelines_items' => [
            'Bądź szczery i pełen szacunku w swoim odwołaniu',
            'Nie kłam ani nie podawaj fałszywych informacji',
            'Nie spamuj ani nie składaj wielu odwołań',
            'Zaakceptuj ostateczną decyzję zespołu',
            'Unikanie bana będzie skutkować banem permanentnym'
        ],
        'warning_title' => 'Ostrzeżenie',
        'warning_desc' => 'Podawanie fałszywych informacji lub próba oszukania administracji spowoduje odrzucenie Twojego odwołania i może prowadzić do przedłużenia kary.',
        'form_not_available' => 'Bezpośrednie składanie odwołań nie jest w tej chwili dostępne. Prosimy o skorzystanie z jednej z powyższych metod kontaktu.'
    ],
    
    'admin' => [
        'dashboard' => 'Panel administratora',
        'login' => 'Logowanie administratora',
        'logout' => 'Wyloguj',
        'password' => 'Hasło',
        'export_data' => 'Eksportuj dane',
        'export_desc' => 'Eksportuj dane o karach w różnych formatach',
        'import_data' => 'Importuj dane',
        'import_desc' => 'Importuj dane o karach z plików JSON lub XML',
        'data_type' => 'Typ danych',
        'all_punishments' => 'Wszystkie kary',
        'select_file' => 'Wybierz plik',
        'import' => 'Importuj',
        'settings' => 'Ustawienia',
        'show_player_uuid' => 'Pokaż UUID gracza',
        'footer_site_name' => 'Nazwa strony w stopce',
        'footer_site_name_desc' => 'Nazwa strony wyświetlana w stopce w informacji o prawach autorskich',
    ],
];