<?php
/**
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


/* Biblio Author Adding Pop Windows */

// key to authenticate
define('INDEX_AUTH', '1');

// main system configuration
require '../../../sysconfig.inc.php';
// IP based access limitation
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-bibliography');
// start the session
require SB.'admin/default/session.inc.php';
require SB.'admin/default/session_check.inc.php';
require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO.'simbio_GUI/form_maker/simbio_form_table.inc.php';
require SIMBIO.'simbio_DB/simbio_dbop.inc.php';

// privileges checking
$can_write = utility::havePrivilege('bibliography', 'w');
if (!$can_write) {
  die('<div class="errorBox">'.__('You are not authorized to view this section').'</div>');
}

// page title
$page_title = 'Authority List';
// check for biblioID in url
$biblioID = 0;
if (isset($_GET['biblioID']) AND $_GET['biblioID']) {
    $biblioID = (integer)$_GET['biblioID'];
}

// utility function to check author name
function checkCOntributor($str_contributor_name, $str_contributor_type = 'p')
{
  global $dbs;
  $_q = $dbs->query('SELECT contributor_id FROM mst_contributor WHERE contributor_name=\''.$str_contributor_name.'\' AND contributor_type=\''.$str_contributor_type.'\'');
  if ($_q->num_rows > 0) {
    $_d = $_q->fetch_row();
    // return the author ID
    return $_d[0];
  }
  return false;
}
// Setiadi 2 Author Type
// $sysconf['authority_type'] = array('p' => 'Nama Orang');
// $sysconf['authority_level'] = array(1 => 'Penulis', 2 => 'Dosen Pembimbing 1', 3 => 'Dosen Pembimbing 2', 4 => 'Penguji 1', 5 => 'Penguji 2');
// start the output buffer
ob_start();
/* main content */
// biblio author save proccess
if (isset($_POST['save']) AND (isset($_POST['contributorID']) OR trim($_POST['search_str']))) {
  $contributor_name = trim($dbs->escape_string(strip_tags($_POST['search_str'])));
  // create new sql op object
  $sql_op = new simbio_dbop($dbs);
  // check if biblioID POST var exists
  if (isset($_POST['biblioID']) AND !empty($_POST['biblioID'])) {
      $data['biblio_id'] = intval($_POST['biblioID']);
      // check if the author select list is empty or not
      if (isset($_POST['contributorID']) AND !empty($_POST['contributorID'])) {
          $data['contributor_id'] = $_POST['contributorID'];
      } else if ($contributor_name AND empty($_POST['contributorID'])) {
          // check contributor
          $contributor_id = checkContributor($contributor_name);
          if ($contributor_id !== false) {
              $data['contributor_id'] = $contributor_id;
          } else {
              // adding new author
              $contributor_data['contributor_name']         = $contributor_name;
              $contributor_data['contributor_type']         = $_POST['type'];
              $contributor_data['input_date']               = date('Y-m-d');
              $contributor_data['last_update']              = date('Y-m-d');
              // insert new contributor to contributor master table
              @$sql_op->insert('mst_contributor', $contributor_data);
              $data['contributor_id'] = $sql_op->insert_id;
          }
      }
      $data['level'] = intval($_POST['level']);

      if ($sql_op->insert('biblio_contributor', $data)) {
          echo '<script type="text/javascript">';
          echo 'alert(\''.__('Contributor succesfully updated!').'\');';
          echo 'parent.setIframeContent(\'\contributorIframe\', \''.MWB.'bibliography/iframe_contributor.php?biblioID='.$data['biblio_id'].'\');';
          echo '</script>';
      } else {
          utility::jsAlert(__('Contributor FAILED to Add. Please Contact System Administrator')."\n".$sql_op->error);
      }
  } else {
      if (isset($_POST['contributorID']) AND !empty($_POST['contributorID'])) {
          // add to current session
          $_SESSION['biblioContributor'][$_POST['contributorID']] = array($_POST['contributorID'], intval($_POST['level']));
      } else if ($contributor_name AND empty($_POST['contributorID'])) {
          // check author
          $contributor_id = checkContributor($contributor_name);
          if ($contributor_id !== false) {
              $last_id = $contributor_id;
          } else {
              // adding new author
              $data['contributor_name']         = $contributor_name;
              $data['contributor_type']         = $_POST['type'];
              $data['input_date']               = date('Y-m-d');
              $data['last_update']              = date('Y-m-d');
              // insert new author to author master table
              $sql_op->insert('mst_contributor', $data);
              $last_id = $sql_op->insert_id;
          }
          $_SESSION['biblioContributor'][$last_id] = array($last_id, intval($_POST['level']));
      }

      echo '<script type="text/javascript">';
      echo 'alert(\''.__('Contributoradded!').'\');';
      echo 'parent.setIframeContent(\'contributorIframe\', \''.MWB.'bibliography/iframe_contributor.php\');';
      echo '</script>';
  }
}

?>

<div class="popUpForm">
<form name="mainForm" action="pop_contributor.php?biblioID=<?php echo $biblioID; ?>" method="post">
<div>
    <strong><?php echo __('Add Contributor'); ?> </strong>
    <hr />
    <form name="searchContributor" method="post" style="display: inline;">
    <?php
    $ajax_exp = "ajaxFillSelect('../../AJAX_lookup_handler.php', 'mst_contributor', 'contributor_id:contributor_name', 'contributorID', $('#search_str').val())";
    echo __('Contributor Name'); ?> : <input type="text" name="search_str" id="search_str" style="width: 30%;" onkeyup="<?php echo $ajax_exp; ?>" onchange="<?php echo $ajax_exp; ?>" />
 
 
    
    <select name="type" style="width: 20%;" autofocus><?php
    foreach ($sysconf['contributor_type'] as $type_id => $type) {
        echo '<option value="'.$type_id.'">'.$type.'</option>';
    }
    ?></select>

    <select name="level" style="width: 20%;"><?php
    foreach ($sysconf['authority_level_contributor'] as $level_id => $level) {
        echo '<option value="'.$level_id.'">'.$level.'</option>';
    }
    ?></select>
</div>
<div class="popUpSubForm">
<select name="contributorID" id="contributorID" size="5" style="width: 100%;"><option value="0"><?php echo __('Type to search for existing authors or to add a new one'); ?></option></select>
<?php if ($biblioID) { echo '<input type="hidden" name="biblioID" value="'.$biblioID.'" />'; } ?>
<input type="submit" name="save" value="<?php echo __('Insert To Bibliography'); ?>" class="popUpSubmit btn btn-primary" />
</div>
</form>
</div>

<?php
/* main content end */
$content = ob_get_clean();
// include the page template
require SB.'/admin/'.$sysconf['admin_template']['dir'].'/notemplate_page_tpl.php';
