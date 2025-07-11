<?php
/* Copyright (C) 2025 IT-BOX Maroc
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

/**
 * Page to create/edit/view machine
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
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formprojet.class.php';
dol_include_once('/printcostsim/class/machine.class.php');
dol_include_once('/printcostsim/lib/printcostsim.lib.php');

// Load translation files required by the page
$langs->loadLangs(array("printcostsim@printcostsim", "other"));

// Get parameters
$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');
$action = GETPOST('action', 'aZ09');
$confirm = GETPOST('confirm', 'alpha');
$cancel = GETPOST('cancel', 'aZ09');
$contextpage = GETPOST('contextpage', 'aZ') ? GETPOST('contextpage', 'aZ') : 'machinecard'; // To manage different context of search
$backtopage = GETPOST('backtopage', 'alpha');
$backtopageforcancel = GETPOST('backtopageforcancel', 'alpha');
$lineid   = GETPOST('lineid', 'int');

// Initialize technical objects
$object = new Machine($db);
$extrafields = new ExtraFields($db);
$diroutputmassaction = $conf->printcostsim->dir_output.'/temp/massgeneration/'.$user->id;
$hookmanager->initHooks(array('machinecard', 'globalcard')); // Note that conf->hooks_modules contains array

// Fetch extrafields
$extrafields->fetch_name_optionals_label($object->table_element);

$search_array_options = $extrafields->getOptionalsFromPost($object->table_element, '', 'search_');

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

// Load object
include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php'; // Must be include, not include_once.

// There is several ways to check permission.
// Set $enablepermissioncheck to 1 to enable a minimum low level of checks
$enablepermissioncheck = 0;
if ($enablepermissioncheck) {
    $permissiontoread = $user->rights->printcostsim->machine->read;
    $permissiontoadd = $user->rights->printcostsim->machine->write; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
    $permissiontodelete = $user->rights->printcostsim->machine->delete || ($permissiontoadd && isset($object->status) && $object->status == $object::STATUS_DRAFT);
    $permissionnote = $user->rights->printcostsim->machine->write; // Used by the include of actions_setnotes.inc.php
    $permissiondellink = $user->rights->printcostsim->machine->write; // Used by the include of actions_dellink.inc.php
} else {
    $permissiontoread = 1;
    $permissiontoadd = 1; // Used by the include of actions_addupdatedelete.inc.php and actions_lineupdown.inc.php
    $permissiontodelete = 1;
    $permissionnote = 1;
    $permissiondellink = 1;
}

$upload_dir = $conf->printcostsim->multidir_output[isset($object->entity) ? $object->entity : 1].'/machine';

// Security check (enable the most restrictive one)
//if ($user->socid > 0) accessforbidden();
//if ($user->socid > 0) $socid = $user->socid;
//if (!isModEnabled("printcostsim")) {
//	accessforbidden();
//}
//if (!$permissiontoread) {
//	accessforbidden();
//}

/*
 * Actions
 */

$parameters = array();
$reshook = $hookmanager->executeHooks('doActions', $parameters, $object, $action); // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) {
    setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
}

if (empty($reshook)) {
    $error = 0;

    $backurlforlist = dol_buildpath('/printcostsim/machine/list.php', 1);

    if (empty($backtopage) || ($cancel && empty($backtopage))) {
        if (empty($backtopage) || ($cancel && strpos($backtopage, '__ID__'))) {
            if (empty($id) && (($action != 'add' && $action != 'create') || $cancel)) {
                $backtopage = $backurlforlist;
            } else {
                $backtopage = dol_buildpath('/printcostsim/machine/card.php', 1).'?id='.((!empty($id) && $id > 0) ? $id : '__ID__');
            }
        }
    }

    $triggermodname = 'PRINTCOSTSIM_MACHINE_MODIFY'; // Name of trigger action code to execute when we modify record

    // Actions cancel, add, update, update_extras, confirm_validate, confirm_delete, confirm_deleteline, confirm_clone, confirm_close, confirm_setdraft, confirm_reopen
    include DOL_DOCUMENT_ROOT.'/core/actions_addupdatedelete.inc.php';

    // Actions when linking object to another
    include DOL_DOCUMENT_ROOT.'/core/actions_dellink.inc.php';

    // Actions when printing a doc from card
    include DOL_DOCUMENT_ROOT.'/core/actions_printing.inc.php';

    // Action to move up and down lines of object
    //include DOL_DOCUMENT_ROOT.'/core/actions_lineupdown.inc.php';

    // Action to build doc
    include DOL_DOCUMENT_ROOT.'/core/actions_builddoc.inc.php';

    if ($action == 'set_thirdparty' && $permissiontoadd) {
        $object->setValueFrom('fk_soc', GETPOST('fk_soc', 'int'), '', '', 'date', '', $user, $triggermodname);
    }
    if ($action == 'classin' && $permissiontoadd) {
        $object->setProject(GETPOST('projectid', 'int'));
    }

    // Actions to send emails
    $triggersendname = 'PRINTCOSTSIM_MACHINE_SENTBYMAIL';
    $autocopy = 'MAIN_MAIL_AUTOCOPY_MACHINE_TO';
    $trackid = 'machine'.$object->id;
    include DOL_DOCUMENT_ROOT.'/core/actions_sendmails.inc.php';
}

/*
 * View
 *
 * Put here all code to build page
 */

$form = new Form($db);
$formfile = new FormFile($db);
$formproject = new FormProjets($db);

$title = $langs->trans("Machine");
$help_url = '';
llxHeader('', $title, $help_url);

// Example : Adding jquery code
// print '<script type="text/javascript" language="javascript">
// jQuery(document).ready(function() {
// 	function init_myfunc()
// 	{
// 		jQuery("#myid").removeAttr(\'disabled\');
// 		jQuery("#myid").attr(\'disabled\',\'disabled\');
// 	}
// 	init_myfunc();
// 	jQuery("#mybutton").click(function() {
// 		init_myfunc();
// 	});
// });
// </script>';

// Part to create
if ($action == 'create') {
    if (empty($permissiontoadd)) {
        accessforbidden($langs->trans('NotEnoughPermissions'), 0, 1);
        exit;
    }

    print load_fiche_titre($langs->trans("NewObject", $langs->transnoentitiesnoconv("Machine")), '', 'object_'.$object->picto);

    print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
    print '<input type="hidden" name="token" value="'.newToken().'">';
    print '<input type="hidden" name="action" value="add">';
    if ($backtopage) {
        print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
    }
    if ($backtopageforcancel) {
        print '<input type="hidden" name="backtopageforcancel" value="'.$backtopageforcancel.'">';
    }

    print dol_get_fiche_head(array(), '');

    // Set some default values
    //if (! GETPOSTISSET('fieldname')) $_POST['fieldname'] = 'myvalue';

    print '<table class="border centpercent tableforfieldcreate">'."\n";

    // Common attributes
    include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_add.tpl.php';

    // Other attributes
    include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_add.tpl.php';

    print '</table>'."\n";

    print dol_get_fiche_end();

    print $form->buttonsSaveCancel("Create");

    print '</form>';

    //dol_set_focus('input[name="ref"]');
}

// Part to edit record
if (($id || $ref) && $action == 'edit') {
    print load_fiche_titre($langs->trans("Machine"), '', 'object_'.$object->picto);

    print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
    print '<input type="hidden" name="token" value="'.newToken().'">';
    print '<input type="hidden" name="action" value="update">';
    print '<input type="hidden" name="id" value="'.$object->id.'">';
    if ($backtopage) {
        print '<input type="hidden" name="backtopage" value="'.$backtopage.'">';
    }
    if ($backtopageforcancel) {
        print '<input type="hidden" name="backtopageforcancel" value="'.$backtopageforcancel.'">';
    }

    print dol_get_fiche_head();

    print '<table class="border centpercent tableforfieldedit">'."\n";

    // Common attributes
    include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_edit.tpl.php';

    // Other attributes
    include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_edit.tpl.php';

    print '</table>';

    print dol_get_fiche_end();

    print $form->buttonsSaveCancel();

    print '</form>';
}

// Part to show record
if ($object->id > 0 && (empty($action) || ($action != 'edit' && $action != 'create'))) {
    $res = $object->fetch_optionals();

    $head = machinePrepareHead($object);
    print dol_get_fiche_head($head, 'card', $langs->trans("Machine"), -1, $object->picto);

    $formconfirm = '';

    // Confirmation to delete
    if ($action == 'delete') {
        $formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('DeleteMachine'), $langs->trans('ConfirmDeleteObject'), 'confirm_delete', '', 0, 1);
    }
    // Confirmation to delete line
    if ($action == 'deleteline') {
        $formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id.'&lineid='.$lineid, $langs->trans('DeleteLine'), $langs->trans('ConfirmDeleteLine'), 'confirm_deleteline', '', 0, 1);
    }

    // Clone confirmation
    if ($action == 'clone') {
        // Create an array for form
        $formquestion = array();
        $formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('ToClone'), $langs->trans('ConfirmCloneAsk', $object->ref), 'confirm_clone', $formquestion, 'yes', 1);
    }

    // Confirmation of action xxxx (You can use it for xxx = 'close', xxx = 'reopen', ...)
    if ($action == 'xxx') {
        $formquestion = array();
        /*
        $forcecombo=0;
        if ($conf->browser->name == 'ie') $forcecombo = 1;	// There is a bug in IE10 that make combo inside popup crazy
        $formquestion = array(
            // 'text' => $langs->trans("ConfirmClone"),
            // array('type' => 'checkbox', 'name' => 'clone_content', 'label' => $langs->trans("CloneMainAttributes"), 'value' => 1),
            // array('type' => 'checkbox', 'name' => 'update_prices', 'label' => $langs->trans("PuttingPricesUpToDate"), 'value' => 1),
            // array('type' => 'other',    'name' => 'idwarehouse',   'label' => $langs->trans("SelectWarehouseForStockDecrease"), 'value' => $formproduct->selectWarehouses(GETPOST('idwarehouse')?GETPOST('idwarehouse'):'ifone', 'idwarehouse', '', 1, 0, 0, '', 0, $forcecombo))
        );
        */
        $formconfirm = $form->formconfirm($_SERVER["PHP_SELF"].'?id='.$object->id, $langs->trans('XXX'), $text, 'confirm_xxx', $formquestion, 0, 1, 220);
    }

    // Call Hook formConfirm
    $parameters = array('formConfirm' => $formconfirm, 'lineid' => $lineid);
    $reshook = $hookmanager->executeHooks('formConfirm', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
    if (empty($reshook)) {
        $formconfirm .= $hookmanager->resPrint;
    } elseif ($reshook > 0) {
        $formconfirm = $hookmanager->resPrint;
    }

    // Print form confirm
    print $formconfirm;

    // Object card
    // ------------------------------------------------------------
    $linkback = '<a href="'.dol_buildpath('/printcostsim/machine/list.php', 1).'?restore_lastsearch_values=1'.(!empty($socid) ? '&socid='.$socid : '').'">'.$langs->trans("BackToList").'</a>';

    $morehtmlref = '<div class="refidno">';
    /*
     // Ref customer
     $morehtmlref.=$form->editfieldkey("RefCustomer", 'ref_client', $object->ref_client, $object, 0, 'string', '', 0, 1);
     $morehtmlref.=$form->editfieldval("RefCustomer", 'ref_client', $object->ref_client, $object, 0, 'string', '', null, null, '', 1);
     // Thirdparty
     $morehtmlref.='<br>'.$langs->trans('ThirdParty') . ' : ' . (is_object($object->thirdparty) ? $object->thirdparty->getNomUrl(1) : '');
     // Project
     if (isModEnabled('projet')) {
         $langs->load("projects");
         $morehtmlref .= '<br>'.$langs->trans('Project') . ' ';
         if ($permissiontoadd) {
             //if ($action != 'classify') $morehtmlref.='<a class="editfielda" href="' . $_SERVER['PHP_SELF'] . '?action=classify&token='.newToken().'&id=' . $object->id . '">' . img_edit($langs->transnoentitiesnoconv('SetProject')) . '</a> ';
             $morehtmlref .= ' : ';
             if ($action == 'classify') {
                 //$morehtmlref .= $form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->socid, $object->fk_project, 'projectid', 0, 0, 0, 1, '', 1);
                 $morehtmlref .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$object->id.'">';
                 $morehtmlref .= '<input type="hidden" name="action" value="classin">';
                 $morehtmlref .= '<input type="hidden" name="token" value="'.newToken().'">';
                 $morehtmlref .= $formproject->select_projects($object->socid, $object->fk_project, 'projectid', $maxlength, 0, 1, 0, 1, 0, 0, '', 1);
                 $morehtmlref .= '<input type="submit" class="button valignmiddle" value="'.$langs->trans("Modify").'">';
                 $morehtmlref .= '</form>';
             } else {
                 $morehtmlref.=$form->form_project($_SERVER['PHP_SELF'] . '?id=' . $object->id, $object->socid, $object->fk_project, 'none', 0, 0, 0, 1, '', 1);
             }
         } else {
             if (!empty($object->fk_project)) {
                 $proj = new Project($db);
                 $proj->fetch($object->fk_project);
                 $morehtmlref .= ': '.$proj->getNomUrl();
             } else {
                 $morehtmlref .= '';
             }
         }
     }*/
    $morehtmlref .= '</div>';

    dol_banner_tab($object, 'ref', $linkback, 1, 'ref', 'ref', $morehtmlref);

    print '<div class="fichecenter">';
    print '<div class="fichehalfleft">';
    print '<div class="underbanner clearboth"></div>';
    print '<table class="border centpercent tableforfield">'."\n";

    // Common attributes
    //$keyforbreak='fieldkeytoswitchonsecondcolumn';	// We change column just before this field
    //unset($object->fields['fk_project']);				// Hide field already shown in banner
    //unset($object->fields['fk_soc']);					// Hide field already shown in banner
    include DOL_DOCUMENT_ROOT.'/core/tpl/commonfields_view.tpl.php';

    // Other attributes. Fields from hook formObjectOptions and Extrafields.
    include DOL_DOCUMENT_ROOT.'/core/tpl/extrafields_view.tpl.php';

    print '</table>';
    print '</div>';
    print '</div>';

    print '<div class="clearboth"></div>';

    print dol_get_fiche_end();

    /*
     * Lines
     */

    if (!empty($object->table_element_line)) {
        // Show object lines
        $result = $object->getLinesArray();

        print '	<form name="addproduct" id="addproduct" action="'.$_SERVER["PHP_SELF"].'?id='.$object->id.(($action != 'editline') ? '' : '#line_'.GETPOST('lineid', 'int')).'" method="POST">
		<input type="hidden" name="token" value="' . newToken().'">
		<input type="hidden" name="action" value="' . (($action != 'editline') ? 'addline' : 'updateline').'">
		<input type="hidden" name="mode" value="">
		<input type="hidden" name="page_y" value="">
		<input type="hidden" name="id" value="' . $object->id.'">
		';

        if (!empty($conf->use_javascript_ajax) && $object->status == 0) {
            include DOL_DOCUMENT_ROOT.'/core/tpl/ajaxrow.tpl.php';
        }

        print '<div class="div-table-responsive-no-min">';
        if (!empty($object->lines) || ($object->status == $object::STATUS_DRAFT && $permissiontoadd && $action != 'selectlines' && $action != 'editline')) {
            print '<table id="tablelines" class="noborder noshadow" width="100%">';
        }

        if (!empty($object->lines)) {
            $object->printObjectLines($action, $mysoc, null, GETPOST('lineid', 'int'), 1);
        }

        // Form to add new line
        if ($object->status == 0 && $permissiontoadd && $action != 'selectlines') {
            if ($action != 'editline') {
                // Add products/services form

                $parameters = array();
                $reshook = $hookmanager->executeHooks('formAddObjectLine', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
                if ($reshook < 0) {
                    setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
                }
                if (empty($reshook)) {
                    $object->formAddObjectLine(1, $mysoc, $soc);
                }
            }
        }

        if (!empty($object->lines) || ($object->status == $object::STATUS_DRAFT && $permissiontoadd && $action != 'selectlines' && $action != 'editline')) {
            print '</table>';
        }
        print '</div>';

        print "</form>\n";
    }

    // Buttons for actions

    if ($action != 'presend' && $action != 'editline') {
        print '<div class="tabsAction">'."\n";
        $parameters = array();
        $reshook = $hookmanager->executeHooks('addMoreActionsButtons', $parameters, $object, $action); // Note that $action and $object may have been modified by hook
        if ($reshook < 0) {
            setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
        }

        if (empty($reshook)) {
            // Send
            if (empty($user->socid)) {
                print dolGetButtonAction('', $langs->trans('SendMail'), 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=presend&mode=init&token='.newToken().'#formmailbeforetitle');
            }

            // Back to draft
            if ($object->status == $object::STATUS_VALIDATED) {
                print dolGetButtonAction('', $langs->trans('SetToDraft'), 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=confirm_setdraft&confirm=yes&token='.newToken(), '', $permissiontoadd);
            }

            print dolGetButtonAction('', $langs->trans('Modify'), 'default', $_SERVER["PHP_SELF"].'?id='.$object->id.'&action=edit&token='.newToken(), '', $permissiontoadd);

            // Validate
            if ($object->status == $object::STATUS_DRAFT) {
                if (empty($object->table_element_line) || (is_array($object->lines) && count($object->lines) > 0)) {
                    print dolGetButtonAction('', $langs->trans('Validate'), 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=confirm_validate&confirm=yes&token='.newToken(), '', $permissiontoadd);
                } else {
                    $langs->load("errors");
                    print dolGetButtonAction($langs->trans("ErrorAddAtLeastOneLineFirst"), $langs->trans("Validate"), 'default', '#', '', 0);
                }
            }

            // Clone
            print dolGetButtonAction('', $langs->trans('ToClone'), 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.(!empty($object->socid) ? '&socid='.$object->socid : '').'&action=clone&token='.newToken(), '', $permissiontoadd);

            /*
            if ($permissiontoadd) {
                if ($object->status == $object::STATUS_ENABLED) {
                    print dolGetButtonAction('', $langs->trans('Disable'), 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=disable&token='.newToken(), '', $permissiontoadd);
                } else {
                    print dolGetButtonAction('', $langs->trans('Enable'), 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=enable&token='.newToken(), '', $permissiontoadd);
                }
            }
            if ($permissiontoadd) {
                if ($object->status == $object::STATUS_VALIDATED) {
                    print dolGetButtonAction('', $langs->trans('Cancel'), 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=close&token='.newToken(), '', $permissiontoadd);
                } else {
                    print dolGetButtonAction('', $langs->trans('Re-Open'), 'default', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=reopen&token='.newToken(), '', $permissiontoadd);
                }
            }
            */

            // Delete (need delete permission, or if draft, just need create/modify permission)
            print dolGetButtonAction('', $langs->trans('Delete'), 'delete', $_SERVER['PHP_SELF'].'?id='.$object->id.'&action=delete&token='.newToken(), '', $permissiontodelete);
        }
        print '</div>'."\n";
    }

    // Select mail models is same action as presend
    if (GETPOST('modelselected')) {
        $action = 'presend';
    }

    if ($action != 'presend') {
        print '<div class="fichecenter"><div class="fichehalfleft">';
        print '<a name="builddoc"></a>'; // ancre

        $includedocgeneration = 0;

        // Documents
        if ($includedocgeneration) {
            $objref = dol_sanitizeFileName($object->ref);
            $relativepath = $objref.'/'.$objref.'.pdf';
            $filedir = $conf->printcostsim->dir_output.'/'.$object->element.'/'.$objref;
            $urlsource = $_SERVER["PHP_SELF"]."?id=".$object->id;
            $genallowed = $permissiontoread; // If you can read, you can build the PDF to read content
            $delallowed = $permissiontoadd; // If you can create/edit, you can remove a file on card
            print $formfile->showdocuments('printcostsim:Machine', $object->element.'/'.$objref, $filedir, $urlsource, $genallowed, $delallowed, $object->model_pdf, 1, 0, 0, 28, 0, '', '', '', $langs->defaultlang);
        }

        // Show links to link elements
        $linktoelem = $form->showLinkToObjectBlock($object, null, array('machine'));
        $somethingshown = $form->showLinkedObjectBlock($object, $linktoelem);

        print '</div><div class="fichehalfright">';

        $MAXEVENT = 10;

        $morehtmlcenter = dolGetButtonTitle($langs->trans('SeeAll'), '', 'fa fa-bars imgforviewmode', dol_buildpath('/printcostsim/machine/agenda.php', 1).'?id='.$object->id);

        // List of actions on element
        include_once DOL_DOCUMENT_ROOT.'/core/class/html.formactions.class.php';
        $formactions = new FormActions($db);
        $somethingshown = $formactions->showactions($object, $object->element.'@'.$object->module, (is_object($object->thirdparty) ? $object->thirdparty->id : 0), 1, '', $MAXEVENT, '', $morehtmlcenter);

        print '</div></div>';
    }

    //Select mail models is same action as presend
    if (GETPOST('modelselected')) {
        $action = 'presend';
    }

    // Presend form
    $modelmail = 'machine';
    $defaulttopic = 'InformationMessage';
    $diroutput = $conf->printcostsim->dir_output;
    $trackid = 'machine'.$object->id;

    include DOL_DOCUMENT_ROOT.'/core/tpl/card_presend.tpl.php';
}

// End of page
llxFooter();
$db->close();

