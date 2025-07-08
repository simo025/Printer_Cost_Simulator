<?php
/* Copyright (C) 2025 IT-BOX Maroc
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

/**
 * \file       lib/printcostsim.lib.php
 * \ingroup    printcostsim
 * \brief      Library files with common functions for PrintCostSim
 */

/**
 * Prepare admin pages header
 *
 * @return array
 */
function printcostsimAdminPrepareHead()
{
    global $langs, $conf;

    $langs->load("printcostsim@printcostsim");

    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/printcostsim/admin/setup.php", 1);
    $head[$h][1] = $langs->trans("Settings");
    $head[$h][2] = 'settings';
    $h++;

    /*
    $head[$h][0] = dol_buildpath("/printcostsim/admin/myobject_extrafields.php", 1);
    $head[$h][1] = $langs->trans("ExtraFields");
    $head[$h][2] = 'myobject_extrafields';
    $h++;
    */

    $head[$h][0] = dol_buildpath("/printcostsim/admin/about.php", 1);
    $head[$h][1] = $langs->trans("About");
    $head[$h][2] = 'about';
    $h++;

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    //$this->tabs = array(
    //	'entity:+tabname:Title:@printcostsim:/printcostsim/mypage.php?id=__ID__'
    //); // to add new tab
    //$this->tabs = array(
    //	'entity:-tabname:Title:@printcostsim:/printcostsim/mypage.php?id=__ID__'
    //); // to remove a tab
    complete_head_from_modules($conf, $langs, null, $head, $h, 'printcostsim@printcostsim');

    complete_head_from_modules($conf, $langs, null, $head, $h, 'printcostsim@printcostsim', 'remove');

    return $head;
}

/**
 * Prepare machine pages header
 *
 * @param   Machine $object     Machine object
 * @return  array               Array of tabs
 */
function machinePrepareHead($object)
{
    global $db, $langs, $conf;

    $langs->load("printcostsim@printcostsim");

    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/printcostsim/machine/card.php", 1).'?id='.$object->id;
    $head[$h][1] = $langs->trans("Card");
    $head[$h][2] = 'card';
    $h++;

    if (isset($object->fields['note_public']) || isset($object->fields['note_private'])) {
        $nbNote = 0;
        if (!empty($object->note_private)) {
            $nbNote++;
        }
        if (!empty($object->note_public)) {
            $nbNote++;
        }
        $head[$h][0] = dol_buildpath('/printcostsim/machine/note.php', 1).'?id='.$object->id;
        $head[$h][1] = $langs->trans('Notes');
        if ($nbNote > 0) {
            $head[$h][1] .= '<span class="badge marginleftonlyshort">'.$nbNote.'</span>';
        }
        $head[$h][2] = 'note';
        $h++;
    }

    require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
    require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
    $upload_dir = $conf->printcostsim->dir_output."/machine/".dol_sanitizeFileName($object->ref);
    $nbFiles = count(dol_dir_list($upload_dir, 'files', 0, '', '(\.meta|_preview.*\.png)$'));
    $nbLinks = Link::count($db, $object->element, $object->id);
    $head[$h][0] = dol_buildpath("/printcostsim/machine/document.php", 1).'?id='.$object->id;
    $head[$h][1] = $langs->trans('Documents');
    if (($nbFiles + $nbLinks) > 0) {
        $head[$h][1] .= '<span class="badge marginleftonlyshort">'.($nbFiles + $nbLinks).'</span>';
    }
    $head[$h][2] = 'document';
    $h++;

    $head[$h][0] = dol_buildpath("/printcostsim/machine/agenda.php", 1).'?id='.$object->id;
    $head[$h][1] = $langs->trans("Events");
    $head[$h][2] = 'agenda';
    $h++;

    // Show more tabs from modules
    // Entries must be declared in modules descriptor with line
    //$this->tabs = array(
    //	'entity:+tabname:Title:@printcostsim:/printcostsim/mypage.php?id=__ID__'
    //); // to add new tab
    //$this->tabs = array(
    //	'entity:-tabname:Title:@printcostsim:/printcostsim/mypage.php?id=__ID__'
    //); // to remove a tab
    complete_head_from_modules($conf, $langs, $object, $head, $h, 'machine@printcostsim');

    complete_head_from_modules($conf, $langs, $object, $head, $h, 'machine@printcostsim', 'remove');

    return $head;
}

/**
 * Prepare consumable pages header
 *
 * @param   Consumable $object     Consumable object
 * @return  array                  Array of tabs
 */
function consumablePrepareHead($object)
{
    global $db, $langs, $conf;

    $langs->load("printcostsim@printcostsim");

    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/printcostsim/consumable/card.php", 1).'?id='.$object->id;
    $head[$h][1] = $langs->trans("Card");
    $head[$h][2] = 'card';
    $h++;

    if (isset($object->fields['note_public']) || isset($object->fields['note_private'])) {
        $nbNote = 0;
        if (!empty($object->note_private)) {
            $nbNote++;
        }
        if (!empty($object->note_public)) {
            $nbNote++;
        }
        $head[$h][0] = dol_buildpath('/printcostsim/consumable/note.php', 1).'?id='.$object->id;
        $head[$h][1] = $langs->trans('Notes');
        if ($nbNote > 0) {
            $head[$h][1] .= '<span class="badge marginleftonlyshort">'.$nbNote.'</span>';
        }
        $head[$h][2] = 'note';
        $h++;
    }

    require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
    require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
    $upload_dir = $conf->printcostsim->dir_output."/consumable/".dol_sanitizeFileName($object->ref);
    $nbFiles = count(dol_dir_list($upload_dir, 'files', 0, '', '(\.meta|_preview.*\.png)$'));
    $nbLinks = Link::count($db, $object->element, $object->id);
    $head[$h][0] = dol_buildpath("/printcostsim/consumable/document.php", 1).'?id='.$object->id;
    $head[$h][1] = $langs->trans('Documents');
    if (($nbFiles + $nbLinks) > 0) {
        $head[$h][1] .= '<span class="badge marginleftonlyshort">'.($nbFiles + $nbLinks).'</span>';
    }
    $head[$h][2] = 'document';
    $h++;

    $head[$h][0] = dol_buildpath("/printcostsim/consumable/agenda.php", 1).'?id='.$object->id;
    $head[$h][1] = $langs->trans("Events");
    $head[$h][2] = 'agenda';
    $h++;

    // Show more tabs from modules
    complete_head_from_modules($conf, $langs, $object, $head, $h, 'consumable@printcostsim');
    complete_head_from_modules($conf, $langs, $object, $head, $h, 'consumable@printcostsim', 'remove');

    return $head;
}

/**
 * Prepare simulation pages header
 *
 * @param   Simulation $object     Simulation object
 * @return  array                  Array of tabs
 */
function simulationPrepareHead($object)
{
    global $db, $langs, $conf;

    $langs->load("printcostsim@printcostsim");

    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/printcostsim/simulation/card.php", 1).'?id='.$object->id;
    $head[$h][1] = $langs->trans("Card");
    $head[$h][2] = 'card';
    $h++;

    if (isset($object->fields['note_public']) || isset($object->fields['note_private'])) {
        $nbNote = 0;
        if (!empty($object->note_private)) {
            $nbNote++;
        }
        if (!empty($object->note_public)) {
            $nbNote++;
        }
        $head[$h][0] = dol_buildpath('/printcostsim/simulation/note.php', 1).'?id='.$object->id;
        $head[$h][1] = $langs->trans('Notes');
        if ($nbNote > 0) {
            $head[$h][1] .= '<span class="badge marginleftonlyshort">'.$nbNote.'</span>';
        }
        $head[$h][2] = 'note';
        $h++;
    }

    require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
    require_once DOL_DOCUMENT_ROOT.'/core/class/link.class.php';
    $upload_dir = $conf->printcostsim->dir_output."/simulation/".dol_sanitizeFileName($object->ref);
    $nbFiles = count(dol_dir_list($upload_dir, 'files', 0, '', '(\.meta|_preview.*\.png)$'));
    $nbLinks = Link::count($db, $object->element, $object->id);
    $head[$h][0] = dol_buildpath("/printcostsim/simulation/document.php", 1).'?id='.$object->id;
    $head[$h][1] = $langs->trans('Documents');
    if (($nbFiles + $nbLinks) > 0) {
        $head[$h][1] .= '<span class="badge marginleftonlyshort">'.($nbFiles + $nbLinks).'</span>';
    }
    $head[$h][2] = 'document';
    $h++;

    $head[$h][0] = dol_buildpath("/printcostsim/simulation/agenda.php", 1).'?id='.$object->id;
    $head[$h][1] = $langs->trans("Events");
    $head[$h][2] = 'agenda';
    $h++;

    // Show more tabs from modules
    complete_head_from_modules($conf, $langs, $object, $head, $h, 'simulation@printcostsim');
    complete_head_from_modules($conf, $langs, $object, $head, $h, 'simulation@printcostsim', 'remove');

    return $head;
}

/**
 * Return array of tabs to used on pages for third parties cards.
 *
 * @param 	Machine|Consumable|Simulation	$object		Object company shown
 * @return 	array				Array of tabs
 */
function printcostsim_prepare_head($object)
{
    return machinePrepareHead($object);
}

/**
 * Return array of tabs to used on pages to setup a module.
 *
 * @return 	array				Array of tabs
 */
function printcostsim_admin_prepare_head()
{
    return printcostsimAdminPrepareHead();
}

