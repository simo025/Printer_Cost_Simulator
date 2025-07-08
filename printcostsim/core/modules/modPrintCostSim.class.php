<?php
/* Copyright (C) 2025 IT-BOX Maroc
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

/**
 * Module descriptor for PrintCostSim
 * 
 * @author IT-BOX Maroc
 * @version 4.0.0
 */

include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

class modPrintCostSim extends DolibarrModules
{
    public function __construct($db)
    {
        global $langs, $conf;
        
        $this->db = $db;
        
        // Module identification
        $this->numero = 50004;
        $this->rights_class = 'printcostsim';
        $this->family = "products";
        $this->module_position = '90';
        $this->name = preg_replace('/^mod/i', '', get_class($this));
        $this->description = "Module de simulation des coûts d'impression avec IA";
        $this->descriptionlong = "Module complet pour simuler et optimiser les coûts d'impression";
        
        // Version
        $this->version = '4.0.0';
        $this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
        
        // Author
        $this->editor_name = 'IT-BOX Maroc';
        $this->editor_url = 'https://github.com/simo025';
        
        // Module parts
        $this->module_parts = array(
            'triggers' => 0,
            'login' => 0,
            'substitutions' => 0,
            'menus' => 1,
            'theme' => 0,
            'tpl' => 0,
            'barcode' => 0,
            'models' => 0,
            'css' => array('/printcostsim/css/printcostsim.css.php'),
            'js' => array('/printcostsim/js/printcostsim.js.php'),
            'hooks' => array(),
            'moduleforexternal' => 0,
        );
        
        // Data directories
        $this->dirs = array();
        
        // Config pages
        $this->config_page_url = array("setup.php@printcostsim");
        
        // Dependencies
        $this->hidden = false;
        $this->depends = array();
        $this->requiredby = array();
        $this->conflictwith = array();
        $this->langfiles = array("printcostsim@printcostsim");
        $this->phpmin = array(7, 4);
        $this->need_dolibarr_version = array(15, 0);
        $this->warnings_activation = array();
        $this->warnings_activation_ext = array();
        
        // Constants
        $this->const = array();
        
        // Boxes
        $this->boxes = array();
        
        // Cronjobs
        $this->cronjobs = array();
        
        // Permissions
        $r = 0;
        
        // Permission pour lire
        $this->rights[$r][0] = $this->numero + $r;
        $this->rights[$r][1] = 'Lire les simulations PrintCostSim';
        $this->rights[$r][4] = 'read';
        $this->rights[$r][5] = 1;
        $r++;
        
        // Permission pour créer/modifier
        $this->rights[$r][0] = $this->numero + $r;
        $this->rights[$r][1] = 'Créer/modifier les simulations PrintCostSim';
        $this->rights[$r][4] = 'write';
        $this->rights[$r][5] = 0;
        $r++;
        
        // Permission pour supprimer
        $this->rights[$r][0] = $this->numero + $r;
        $this->rights[$r][1] = 'Supprimer les simulations PrintCostSim';
        $this->rights[$r][4] = 'delete';
        $this->rights[$r][5] = 0;
        $r++;
        
        // Menus
        $this->menu = array();
        $r = 0;
        
        // Menu principal
        $this->menu[$r++] = array(
            'fk_menu' => '',
            'type' => 'top',
            'titre' => 'PrintCostSim',
            'mainmenu' => 'printcostsim',
            'leftmenu' => '',
            'url' => '/custom/printcostsim/index.php',
            'langs' => 'printcostsim@printcostsim',
            'position' => 1000 + $r,
            'enabled' => '$conf->printcostsim->enabled',
            'perms' => '$user->rights->printcostsim->read',
            'target' => '',
            'user' => 2,
        );
        
        // Menu latéral - Machines
        $this->menu[$r++] = array(
            'fk_menu' => 'fk_mainmenu=printcostsim',
            'type' => 'left',
            'titre' => 'Machines',
            'mainmenu' => 'printcostsim',
            'leftmenu' => 'printcostsim_machines',
            'url' => '/custom/printcostsim/machine/list.php',
            'langs' => 'printcostsim@printcostsim',
            'position' => 1000 + $r,
            'enabled' => '$conf->printcostsim->enabled',
            'perms' => '$user->rights->printcostsim->read',
            'target' => '',
            'user' => 2,
        );
        
        // Menu latéral - Nouvelle machine
        $this->menu[$r++] = array(
            'fk_menu' => 'fk_mainmenu=printcostsim,fk_leftmenu=printcostsim_machines',
            'type' => 'left',
            'titre' => 'Nouvelle machine',
            'mainmenu' => 'printcostsim',
            'leftmenu' => '',
            'url' => '/custom/printcostsim/machine/card.php?action=create',
            'langs' => 'printcostsim@printcostsim',
            'position' => 1000 + $r,
            'enabled' => '$conf->printcostsim->enabled',
            'perms' => '$user->rights->printcostsim->write',
            'target' => '',
            'user' => 2,
        );
        
        // Menu latéral - Consommables
        $this->menu[$r++] = array(
            'fk_menu' => 'fk_mainmenu=printcostsim',
            'type' => 'left',
            'titre' => 'Consommables',
            'mainmenu' => 'printcostsim',
            'leftmenu' => 'printcostsim_consumables',
            'url' => '/custom/printcostsim/consumable/list.php',
            'langs' => 'printcostsim@printcostsim',
            'position' => 1000 + $r,
            'enabled' => '$conf->printcostsim->enabled',
            'perms' => '$user->rights->printcostsim->read',
            'target' => '',
            'user' => 2,
        );
        
        // Menu latéral - Nouveau consommable
        $this->menu[$r++] = array(
            'fk_menu' => 'fk_mainmenu=printcostsim,fk_leftmenu=printcostsim_consumables',
            'type' => 'left',
            'titre' => 'Nouveau consommable',
            'mainmenu' => 'printcostsim',
            'leftmenu' => '',
            'url' => '/custom/printcostsim/consumable/card.php?action=create',
            'langs' => 'printcostsim@printcostsim',
            'position' => 1000 + $r,
            'enabled' => '$conf->printcostsim->enabled',
            'perms' => '$user->rights->printcostsim->write',
            'target' => '',
            'user' => 2,
        );
        
        // Menu latéral - Simulations
        $this->menu[$r++] = array(
            'fk_menu' => 'fk_mainmenu=printcostsim',
            'type' => 'left',
            'titre' => 'Simulations',
            'mainmenu' => 'printcostsim',
            'leftmenu' => 'printcostsim_simulations',
            'url' => '/custom/printcostsim/simulation/list.php',
            'langs' => 'printcostsim@printcostsim',
            'position' => 1000 + $r,
            'enabled' => '$conf->printcostsim->enabled',
            'perms' => '$user->rights->printcostsim->read',
            'target' => '',
            'user' => 2,
        );
        
        // Menu latéral - Nouvelle simulation
        $this->menu[$r++] = array(
            'fk_menu' => 'fk_mainmenu=printcostsim,fk_leftmenu=printcostsim_simulations',
            'type' => 'left',
            'titre' => 'Nouvelle simulation',
            'mainmenu' => 'printcostsim',
            'leftmenu' => '',
            'url' => '/custom/printcostsim/simulation/card.php?action=create',
            'langs' => 'printcostsim@printcostsim',
            'position' => 1000 + $r,
            'enabled' => '$conf->printcostsim->enabled',
            'perms' => '$user->rights->printcostsim->write',
            'target' => '',
            'user' => 2,
        );
    }
    
    public function init($options = '')
    {
        global $conf, $langs, $user;
        
        dol_syslog("PrintCostSim: Début de l'activation du module", LOG_INFO);
        
        // Créer les tables SQL
        $result = $this->loadTables();
        if ($result < 0) {
            dol_syslog("PrintCostSim: Erreur lors de la création des tables", LOG_ERR);
            return -1;
        }
        
        dol_syslog("PrintCostSim: Tables créées avec succès", LOG_INFO);
        
        $result = $this->_init($this->const, $this->boxes, $this->cronjobs, $this->dirs, $this->menu, $this->rights);
        
        if ($result > 0) {
            dol_syslog("PrintCostSim: Module activé avec succès", LOG_INFO);
        } else {
            dol_syslog("PrintCostSim: Erreur lors de l'activation du module", LOG_ERR);
        }
        
        return $result;
    }
    
    public function remove($options = '')
    {
        $sql = array();
        return $this->_remove($sql, $options);
    }
    
    /**
     * Créer les tables SQL du module
     */
    private function loadTables()
    {
        global $conf;
        
        dol_syslog("PrintCostSim: Début de la création des tables", LOG_INFO);
        
        $error = 0;
        $sql_files = array(
            'llx_printcostsim_machine.sql',
            'llx_printcostsim_consumable.sql', 
            'llx_printcostsim_simulation.sql',
            'llx_printcostsim_simulation_consumable.sql'
        );
        
        foreach ($sql_files as $sql_file) {
            // Chemin correct vers les fichiers SQL
            $sql_path = dol_buildpath('/printcostsim/sql/'.$sql_file, 0);
            
            dol_syslog("PrintCostSim: Tentative de lecture du fichier $sql_path", LOG_INFO);
            
            if (file_exists($sql_path)) {
                $sql_content = file_get_contents($sql_path);
                if ($sql_content === false) {
                    dol_syslog("PrintCostSim: Impossible de lire le fichier $sql_file", LOG_ERR);
                    $error++;
                    continue;
                }
                
                // Remplacer le préfixe de base de données
                $sql_content = str_replace('@DB_PREFIX@', MAIN_DB_PREFIX, $sql_content);
                
                dol_syslog("PrintCostSim: Contenu SQL pour $sql_file: ".substr($sql_content, 0, 200)."...", LOG_DEBUG);
                
                // Exécuter les requêtes SQL
                $sql_queries = explode(';', $sql_content);
                foreach ($sql_queries as $query) {
                    $query = trim($query);
                    if (!empty($query) && !preg_match('/^--/', $query)) {
                        dol_syslog("PrintCostSim: Exécution de la requête: ".substr($query, 0, 100)."...", LOG_DEBUG);
                        
                        $result = $this->db->query($query);
                        if (!$result) {
                            $error_msg = $this->db->lasterror();
                            // Ignorer les erreurs "table already exists"
                            if (!preg_match('/already exists|table.*exists/i', $error_msg)) {
                                dol_syslog("PrintCostSim: Erreur SQL dans $sql_file: ".$error_msg, LOG_ERR);
                                $error++;
                            } else {
                                dol_syslog("PrintCostSim: Table déjà existante (ignoré): ".$error_msg, LOG_INFO);
                            }
                        } else {
                            dol_syslog("PrintCostSim: Requête exécutée avec succès", LOG_DEBUG);
                        }
                    }
                }
            } else {
                dol_syslog("PrintCostSim: Fichier SQL non trouvé: $sql_path", LOG_WARNING);
                // Ne pas considérer comme une erreur fatale si le fichier n'existe pas
            }
        }
        
        if ($error > 0) {
            dol_syslog("PrintCostSim: $error erreurs lors de la création des tables", LOG_ERR);
            return -1;
        } else {
            dol_syslog("PrintCostSim: Toutes les tables créées avec succès", LOG_INFO);
            return 1;
        }
    }
}

