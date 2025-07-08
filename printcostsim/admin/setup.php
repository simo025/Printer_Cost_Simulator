<?php
/* Copyright (C) 2025 IT-BOX Maroc
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

/**
 * Page de configuration du module PrintCostSim
 */

// Load Dolibarr environment
$res = 0;
if (!$res && file_exists("../../main.inc.php")) {
    $res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
    $res = @include "../../../main.inc.php";
}
if (!$res && file_exists("../../../../main.inc.php")) {
    $res = @include "../../../../main.inc.php";
}
if (!$res) {
    die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

// Load translation files
$langs->loadLangs(array("admin", "printcostsim@printcostsim"));

// Access control
if (!$user->admin) {
    accessforbidden();
}

// Parameters
$action = GETPOST('action', 'aZ09');
$value = GETPOST('value', 'alpha');
$modulepart = GETPOST('modulepart', 'aZ09');

$arrayofparameters = array(
    'PRINTCOSTSIM_DEFAULT_MARGIN' => array('type' => 'string', 'css' => 'minwidth200', 'enabled' => 1),
    'PRINTCOSTSIM_AI_ENABLED' => array('type' => 'yesno', 'enabled' => 1),
    'PRINTCOSTSIM_AUTO_CALC' => array('type' => 'yesno', 'enabled' => 1),
);

/*
 * Actions
 */

if ($action == 'updateMask') {
    $maskconstorder = GETPOST('maskconstorder', 'aZ09');
    $maskorder = GETPOST('maskorder', 'alpha');

    if ($maskconstorder && preg_match('/^[A-Z_]+$/', $maskconstorder)) {
        $res = dolibarr_set_const($db, $maskconstorder, $maskorder, 'chaine', 0, '', $conf->entity);
        if (!($res > 0)) {
            $error++;
        }
    }

    if (!$error) {
        setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    } else {
        setEventMessages($langs->trans("Error"), null, 'errors');
    }
}

include DOL_DOCUMENT_ROOT.'/core/actions_setmoduleoptions.inc.php';

/*
 * View
 */

$form = new Form($db);

llxHeader("", $langs->trans("PrintCostSimSetup"));

$linkback = '<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($langs->trans("PrintCostSimSetup"), $linkback, 'title_setup');

$head = array();
$head[0][0] = DOL_URL_ROOT.'/custom/printcostsim/admin/setup.php';
$head[0][1] = $langs->trans("Settings");
$head[0][2] = 'settings';

print dol_get_fiche_head($head, 'settings', '', -1, '');

print '<span class="opacitymedium">'.$langs->trans("PrintCostSimSetupPage").'</span><br><br>';

if ($action == 'edit') {
    print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
    print '<input type="hidden" name="token" value="'.newToken().'">';
    print '<input type="hidden" name="action" value="update">';

    print '<table class="noborder centpercent">';
    print '<tr class="liste_titre">';
    print '<td class="titlefield">'.$langs->trans("Parameter").'</td>';
    print '<td>'.$langs->trans("Value").'</td>';
    print '</tr>';

    foreach ($arrayofparameters as $constname => $val) {
        if (empty($val['enabled']) || verifCond($val['enabled'])) {
            continue;
        }
        print '<tr class="oddeven">';
        print '<td>';
        $tooltiphelp = (($langs->trans($constname.'Tooltip') != $constname.'Tooltip') ? $langs->trans($constname.'Tooltip') : '');
        print $form->textwithpicto($langs->trans($constname), $tooltiphelp);
        print '</td>';
        print '<td>';

        if ($val['type'] == 'yesno') {
            print $form->selectyesno($constname, $conf->global->$constname, 1);
        } elseif ($val['type'] == 'textwithpicto') {
            print $form->textwithpicto('', $langs->trans($constname.'Tooltip'), 1, 'info');
        } else {
            print '<input name="'.$constname.'" class="flat '.(empty($val['css']) ? 'minwidth200' : $val['css']).'" value="'.$conf->global->$constname.'">';
        }
        print '</td></tr>';
    }
    print '</table>';

    print '<br><div class="center">';
    print '<input class="button button-save" type="submit" value="'.$langs->trans("Save").'">';
    print '&nbsp;';
    print '<input class="button button-cancel" type="submit" name="cancel" value="'.$langs->trans("Cancel").'">';
    print '</div>';

    print '</form>';
} else {
    print '<div class="div-table-responsive-no-min">';
    print '<table class="noborder centpercent">';
    print '<tr class="liste_titre">';
    print '<td class="titlefield">'.$langs->trans("Parameter").'</td>';
    print '<td>'.$langs->trans("Value").'</td>';
    print '<td class="center width20">&nbsp;</td>';
    print '</tr>';

    foreach ($arrayofparameters as $constname => $val) {
        if (empty($val['enabled']) || verifCond($val['enabled'])) {
            continue;
        }

        print '<tr class="oddeven">';
        print '<td>';
        $tooltiphelp = (($langs->trans($constname.'Tooltip') != $constname.'Tooltip') ? $langs->trans($constname.'Tooltip') : '');
        print $form->textwithpicto($langs->trans($constname), $tooltiphelp);
        print '</td>';
        print '<td>';

        if ($val['type'] == 'yesno') {
            print ajax_constantonoff($constname);
        } elseif ($val['type'] == 'textwithpicto') {
            print $form->textwithpicto('', $langs->trans($constname.'Tooltip'), 1, 'info');
        } else {
            print $conf->global->$constname;
        }
        print '</td>';

        print '<td class="center">';
        if ($val['type'] != 'yesno' && $val['type'] != 'textwithpicto') {
            print '<a class="editfielda" href="'.$_SERVER['PHP_SELF'].'?action=edit&token='.newToken().'#'.$constname.'">'.$langs->trans("Modify").'</a>';
        }
        print '</td>';
        print '</tr>';
    }
    print '</table>';
    print '</div>';

    print '<div class="tabsAction">';
    print '<a class="butAction" href="'.$_SERVER["PHP_SELF"].'?action=edit&token='.newToken().'">'.$langs->trans("Modify").'</a>';
    print '</div>';
}

print dol_get_fiche_end();

// End of page
llxFooter();
$db->close();

