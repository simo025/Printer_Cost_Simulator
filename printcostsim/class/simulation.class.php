<?php
/* Copyright (C) 2025 IT-BOX Maroc
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

/**
 * Class for Simulation object
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';

class Simulation extends CommonObject
{
    /**
     * @var string ID to identify managed object
     */
    public $element = 'simulation';

    /**
     * @var string Name of table without prefix where object is stored
     */
    public $table_element = 'printcostsim_simulation';

    /**
     * @var int  Does object support multicompany module ? 0=No test on entity, 1=Test with field entity, 2=Test with link by societe
     */
    public $ismultientitymanaged = 0;

    /**
     * @var int  Does object support extrafields ? 0=No, 1=Yes
     */
    public $isextrafieldmanaged = 0;

    /**
     * @var string String with name of icon for myobject. Must be the part after the 'object_' into object_myobject.png
     */
    public $picto = 'simulation@printcostsim';

    const STATUS_DRAFT = 0;
    const STATUS_VALIDATED = 1;
    const STATUS_CANCELED = 9;

    /**
     * @var array  Array with all fields and their property. Do not use it as a static var. It may be modified by constructor.
     */
    public $fields = array(
        'rowid' => array('type' => 'integer', 'label' => 'TechnicalID', 'enabled' => '1', 'position' => 1, 'notnull' => 1, 'visible' => 0, 'noteditable' => '1', 'index' => 1, 'comment' => "Id"),
        'ref' => array('type' => 'varchar(128)', 'label' => 'Ref', 'enabled' => '1', 'position' => 10, 'notnull' => 1, 'visible' => 1, 'noteditable' => '0', 'default' => '', 'index' => 1, 'searchall' => 1, 'showoncombobox' => '1', 'comment' => "Reference of object"),
        'label' => array('type' => 'varchar(255)', 'label' => 'Label', 'enabled' => '1', 'position' => 20, 'notnull' => 1, 'visible' => 1, 'searchall' => 1, 'css' => 'minwidth300', 'help' => "Help text", 'showoncombobox' => '2'),
        'description' => array('type' => 'text', 'label' => 'Description', 'enabled' => '1', 'position' => 30, 'notnull' => 0, 'visible' => 3),
        'fk_machine' => array('type' => 'integer:Machine:printcostsim/class/machine.class.php:1', 'label' => 'Machine', 'enabled' => '1', 'position' => 40, 'notnull' => 1, 'visible' => 1, 'index' => 1),
        'quantity' => array('type' => 'integer', 'label' => 'Quantity', 'enabled' => '1', 'position' => 50, 'notnull' => 0, 'visible' => 1, 'default' => '1'),
        'pages_per_document' => array('type' => 'integer', 'label' => 'PagesPerDocument', 'enabled' => '1', 'position' => 60, 'notnull' => 0, 'visible' => 1, 'default' => '1'),
        'paper_format' => array('type' => 'varchar(32)', 'label' => 'PaperFormat', 'enabled' => '1', 'position' => 70, 'notnull' => 0, 'visible' => 1, 'default' => 'A4'),
        'paper_weight' => array('type' => 'integer', 'label' => 'PaperWeight', 'enabled' => '1', 'position' => 80, 'notnull' => 0, 'visible' => 1, 'default' => '80'),
        'paper_cost' => array('type' => 'double(10,4)', 'label' => 'PaperCost', 'enabled' => '1', 'position' => 90, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'print_quality' => array('type' => 'varchar(32)', 'label' => 'PrintQuality', 'enabled' => '1', 'position' => 100, 'notnull' => 0, 'visible' => 1, 'default' => 'normal'),
        'color_mode' => array('type' => 'varchar(32)', 'label' => 'ColorMode', 'enabled' => '1', 'position' => 110, 'notnull' => 0, 'visible' => 1, 'default' => 'color'),
        'duplex' => array('type' => 'integer', 'label' => 'Duplex', 'enabled' => '1', 'position' => 120, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'margin_percent' => array('type' => 'double(5,2)', 'label' => 'MarginPercent', 'enabled' => '1', 'position' => 130, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'total_cost' => array('type' => 'price', 'label' => 'TotalCost', 'enabled' => '1', 'position' => 140, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'cost_per_page' => array('type' => 'double(10,4)', 'label' => 'CostPerPage', 'enabled' => '1', 'position' => 150, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'cost_per_document' => array('type' => 'double(10,4)', 'label' => 'CostPerDocument', 'enabled' => '1', 'position' => 160, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'ai_optimized' => array('type' => 'integer', 'label' => 'AIOptimized', 'enabled' => '1', 'position' => 170, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'ai_suggestions' => array('type' => 'text', 'label' => 'AISuggestions', 'enabled' => '1', 'position' => 180, 'notnull' => 0, 'visible' => 3),
        'date_creation' => array('type' => 'datetime', 'label' => 'DateCreation', 'enabled' => '1', 'position' => 500, 'notnull' => 1, 'visible' => -2),
        'tms' => array('type' => 'timestamp', 'label' => 'DateModification', 'enabled' => '1', 'position' => 501, 'notnull' => 0, 'visible' => -2),
        'fk_user_creat' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserAuthor', 'enabled' => '1', 'position' => 510, 'notnull' => 1, 'visible' => -2, 'foreignkey' => 'user.rowid'),
        'fk_user_modif' => array('type' => 'integer:User:user/class/user.class.php', 'label' => 'UserModif', 'enabled' => '1', 'position' => 511, 'notnull' => -1, 'visible' => -2),
        'import_key' => array('type' => 'varchar(14)', 'label' => 'ImportId', 'enabled' => '1', 'position' => 1000, 'notnull' => -1, 'visible' => -2),
        'status' => array('type' => 'smallint', 'label' => 'Status', 'enabled' => '1', 'position' => 1000, 'notnull' => 1, 'visible' => 1, 'default' => '1', 'index' => 1, 'arrayofkeyval' => array('0' => 'Draft', '1' => 'Active', '9' => 'Canceled')),
    );

    public $rowid;
    public $ref;
    public $label;
    public $description;
    public $fk_machine;
    public $quantity;
    public $pages_per_document;
    public $paper_format;
    public $paper_weight;
    public $paper_cost;
    public $print_quality;
    public $color_mode;
    public $duplex;
    public $margin_percent;
    public $total_cost;
    public $cost_per_page;
    public $cost_per_document;
    public $ai_optimized;
    public $ai_suggestions;
    public $date_creation;
    public $tms;
    public $fk_user_creat;
    public $fk_user_modif;
    public $import_key;
    public $status;

    /**
     * Constructor
     *
     * @param DoliDb $db Database handler
     */
    public function __construct(DoliDB $db)
    {
        global $conf, $langs;

        $this->db = $db;

        if (empty($conf->global->MAIN_SHOW_TECHNICAL_ID) && isset($this->fields['rowid'])) {
            $this->fields['rowid']['visible'] = 0;
        }
        if (empty($conf->multicompany->enabled) && isset($this->fields['entity'])) {
            $this->fields['entity']['enabled'] = 0;
        }

        // Unset fields that are disabled
        foreach ($this->fields as $key => $val) {
            if (isset($val['enabled']) && empty($val['enabled'])) {
                unset($this->fields[$key]);
            }
        }

        // Translate some data of arrayofkeyval
        if (is_object($langs)) {
            foreach ($this->fields as $key => $val) {
                if (!empty($val['arrayofkeyval']) && is_array($val['arrayofkeyval'])) {
                    foreach ($val['arrayofkeyval'] as $key2 => $val2) {
                        $this->fields[$key]['arrayofkeyval'][$key2] = $langs->trans($val2);
                    }
                }
            }
        }
    }

    /**
     * Create object into database
     *
     * @param  User $user      User that creates
     * @param  bool $notrigger false=launch triggers after, true=disable triggers
     * @return int             <0 if KO, Id of created object if OK
     */
    public function create(User $user, $notrigger = false)
    {
        $resultcreate = $this->createCommon($user, $notrigger);

        if ($resultcreate < 0) {
            return $resultcreate;
        }

        return $resultcreate;
    }

    /**
     * Load object in memory from the database
     *
     * @param int    $id   Id object
     * @param string $ref  Ref
     * @return int         <0 if KO, 0 if not found, >0 if OK
     */
    public function fetch($id, $ref = null)
    {
        $result = $this->fetchCommon($id, $ref);
        if ($result > 0 && !empty($this->table_element_line)) {
            $this->fetchLines();
        }
        return $result;
    }

    /**
     * Update object into database
     *
     * @param  User $user      User that modifies
     * @param  bool $notrigger false=launch triggers after, true=disable triggers
     * @return int             <0 if KO, >0 if OK
     */
    public function update(User $user, $notrigger = false)
    {
        return $this->updateCommon($user, $notrigger);
    }

    /**
     * Delete object in database
     *
     * @param User $user       User that deletes
     * @param bool $notrigger  false=launch triggers after, true=disable triggers
     * @return int             <0 if KO, >0 if OK
     */
    public function delete(User $user, $notrigger = false)
    {
        return $this->deleteCommon($user, $notrigger);
    }

    /**
     *  Return a link to the object card (with optionaly the picto)
     *
     *  @param  int     $withpicto                  Include picto in link (0=No picto, 1=Include picto into link, 2=Only picto)
     *  @param  string  $option                     On what the link point to ('nolink', ...)
     *  @param  int     $notooltip                  1=Disable tooltip
     *  @param  string  $morecss                    Add more css on link
     *  @param  int     $save_lastsearch_value      -1=Auto, 0=No save of lastsearch_values when clicking, 1=Save lastsearch_values whenclicking
     *  @return	string                              String with URL
     */
    public function getNomUrl($withpicto = 0, $option = '', $notooltip = 0, $morecss = '', $save_lastsearch_value = -1)
    {
        global $conf, $langs, $hookmanager;

        if (!empty($conf->dol_no_mouse_hover)) {
            $notooltip = 1; // Force disable tooltips
        }

        $result = '';

        $label = img_picto('', $this->picto).' <u>'.$langs->trans("Simulation").'</u>';
        if (isset($this->status)) {
            $label .= ' '.$this->getLibStatut(5);
        }
        $label .= '<br>';
        $label .= '<b>'.$langs->trans('Ref').':</b> '.$this->ref;

        $url = dol_buildpath('/printcostsim/simulation/card.php', 1).'?id='.$this->id;

        if ($option != 'nolink') {
            // Add param to save lastsearch_values or not
            $add_save_lastsearch_values = ($save_lastsearch_value == 1 ? 1 : 0);
            if ($save_lastsearch_value == -1 && preg_match('/list\.php/', $_SERVER["PHP_SELF"])) {
                $add_save_lastsearch_values = 1;
            }
            if ($add_save_lastsearch_values) {
                $url .= '&save_lastsearch_values=1';
            }
        }

        $linkclose = '';
        if (empty($notooltip)) {
            if (!empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER)) {
                $label = $langs->trans("ShowSimulation");
                $linkclose .= ' alt="'.$label.'"';
            }
            $linkclose .= ' title="'.dol_escape_htmltag($label, 1).'"';
            $linkclose .= ' class="classfortooltip'.($morecss ? ' '.$morecss : '').'"';
        } else {
            $linkclose = ($morecss ? ' class="'.$morecss.'"' : '');
        }

        if ($option == 'nolink') {
            $linkstart = '<span';
        } else {
            $linkstart = '<a href="'.$url.'"';
        }
        $linkstart .= $linkclose.'>';
        if ($option == 'nolink') {
            $linkend = '</span>';
        } else {
            $linkend = '</a>';
        }

        $result .= $linkstart;

        if ($withpicto) {
            $result .= img_object(($notooltip ? '' : $label), ($this->picto ? $this->picto : 'generic'), ($notooltip ? (($withpicto != 2) ? 'class="paddingright"' : '') : 'class="'.(($withpicto != 2) ? 'paddingright ' : '').'classfortooltip"'), 0, 0, $notooltip ? 0 : 1);
        }

        if ($withpicto != 2) {
            $result .= $this->ref;
        }

        $result .= $linkend;

        return $result;
    }

    /**
     *  Return label of the status
     *
     *  @param  int		$mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
     *  @return	string 			       Label of status
     */
    public function getLibStatut($mode = 0)
    {
        return $this->LibStatut($this->status, $mode);
    }

    /**
     *  Return the status
     *
     *  @param	int		$status        Id status
     *  @param  int		$mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
     *  @return string 			       Label of status
     */
    public function LibStatut($status, $mode = 0)
    {
        if (empty($this->labelStatus) || empty($this->labelStatusShort)) {
            global $langs;
            $this->labelStatus[self::STATUS_DRAFT] = $langs->trans('Draft');
            $this->labelStatus[self::STATUS_VALIDATED] = $langs->trans('Enabled');
            $this->labelStatus[self::STATUS_CANCELED] = $langs->trans('Disabled');
            $this->labelStatusShort[self::STATUS_DRAFT] = $langs->trans('Draft');
            $this->labelStatusShort[self::STATUS_VALIDATED] = $langs->trans('Enabled');
            $this->labelStatusShort[self::STATUS_CANCELED] = $langs->trans('Disabled');
        }

        $statusType = 'status'.$status;
        if ($status == self::STATUS_CANCELED) {
            $statusType = 'status6';
        }

        return dolGetStatus($this->labelStatus[$status], $this->labelStatusShort[$status], '', $statusType, $mode);
    }

    /**
     * Initialise object with example values
     * Id must be 0 if object instance is a specimen
     *
     * @return void
     */
    public function initAsSpecimen()
    {
        $this->initAsSpecimenCommon();
    }

    /**
     *  Returns the reference to the object
     *
     *  @return     string      Reference to the object
     */
    public function getNextNumRef()
    {
        global $langs, $conf;
        $langs->load("printcostsim@printcostsim");

        if (empty($conf->global->PRINTCOSTSIM_SIMULATION_ADDON)) {
            $conf->global->PRINTCOSTSIM_SIMULATION_ADDON = 'mod_simulation_standard';
        }

        if (!empty($conf->global->PRINTCOSTSIM_SIMULATION_ADDON)) {
            $mybool = false;

            $file = $conf->global->PRINTCOSTSIM_SIMULATION_ADDON.".php";
            $classname = $conf->global->PRINTCOSTSIM_SIMULATION_ADDON;

            // Include file with class
            $dirmodels = array_merge(array('/'), (array) $conf->modules_parts['models']);
            foreach ($dirmodels as $reldir) {
                $dir = dol_buildpath($reldir."core/modules/printcostsim/");

                // Load file with numbering class (if found)
                $mybool |= @include_once $dir.$file;
            }

            if ($mybool === false) {
                dol_print_error('', "Failed to include file ".$file);
                return '';
            }

            if (class_exists($classname)) {
                $obj = new $classname();
                $numref = $obj->getNextValue($this);

                if ($numref != '' && $numref != '-1') {
                    return $numref;
                } else {
                    $this->error = $obj->error;
                    return "";
                }
            } else {
                print $langs->trans("Error")." ".$langs->trans("ClassNotFound").' '.$classname;
                return "";
            }
        } else {
            print $langs->trans("ErrorNumberingModuleNotSetup", $this->element);
            return "";
        }
    }

    /**
     * Calculate total cost of simulation
     *
     * @return float Total cost
     */
    public function calculateTotalCost()
    {
        global $db;
        
        $total_cost = 0;
        
        // Paper cost
        $paper_cost = $this->paper_cost * $this->quantity * $this->pages_per_document;
        $total_cost += $paper_cost;
        
        // Machine cost (based on time)
        if ($this->fk_machine > 0) {
            require_once DOL_DOCUMENT_ROOT.'/custom/printcostsim/class/machine.class.php';
            $machine = new Machine($db);
            if ($machine->fetch($this->fk_machine) > 0) {
                // Estimate time based on speed (pages per minute)
                $total_pages = $this->quantity * $this->pages_per_document;
                $time_minutes = $total_pages / $machine->speed_ppm;
                $time_hours = $time_minutes / 60;
                
                $machine_cost = $time_hours * $machine->cost_per_hour;
                $total_cost += $machine_cost;
                $total_cost += $machine->maintenance_cost;
            }
        }
        
        // Consumable costs
        $sql = "SELECT sc.quantity_used, sc.cost_total";
        $sql .= " FROM ".MAIN_DB_PREFIX."printcostsim_simulation_consumable sc";
        $sql .= " WHERE sc.fk_simulation = ".((int) $this->id);
        
        $resql = $db->query($sql);
        if ($resql) {
            while ($obj = $db->fetch_object($resql)) {
                $total_cost += $obj->cost_total;
            }
        }
        
        // Apply margin
        if ($this->margin_percent > 0) {
            $total_cost = $total_cost * (1 + ($this->margin_percent / 100));
        }
        
        return $total_cost;
    }

    /**
     * Calculate cost per page
     *
     * @return float Cost per page
     */
    public function calculateCostPerPage()
    {
        $total_pages = $this->quantity * $this->pages_per_document;
        if ($total_pages > 0) {
            return $this->total_cost / $total_pages;
        }
        return 0;
    }

    /**
     * Calculate cost per document
     *
     * @return float Cost per document
     */
    public function calculateCostPerDocument()
    {
        if ($this->quantity > 0) {
            return $this->total_cost / $this->quantity;
        }
        return 0;
    }
}

