<?php

return [
    'site' => [
        'name' => 'LiteBans',
        'title' => '{page} - LiteBans',
        'description' => 'Öffentliche Oberfläche zur Anzeige von Strafen und Banns auf dem Server'
    ],
    
    'nav' => [
        'home' => 'Startseite',
        'bans' => 'Banns',
        'mutes' => 'Stummschaltungen',
        'warnings' => 'Verwarnungen',
        'kicks' => 'Kicks',
        'statistics' => 'Statistiken',
        'language' => 'Sprache',
        'theme' => 'Design',
        'admin' => 'Admin',
        'protest' => 'Bann-Einspruch',
    ],
    
    'home' => [
        'welcome' => 'Strafen auf dem Server',
        'description' => 'Suche nach Spieler-Strafen und sieh dir die letzten Aktivitäten an',
        'recent_activity' => 'Letzte Aktivitäten',
        'recent_bans' => 'Letzte Banns',
        'recent_mutes' => 'Letzte Stummschaltungen',
        'no_recent_bans' => 'Keine kürzlichen Banns gefunden',
        'no_recent_mutes' => 'Keine kürzlichen Stummschaltungen gefunden',
        'view_all_bans' => 'Alle Banns ansehen',
        'view_all_mutes' => 'Alle Stummschaltungen ansehen'
    ],
    
    'search' => [
        'title' => 'Spielersuche',
        'placeholder' => 'Spielername oder UUID eingeben...',
        'help' => 'Du kannst nach Spielername oder vollständiger UUID suchen',
        'button' => 'Suchen',
        'no_results' => 'Keine Strafen für diesen Spieler gefunden',
        'error' => 'Suchfehler aufgetreten',
        'network_error' => 'Netzwerkfehler aufgetreten. Bitte versuche es erneut.'
    ],
    
    'stats' => [
        'title' => 'Server-Statistiken',
        'active_bans' => 'Aktive Banns',
        'active_mutes' => 'Aktive Stummschaltungen',
        'total_warnings' => 'Gesamte Verwarnungen',
        'total_kicks' => 'Gesamte Kicks',
        'total_of' => 'von',
        'all_time' => 'insgesamt',
        'most_banned_players' => 'Am häufigsten gebannte Spieler',
        'most_active_staff' => 'Aktivste Teammitglieder',
        'top_ban_reasons' => 'Häufigste Bann-Gründe',
        'recent_activity_overview' => 'Übersicht der letzten Aktivitäten',
        'activity_by_day' => 'Aktivität pro Tag',
        'cache_cleared' => 'Statistik-Cache erfolgreich geleert',
        'cache_clear_failed' => 'Fehler beim Leeren des Statistik-Caches',
        'clear_cache' => 'Cache leeren',
        'last_24h' => 'Letzte 24 Stunden',
        'last_7d' => 'Letzte 7 Tage',
        'last_30d' => 'Letzte 30 Tage'
    ],
    
    'table' => [
        'player' => 'Spieler',
        'reason' => 'Grund',
        'staff' => 'Teammitglied',
        'date' => 'Datum',
        'expires' => 'Läuft ab',
        'status' => 'Status',
        'actions' => 'Aktionen',
        'type' => 'Typ',
        'view' => 'Ansehen',
        'total' => 'Gesamt',
        'active' => 'Aktiv',
        'last_ban' => 'Letzter Bann',
        'last_action' => 'Letzte Aktion',
        'server' => 'Server',
    ],
    
    'status' => [
        'active' => 'Aktiv',
        'inactive' => 'Inaktiv',
        'expired' => 'Abgelaufen',
        'removed' => 'Aufgehoben',
        'completed' => 'Abgeschlossen',
        'removed_by' => 'Aufgehoben von'
    ],
    
    'punishment' => [
        'permanent' => 'Permanent',
        'expired' => 'Abgelaufen'
    ],
    
    'punishments' => [
        'no_data' => 'Keine Strafen gefunden',
        'no_data_desc' => 'Derzeit gibt es keine Strafen zum Anzeigen'
    ],
    
    'detail' => [
        'duration' => 'Dauer',
        'time_left' => 'Verbleibende Zeit',
        'progress' => 'Fortschritt',
        'removed_by' => 'Aufgehoben von',
        'removed_date' => 'Datum der Aufhebung',
        'flags' => 'Flags',
        'other_punishments' => 'Andere Strafen'
    ],
    
    'time' => [
        'days' => '{count} Tage',
        'hours' => '{count} Stunden',
        'minutes' => '{count} Minuten'
    ],
    
    'pagination' => [
        'label' => 'Seitennavigation',
        'previous' => 'Zurück',
        'next' => 'Weiter',
        'page_info' => 'Seite {current} von {total}'
    ],
    
    'footer' => [
        'rights' => 'Alle Rechte vorbehalten.',
        'powered_by' => 'Unterstützt von',
        'license' => 'Lizenziert unter'
    ],
    
    'error' => [
        'not_found' => 'Seite nicht gefunden',
        'server_error' => 'Serverfehler aufgetreten',
        'invalid_request' => 'Ungültige Anfrage',
        'punishment_not_found' => 'Die angeforderte Strafe konnte nicht gefunden werden.',
        'loading_failed' => 'Fehler beim Laden der Strafdetails.'
    ],
    
    'protest' => [
        'title' => 'Bann-Einspruch',
        'description' => 'Wenn du glaubst, dass dein Bann fälschlicherweise ausgestellt wurde, kannst du einen Einspruch zur Überprüfung einreichen.',
        'how_to_title' => 'Wie man einen Bann-Einspruch einreicht',
        'how_to_subtitle' => 'Befolge diese Schritte, um eine Entbannung zu beantragen:',
        'step1_title' => '1. Sammle deine Informationen',
        'step1_desc' => 'Bevor du einen Einspruch einreichst, stelle sicher, dass du Folgendes hast:',
        'step1_items' => [
            'Deinen Minecraft-Benutzernamen',
            'Das Datum und die Uhrzeit deines Banns',
            'Den angegebenen Grund für deinen Bann',
            'Jegliche Beweise, die deinen Fall unterstützen'
        ],
        'step2_title' => '2. Kontaktmethoden',
        'step2_desc' => 'Du kannst deinen Bann-Einspruch über eine der folgenden Methoden einreichen:',
        'discord_title' => 'Discord (Empfohlen)',
        'discord_desc' => 'Tritt unserem Discord-Server bei und erstelle ein Ticket im Kanal #bann-einsprueche',
        'discord_button' => 'Discord beitreten',
        'email_title' => 'E-Mail',
        'email_desc' => 'Sende eine detaillierte E-Mail mit deinem Einspruch an:',
        'forum_title' => 'Forum',
        'forum_desc' => 'Erstelle einen neuen Beitrag im Abschnitt "Bann-Einsprüche" unseres Forums.',
        'forum_button' => 'Forum besuchen',
        'step3_title' => '3. Was du angeben solltest',
        'step3_desc' => 'Dein Einspruch sollte enthalten: Deinen Nickname - Was passiert ist - Warum du Einspruch einlegst - Was du erreichen möchtest - ID von der Seite (z.B. ban&id=181) - Optional: Screenshot als Beweis.',
        'step3_items' => [
            'Deinen Minecraft-Benutzernamen',
            'Das Datum und die ungefähre Uhrzeit des Banns',
            'Das Teammitglied, das dich gebannt hat (falls bekannt)',
            'Eine detaillierte Erklärung, warum du glaubst, dass der Bann unfair war',
            'Jegliche Screenshots oder Beweise, die deinen Fall unterstützen',
            'Eine ehrliche Darstellung der Geschehnisse'
        ],
        'step4_title' => '4. Warte auf die Überprüfung',
        'step4_desc' => 'Unser Team wird deinen Einspruch innerhalb von 48-72 Stunden überprüfen. Bitte sei geduldig und reiche nicht mehrere Einsprüche für denselben Bann ein.',
        'guidelines_title' => 'Wichtige Richtlinien',
        'guidelines_items' => [
            'Sei ehrlich und respektvoll in deinem Einspruch',
            'Lüge nicht und gib keine falschen Informationen an',
            'Spamme nicht und reiche nicht mehrere Einsprüche ein',
            'Akzeptiere die endgültige Entscheidung des Teams',
            'Bann-Umgehung führt zu einem permanenten Bann'
        ],
        'warning_title' => 'Warnung',
        'warning_desc' => 'Das Einreichen falscher Informationen oder der Versuch, das Team zu täuschen, führt zur Ablehnung deines Einspruchs und kann zu einer verlängerten Strafe führen.',
        'form_not_available' => 'Die direkte Einreichung von Einsprüchen ist derzeit nicht verfügbar. Bitte nutze eine der oben genannten Kontaktmethoden.'
    ],
    
    'admin' => [
        'dashboard' => 'Admin-Dashboard',
        'login' => 'Admin-Anmeldung',
        'logout' => 'Abmelden',
        'password' => 'Passwort',
        'export_data' => 'Daten exportieren',
        'export_desc' => 'Strafdaten in verschiedenen Formaten exportieren',
        'import_data' => 'Daten importieren',
        'import_desc' => 'Strafdaten aus JSON- oder XML-Dateien importieren',
        'data_type' => 'Datentyp',
        'all_punishments' => 'Alle Strafen',
        'select_file' => 'Datei auswählen',
        'import' => 'Importieren',
        'settings' => 'Einstellungen',
        'show_player_uuid' => 'Spieler-UUID anzeigen',
        'footer_site_name' => 'Seitenname in der Fußzeile',
        'footer_site_name_desc' => 'Seitenname, der im Copyright-Text der Fußzeile angezeigt wird',
    ],
];