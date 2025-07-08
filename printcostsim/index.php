<?php
/* Copyright (C) 2025 IT-BOX Maroc
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

/**
 * Page d'accueil du module PrintCostSim
 */

// Load Dolibarr environment
$res = 0;
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

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

// Load translation files
$langs->loadLangs(array("printcostsim@printcostsim"));

// Access control
if (!$user->rights->printcostsim->read) {
    accessforbidden();
}

// Parameters
$action = GETPOST('action', 'aZ09');

/*
 * Actions
 */

/*
 * View
 */

$form = new Form($db);

llxHeader("", $langs->trans("PrintCostSim"), "");

print load_fiche_titre($langs->trans("PrintCostSim"), '', 'printcostsim@printcostsim');

print '<div class="fichecenter">';

// Statistiques rapides
print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<th colspan="2">'.$langs->trans("Statistics").'</th>';
print '</tr>';

// Nombre de machines
$sql = "SELECT COUNT(*) as nb FROM ".MAIN_DB_PREFIX."printcostsim_machine WHERE active = 1";
$resql = $db->query($sql);
$nb_machines = 0;
if ($resql) {
    $obj = $db->fetch_object($resql);
    $nb_machines = $obj->nb;
}

print '<tr class="oddeven">';
print '<td>'.$langs->trans("ActiveMachines").'</td>';
print '<td class="right"><span class="badge badge-info">'.$nb_machines.'</span></td>';
print '</tr>';

// Nombre de consommables
$sql = "SELECT COUNT(*) as nb FROM ".MAIN_DB_PREFIX."printcostsim_consumable WHERE active = 1";
$resql = $db->query($sql);
$nb_consumables = 0;
if ($resql) {
    $obj = $db->fetch_object($resql);
    $nb_consumables = $obj->nb;
}

print '<tr class="oddeven">';
print '<td>'.$langs->trans("ActiveConsumables").'</td>';
print '<td class="right"><span class="badge badge-info">'.$nb_consumables.'</span></td>';
print '</tr>';

// Nombre de simulations
$sql = "SELECT COUNT(*) as nb FROM ".MAIN_DB_PREFIX."printcostsim_simulation";
$resql = $db->query($sql);
$nb_simulations = 0;
if ($resql) {
    $obj = $db->fetch_object($resql);
    $nb_simulations = $obj->nb;
}

print '<tr class="oddeven">';
print '<td>'.$langs->trans("TotalSimulations").'</td>';
print '<td class="right"><span class="badge badge-success">'.$nb_simulations.'</span></td>';
print '</tr>';

print '</table>';
print '</div>';

print '<br>';

// Actions rapides
print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<th colspan="2">'.$langs->trans("QuickActions").'</th>';
print '</tr>';

print '<tr class="oddeven">';
print '<td><a href="machine/list.php">'.$langs->trans("ManageMachines").'</a></td>';
print '<td>'.$langs->trans("ManageMachinesDesc").'</td>';
print '</tr>';

print '<tr class="oddeven">';
print '<td><a href="consumable/list.php">'.$langs->trans("ManageConsumables").'</a></td>';
print '<td>'.$langs->trans("ManageConsumablesDesc").'</td>';
print '</tr>';

print '<tr class="oddeven">';
print '<td><a href="simulation/list.php">'.$langs->trans("ViewSimulations").'</a></td>';
print '<td>'.$langs->trans("ViewSimulationsDesc").'</td>';
print '</tr>';

print '<tr class="oddeven">';
print '<td><a href="simulation/card.php?action=create">'.$langs->trans("NewSimulation").'</a></td>';
print '<td>'.$langs->trans("NewSimulationDesc").'</td>';
print '</tr>';

print '</table>';
print '</div>';

print '</div>';

// End of page
llxFooter();
$db->close();

