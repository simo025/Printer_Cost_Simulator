<?php
/* Copyright (C) 2025 IT-BOX Maroc */

if (!defined('NOREQUIREUSER')) {
    define('NOREQUIREUSER', '1');
}
if (!defined('NOREQUIREDB')) {
    define('NOREQUIREDB', '1');
}
if (!defined('NOREQUIRESOC')) {
    define('NOREQUIRESOC', '1');
}
if (!defined('NOREQUIRETRAN')) {
    define('NOREQUIRETRAN', '1');
}
if (!defined('NOCSRFCHECK')) {
    define('NOCSRFCHECK', 1);
}
if (!defined('NOTOKENRENEWAL')) {
    define('NOTOKENRENEWAL', 1);
}
if (!defined('NOLOGIN')) {
    define('NOLOGIN', 1);
}
if (!defined('NOREQUIREMENU')) {
    define('NOREQUIREMENU', 1);
}
if (!defined('NOREQUIREHTML')) {
    define('NOREQUIREHTML', 1);
}
if (!defined('NOREQUIREAJAX')) {
    define('NOREQUIREAJAX', '1');
}

require_once '../../main.inc.php';

// Define js type
header('Content-type: application/javascript');
// Important: Following code is to avoid page request by browser and PHP CPU at each Dolibarr page access.
if (empty($dolibarr_nocache)) {
    header('Cache-Control: max-age=10800, public, must-revalidate');
} else {
    header('Cache-Control: no-cache');
}
?>

/* JavaScript for PrintCostSim module */

var PrintCostSim = {
    
    init: function() {
        console.log('PrintCostSim module initialized');
        this.bindEvents();
    },
    
    bindEvents: function() {
        // Auto-calculate costs when values change
        jQuery(document).on('change', '.printcostsim-calc-field', function() {
            PrintCostSim.calculateCosts();
        });
        
        // Refresh statistics
        jQuery(document).on('click', '.printcostsim-refresh-stats', function() {
            PrintCostSim.refreshStats();
        });
    },
    
    calculateCosts: function() {
        // Auto-calculation logic will be implemented here
        console.log('Calculating costs...');
    },
    
    refreshStats: function() {
        // Refresh statistics via AJAX
        console.log('Refreshing statistics...');
    },
    
    showMessage: function(message, type) {
        type = type || 'info';
        // Show notification message
        if (typeof $.jnotify !== 'undefined') {
            $.jnotify(message, type);
        } else {
            alert(message);
        }
    }
};

// Initialize when document is ready
jQuery(document).ready(function() {
    PrintCostSim.init();
});

