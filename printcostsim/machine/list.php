<?php
/* Copyright (C) 2025 IT-BOX Maroc
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

/**
 * Page to list machines
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
    $res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
    $i--; $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
    $res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
    $res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) {
    $res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
    $res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
    $res = @include "../../../main.inc.php";
}
if (!$res) {
    die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcompany.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/company.lib.php';

dol_include_once('/printcostsim/class/machine.class.php');
dol_include_once('/printcostsim/lib/printcostsim.lib.php');

// Load translation files required by the page
$langs->loadLangs(array("printcostsim@printcostsim", "other"));

$action = GETPOST('action', 'aZ09');
$massaction = GETPOST('massaction', 'alpha');
$show_files = GETPOST('show_files', 'int');
$confirm = GETPOST('confirm', 'alpha');
$cancel = GETPOST('cancel', 'aZ09');
$toselect = GETPOST('toselect', 'array');
$contextpage = GETPOST('contextpage', 'aZ') ? GETPOST('contextpage', 'aZ') : 'machinelist'; // To manage different context of search
$backtopage = GETPOST('backtopage', 'alpha');
$optioncss = GETPOST('optioncss', 'aZ'); // Option for the css output (always '' except when 'print')

// Create object
$object = new Machine($db);

// Load variable for pagination
$limit = GETPOST('limit', 'int') ? GETPOST('limit', 'int') : $conf->liste_limit;
$sortfield = GETPOST('sortfield', 'aZ09comma');
$sortorder = GETPOST('sortorder', 'aZ09comma');
$page = GETPOSTISSET('pageplusone') ? (GETPOST('pageplusone') - 1) : GETPOST("page", 'int');
if (empty($page) || $page == -1) {
    $page = 0;
}     // If $page is not defined, or '' or -1
$offset = $limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (!$sortfield) {
    $sortfield = "t.ref";  // Default sort field
}
if (!$sortorder) {
    $sortorder = "ASC";
}

// Initialize array of search criterias
$search_all = GETPOST("search_all", 'alpha');
$search = array();
foreach ($object->fields as $key => $val) {
    if (GETPOST('search_'.$key, 'alpha')) {
        $search[$key] = GETPOST('search_'.$key, 'alpha');
    }
}

if (empty($action) && empty($id) && empty($ref)) {
    $action = 'view';
}

// Security check - Protection if external user
$socid = GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0) {
    $action = '';
    $socid = $user->socid;
}

$permissiontoread = $user->rights->printcostsim->read;
$permissiontoadd = $user->rights->printcostsim->write;
$permissiontodelete = $user->rights->printcostsim->delete;

// Security check
if (!$permissiontoread) {
    accessforbidden();
}

/*
 * Actions
 */

if (GETPOST('cancel', 'alpha')) {
    $action = 'list';
    $massaction = '';
}
if (!GETPOST('confirmmassaction', 'alpha') && $massaction != 'presend' && $massaction != 'confirm_presend') {
    $massaction = '';
}

$parameters = array();
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) {
    setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
}

if (empty($reshook)) {
    // Selection of new fields
    include DOL_DOCUMENT_ROOT.'/core/actions_changeselectedfields.inc.php';

    // Purge search criteria
    if (GETPOST('button_removefilter_x', 'alpha') || GETPOST('button_removefilter.x', 'alpha') || GETPOST('button_removefilter', 'alpha')
        || GETPOST('button_search_x', 'alpha') || GETPOST('button_search.x', 'alpha') || GETPOST('button_search', 'alpha')) {
        foreach ($object->fields as $key => $val) {
            $search[$key] = '';
            if (preg_match('/^(date|timestamp|datetime)/', $val['type'])) {
                $search[$key.'_dtstart'] = '';
                $search[$key.'_dtend'] = '';
            }
        }
        $toselect = '';
        $search_array_options = array();
    }
    if (GETPOST('button_removefilter_x', 'alpha') || GETPOST('button_removefilter.x', 'alpha') || GETPOST('button_removefilter', 'alpha')
        || GETPOST('button_search_x', 'alpha') || GETPOST('button_search.x', 'alpha') || GETPOST('button_search', 'alpha')) {
        $massaction = ''; // Protection to avoid mass action if we force a new search during a mass action confirmation
    }

    // Mass actions
    $objectclass = 'Machine';
    $objectlabel = 'Machine';
    $uploaddir = $conf->printcostsim->dir_output;
    include DOL_DOCUMENT_ROOT.'/core/actions_massactions.inc.php';
}

/*
 * View
 */

$form = new Form($db);

$now = dol_now();

$help_url = '';
$title = $langs->trans("Machines");
$morejs = array();
$morecss = array();

// Build and execute select
// --------------------------------------------------------------------
$sql = 'SELECT ';
$sql .= $object->getFieldList('t');
$sql .= " FROM ".MAIN_DB_PREFIX.$object->table_element." as t";
if ($object->ismultientitymanaged == 1) {
    $sql .= " WHERE t.entity IN (".getEntity($object->element).")";
} else {
    $sql .= " WHERE 1 = 1";
}

foreach ($search as $key => $val) {
    if (array_key_exists($key, $object->fields)) {
        if ($key == 'status' && $search[$key] == -1) {
            continue;
        }
        $mode_search = (($object->isInt($object->fields[$key]) || $object->isFloat($object->fields[$key])) ? 1 : 0);
        if ((strpos($object->fields[$key]['type'], 'integer:') === 0) || (strpos($object->fields[$key]['type'], 'sellist:') === 0) || !empty($object->fields[$key]['arrayofkeyval'])) {
            if ($search[$key] == '-1' || ($search[$key] === '0' && (empty($object->fields[$key]['arrayofkeyval']) || !array_key_exists('0', $object->fields[$key]['arrayofkeyval'])))) {
                $search[$key] = '';
            }
            $mode_search = 2;
        }
        if ($search[$key] != '') {
            $sql .= natural_search("t.".$key, $search[$key], (($key == 'status') ? 2 : $mode_search));
        }
    }
}

if ($search_all) {
    $fieldstosearchall = array();
    foreach ($object->fields as $key => $val) {
        if (!empty($val['searchall'])) {
            $fieldstosearchall['t.'.$key] = $val['label'];
        }
    }
    $sql .= natural_search(array_keys($fieldstosearchall), $search_all);
}

$sql .= $db->order($sortfield, $sortorder);

// Count total nb of records
$nbtotalofrecords = '';
if (empty($conf->global->MAIN_DISABLE_FULL_SCANLIST)) {
    $sqlforcount = preg_replace('/^SELECT[^,]*,/', 'SELECT COUNT(*) as nbtotalofrecords,', $sql);
    $sqlforcount = preg_replace('/GROUP BY .*$/', '', $sqlforcount);
    $resql = $db->query($sqlforcount);
    if ($resql) {
        $objforcount = $db->fetch_object($resql);
        $nbtotalofrecords = $objforcount->nbtotalofrecords;
    } else {
        dol_print_error($db);
    }

    if (($page * $limit) > $nbtotalofrecords) {
        $page = 0;
        $offset = 0;
    }
    $db->free($resql);
}

// Complete request and execute it with limit
$sql .= $db->plimit($limit + 1, $offset);

$resql = $db->query($sql);
if (!$resql) {
    dol_print_error($db);
    exit;
}

$num = $db->num_rows($resql);

// Direct jump if only one record found
if ($num == 1 && !empty($conf->global->MAIN_SEARCH_DIRECT_OPEN_IF_ONLY_ONE) && $search_all && !$page) {
    $obj = $db->fetch_object($resql);
    $id = $obj->rowid;
    header("Location: ".dol_buildpath('/printcostsim/machine/card.php', 1).'?id='.$id);
    exit;
}

// Output page
// --------------------------------------------------------------------

llxHeader('', $title, $help_url);

$arrayofselected = is_array($toselect) ? $toselect : array();

$param = '';
if (!empty($mode)) {
    $param .= '&mode='.urlencode($mode);
}
if (!empty($contextpage) && $contextpage != $_SERVER["PHP_SELF"]) {
    $param .= '&contextpage='.urlencode($contextpage);
}
if ($limit > 0 && $limit != $conf->liste_limit) {
    $param .= '&limit='.urlencode($limit);
}
foreach ($search as $key => $val) {
    if (is_array($search[$key]) && count($search[$key])) {
        foreach ($search[$key] as $skey) {
            $param .= '&search_'.$key.'[]='.urlencode($skey);
        }
    } else {
        $param .= '&search_'.$key.'='.urlencode($search[$key]);
    }
}
if ($optioncss != '') {
    $param .= '&optioncss='.urlencode($optioncss);
}

// Add $param from extra fields
include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_list_search_param.tpl.php';

// List of mass actions available
$arrayofmassactions = array();
if ($permissiontodelete) {
    $arrayofmassactions['predelete'] = img_picto('', 'delete', 'class="pictofixedwidth"').$langs->trans("Delete");
}
if (GETPOST('nomassaction', 'int') || in_array($massaction, array('presend', 'predelete'))) {
    $arrayofmassactions = array();
}
$massactionbutton = $form->selectMassAction('', $arrayofmassactions);

print '<form method="POST" id="searchFormList" action="'.$_SERVER["PHP_SELF"].'">';
if ($optioncss != '') {
    print '<input type="hidden" name="optioncss" value="'.$optioncss.'">';
}
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="formfilteraction" id="formfilteraction" value="list">';
print '<input type="hidden" name="action" value="list">';
print '<input type="hidden" name="sortfield" value="'.$sortfield.'">';
print '<input type="hidden" name="sortorder" value="'.$sortorder.'">';
print '<input type="hidden" name="page" value="'.$page.'">';
print '<input type="hidden" name="contextpage" value="'.$contextpage.'">';

$newcardbutton = '';
$newcardbutton .= dolGetButtonTitle($langs->trans('ViewList'), '', 'fa fa-bars imgforviewmode', $_SERVER["PHP_SELF"].'?mode=common'.preg_replace('/(&|\?)*mode=[^&]+/', '', $param), '', ((empty($mode) || $mode == 'common') ? 2 : 1), array('morecss'=>'reposition'));
$newcardbutton .= dolGetButtonTitle($langs->trans('ViewKanban'), '', 'fa fa-th-list imgforviewmode', $_SERVER["PHP_SELF"].'?mode=kanban'.preg_replace('/(&|\?)*mode=[^&]+/', '', $param), '', (($mode == 'kanban') ? 2 : 1), array('morecss'=>'reposition'));
if ($permissiontoadd) {
    $newcardbutton .= dolGetButtonTitle($langs->trans('New'), '', 'fa fa-plus-circle', dol_buildpath('/printcostsim/machine/card.php', 1).'?action=create&backtopage='.urlencode($_SERVER['PHP_SELF']), '', $permissiontoadd);
}

print_barre_liste($title, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, $massactionbutton, $num, $nbtotalofrecords, 'object_'.$object->picto, 0, $newcardbutton, '', $limit, 0, 0, 1);

// Add code for pre mass action (confirmation or email presend form)
$topicmail = "SendMachineRef";
$modelmail = "machine";
$objecttmp = new Machine($db);
$trackid = 'xxxx'.$object->id;
include DOL_DOCUMENT_ROOT.'/core/tpl/massactions_pre.tpl.php';

if ($search_all) {
    foreach ($fieldstosearchall as $key => $val) {
        $fieldstosearchall[$key] = $langs->trans($val);
    }
    print '<div class="divsearchfieldfilter">'.$langs->trans("FilterOnInto", $search_all).join(', ', $fieldstosearchall).'</div>';
}

$moreforfilter = '';

$parameters = array();
$reshook = $hookmanager->executeHooks('printFieldPreListTitle', $parameters, $object); // Note that $action and $object may have been modified by hook
if (empty($reshook)) {
    $moreforfilter .= $hookmanager->resPrint;
}

if (!empty($moreforfilter)) {
    print '<div class="liste_titre liste_titre_bydiv centpercent">';
    print $moreforfilter;
    print '</div>';
}

$varpage = empty($contextpage) ? $_SERVER["PHP_SELF"] : $contextpage;
$selectedfields = $form->multiSelectArrayWithCheckbox('selectedfields', $arrayfields, $varpage); // This also change content of $arrayfields

// Fields title search
// --------------------------------------------------------------------
print '<div class="div-table-responsive">';
print '<table class="tagtable nobottomiftotal liste'.($moreforfilter ? " listwithfilterbefore" : "").'">'."\n";

print '<tr class="liste_titre">';
foreach ($object->fields as $key => $val) {
    $cssforfield = (empty($val['csslist']) ? (empty($val['css']) ? '' : $val['css']) : $val['csslist']);
    if ($key == 'status') {
        $cssforfield .= ($cssforfield ? ' ' : '').'center';
    } elseif (in_array($val['type'], array('date', 'datetime', 'timestamp'))) {
        $cssforfield .= ($cssforfield ? ' ' : '').'center';
    } elseif (in_array($val['type'], array('timestamp'))) {
        $cssforfield .= ($cssforfield ? ' ' : '').'nowrap';
    } elseif (in_array($val['type'], array('double(24,8)', 'double(6,3)', 'integer', 'real', 'price')) && $val['label'] != 'TechnicalID' && empty($val['arrayofkeyval'])) {
        $cssforfield .= ($cssforfield ? ' ' : '').'right';
    }
    if (!empty($arrayfields['t.'.$key]['checked'])) {
        print getTitleFieldOfList($arrayfields['t.'.$key]['label'], 0, $_SERVER['PHP_SELF'], 't.'.$key, '', $param, ($cssforfield ? 'class="'.$cssforfield.'"' : ''), $sortfield, $sortorder, ($cssforfield ? $cssforfield.' ' : ''))."\n";
    }
}
// Extra fields
$parameters = array('arrayfields'=>$arrayfields, 'param'=>$param, 'sortfield'=>$sortfield, 'sortorder'=>$sortorder);
$reshook = $hookmanager->executeHooks('printFieldListTitle', $parameters, $object); // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;
// Hook fields
$parameters = array('arrayfields'=>$arrayfields, 'param'=>$param, 'sortfield'=>$sortfield, 'sortorder'=>$sortorder);
$reshook = $hookmanager->executeHooks('printFieldListTitle', $parameters, $object); // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;
// Action column
print getTitleFieldOfList($selectedfields, 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'center maxwidthsearch ')."\n";
print '</tr>'."\n";

// Fields title search
// --------------------------------------------------------------------
print '<tr class="liste_titre">';
foreach ($object->fields as $key => $val) {
    $cssforfield = (empty($val['csslist']) ? (empty($val['css']) ? '' : $val['css']) : $val['csslist']);
    if ($key == 'status') {
        $cssforfield .= ($cssforfield ? ' ' : '').'center';
    } elseif (in_array($val['type'], array('date', 'datetime', 'timestamp'))) {
        $cssforfield .= ($cssforfield ? ' ' : '').'center';
    } elseif (in_array($val['type'], array('timestamp'))) {
        $cssforfield .= ($cssforfield ? ' ' : '').'nowrap';
    } elseif (in_array($val['type'], array('double(24,8)', 'double(6,3)', 'integer', 'real', 'price')) && $val['label'] != 'TechnicalID' && empty($val['arrayofkeyval'])) {
        $cssforfield .= ($cssforfield ? ' ' : '').'right';
    }
    if (!empty($arrayfields['t.'.$key]['checked'])) {
        print '<td class="liste_titre'.($cssforfield ? ' '.$cssforfield : '').'">';
        if (!empty($val['arrayofkeyval']) && is_array($val['arrayofkeyval'])) {
            print $form->selectarray('search_'.$key, $val['arrayofkeyval'], (isset($search[$key]) ? $search[$key] : ''), $val['notnull'], 0, 0, '', 1, 0, 0, '', 'maxwidth100', 1);
        } elseif ((strpos($val['type'], 'integer:') === 0) || (strpos($val['type'], 'sellist:') === 0)) {
            print $object->showInputField($val, $key, (isset($search[$key]) ? $search[$key] : ''), '', '', 'search_', 'maxwidth125', 1);
        } elseif (preg_match('/^(date|timestamp|datetime)/', $val['type'])) {
            print '<div class="nowrap">';
            print $form->selectDate($search[$key.'_dtstart'] ? $search[$key.'_dtstart'] : '', "search_".$key."_dtstart", 0, 0, 1, '', 1, 0, 0, '', '', '', '', 1, '', $langs->trans('From'));
            print '<br>';
            print $form->selectDate($search[$key.'_dtend'] ? $search[$key.'_dtend'] : '', "search_".$key."_dtend", 0, 0, 1, '', 1, 0, 0, '', '', '', '', 1, '', $langs->trans('to'));
            print '</div>';
        } elseif ($key == 'lang') {
            require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
            $formadmin = new FormAdmin($db);
            print $formadmin->select_language($search[$key], 'search_lang', 0, null, 1, 0, 0, 'minwidth100imp maxwidth125', 2);
        } else {
            print '<input type="text" class="flat maxwidth75" name="search_'.$key.'" value="'.dol_escape_htmltag(isset($search[$key]) ? $search[$key] : '').'">';
        }
        print '</td>';
    }
}
// Extra fields
include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_list_search_input.tpl.php';

// Fields from hook
$parameters = array('arrayfields'=>$arrayfields);
$reshook = $hookmanager->executeHooks('printFieldListOption', $parameters, $object); // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;
// Action column
print '<td class="liste_titre maxwidthsearch">';
$searchpicto = $form->showFilterButtons();
print $searchpicto;
print '</td>';
print '</tr>'."\n";

$totalarray = array();
$totalarray['nbfield'] = 0;

// Fields title line
// --------------------------------------------------------------------
print '<tr class="liste_titre">';
foreach ($object->fields as $key => $val) {
    $cssforfield = (empty($val['csslist']) ? (empty($val['css']) ? '' : $val['css']) : $val['csslist']);
    if ($key == 'status') {
        $cssforfield .= ($cssforfield ? ' ' : '').'center';
    } elseif (in_array($val['type'], array('date', 'datetime', 'timestamp'))) {
        $cssforfield .= ($cssforfield ? ' ' : '').'center';
    } elseif (in_array($val['type'], array('timestamp'))) {
        $cssforfield .= ($cssforfield ? ' ' : '').'nowrap';
    } elseif (in_array($val['type'], array('double(24,8)', 'double(6,3)', 'integer', 'real', 'price')) && $val['label'] != 'TechnicalID' && empty($val['arrayofkeyval'])) {
        $cssforfield .= ($cssforfield ? ' ' : '').'right';
    }
    $totalarray['nbfield']++;
    if (!empty($arrayfields['t.'.$key]['checked'])) {
        print getTitleFieldOfList($arrayfields['t.'.$key]['label'], 0, $_SERVER['PHP_SELF'], 't.'.$key, '', $param, ($cssforfield ? 'class="'.$cssforfield.'"' : ''), $sortfield, $sortorder, ($cssforfield ? $cssforfield.' ' : ''))."\n";
        $totalarray['nbfield']++;
    }
}
// Extra fields
include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_list_search_title.tpl.php';
// Hook fields
$parameters = array('arrayfields'=>$arrayfields, 'param'=>$param, 'sortfield'=>$sortfield, 'sortorder'=>$sortorder, 'totalarray'=>&$totalarray);
$reshook = $hookmanager->executeHooks('printFieldListTitle', $parameters, $object); // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;
// Action column
print getTitleFieldOfList($selectedfields, 0, $_SERVER["PHP_SELF"], '', '', '', '', $sortfield, $sortorder, 'center maxwidthsearch ')."\n";
$totalarray['nbfield']++;
print '</tr>'."\n";

// Detect if we need a fetch on each output line
$needToFetchEachLine = 0;
if (isset($extrafields->attributes[$object->table_element]['computed']) && is_array($extrafields->attributes[$object->table_element]['computed']) && count($extrafields->attributes[$object->table_element]['computed']) > 0) {
    foreach ($extrafields->attributes[$object->table_element]['computed'] as $key => $val) {
        if (!is_null($val) && preg_match('/\$object/', $val)) {
            $needToFetchEachLine++; // There is at least one compute field that use $object
        }
    }
}

// Loop on record
// --------------------------------------------------------------------
$i = 0;
$savnbfield = $totalarray['nbfield'];
$totalarray = array();
$totalarray['nbfield'] = 0;
$imaxinloop = ($limit ? min($num, $limit) : $num);
while ($i < $imaxinloop) {
    $obj = $db->fetch_object($resql);
    if (empty($obj)) {
        break; // Should not happen
    }

    // Store properties in $object
    $object->setVarsFromFetchObj($obj);

    // Show here line of result
    $j = 0;
    print '<tr data-rowid="'.$object->id.'" class="oddeven">';
    foreach ($object->fields as $key => $val) {
        $cssforfield = (empty($val['csslist']) ? (empty($val['css']) ? '' : $val['css']) : $val['csslist']);
        if (in_array($val['type'], array('date', 'datetime', 'timestamp'))) {
            $cssforfield .= ($cssforfield ? ' ' : '').'center';
        } elseif ($key == 'status') {
            $cssforfield .= ($cssforfield ? ' ' : '').'center';
        }

        if (in_array($val['type'], array('timestamp'))) {
            $cssforfield .= ($cssforfield ? ' ' : '').'nowrap';
        } elseif ($key == 'ref') {
            $cssforfield .= ($cssforfield ? ' ' : '').'nowrap';
        }

        if (in_array($val['type'], array('double(24,8)', 'double(6,3)', 'integer', 'real', 'price')) && !in_array($key, array('rowid', 'ref', 'status')) && empty($val['arrayofkeyval'])) {
            $cssforfield .= ($cssforfield ? ' ' : '').'right';
        }
        //if (in_array($key, array('fk_soc', 'fk_user', 'fk_warehouse'))) $cssforfield = 'tdoverflowmax100';

        if (!empty($arrayfields['t.'.$key]['checked'])) {
            print '<td'.($cssforfield ? ' class="'.$cssforfield.'"' : '').'>';
            if ($key == 'status') {
                print $object->getLibStatut(5);
            } elseif ($key == 'rowid') {
                print $object->showOutputField($val, $key, $object->id, '');
            } else {
                print $object->showOutputField($val, $key, $object->$key, '');
            }
            print '</td>';
            if (!$i) {
                $totalarray['nbfield']++;
            }
            if (!empty($val['isameasure']) && $val['isameasure'] == 1) {
                if (!$i) {
                    $totalarray['pos'][$totalarray['nbfield']] = 't.'.$key;
                }
                if (isset($totalarray['val'])) {
                    $totalarray['val']['t.'.$key] += $object->$key;
                } else {
                    $totalarray['val']['t.'.$key] = $object->$key;
                }
            }
        }
        $j++;
    }
    // Extra fields
    include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_list_print_fields.tpl.php';
    // Fields from hook
    $parameters = array('arrayfields'=>$arrayfields, 'object'=>$object, 'obj'=>$obj, 'i'=>$i, 'totalarray'=>&$totalarray);
    $reshook = $hookmanager->executeHooks('printFieldListValue', $parameters, $object); // Note that $action and $object may have been modified by hook
    print $hookmanager->resPrint;
    // Action column
    print '<td class="nowrap center">';
    if ($massactionbutton || $massaction) {   // If we are in select mode (massactionbutton defined) or if we have already selected and sent an action ($massaction) defined
        $selected = 0;
        if (in_array($object->id, $arrayofselected)) {
            $selected = 1;
        }
        print '<input id="cb'.$object->id.'" class="flat checkforselect" type="checkbox" name="toselect[]" value="'.$object->id.'"'.($selected ? ' checked="checked"' : '').'>';
    }
    print '</td>';
    if (!$i) {
        $totalarray['nbfield']++;
    }

    print '</tr>'."\n";

    $i++;
}

// Show total line
include DOL_DOCUMENT_ROOT.'/core/tpl/list_print_total.tpl.php';

// If no record found
if ($num == 0) {
    $colspan = 1;
    foreach ($arrayfields as $key => $val) {
        if (!empty($val['checked'])) {
            $colspan++;
        }
    }
    print '<tr><td colspan="'.$colspan.'"><span class="opacitymedium">'.$langs->trans("NoRecordFound").'</span></td></tr>';
}

$db->free($resql);

$parameters = array('arrayfields'=>$arrayfields, 'sql'=>$sql);
$reshook = $hookmanager->executeHooks('printFieldListFooter', $parameters, $object); // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;

print '</table>'."\n";
print '</div>'."\n";

print '</form>'."\n";

// End of page
llxFooter();
$db->close();

