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

session_cache_limiter('public');

require_once '../../main.inc.php';

// Define css type
header('Content-type: text/css');
// Important: Following code is to avoid page request by browser and PHP CPU at each Dolibarr page access.
if (empty($dolibarr_nocache)) {
    header('Cache-Control: max-age=10800, public, must-revalidate');
} else {
    header('Cache-Control: no-cache');
}
?>

/* CSS for PrintCostSim module */

.printcostsim-dashboard {
    margin: 20px 0;
}

.printcostsim-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 30px;
}

.printcostsim-stat-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    flex: 1;
    min-width: 200px;
    text-align: center;
}

.printcostsim-stat-number {
    font-size: 2em;
    font-weight: bold;
    color: #007bff;
    margin-bottom: 10px;
}

.printcostsim-stat-label {
    color: #6c757d;
    font-size: 0.9em;
}

.printcostsim-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.printcostsim-action-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    transition: box-shadow 0.3s ease;
}

.printcostsim-action-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.printcostsim-icon {
    width: 48px;
    height: 48px;
    margin-bottom: 15px;
}

@media (max-width: 768px) {
    .printcostsim-stats {
        flex-direction: column;
    }
    
    .printcostsim-actions {
        grid-template-columns: 1fr;
    }
}

