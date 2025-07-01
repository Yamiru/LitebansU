<?php

return [
    'site' => [
        'name' => 'LiteBans',
        'title' => '{page} - LiteBans',
        'description' => 'Interfață publică pentru vizualizarea sancțiunilor și ban-urilor de pe server'
    ],
    
    'nav' => [
        'home' => 'Acasă',
        'bans' => 'Ban-uri',
        'mutes' => 'Mute-uri',
        'warnings' => 'Avertismente',
        'kicks' => 'Kick-uri',
        'statistics' => 'Statistici',
        'language' => 'Limbă',
        'theme' => 'Temă',
        'admin' => 'Admin',
        'protest' => 'Contestație Ban',
    ],
    
    'home' => [
        'welcome' => 'Sancțiuni pe Server',
        'description' => 'Caută sancțiunile jucătorilor și vezi activitatea recentă',
        'recent_activity' => 'Activitate Recentă',
        'recent_bans' => 'Ban-uri Recente',
        'recent_mutes' => 'Mute-uri Recente',
        'no_recent_bans' => 'Niciun ban recent găsit',
        'no_recent_mutes' => 'Niciun mute recent găsit',
        'view_all_bans' => 'Vezi toate ban-urile',
        'view_all_mutes' => 'Vezi toate mute-urile'
    ],
    
    'search' => [
        'title' => 'Căutare Jucător',
        'placeholder' => 'Introdu numele jucătorului sau UUID...',
        'help' => 'Poți căuta după numele jucătorului sau UUID-ul complet',
        'button' => 'Caută',
        'no_results' => 'Nicio sancțiune găsită pentru acest jucător',
        'error' => 'A apărut o eroare la căutare',
        'network_error' => 'A apărut o eroare de rețea. Te rugăm să încerci din nou.'
    ],
    
    'stats' => [
        'title' => 'Statistici Server',
        'active_bans' => 'Ban-uri Active',
        'active_mutes' => 'Mute-uri Active',
        'total_warnings' => 'Total Avertismente',
        'total_kicks' => 'Total Kick-uri',
        'total_of' => 'din',
        'all_time' => 'total',
        'most_banned_players' => 'Cei mai banați jucători',
        'most_active_staff' => 'Cel mai activ staff',
        'top_ban_reasons' => 'Cele mai frecvente motive de ban',
        'recent_activity_overview' => 'Prezentare generală a activității recente',
        'activity_by_day' => 'Activitate pe zi',
        'cache_cleared' => 'Cache-ul statisticilor a fost golit cu succes',
        'cache_clear_failed' => 'Eroare la golirea cache-ului statisticilor',
        'clear_cache' => 'Golește Cache',
        'last_24h' => 'Ultimele 24 de ore',
        'last_7d' => 'Ultimele 7 zile',
        'last_30d' => 'Ultimele 30 de zile'
    ],
    
    'table' => [
        'player' => 'Jucător',
        'reason' => 'Motiv',
        'staff' => 'Staff',
        'date' => 'Data',
        'expires' => 'Expiră',
        'status' => 'Status',
        'actions' => 'Acțiuni',
        'type' => 'Tip',
        'view' => 'Vezi',
        'total' => 'Total',
        'active' => 'Activ',
        'last_ban' => 'Ultimul Ban',
        'last_action' => 'Ultima Acțiune',
        'server' => 'Server',
    ],
    
    'status' => [
        'active' => 'Activ',
        'inactive' => 'Inactiv',
        'expired' => 'Expirat',
        'removed' => 'Înlăturat',
        'completed' => 'Finalizat',
        'removed_by' => 'Înlăturat de'
    ],
    
    'punishment' => [
        'permanent' => 'Permanent',
        'expired' => 'Expirat'
    ],
    
    'punishments' => [
        'no_data' => 'Nicio sancțiune găsită',
        'no_data_desc' => 'Momentan nu există sancțiuni de afișat'
    ],
    
    'detail' => [
        'duration' => 'Durată',
        'time_left' => 'Timp rămas',
        'progress' => 'Progres',
        'removed_by' => 'Înlăturat de',
        'removed_date' => 'Data înlăturării',
        'flags' => 'Marcaje',
        'other_punishments' => 'Alte Sancțiuni'
    ],
    
    'time' => [
        'days' => '{count} zile',
        'hours' => '{count} ore',
        'minutes' => '{count} minute'
    ],
    
    'pagination' => [
        'label' => 'Navigare pagină',
        'previous' => 'Anterior',
        'next' => 'Următor',
        'page_info' => 'Pagina {current} din {total}'
    ],
    
    'footer' => [
        'rights' => 'Toate drepturile rezervate.',
        'powered_by' => 'Susținut de',
        'license' => 'Licențiat sub'
    ],
    
    'error' => [
        'not_found' => 'Pagina nu a fost găsită',
        'server_error' => 'A apărut o eroare de server',
        'invalid_request' => 'Cerere invalidă',
        'punishment_not_found' => 'Sancțiunea cerută nu a putut fi găsită.',
        'loading_failed' => 'Eroare la încărcarea detaliilor sancțiunii.'
    ],
    
    'protest' => [
        'title' => 'Contestație Ban',
        'description' => 'Dacă crezi că ban-ul tău a fost acordat dintr-o eroare, poți trimite o contestație pentru a fi revizuită.',
        'how_to_title' => 'Cum să trimiți o contestație',
        'how_to_subtitle' => 'Urmează acești pași pentru a cere un-ban:',
        'step1_title' => '1. Adună informațiile',
        'step1_desc' => 'Înainte de a trimite o contestație, asigură-te că ai:',
        'step1_items' => [
            'Numele tău de utilizator Minecraft',
            'Data și ora ban-ului tău',
            'Motivul dat pentru ban-ul tău',
            'Orice dovadă care îți susține cazul'
        ],
        'step2_title' => '2. Metode de contact',
        'step2_desc' => 'Poți trimite contestația ta prin una dintre următoarele metode:',
        'discord_title' => 'Discord (Recomandat)',
        'discord_desc' => 'Intră pe serverul nostru de Discord și creează un tichet în canalul #contestatii-ban',
        'discord_button' => 'Intră pe Discord',
        'email_title' => 'E-mail',
        'email_desc' => 'Trimite un e-mail detaliat cu contestația ta la:',
        'forum_title' => 'Forum',
        'forum_desc' => 'Creează o postare nouă în secțiunea Contestații Ban de pe forumul site-ului nostru.',
        'forum_button' => 'Vizitează Forumul',
        'step3_title' => '3. Ce să incluzi',
        'step3_desc' => 'Contestația ta ar trebui să includă: Nickname-ul tău - Ce s-a întâmplat - De ce contesti - Ce vrei să se întâmple - ID-ul de pe site (ex. ban&id=181) - Opțional: Captură de ecran ca dovadă.',
        'step3_items' => [
            'Numele tău de utilizator Minecraft',
            'Data și ora aproximativă a ban-ului',
            'Membrul staff-ului care te-a banat (dacă este cunoscut)',
            'O explicație detaliată a motivului pentru care crezi că ban-ul a fost nedrept',
            'Orice capturi de ecran sau dovezi care îți susțin cazul',
            'O relatare onestă a celor întâmplate'
        ],
        'step4_title' => '4. Așteaptă revizuirea',
        'step4_desc' => 'Echipa noastră de staff va revizui contestația ta în 48-72 de ore. Te rugăm să fii răbdător și să nu trimiți mai multe contestații pentru același ban.',
        'guidelines_title' => 'Ghid important',
        'guidelines_items' => [
            'Fii onest și respectuos în contestația ta',
            'Nu minți și nu furniza informații false',
            'Nu face spam și nu trimite mai multe contestații',
            'Acceptă decizia finală a echipei de staff',
            'Evitarea ban-ului va duce la un ban permanent'
        ],
        'warning_title' => 'Atenție',
        'warning_desc' => 'Trimiterea de informații false sau încercarea de a înșela staff-ul va duce la respingerea contestației tale și poate duce la o prelungire a sancțiunii.',
        'form_not_available' => 'Trimiterea directă a contestațiilor nu este disponibilă în acest moment. Te rugăm să folosești una dintre metodele de contact de mai sus.'
    ],
    
    'admin' => [
        'dashboard' => 'Panou de administrare',
        'login' => 'Autentificare Admin',
        'logout' => 'Deconectare',
        'password' => 'Parolă',
        'export_data' => 'Exportă date',
        'export_desc' => 'Exportă datele sancțiunilor în diverse formate',
        'import_data' => 'Importă date',
        'import_desc' => 'Importă datele sancțiunilor din fișiere JSON sau XML',
        'data_type' => 'Tip de date',
        'all_punishments' => 'Toate sancțiunile',
        'select_file' => 'Selectează fișier',
        'import' => 'Importă',
        'settings' => 'Setări',
        'show_player_uuid' => 'Arată UUID-ul jucătorului',
        'footer_site_name' => 'Numele site-ului în subsol',
        'footer_site_name_desc' => 'Numele site-ului afișat în textul de copyright din subsol',
    ],
];