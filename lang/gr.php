<?php

return [
    'site' => [
        'name' => 'LiteBans',
        'title' => '{page} - LiteBans',
        'description' => 'Δημόσια διεπαφή για την προβολή ποινών και αποκλεισμών του διακομιστή'
    ],
    
    'nav' => [
        'home' => 'Αρχική',
        'bans' => 'Αποκλεισμοί',
        'mutes' => 'Σιγήσεις',
        'warnings' => 'Προειδοποιήσεις',
        'kicks' => 'Αποβολές',
        'statistics' => 'Στατιστικά',
        'language' => 'Γλώσσα',
        'theme' => 'Θέμα',
        'admin' => 'Διαχείριση',
        'protest' => 'Έφεση Αποκλεισμού',
    ],
    
    'home' => [
        'welcome' => 'Ποινές Διακομιστή',
        'description' => 'Αναζητήστε ποινές παικτών και δείτε την πρόσφατη δραστηριότητα',
        'recent_activity' => 'Πρόσφατη Δραστηριότητα',
        'recent_bans' => 'Πρόσφατοι Αποκλεισμοί',
        'recent_mutes' => 'Πρόσφατες Σιγήσεις',
        'no_recent_bans' => 'Δεν βρέθηκαν πρόσφατοι αποκλεισμοί',
        'no_recent_mutes' => 'Δεν βρέθηκαν πρόσφατες σιγήσεις',
        'view_all_bans' => 'Προβολή όλων των αποκλεισμών',
        'view_all_mutes' => 'Προβολή όλων των σιγήσεων'
    ],
    
    'search' => [
        'title' => 'Αναζήτηση Παίκτη',
        'placeholder' => 'Εισαγάγετε όνομα παίκτη ή UUID...',
        'help' => 'Μπορείτε να αναζητήσετε με βάση το όνομα παίκτη ή το πλήρες UUID',
        'button' => 'Αναζήτηση',
        'no_results' => 'Δεν βρέθηκαν ποινές για αυτόν τον παίκτη',
        'error' => 'Παρουσιάστηκε σφάλμα κατά την αναζήτηση',
        'network_error' => 'Παρουσιάστηκε σφάλμα δικτύου. Παρακαλώ προσπαθήστε ξανά.'
    ],
    
    'stats' => [
        'title' => 'Στατιστικά Διακομιστή',
        'active_bans' => 'Ενεργοί Αποκλεισμοί',
        'active_mutes' => 'Ενεργές Σιγήσεις',
        'total_warnings' => 'Σύνολο Προειδοποιήσεων',
        'total_kicks' => 'Σύνολο Αποβολών',
        'total_of' => 'από',
        'all_time' => 'συνολικά',
        'most_banned_players' => 'Οι πιο αποκλεισμένοι παίκτες',
        'most_active_staff' => 'Το πιο ενεργό προσωπικό',
        'top_ban_reasons' => 'Κορυφαίοι λόγοι αποκλεισμού',
        'recent_activity_overview' => 'Επισκόπηση πρόσφατης δραστηριότητας',
        'activity_by_day' => 'Δραστηριότητα ανά ημέρα',
        'cache_cleared' => 'Η προσωρινή μνήμη στατιστικών καθαρίστηκε επιτυχώς',
        'cache_clear_failed' => 'Αποτυχία καθαρισμού της προσωρινής μνήμης στατιστικών',
        'clear_cache' => 'Καθαρισμός Cache',
        'last_24h' => 'Τελευταίες 24 ώρες',
        'last_7d' => 'Τελευταίες 7 ημέρες',
        'last_30d' => 'Τελευταίες 30 ημέρες'
    ],
    
    'table' => [
        'player' => 'Παίκτης',
        'reason' => 'Λόγος',
        'staff' => 'Διαχειριστής',
        'date' => 'Ημερομηνία',
        'expires' => 'Λήγει',
        'status' => 'Κατάσταση',
        'actions' => 'Ενέργειες',
        'type' => 'Τύπος',
        'view' => 'Προβολή',
        'total' => 'Σύνολο',
        'active' => 'Ενεργός',
        'last_ban' => 'Τελευταίος Αποκλεισμός',
        'last_action' => 'Τελευταία Ενέργεια',
        'server' => 'Διακομιστής',
    ],
    
    'status' => [
        'active' => 'Ενεργός',
        'inactive' => 'Ανενεργός',
        'expired' => 'Έληξε',
        'removed' => 'Αφαιρέθηκε',
        'completed' => 'Ολοκληρώθηκε',
        'removed_by' => 'Αφαιρέθηκε από'
    ],
    
    'punishment' => [
        'permanent' => 'Μόνιμος',
        'expired' => 'Έληξε'
    ],
    
    'punishments' => [
        'no_data' => 'Δεν βρέθηκαν ποινές',
        'no_data_desc' => 'Προς το παρόν δεν υπάρχουν ποινές για εμφάνιση'
    ],
    
    'detail' => [
        'duration' => 'Διάρκεια',
        'time_left' => 'Υπόλοιπος χρόνος',
        'progress' => 'Πρόοδος',
        'removed_by' => 'Αφαιρέθηκε από',
        'removed_date' => 'Ημερομηνία αφαίρεσης',
        'flags' => 'Σημάνσεις',
        'other_punishments' => 'Άλλες Ποινές'
    ],
    
    'time' => [
        'days' => '{count} ημέρες',
        'hours' => '{count} ώρες',
        'minutes' => '{count} λεπτά'
    ],
    
    'pagination' => [
        'label' => 'Πλοήγηση σελίδας',
        'previous' => 'Προηγούμενη',
        'next' => 'Επόμενη',
        'page_info' => 'Σελίδα {current} από {total}'
    ],
    
    'footer' => [
        'rights' => 'Με επιφύλαξη παντός δικαιώματος.',
        'powered_by' => 'Με την υποστήριξη του',
        'license' => 'Με άδεια χρήσης'
    ],
    
    'error' => [
        'not_found' => 'Η σελίδα δεν βρέθηκε',
        'server_error' => 'Παρουσιάστηκε σφάλμα διακομιστή',
        'invalid_request' => 'Μη έγκυρο αίτημα',
        'punishment_not_found' => 'Η ποινή που ζητήθηκε δεν βρέθηκε.',
        'loading_failed' => 'Αποτυχία φόρτωσης των λεπτομερειών της ποινής.'
    ],
    
    'protest' => [
        'title' => 'Έφεση Αποκλεισμού',
        'description' => 'Αν πιστεύετε ότι ο αποκλεισμός σας έγινε κατά λάθος, μπορείτε να υποβάλετε έφεση για επανεξέταση.',
        'how_to_title' => 'Πώς να υποβάλετε έφεση',
        'how_to_subtitle' => 'Ακολουθήστε αυτά τα βήματα για να ζητήσετε την άρση του αποκλεισμού:',
        'step1_title' => '1. Συγκεντρώστε τις πληροφορίες σας',
        'step1_desc' => 'Πριν υποβάλετε μια έφεση, βεβαιωθείτε ότι έχετε:',
        'step1_items' => [
            'Το όνομα χρήστη σας στο Minecraft',
            'Την ημερομηνία και την ώρα του αποκλεισμού σας',
            'Τον λόγο που δόθηκε για τον αποκλεισμό σας',
            'Οποιαδήποτε αποδεικτικά στοιχεία που υποστηρίζουν την περίπτωσή σας'
        ],
        'step2_title' => '2. Μέθοδοι Επικοινωνίας',
        'step2_desc' => 'Μπορείτε να υποβάλετε την έφεσή σας μέσω μιας από τις ακόλουθες μεθόδους:',
        'discord_title' => 'Discord (Προτείνεται)',
        'discord_desc' => 'Εγγραφείτε στον διακομιστή μας στο Discord και δημιουργήστε ένα ticket στο κανάλι #ban-protests',
        'discord_button' => 'Σύνδεση στο Discord',
        'email_title' => 'Email',
        'email_desc' => 'Στείλτε ένα λεπτομερές email με την έφεσή σας στο:',
        'forum_title' => 'Forum',
        'forum_desc' => 'Δημιουργήστε μια νέα ανάρτηση στην ενότητα Εφέσεις Αποκλεισμών του φόρουμ της ιστοσελίδας μας.',
        'forum_button' => 'Επισκεφθείτε το Forum',
        'step3_title' => '3. Τι να συμπεριλάβετε',
        'step3_desc' => 'Η έφεσή σας πρέπει να περιλαμβάνει: Το ψευδώνυμό σας - Τι συνέβη - Γιατί διαμαρτύρεστε - Τι θέλετε να συμβεί - ID από τον ιστότοπο (π.χ. ban&id=181) - Προαιρετικά: Στιγμιότυπο οθόνης ως απόδειξη.',
        'step3_items' => [
            'Το όνομα χρήστη σας στο Minecraft',
            'Την ημερομηνία και την κατά προσέγγιση ώρα του αποκλεισμού',
            'Το μέλος του προσωπικού που σας απέκλεισε (αν είναι γνωστό)',
            'Μια λεπτομερή εξήγηση του γιατί πιστεύετε ότι ο αποκλεισμός ήταν άδικος',
            'Οποιαδήποτε στιγμιότυπα οθόνης ή αποδεικτικά στοιχεία που υποστηρίζють την περίπτωσή σας',
            'Μια ειλικρινή περιγραφή του τι συνέβη'
        ],
        'step4_title' => '4. Περιμένετε για επανεξέταση',
        'step4_desc' => 'Η ομάδα του προσωπικού μας θα εξετάσει την έφεσή σας εντός 48-72 ωρών. Παρακαλούμε να είστε υπομονετικοί και να μην υποβάλλετε πολλαπλές εφέσεις για τον ίδιο αποκλεισμό.',
        'guidelines_title' => 'Σημαντικές Οδηγίες',
        'guidelines_items' => [
            'Να είστε ειλικρινείς και με σεβασμό στην έφεσή σας',
            'Μην λέτε ψέματα και μην παρέχετε ψευδείς πληροφορίες',
            'Μην κάνετε spam και μην υποβάλλετε πολλαπλές εφέσεις',
            'Αποδεχτείτε την τελική απόφαση της ομάδας του προσωπικού',
            'Η αποφυγή του αποκλεισμού θα οδηγήσει σε μόνιμο αποκλεισμό'
        ],
        'warning_title' => 'Προειδοποίηση',
        'warning_desc' => 'Η υποβολή ψευδών πληροφοριών ή η προσπάθεια εξαπάτησης του προσωπικού θα οδηγήσει στην απόρριψη της έφεσής σας και μπορεί να οδηγήσει σε παράταση της ποινής.',
        'form_not_available' => 'Η απευθείας υποβολή έφεσης δεν είναι διαθέσιμη αυτή τη στιγμή. Παρακαλούμε χρησιμοποιήστε μία από τις παραπάνω μεθόδους επικοινωνίας.'
    ],
    
    'admin' => [
        'dashboard' => 'Πίνακας Ελέγχou Διαχειριστή',
        'login' => 'Σύνδεση Διαχειριστή',
        'logout' => 'Αποσύνδεση',
        'password' => 'Κωδικός πρόσβασης',
        'export_data' => 'Εξαγωγή Δεδομένων',
        'export_desc' => 'Εξαγωγή δεδομένων ποινών σε διάφορες μορφές',
        'import_data' => 'Εισαγωγή Δεδομένων',
        'import_desc' => 'Εισαγωγή δεδομένων ποινών από αρχεία JSON ή XML',
        'data_type' => 'Τύπος Δεδομένων',
        'all_punishments' => 'Όλες οι Ποινές',
        'select_file' => 'Επιλογή Αρχείου',
        'import' => 'Εισαγωγή',
        'settings' => 'Ρυθμίσεις',
        'show_player_uuid' => 'Εμφάνιση UUID Παίκτη',
        'footer_site_name' => 'Όνομα ιστότοπου στο υποσέλιδο',
        'footer_site_name_desc' => 'Όνομα ιστότοπου που εμφανίζεται στο κείμενο πνευματικών δικαιωμάτων στο υποσέλιδο',
    ],
];