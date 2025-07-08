<?php
/* Copyright (C) 2025 IT-BOX Maroc
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

/**
 * Class for Consumable object
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';

class Consumable extends CommonObject
{
    /**
     * @var string ID to identify managed object
     */
    public $element = 'consumable';

    /**
     * @var string Name of table without prefix where object is stored
     */
    public $table_element = 'printcostsim_consumable';

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
    public $picto = 'consumable@printcostsim';

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
        'type' => array('type' => 'varchar(64)', 'label' => 'Type', 'enabled' => '1', 'position' => 40, 'notnull' => 0, 'visible' => 1, 'default' => 'ink'),
        'color' => array('type' => 'varchar(32)', 'label' => 'Color', 'enabled' => '1', 'position' => 50, 'notnull' => 0, 'visible' => 1),
        'brand' => array('type' => 'varchar(128)', 'label' => 'Brand', 'enabled' => '1', 'position' => 60, 'notnull' => 0, 'visible' => 1),
        'model' => array('type' => 'varchar(128)', 'label' => 'Model', 'enabled' => '1', 'position' => 70, 'notnull' => 0, 'visible' => 1),
        'capacity_ml' => array('type' => 'double(10,2)', 'label' => 'CapacityML', 'enabled' => '1', 'position' => 80, 'notnull' => 0, 'visible' => 1),
        'capacity_pages' => array('type' => 'integer', 'label' => 'CapacityPages', 'enabled' => '1', 'position' => 90, 'notnull' => 0, 'visible' => 1),
        'cost_unit' => array('type' => 'price', 'label' => 'CostUnit', 'enabled' => '1', 'position' => 100, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'cost_per_ml' => array('type' => 'double(10,4)', 'label' => 'CostPerML', 'enabled' => '1', 'position' => 110, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'cost_per_page' => array('type' => 'double(10,4)', 'label' => 'CostPerPage', 'enabled' => '1', 'position' => 120, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'coverage_percent' => array('type' => 'double(5,2)', 'label' => 'CoveragePercent', 'enabled' => '1', 'position' => 130, 'notnull' => 0, 'visible' => 1, 'default' => '5'),
        'active' => array('type' => 'integer', 'label' => 'Active', 'enabled' => '1', 'position' => 140, 'notnull' => 0, 'visible' => 1, 'default' => '1'),
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
    public $type;
    public $color;
    public $brand;
    public $model;
    public $capacity_ml;
    public $capacity_pages;
    public $cost_unit;
    public $cost_per_ml;
    public $cost_per_page;
    public $coverage_percent;
    public $active;
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

        $label = img_picto('', $this->picto).' <u>'.$langs->trans("Consumable").'</u>';
        if (isset($this->status)) {
            $label .= ' '.$this->getLibStatut(5);
        }
        $label .= '<br>';
        $label .= '<b>'.$langs->trans('Ref').':</b> '.$this->ref;

        $url = dol_buildpath('/printcostsim/consumable/card.php', 1).'?id='.$this->id;

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
                $label = $langs->trans("ShowConsumable");
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

        if (empty($conf->global->PRINTCOSTSIM_CONSUMABLE_ADDON)) {
            $conf->global->PRINTCOSTSIM_CONSUMABLE_ADDON = 'mod_consumable_standard';
        }

        if (!empty($conf->global->PRINTCOSTSIM_CONSUMABLE_ADDON)) {
            $mybool = false;

            $file = $conf->global->PRINTCOSTSIM_CONSUMABLE_ADDON.".php";
            $classname = $conf->global->PRINTCOSTSIM_CONSUMABLE_ADDON;

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
     * Calculate cost per page based on capacity and unit cost
     *
     * @return float Cost per page
     */
    public function calculateCostPerPage()
    {
        if ($this->capacity_pages > 0 && $this->cost_unit > 0) {
            return $this->cost_unit / $this->capacity_pages;
        }
        return 0;
    }

    /**
     * Calculate cost per ml based on capacity and unit cost
     *
     * @return float Cost per ml
     */
    public function calculateCostPerML()
    {
        if ($this->capacity_ml > 0 && $this->cost_unit > 0) {
            return $this->cost_unit / $this->capacity_ml;
        }
        return 0;
    }
}

