<?php

/**
 *
 * Copyright (C) 2007,2008  Arie Nugraha (dicarve@yahoo.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */

/* Dynamic content Management section */


// key to authenticate
define('INDEX_AUTH', '1');
// key to get full database access
define('DB_ACCESS', 'fa');

// main system configuration
require '../../../sysconfig.inc.php';
// IP based access limitation
require LIB . 'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-bibliography');
// start the session
require SB . 'admin/default/session.inc.php';
require SB . 'admin/default/session_check.inc.php';
require SIMBIO . 'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO . 'simbio_GUI/template_parser/simbio_template_parser.inc.php';
require SIMBIO . 'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO . 'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO . 'simbio_DB/datagrid/simbio_dbgrid.inc.php';
require SIMBIO . 'simbio_DB/simbio_dbop.inc.php';
require LIB . 'phpmailer/class.phpmailer.php';

// privileges checking
$can_read = utility::havePrivilege('bibliography', 'r');
$can_write = utility::havePrivilege('bibliography', 'w');

if (!$can_read) {
    die('<div class="errorBox">' . __('You don\'t have enough privileges to view this section') . '</div>');
}

/* RECORD OPERATION */
if (isset($_POST['saveData'])) {
    $toEmail        = trim(strip_tags($_POST['toEmail']));
    $subjectEmail   = trim(strip_tags($_POST['subjectEmail']));
    $emailDesc      = trim(strip_tags($_POST['emailDesc']));
    // check form validity
    if (empty($toEmail)) {
        utility::jsAlert(__('To email can\'t be empty!'));
        exit();
    } else {


        
    //   ==========================================
    //   ========== START CUSTOM EMAIL ============
    //   ==========================================
      
      $_mail = new PHPMailer(false);
      $_mail->IsSMTP(); // telling the class to use SMTP

      $_mail->SMTPAuth = $sysconf['mail']['auth_enable'];
      $_mail->Host = $sysconf['mail']['server'];
      $_mail->Port = $sysconf['mail']['server_port'];
      $_mail->Username = $sysconf['mail']['auth_username'];
      $_mail->Password = $sysconf['mail']['auth_password'];
      $_mail->SetFrom('sirkulasi.lib@unsyiah.ac.id', 'Sirkulasi UPT. Perpustakaan UNSYIAH');
      $_mail->AddReplyTo($sysconf['mail']['reply_to'], $sysconf['mail']['reply_to_name']);      
      $_mail->Subject = "$subjectEmail";
      $_mail->AddAddress($toEmail, $toEmail);
     
      $_mail->MsgHTML($emailDesc);
      //$_sent = $_mail->Send();


    //   ==========================================
    //   ========== ENDb CUSTOM EMAIL ============
    //   ==========================================


        // // create sql op object
       
            /* INSERT RECORD MODE */
            // insert the data
            if ($_mail->Send()) {
                // write log
                //utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'system', $_SESSION['realname'] . ' add new content (' . $data['content_title'] . ') with contentname (' . $data['contentname'] . ')');
                utility::jsAlert(__('Email berhasil di kirim'));
                echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(\'' . $_SERVER['PHP_SELF'] . '\');</script>';
            } else {
                utility::jsAlert(__('Email FAILED to send!') . "\n" . $sql_op->error);
            }
            exit();
        
    }
    exit();
} 
/* RECORD OPERATION END */

/* search form */
?>
<fieldset class="menuBox">
    <div class="menuBoxInner systemIcon">
        <div class="per_title">
            <h2><?php echo __('Send Mail'); ?></h2>
        </div>
    </div>
</fieldset>
<?php
/* main content */

if (!($can_read and $can_write)) {
    die('<div class="errorBox">' . __('You don\'t have enough privileges to view this section') . '</div>');
}
/* RECORD FORM */


// create new instance
$form = new simbio_form_table_AJAX('mainForm', $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'], 'post');
$form->submit_button_attr = 'name="saveData" value="' . __('Send Email') . '" class="btn btn-default"';

// form table attributes
$form->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
$form->table_header_attr = 'class="alterCell" style="font-weight: bold;"';
$form->table_content_attr = 'class="alterCell2"';



/* Form Element(s) */
// To email
// get mtype data related to this record from database
$form->addTextField('text', 'toEmail', __('To Email'), null, 'style="width: 40%;"');
// subject email path
$form->addTextField('text', 'subjectEmail', __('Subject Email'), null, 'style="width: 40%;"');
// content description
$form->addTextField('textarea', 'emailDesc', __('Email Description'), null, 'style="width: 100%; height: 500px;"');
// edit mode messagge

// print out the form object
// init TinyMCE instance
echo '<script type="text/javascript">tinyMCE.init({
        // Options
        mode : "exact", elements : "emailDesc", theme : "advanced",
        plugins : "safari,style,table,media,searchreplace,directionality,visualchars,xhtmlxtras,compat2x",
        skin : "o2k7", skin_variant : "silver",
        // Theme options
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,fontsizeselect,|,bullist,numlist,|,link,unlink,anchor,image,cleanup,code,forecolor",
        theme_advanced_buttons2 : "undo,redo,|,tablecontrols,|,hr,removeformat,visualaid,|,charmap,media,|,ltr,rtl,|,search,replace",
        theme_advanced_buttons3 : null, theme_advanced_toolbar_location : "top", theme_advanced_toolbar_align : "center", theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : false, content_css : "' . (SWB . 'admin/' . $sysconf['admin_template']['css']) . '",
        });</script>';
echo $form->printOut();

/* main content end */
