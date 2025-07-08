<?php
/* Copyright (C) 2025 IT-BOX Maroc
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

/**
 * Class for Machine object
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonobject.class.php';

class Machine extends CommonObject
{
    /**
     * @var string ID to identify managed object
     */
    public $element = 'machine';

    /**
     * @var string Name of table without prefix where object is stored
     */
    public $table_element = 'printcostsim_machine';

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
    public $picto = 'machine@printcostsim';

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
        'brand' => array('type' => 'varchar(128)', 'label' => 'Brand', 'enabled' => '1', 'position' => 40, 'notnull' => 0, 'visible' => 1),
        'model' => array('type' => 'varchar(128)', 'label' => 'Model', 'enabled' => '1', 'position' => 50, 'notnull' => 0, 'visible' => 1),
        'print_type' => array('type' => 'varchar(64)', 'label' => 'PrintType', 'enabled' => '1', 'position' => 60, 'notnull' => 0, 'visible' => 1, 'default' => 'inkjet'),
        'max_width' => array('type' => 'integer', 'label' => 'MaxWidth', 'enabled' => '1', 'position' => 70, 'notnull' => 0, 'visible' => 1, 'default' => '210'),
        'max_height' => array('type' => 'integer', 'label' => 'MaxHeight', 'enabled' => '1', 'position' => 80, 'notnull' => 0, 'visible' => 1, 'default' => '297'),
        'resolution_dpi' => array('type' => 'integer', 'label' => 'ResolutionDPI', 'enabled' => '1', 'position' => 90, 'notnull' => 0, 'visible' => 1, 'default' => '300'),
        'speed_ppm' => array('type' => 'integer', 'label' => 'SpeedPPM', 'enabled' => '1', 'position' => 100, 'notnull' => 0, 'visible' => 1, 'default' => '10'),
        'cost_per_hour' => array('type' => 'price', 'label' => 'CostPerHour', 'enabled' => '1', 'position' => 110, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'maintenance_cost' => array('type' => 'price', 'label' => 'MaintenanceCost', 'enabled' => '1', 'position' => 120, 'notnull' => 0, 'visible' => 1, 'default' => '0'),
        'active' => array('type' => 'integer', 'label' => 'Active', 'enabled' => '1', 'position' => 130, 'notnull' => 0, 'visible' => 1, 'default' => '1'),
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
    public $brand;
    public $model;
    public $print_type;
    public $max_width;
    public $max_height;
    public $resolution_dpi;
    public $speed_ppm;
    public $cost_per_hour;
    public $maintenance_cost;
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

        // Example to show how to set values of fields definition dynamically
        /*if ($user->rights->printcostsim->machine->read) {
            $this->fields['myfield']['visible'] = 1;
            $this->fields['myfield']['noteditable'] = 0;
        }*/

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

        $label = img_picto('', $this->picto).' <u>'.$langs->trans("Machine").'</u>';
        if (isset($this->status)) {
            $label .= ' '.$this->getLibStatut(5);
        }
        $label .= '<br>';
        $label .= '<b>'.$langs->trans('Ref').':</b> '.$this->ref;

        $url = dol_buildpath('/printcostsim/machine/card.php', 1).'?id='.$this->id;

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
                $label = $langs->trans("ShowMachine");
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

        if (empty($this->showphoto_on_popup)) {
            if ($withpicto) {
                $result .= img_object(($notooltip ? '' : $label), ($this->picto ? $this->picto : 'generic'), ($notooltip ? (($withpicto != 2) ? 'class="paddingright"' : '') : 'class="'.(($withpicto != 2) ? 'paddingright ' : '').'classfortooltip"'), 0, 0, $notooltip ? 0 : 1);
            }
        } else {
            if ($withpicto) {
                require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

                list($class, $module) = explode('@', $this->picto);
                $upload_dir = $conf->$module->multidir_output[$conf->entity]."/$class/".dol_sanitizeFileName($this->ref);
                $filearray = dol_dir_list($upload_dir, "files", 0, '', '(\.meta|_preview.*\.png)$', $conf->global->MAIN_SORT_IN_LISTS_BY_NAME ? 'name' : 'date', SORT_DESC, 1);
                if (count($filearray)) {
                    $filename = $filearray[0]['name'];
                    $origfile = $upload_dir.'/'.$filename;
                    $file = $upload_dir.'/'.$filename;
                }
                if (!empty($filename)) {
                    $result .= '<div class="floatleft inline-block valignmiddle divphotoref"><div class="photoref"><img class="photo'.$module.'" alt="No photo" border="0" src="'.DOL_URL_ROOT.'/viewimage.php?modulepart='.$module.'&entity='.$conf->entity.'&file='.urlencode($class.'/'.$this->ref.'/'.$filename).'"></div></div>';
                } else {
                    $result .= img_object(($notooltip ? '' : $label), ($this->picto ? $this->picto : 'generic'), ($notooltip ? (($withpicto != 2) ? 'class="paddingright"' : '') : 'class="'.(($withpicto != 2) ? 'paddingright ' : '').'classfortooltip"'), 0, 0, $notooltip ? 0 : 1);
                }
            }
        }

        if ($withpicto != 2) {
            $result .= $this->ref;
        }

        $result .= $linkend;

        global $action, $hookmanager;
        $hookmanager->initHooks(array('machinedao'));
        $parameters = array('id'=>$this->id, 'getnomurl'=>$result);
        $reshook = $hookmanager->executeHooks('getNomUrl', $parameters, $this, $action); // Note that $action and $object may have been modified by some hooks
        if ($reshook > 0) {
            $result = $hookmanager->resPrint;
        } else {
            $result .= $hookmanager->resPrint;
        }

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

    // phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
    /**
     *  Return the status
     *
     *  @param	int		$status        Id status
     *  @param  int		$mode          0=long label, 1=short label, 2=Picto + short label, 3=Picto, 4=Picto + long label, 5=Short label + Picto, 6=Long label + Picto
     *  @return string 			       Label of status
     */
    public function LibStatut($status, $mode = 0)
    {
        // phpcs:enable
        if (empty($this->labelStatus) || empty($this->labelStatusShort)) {
            global $langs;
            //$langs->load("printcostsim@printcostsim");
            $this->labelStatus[self::STATUS_DRAFT] = $langs->trans('Draft');
            $this->labelStatus[self::STATUS_VALIDATED] = $langs->trans('Enabled');
            $this->labelStatus[self::STATUS_CANCELED] = $langs->trans('Disabled');
            $this->labelStatusShort[self::STATUS_DRAFT] = $langs->trans('Draft');
            $this->labelStatusShort[self::STATUS_VALIDATED] = $langs->trans('Enabled');
            $this->labelStatusShort[self::STATUS_CANCELED] = $langs->trans('Disabled');
        }

        $statusType = 'status'.$status;
        //if ($status == self::STATUS_VALIDATED) $statusType = 'status1';
        if ($status == self::STATUS_CANCELED) {
            $statusType = 'status6';
        }

        return dolGetStatus($this->labelStatus[$status], $this->labelStatusShort[$status], '', $statusType, $mode);
    }

    /**
     *	Load the info information in the object
     *
     *	@param  int		$id       Id of object
     *	@return	void
     */
    public function info($id)
    {
        $sql = 'SELECT rowid, date_creation as datec, tms as datem,';
        $sql .= ' fk_user_creat, fk_user_modif';
        $sql .= ' FROM '.MAIN_DB_PREFIX.$this->table_element.' as t';
        $sql .= ' WHERE t.rowid = '.((int) $id);
        $result = $this->db->query($sql);
        if ($result) {
            if ($this->db->num_rows($result)) {
                $obj = $this->db->fetch_object($result);
                $this->id = $obj->rowid;
                if (!empty($obj->fk_user_author)) {
                    $cuser = new User($this->db);
                    $cuser->fetch($obj->fk_user_author);
                    $this->user_creation = $cuser;
                }

                if (!empty($obj->fk_user_valid)) {
                    $vuser = new User($this->db);
                    $vuser->fetch($obj->fk_user_valid);
                    $this->user_validation = $vuser;
                }

                if (!empty($obj->fk_user_cloture)) {
                    $cluser = new User($this->db);
                    $cluser->fetch($obj->fk_user_cloture);
                    $this->user_cloture = $cluser;
                }

                $this->date_creation     = $this->db->jdate($obj->datec);
                $this->date_modification = $this->db->jdate($obj->datem);
                $this->date_validation   = $this->db->jdate($obj->datev);
            }

            $this->db->free($result);
        } else {
            dol_print_error($this->db);
        }
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
     * 	Create an array of lines
     *
     * 	@return array|int		array of lines if OK, <0 if KO
     */
    public function getLinesArray()
    {
        $this->lines = array();

        $objectline = new MachineLine($this->db);
        $result = $objectline->fetchAll('ASC', 'position', 0, 0, array('customsql'=>'fk_machine = '.((int) $this->id)));

        if (is_numeric($result)) {
            $this->error = $objectline->error;
            $this->errors = $objectline->errors;
            return $result;
        } else {
            $this->lines = $result;
            return $this->lines;
        }
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

        if (empty($conf->global->PRINTCOSTSIM_MACHINE_ADDON)) {
            $conf->global->PRINTCOSTSIM_MACHINE_ADDON = 'mod_machine_standard';
        }

        if (!empty($conf->global->PRINTCOSTSIM_MACHINE_ADDON)) {
            $mybool = false;

            $file = $conf->global->PRINTCOSTSIM_MACHINE_ADDON.".php";
            $classname = $conf->global->PRINTCOSTSIM_MACHINE_ADDON;

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
                    //dol_print_error($this->db,get_class($this)."::getNextNumRef ".$obj->error);
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
}

