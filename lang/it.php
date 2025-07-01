<?php

return [
    'site' => [
        'name' => 'LiteBans',
        'title' => '{page} - LiteBans',
        'description' => 'Interfaccia pubblica per visualizzare le punizioni e i ban del server'
    ],
    
    'nav' => [
        'home' => 'Home',
        'bans' => 'Ban',
        'mutes' => 'Mute',
        'warnings' => 'Avvertimenti',
        'kicks' => 'Kick',
        'statistics' => 'Statistiche',
        'language' => 'Lingua',
        'theme' => 'Tema',
        'admin' => 'Admin',
        'protest' => 'Appello Ban',
    ],
    
    'home' => [
        'welcome' => 'Punizioni del Server',
        'description' => 'Cerca le punizioni dei giocatori e visualizza l\'attività recente',
        'recent_activity' => 'Attività Recente',
        'recent_bans' => 'Ban Recenti',
        'recent_mutes' => 'Mute Recenti',
        'no_recent_bans' => 'Nessun ban recente trovato',
        'no_recent_mutes' => 'Nessun mute recente trovato',
        'view_all_bans' => 'Vedi tutti i ban',
        'view_all_mutes' => 'Vedi tutti i mute'
    ],
    
    'search' => [
        'title' => 'Ricerca Giocatore',
        'placeholder' => 'Inserisci il nome del giocatore o UUID...',
        'help' => 'Puoi cercare per nome giocatore o UUID completo',
        'button' => 'Cerca',
        'no_results' => 'Nessuna punizione trovata per questo giocatore',
        'error' => 'Si è verificato un errore durante la ricerca',
        'network_error' => 'Si è verificato un errore di rete. Riprova.'
    ],
    
    'stats' => [
        'title' => 'Statistiche del Server',
        'active_bans' => 'Ban Attivi',
        'active_mutes' => 'Mute Attivi',
        'total_warnings' => 'Avvertimenti Totali',
        'total_kicks' => 'Kick Totali',
        'total_of' => 'di',
        'all_time' => 'sempre',
        'most_banned_players' => 'Giocatori più bannati',
        'most_active_staff' => 'Staff più attivo',
        'top_ban_reasons' => 'Motivi di ban più comuni',
        'recent_activity_overview' => 'Riepilogo attività recente',
        'activity_by_day' => 'Attività per giorno',
        'cache_cleared' => 'Cache delle statistiche svuotata con successo',
        'cache_clear_failed' => 'Impossibile svuotare la cache delle statistiche',
        'clear_cache' => 'Svuota Cache',
        'last_24h' => 'Ultime 24 ore',
        'last_7d' => 'Ultimi 7 giorni',
        'last_30d' => 'Ultimi 30 giorni'
    ],
    
    'table' => [
        'player' => 'Giocatore',
        'reason' => 'Motivo',
        'staff' => 'Staff',
        'date' => 'Data',
        'expires' => 'Scadenza',
        'status' => 'Stato',
        'actions' => 'Azioni',
        'type' => 'Tipo',
        'view' => 'Vedi',
        'total' => 'Totale',
        'active' => 'Attivo',
        'last_ban' => 'Ultimo Ban',
        'last_action' => 'Ultima Azione',
        'server' => 'Server',
    ],
    
    'status' => [
        'active' => 'Attivo',
        'inactive' => 'Inattivo',
        'expired' => 'Scaduto',
        'removed' => 'Rimosso',
        'completed' => 'Completato',
        'removed_by' => 'Rimosso da'
    ],
    
    'punishment' => [
        'permanent' => 'Permanente',
        'expired' => 'Scaduto'
    ],
    
    'punishments' => [
        'no_data' => 'Nessuna punizione trovata',
        'no_data_desc' => 'Attualmente non ci sono punizioni da visualizzare'
    ],
    
    'detail' => [
        'duration' => 'Durata',
        'time_left' => 'Tempo rimasto',
        'progress' => 'Avanzamento',
        'removed_by' => 'Rimosso da',
        'removed_date' => 'Data di rimozione',
        'flags' => 'Flag',
        'other_punishments' => 'Altre Punizioni'
    ],
    
    'time' => [
        'days' => '{count} giorni',
        'hours' => '{count} ore',
        'minutes' => '{count} minuti'
    ],
    
    'pagination' => [
        'label' => 'Navigazione pagina',
        'previous' => 'Precedente',
        'next' => 'Successivo',
        'page_info' => 'Pagina {current} di {total}'
    ],
    
    'footer' => [
        'rights' => 'Tutti i diritti riservati.',
        'powered_by' => 'Powered by',
        'license' => 'Concesso in licenza da'
    ],
    
    'error' => [
        'not_found' => 'Pagina non trovata',
        'server_error' => 'Si è verificato un errore del server',
        'invalid_request' => 'Richiesta non valida',
        'punishment_not_found' => 'La punizione richiesta non è stata trovata.',
        'loading_failed' => 'Caricamento dei dettagli della punizione fallito.'
    ],
    
    'protest' => [
        'title' => 'Appello Ban',
        'description' => 'Se credi che il tuo ban sia stato un errore, puoi inviare un appello per una revisione.',
        'how_to_title' => 'Come inviare un appello per un ban',
        'how_to_subtitle' => 'Segui questi passaggi per richiedere la rimozione del ban:',
        'step1_title' => '1. Raccogli le tue informazioni',
        'step1_desc' => 'Prima di inviare un appello, assicurati di avere:',
        'step1_items' => [
            'Il tuo nome utente di Minecraft',
            'La data e l\'ora del tuo ban',
            'Il motivo fornito per il tuo ban',
            'Qualsiasi prova che supporti il tuo caso'
        ],
        'step2_title' => '2. Metodi di contatto',
        'step2_desc' => 'Puoi inviare il tuo appello tramite uno dei seguenti metodi:',
        'discord_title' => 'Discord (Consigliato)',
        'discord_desc' => 'Unisciti al nostro server Discord e crea un ticket nel canale #appelli-ban',
        'discord_button' => 'Unisciti a Discord',
        'email_title' => 'Email',
        'email_desc' => 'Invia un\'email dettagliata con il tuo appello a:',
        'forum_title' => 'Forum',
        'forum_desc' => 'Crea un nuovo post nella sezione Appelli Ban del nostro forum.',
        'forum_button' => 'Visita il Forum',
        'step3_title' => '3. Cosa includere',
        'step3_desc' => 'Il tuo appello dovrebbe includere: Il tuo nickname - Cosa è successo - Perché stai protestando - Cosa vuoi che accada - ID dal sito (es. ban&id=181) - Opzionale: Screenshot come prova.',
        'step3_items' => [
            'Il tuo nome utente di Minecraft',
            'La data e l\'ora approssimativa del ban',
            'Il membro dello staff che ti ha bannato (se noto)',
            'Una spiegazione dettagliata del perché ritieni che il ban sia stato ingiusto',
            'Qualsiasi screenshot o prova che supporti il tuo caso',
            'Un resoconto onesto di ciò che è accaduto'
        ],
        'step4_title' => '4. Attendi la revisione',
        'step4_desc' => 'Il nostro team di staff esaminerà il tuo appello entro 48-72 ore. Sii paziente e non inviare più appelli per lo stesso ban.',
        'guidelines_title' => 'Linee guida importanti',
        'guidelines_items' => [
            'Sii onesto e rispettoso nel tuo appello',
            'Non mentire o fornire informazioni false',
            'Non fare spam o inviare più appelli',
            'Accetta la decisione finale del team di staff',
            'L\'evasione del ban comporterà un ban permanente'
        ],
        'warning_title' => 'Attenzione',
        'warning_desc' => 'L\'invio di informazioni false o il tentativo di ingannare lo staff comporterà il rifiuto del tuo appello e potrebbe portare a una punizione estesa.',
        'form_not_available' => 'L\'invio diretto di appelli non è disponibile al momento. Si prega di utilizzare uno dei metodi di contatto sopra elencati.'
    ],
    
    'admin' => [
        'dashboard' => 'Pannello Admin',
        'login' => 'Login Admin',
        'logout' => 'Logout',
        'password' => 'Password',
        'export_data' => 'Esporta Dati',
        'export_desc' => 'Esporta i dati delle punizioni in vari formati',
        'import_data' => 'Importa Dati',
        'import_desc' => 'Importa i dati delle punizioni da file JSON o XML',
        'data_type' => 'Tipo di Dati',
        'all_punishments' => 'Tutte le Punizioni',
        'select_file' => 'Seleziona File',
        'import' => 'Importa',
        'settings' => 'Impostazioni',
        'show_player_uuid' => 'Mostra UUID Giocatore',
        'footer_site_name' => 'Nome del sito nel piè di pagina',
        'footer_site_name_desc' => 'Nome del sito visualizzato nel testo del copyright nel piè di pagina',
    ],
];