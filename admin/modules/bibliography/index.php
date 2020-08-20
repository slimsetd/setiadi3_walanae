<?php

/**
 * Copyright (C) 2007,2008,2009,2010  Arie Nugraha (dicarve@yahoo.com)
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

/* Bibliography Management section */

// key to authenticate
if (!defined('INDEX_AUTH')) {
  define('INDEX_AUTH', '1');
}
// key to get full database access
define('DB_ACCESS', 'fa');

if (!defined('SB')) {
  // main system configuration
  require '../../../sysconfig.inc.php';
  // start the session
  require SB . 'admin/default/session.inc.php';
}
// IP based access limitation
require LIB . 'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-bibliography');

require SB . 'admin/default/session_check.inc.php';
require SIMBIO . 'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO . 'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO . 'simbio_GUI/form_maker/setiadi_form_maker.php'; //added new by doe
require SIMBIO . 'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO . 'simbio_DB/datagrid/simbio_dbgrid.inc.php';
require SIMBIO . 'simbio_DB/simbio_dbop.inc.php';
require SIMBIO . 'simbio_FILE/simbio_file_upload.inc.php';
require LIB . 'setiadi_utility.inc.php';
require MDLBS . 'bibliography/long_biblio.inc.php';
require MDLBS . 'system/biblio_indexer.inc.php';
require LIB . 'phpmailer/class.phpmailer.php';

// privileges checking
$can_read = utility::havePrivilege('bibliography', 'r');
$can_write = utility::havePrivilege('bibliography', 'w');

if (!$can_read) {
  die('<div class="errorBox">' . __('You are not authorized to view this section') . '</div>');
}

$in_pop_up = false;
// check if we are inside pop-up window
if (isset($_GET['inPopUp'])) {
  $in_pop_up = true;
}

/* REMOVE IMAGE */
if (isset($_POST['removeImage']) && isset($_POST['bimg']) && isset($_POST['img'])) {
  $_delete = $dbs->query(sprintf('UPDATE biblio SET image=NULL WHERE biblio_id=%d', $_POST['bimg']));
  $_delete2 = $dbs->query(sprintf('UPDATE search_biblio SET image=NULL WHERE biblio_id=%d', $_POST['bimg']));
  if ($_delete) {
    @unlink(sprintf(IMGBS . 'docs/%s', $_POST['img']));
    exit('<script type="text/javascript">alert(\'' . $_POST['img'] . ' successfully removed!\'); $(\'#biblioImage, #imageFilename\').remove();</script>');
  }
  exit();
}
/* RECORD OPERATION */
if (isset($_POST['saveData']) and $can_read and $can_write) {
  $title      = trim(strip_tags($_POST['title']));
  $gmd        = trim(strip_tags($_POST['gmd_id']));
  $itemType   = trim(strip_tags($_POST['item_type_id']));

  // check form validity
  if (empty($gmd)) {
    utility::jsAlert(__('GMD can not be empty'));
    exit();
  } elseif (empty($title)) {
    utility::jsAlert(__('Title can not be empty'));
    exit();
  } elseif (empty($itemType)) {
    utility::jsAlert(__('Item Type can not be empty'));
    exit();
  } else {
    // include custom fields file
    if (file_exists(MDLBS . 'bibliography/custom_fields.inc.php')) {
      include MDLBS . 'bibliography/custom_fields.inc.php';
    }

    // create biblio_indexer class instance
    $indexer = new biblio_indexer($dbs);


    /* Main data */
    $data['title'] = $dbs->escape_string($title);
    /* modified by hendro */
    // $data['sor'] = trim($dbs->escape_string(strip_tags($_POST['sor'])));
    /* end of modification */
    $data['edition'] = trim($dbs->escape_string(strip_tags($_POST['edition'])));

    /* student ID */
    $data['student_id'] = trim($dbs->escape_string(strip_tags($_POST['student_id'])));
    $student_id  = trim(strip_tags($_POST['student_id']));

    /* cp email */
    $data['cp_email'] = trim($dbs->escape_string(strip_tags($_POST['cp_email'])));
    $email_cp  = trim(strip_tags($_POST['cp_email']));

    /* departement */
    $data['departement'] = trim($dbs->escape_string(strip_tags($_POST['departement'])));


    // ITEM TYPE
    if (!is_numeric($_POST['item_type_id']) and !empty($_POST['item_type_id'])) {
      $getID = setiadi_utility::newSelect($dbs, 'mst_item_type', 'item_type_name', $dbs->escape_string($_POST['item_type_id']));
      $data['item_type_id'] = trim($dbs->escape_string(strip_tags($getID)));
    } else {
      $data['item_type_id'] = (empty($_POST['item_type_id'])) ? '0' : trim($dbs->escape_string(strip_tags($_POST['item_type_id'])));
    }

    // License
    if (!is_numeric($_POST['license_id']) and !empty($_POST['license_id'])) {
      $getID = setiadi_utility::newSelect($dbs, 'mst_license', 'license_name', $dbs->escape_string($_POST['license_id']));
      $data['license_id'] = trim($dbs->escape_string(strip_tags($getID)));
    } else {
      $data['license_id'] = (empty($_POST['license_id'])) ? '0' : trim($dbs->escape_string(strip_tags($_POST['license_id'])));
    }

    // Code Ministry
    if (!is_numeric($_POST['code_ministry']) and !empty($_POST['code_ministry'])) {
      $getID = setiadi_utility::newSelect($dbs, 'mst_code_ministry', 'name_prodi', $dbs->escape_string($_POST['code_ministry']));
      $data['code_ministry'] = trim($dbs->escape_string(strip_tags($getID)));
    } else {
      $data['code_ministry'] = (empty($_POST['code_ministry'])) ? '0' : trim($dbs->escape_string(strip_tags($_POST['code_ministry'])));
    }



    // Checking for GMD
    if (!is_numeric($_POST['gmd_id']) and !empty($_POST['gmd_id'])) {
      $getID = setiadi_utility::newSelect($dbs, 'mst_gmd', 'gmd_name', $dbs->escape_string($_POST['gmd_id']));
      $data['gmd_id'] = trim($dbs->escape_string(strip_tags($getID)));
    } else {
      $data['gmd_id'] = (empty($_POST['gmd_id'])) ? '0' : trim($dbs->escape_string(strip_tags($_POST['gmd_id'])));
    }
    $data['isbn_issn'] = trim($dbs->escape_string(strip_tags($_POST['isbn_issn'])));
    $data['uid'] = $_SESSION['uid'];

    // check publisher
    if (!is_numeric($_POST['publisherID']) and !empty($_POST['publisherID'])) {
      $getID = setiadi_utility::newSelect($dbs, 'mst_publisher', 'publisher_name', $dbs->escape_string($_POST['publisherID']));
      $data['publisher_id'] = trim($dbs->escape_string(strip_tags($getID)));
    } else {
      $data['publisher_id'] = (empty($_POST['publisherID'])) ? '0' : trim($dbs->escape_string(strip_tags($_POST['publisherID'])));
    }

    $data['publish_year']   = trim($dbs->escape_string(strip_tags($_POST['year'])));
    $data['collation']     = trim($dbs->escape_string(strip_tags($_POST['collation'])));
    //$data['series_title']   = trim($dbs->escape_string(strip_tags($_POST['seriesTitle'])));
    $data['call_number']   = trim($dbs->escape_string(strip_tags($_POST['callNumber'])));
    $data['language_id']  = trim($dbs->escape_string(strip_tags($_POST['languageID'])));
    $data['url_crossref']  = trim($dbs->escape_string(strip_tags($_POST['urlcrossref'])));

    // copyright
    if (!is_numeric($_POST['copyright_id']) and !empty($_POST['copyright_id'])) {
      $getID = setiadi_utility::newSelect($dbs, 'mst_copyright', 'copyright_name', $dbs->escape_string($_POST['copyright_id']));
      $data['copyright_id'] = trim($dbs->escape_string(strip_tags($getID)));
    } else {
      $data['copyright_id'] = (empty($_POST['copyright_id'])) ? '0' : trim($dbs->escape_string(strip_tags($_POST['copyright_id'])));
    }

    // check place
    if (!is_numeric($_POST['placeID']) and !empty($_POST['placeID'])) {
      $getID = setiadi_utility::newSelect($dbs, 'mst_place', 'place_name', $dbs->escape_string($_POST['placeID']));
      $data['publish_place_id'] = trim($dbs->escape_string(strip_tags($getID)));
    } else {
      $data['publish_place_id'] = (empty($_POST['placeID'])) ? '0' : trim($dbs->escape_string(strip_tags($_POST['placeID'])));
    }
    $data['notes'] = $dbs->escape_string(trim($_POST['notes']));
    $data['classification'] = $dbs->escape_string(trim($_POST['class']));
    // $data['frequency_id'] = ($_POST['frequencyID'] == '0') ? 'literal{0}' : (int) $_POST['frequencyID'];
    $data['spec_detail_info'] = trim($dbs->escape_string(strip_tags($_POST['specDetailInfo'])));
    $data['input_date'] = date('Y-m-d H:i:s');
    $data['last_update'] = date('Y-m-d H:i:s');




    // image uploading
    if ($_FILES['image'] and $_FILES['image']['size']) {
      // create upload object
      $image_upload = new simbio_file_upload();
      $image_upload->setAllowableFormat($sysconf['allowed_images']);
      $image_upload->setMaxSize($sysconf['max_image_upload'] * 1024);
      $image_upload->setUploadDir(IMGBS . 'docs');
      // upload the file and change all space characters to underscore
      $img_upload_status = $image_upload->doUpload('image', preg_replace('@\s+@i', '_', $_FILES['image']['name']));
      if ($img_upload_status == UPLOAD_SUCCESS) {
        $data['image'] = $dbs->escape_string($image_upload->new_filename);
        // write log
        utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'bibliography', $_SESSION['realname'] . ' upload image file ' . $image_upload->new_filename);
        utility::jsAlert(__('Image Uploaded Successfully'));
      } else {
        // write log
        utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'bibliography', 'ERROR : ' . $_SESSION['realname'] . ' FAILED TO upload image file ' . $image_upload->new_filename . ', with error (' . $image_upload->error . ')');
        utility::jsAlert(__('Image Uploaded Failed'));
      }
    } else if (!empty($_POST['base64picstring'])) {
      list($filedata, $filedom) = explode('#image/type#', $_POST['base64picstring']);
      $filedata = base64_decode($filedata);
      $fileinfo = getimagesizefromstring($filedata);
      $valid = strlen($filedata) / 1024 < $sysconf['max_image_upload'];
      $valid = (!$fileinfo || $valid === false) ? false : in_array($fileinfo['mime'], $sysconf['allowed_images_mimetype']);
      $new_filename = strtolower('cover_'
        . preg_replace("/[^a-zA-Z0-9]+/", "_", $data['title'])
        . '.' . $filedom);

      if ($valid and file_put_contents(IMGBS . 'docs/' . $new_filename, $filedata)) {
        $data['image'] = $dbs->escape_string($new_filename);
        if (!defined('UPLOAD_SUCCESS')) define('UPLOAD_SUCCESS', 1);
        $upload_status = UPLOAD_SUCCESS;
      }
    }

    // create sql op object
    $sql_op = new simbio_dbop($dbs);
    if (isset($_POST['updateRecordID'])) {
      /* UPDATE RECORD MODE */
      // remove input date
      unset($data['input_date']);
      unset($data['uid']);
      // filter update record ID
      $updateRecordID = (int) $_POST['updateRecordID'];




      // update data
      $update = $sql_op->update('biblio', $data, 'biblio_id=' . $updateRecordID);

      // send an alert
      if ($update) {



        // update custom data
        if (isset($custom_data)) {
          // check if custom data for this record exists
          $_sql_check_custom_q = sprintf('SELECT biblio_id FROM biblio_custom WHERE biblio_id=%d', $updateRecordID);
          $check_custom_q = $dbs->query($_sql_check_custom_q);
          if ($check_custom_q->num_rows) {
            $update2 = @$sql_op->update('biblio_custom', $custom_data, 'biblio_id=' . $updateRecordID);
          } else {
            $custom_data['biblio_id'] = $updateRecordID;
            @$sql_op->insert('biblio_custom', $custom_data);
          }
        }
        if ($sysconf['bibliography_update_notification']) {


          if ($_POST['sendMail'] == 'Send') {
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
            $_mail->SetFrom($sysconf['mail']['from'], $sysconf['mail']['from_name']);
            $_mail->AddReplyTo($sysconf['mail']['reply_to'], $sysconf['mail']['reply_to_name']);
            $_mail->Subject = "Verified approved";
            $_mail->AddAddress($email_cp, $email_cp);
            $_mail->MsgHTML('Hello ' . $email_cp . ',
        </br>
        </br>
        Congratulations! Your ' . $title . ' has been approved. From now on, you can surf our website to explore a lot of interesting things. 
        Best regards');
            $_sent = $_mail->Send();

            utility::jsAlert(__('Email Has Been Send'));
            //   ==========================================
            //   ========== END CUSTOM EMAIL ============
            //   ==========================================

          }


          utility::jsAlert(__('Bibliography Data Successfully Updated'));
        }

        // auto insert catalog to UCS if enabled
        if ($sysconf['ucs']['enable']) {
          echo '<script type="text/javascript">parent.ucsUpload(\'' . MWB . 'bibliography/ucs_upload.php\', \'itemID[]=' . $updateRecordID . '\', false);</script>';
        }
        // write log
        utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'bibliography', $_SESSION['realname'] . ' update bibliographic data (' . $data['title'] . ') with biblio_id (' . $updateRecordID . ')');
        // close window OR redirect main page
        if ($in_pop_up) {
          //$itemCollID = (integer)$_POST['itemCollID'];
          //echo '<script type="text/javascript">top.$(\'#mainContent\').simbioAJAX(parent.jQuery.ajaxHistory[0].url, {method: \'post\', addData: \''.( $itemCollID?'itemID='.$itemCollID.'&detail=true':'' ).'\'});</script>';
          echo '<script type="text/javascript">parent.jQuery.colorbox.close();</script>';
        } else {
          echo '<script type="text/javascript">top.$(\'#mainContent\').simbioAJAX(parent.jQuery.ajaxHistory[0].url);</script>';
        }

        // update index
        // delete from index first
        $sql_op->delete('search_biblio', "biblio_id=$updateRecordID");
        $indexer->makeIndex($updateRecordID);
      } else {
        utility::jsAlert(__('Bibliography Data FAILED to Updated. Please Contact System Administrator') . "\n" . $sql_op->error);
      }
    } else {
      /* INSERT RECORD MODE */


      
    //START UNIQ ID
    $q_unique   = sprintf('SELECT max(unique_id) AS uniqID FROM biblio');
    $unique_max_q = $dbs->query($q_unique);
    $unique_max_d = $unique_max_q->fetch_assoc();
    $unique_rows_d = $unique_max_q->num_rows;

    $max_count    = $unique_max_d['uniqID'];

    if ($unique_rows_d != 0) {
      $max_count++;
    } 

   $data['unique_id'] = $dbs->escape_string($max_count);
    
    //CLOSE UNIQ ID

      // insert the data
      $insert = $sql_op->insert('biblio', $data);
      if ($insert) {




        // get auto id of this record
        $last_biblio_id = $sql_op->insert_id;
        // add authors
        if ($_SESSION['biblioAuthor']) {
          foreach ($_SESSION['biblioAuthor'] as $author) {
            $sql_op->insert('biblio_author', array('biblio_id' => $last_biblio_id, 'author_id' => $author[0], 'level' => $author[1]));
          }
        }
        // add supervisor
        if ($_SESSION['biblioSupervisor']) {
          foreach ($_SESSION['biblioSupervisor'] as $supervisor) {
            $sql_op->insert('biblio_supervisor', array('biblio_id' => $last_biblio_id, 'supervisor_id' => $supervisor[0], 'level' => $supervisor[1]));
          }
        }

        // add Examiner
        if ($_SESSION['biblioExaminer']) {
          foreach ($_SESSION['biblioExaminer'] as $examiner) {
            $sql_op->insert('biblio_examiner', array('biblio_id' => $last_biblio_id, 'examiner_id' => $examiner[0], 'level' => $examiner[1]));
          }
        }

        // add Contributor
        if ($_SESSION['biblioContributor']) {
          foreach ($_SESSION['biblioContributor'] as $contributor) {
            $sql_op->insert('biblio_contributor', array('biblio_id' => $last_biblio_id, 'contributor_id' => $contributor[0], 'level' => $contributor[1]));
          }
        }
        // add topics
        if ($_SESSION['biblioTopic']) {
          foreach ($_SESSION['biblioTopic'] as $topic) {
            $sql_op->insert('biblio_topic', array('biblio_id' => $last_biblio_id, 'topic_id' => $topic[0], 'level' => $topic[1]));
          }
        }
        // add attachment
        if ($_SESSION['biblioAttach']) {
          foreach ($_SESSION['biblioAttach'] as $attachment) {
            $sql_op->insert('biblio_attachment', array('biblio_id' => $last_biblio_id, 'file_id' => $attachment['file_id'], 'access_type' => $attachment['access_type']));
          }
        }

        if ($_POST['sendMail'] == 'Send') {



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
          $_mail->SetFrom($sysconf['mail']['from'], $sysconf['mail']['from_name']);
          $_mail->AddReplyTo($sysconf['mail']['reply_to'], $sysconf['mail']['reply_to_name']);
          $_mail->Subject = "Verified approved";
          $_mail->AddAddress($email_cp, $email_cp);
          $_mail->MsgHTML('Hello ' . $email_cp . ',
          </br>
          </br>
          Congratulations! Your ' . $title . ' has been approved. From now on, you can surf our website to explore a lot of interesting things. 
          Best regards');
          $_sent = $_mail->Send();

          utility::jsAlert(__('Email Has Been Send'));

          //   ==========================================
          //   ========== END CUSTOM EMAIL ============
          //   ==========================================

        }

        utility::jsAlert(__('New Bibliography Data Successfully Saved'));


        // clear related sessions
        $_SESSION['biblioAuthor'] = array();
        $_SESSION['biblioSupervisor'] = array();
        $_SESSION['biblioExaminer'] = array();
        $_SESSION['biblioAttach'] = array();
        $_SESSION['biblioTopic'] = array();
        // write log
        utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'bibliography', $_SESSION['realname'] . ' insert bibliographic data (' . $data['title'] . ') with biblio_id (' . $last_biblio_id . ')');
        // update index
        $indexer->makeIndex($last_biblio_id);
        echo '<script type="text/javascript">top.$(\'#mainContent\').simbioAJAX(parent.jQuery.ajaxHistory[0].url);</script>';
      } else {
        utility::jsAlert(__('Bibliography Data FAILED to Save. Please Contact System Administrator') . "\n" . $sql_op->error);
      }
    }

    // If we are not in Pop
    if (!isset($_POST['inPop'])) {
      echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(\'' . MWB . 'bibliography/index.php\', {method: \'post\', addData: \'itemID=' . (isset($updateRecordID) ? $updateRecordID : $last_biblio_id) . '&detail=true\'});</script>';
    }
    exit();
  }
  exit();
} else if (isset($_POST['itemID']) and !empty($_POST['itemID']) and isset($_POST['itemAction'])) {
  if (!($can_read and $can_write)) {
    die();
  }
  /* DATA DELETION PROCESS */
  // create sql op object
  $sql_op = new simbio_dbop($dbs);
  $failed_array = array();
  $error_num = 0;
  $still_have_item = array();
  if (!is_array($_POST['itemID'])) {
    // make an array
    $_POST['itemID'] = array((int) $_POST['itemID']);
  }
  // loop array
  $http_query = '';
  foreach ($_POST['itemID'] as $itemID) {
    $itemID = (int) $itemID;
    // check if this biblio data still have an item
    $_sql_biblio_item_q = sprintf('SELECT b.title, COUNT(item_id) FROM biblio AS b
      LEFT JOIN item AS i ON b.biblio_id=i.biblio_id
      WHERE b.biblio_id=%d GROUP BY title', $itemID);
    $biblio_item_q = $dbs->query($_sql_biblio_item_q);
    $biblio_item_d = $biblio_item_q->fetch_row();
    if ($biblio_item_d[1] < 1) {
      if (!$sql_op->delete('biblio', "biblio_id=$itemID")) {
        $error_num++;
      } else {
        // write log
        utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'bibliography', $_SESSION['realname'] . ' DELETE bibliographic data (' . $biblio_item_d[0] . ') with biblio_id (' . $itemID . ')');
        // delete related data
        $sql_op->delete('biblio_topic', "biblio_id=$itemID");
        $sql_op->delete('biblio_author', "biblio_id=$itemID");
        $sql_op->delete('biblio_supervisor', "biblio_id=$itemID");
        $sql_op->delete('biblio_examiner', "biblio_id=$itemID");
        $sql_op->delete('biblio_contributor', "biblio_id=$itemID");
        $sql_op->delete('biblio_attachment', "biblio_id=$itemID");
        $sql_op->delete('search_biblio', "biblio_id=$itemID");
        // add to http query for UCS delete
        $http_query .= "itemID[]=$itemID&";
      }
    } else {
      $still_have_item[] = substr($biblio_item_d[0], 0, 45) . '... still have ' . $biblio_item_d[1] . ' copies';
      $error_num++;
    }
  }

  if ($still_have_item) {
    $titles = '';
    foreach ($still_have_item as $title) {
      $titles .= $title . "\n";
    }
    utility::jsAlert(__('Below data can not be deleted:') . "\n" . $titles);
    echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(\'' . $_SERVER['PHP_SELF'] . '\', {addData: \'' . $_POST['lastQueryStr'] . '\'});</script>';
    exit();
  }
  // auto delete data on UCS if enabled
  if ($http_query && $sysconf['ucs']['enable'] && $sysconf['ucs']['auto_delete']) {
    echo '<script type="text/javascript">parent.ucsUpdate(\'' . MWB . 'bibliography/ucs_update.php\', \'nodeOperation=delete&' . $http_query . '\');</script>';
  }
  // error alerting
  if ($error_num == 0) {
    utility::jsAlert(__('All Data Successfully Deleted'));
    echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(\'' . $_SERVER['PHP_SELF'] . '\', {addData: \'' . $_POST['lastQueryStr'] . '\'});</script>';
  } else {
    utility::jsAlert(__('Some or All Data NOT deleted successfully!\nPlease contact system administrator'));
    echo '<script type="text/javascript">parent.parent.$(\'#mainContent\').simbioAJAX(\'' . $_SERVER['PHP_SELF'] . '\', {addData: \'' . $_POST['lastQueryStr'] . '\'});</script>';
  }
  exit();
}
/* RECORD OPERATION END */

if (!$in_pop_up) {
  /* search form */
?>
  <fieldset class="menuBox">
    <div class="menuBoxInner biblioIcon">
      <div class="per_title">
        <h2><?php echo __('Bibliographic'); ?></h2>
      </div>
      <div class="sub_section">
        <div class="btn-group">
          <a href="<?php echo MWB; ?>bibliography/index.php" class="btn btn-default"><i class="glyphicon glyphicon-list-alt"></i>&nbsp;<?php echo __('Bibliographic List'); ?></a>
          <a href="<?php echo MWB; ?>bibliography/index.php?action=detail" class="btn btn-default"><i class="glyphicon glyphicon-plus"></i>&nbsp;<?php echo __('Add New Bibliography'); ?></a>
        </div>
        <form name="search" action="<?php echo MWB; ?>bibliography/index.php" id="search" method="get" style="display: inline;"><?php echo __('Search'); ?> :
          <input type="text" name="keywords" id="keywords" size="30" />
          <select name="field">
            <option value="0"><?php echo __('All Fields'); ?></option>
            <option value="title"><?php echo __('Title/Series Title'); ?> </option>
            <option value="subject"><?php echo __('Topics'); ?></option>
            <option value="author"><?php echo __('Authors'); ?></option>
            <option value="isbn"><?php echo __('ISBN/ISSN'); ?></option>
            <option value="publisher"><?php echo __('Publisher'); ?></option>
          </select>
          <input type="submit" id="doSearch" value="<?php echo __('Search'); ?>" class="btn btn-default" />
        </form>
        <?php
        // enable UCS?
        if ($sysconf['ucs']['enable']) {
        ?>
          <a href="#" onclick="ucsUpload('<?php echo MWB; ?>bibliography/ucs_upload.php', serializeChbox('dataList'))" class="notAJAX">
            <div class="btn btn-default"><?php echo __('Upload Selected Bibliographic data to Union Catalog Server*'); ?></div>
          </a>
        <?php
        }
        ?>
      </div>
    </div>
  </fieldset>
<?php
  /* search form end */
}
/* main content */
if (isset($_POST['detail']) or (isset($_GET['action']) and $_GET['action'] == 'detail')) {
  if (!($can_read and $can_write)) {
    die('<div class="errorBox">' . __('You are not authorized to view this section') . '</div>');
  }
  /* RECORD FORM */
  // try query
  /* Modification for Setiadi 2 by Drajat Hasan */
  $itemID = (int) isset($_POST['itemID']) ? $_POST['itemID'] : 0;
  $select_op = new simbio_dbop($dbs);
  $select_op->coloumn = array('b.*', 'p.publisher_name', 'pl.place_name', 'g.gmd_name', 'lg.language_name', 'cp.copyright_name', 'it.item_type_name', 'ministry.name_prodi', 'ml.license_name');
  $select_op->table  = 'biblio AS b ';
  $select_op->table .= 'LEFT JOIN mst_frequency AS f ON b.frequency_id=f.frequency_id ';
  $select_op->table .= 'LEFT JOIN mst_gmd AS g ON b.gmd_id=g.gmd_id ';
  $select_op->table .= 'LEFT JOIN mst_language AS lg ON b.language_id=lg.language_id ';
  $select_op->table .= 'LEFT JOIN mst_publisher AS p ON b.publisher_id=p.publisher_id ';
  $select_op->table .= 'LEFT JOIN mst_place AS pl ON b.publish_place_id=pl.place_id ';
  $select_op->table .= 'LEFT JOIN mst_item_type AS it ON b.item_type_id=it.item_type_id ';
  $select_op->table .= 'LEFT JOIN mst_copyright AS cp ON b.copyright_id=cp.copyright_id ';
  $select_op->table .= 'LEFT JOIN mst_license AS ml ON b.license_id=ml.license_id ';
  $select_op->table .= 'LEFT JOIN mst_code_ministry AS ministry ON b.code_ministry=ministry.code_ministry';
  $select_op->criteria = sprintf('biblio_id=%d', $itemID);
  $select_op->runQuery();
  $select_op->getRow();
  $select_op->getData('assoc');
  $rec_d = $select_op->data;

  //coll type options
  $coll_q = $dbs->query('SELECT coll_type_id, coll_type_name FROM mst_coll_type');
  $coll_options = array();
  while ($coll_d = $coll_q->fetch_row()) {
    $coll_options[] = array($coll_d[0], $coll_d[1]);
  }

  //Location options
  $loc_q = $dbs->query('SELECT location_id, location_name FROM mst_location');
  $loc_options = array();
  while ($loc_d = $loc_q->fetch_row()) {
    $loc_options[] = array($loc_d[0], $loc_d[1]);
  }

  //Item Type
  $itemType_q = $dbs->query('SELECT item_type_id, item_type_name FROM mst_item_type');
  $itemType_options = array();
  while ($itemType_d = $itemType_q->fetch_row()) {
    $itemType_options[] = array($itemType_d[0], $itemType_d[1]);
  }

  //code Ministry
  $codeMinistry_q = $dbs->query('SELECT code_ministry, name_prodi FROM mst_code_ministry');
  $codeMinistry_options = array();
  while ($codeMinistry_d = $codeMinistry_q->fetch_row()) {
    $codeMinistry_options[] = array($codeMinistry_d[0], $codeMinistry_d[1]);
  }

  //license
  $license_q = $dbs->query('SELECT license_id, license_name FROM mst_license');
  $license_options = array();
  while ($license_d = $license_q->fetch_row()) {
    $license_options[] = array($license_d[0], $license_d[1]);
  }

  //gmd options
  $gmd_q = $dbs->query('SELECT gmd_id, gmd_name FROM mst_gmd');
  $gmd_options = array();
  while ($gmd_d = $gmd_q->fetch_row()) {
    $gmd_options[] = array($gmd_d[0], $gmd_d[1]);
  }
  //languange options
  $lang_q = $dbs->query('SELECT language_id, language_name FROM mst_language');
  $lang_options = array();
  while ($lang_d = $lang_q->fetch_row()) {
    $lang_options[] = array($lang_d[0], $lang_d[1]);
  }

  //copyright options
  $copyright_q = $dbs->query('SELECT copyright_id, copyright_name FROM mst_copyright');
  $copyright_options = array();
  while ($copyright_d = $copyright_q->fetch_row()) {
    $copyright_options[] = array($copyright_d[0], $copyright_d[1]);
  }


  //publisher option
  $publ_options[] = array('0', __('Not Applicable'));
  $publ_q = $dbs->query('SELECT publisher_id, publisher_name FROM mst_publisher');
  while ($publ_d = $publ_q->fetch_row()) {
    $publ_options[] = array($publ_d[0], $publ_d[1]);
  }

  //publish place 
  $plc_options[] = array('0', __('Not Applicable'));
  $plc_q = $dbs->query('SELECT place_id, place_name FROM mst_place');
  while ($plc_d = $plc_q->fetch_row()) {
    $plc_options[] = array($plc_d[0], $plc_d[1]);
  }
?>

  <?php
  // Setiadi Form
  $setiadiForm = new setiadi_form($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'], 'POST', (isset($_GET['inPopUp'])) ? true : false, $select_op->num_rows, $dbs->escape_string($itemID));
  // Edit Mode
  if ($setiadiForm->enableForm) {
    // Additional Attrib
    $item_ID       = ($setiadiForm->itemID != 0) ? '<input type="hidden" name="updateRecordID" id="updateRecordID" value="' . $setiadiForm->itemID . '"/>' : '<input name="updateRecordID" value="0"/>';
    $item_ID      .= ($setiadiForm->inPop) ? '<input type="hidden" name="inPop" value="1"/>' : NULL;
    $visibility    = ($rec_d['image']) ? true : false;
    $upper_dir     = ($setiadiForm->inPop) ? '../../../' : '../';
    $inPopWidth    = ($setiadiForm->inPop) ? '89%' : '94%';
    $style         = ($visibility) ? 'style="float:left; width:' . $inPopWidth . ' !important; padding: 16.5px; margin-bottom: 15px;"' : NULL;
    $info          = '<div class="infoBox" ' . $style . '>Anda akan mengubah data biblio : <b>' . $rec_d['title'] . '</b><br>Terakhir diubah ' . $rec_d['last_update'] . '</div>';
    $info         .= ($visibility) ? '<div id="biblioImage" style="float: right;"><img src="' . $upper_dir . 'lib/minigalnano/createthumb.php?filename=../../images/docs/' . urlencode($rec_d['image']) . '&width=53" style="border: 1px solid #999999" /></div>' : '<div id="biblioImage"><a></a></div>';
    $str_anything  = '<div style="margin-top: 7px;float: left;width: 100%;">';
    $str_anything .= '<button name="saveData" class="save btn btn-success">Simpan</button>&nbsp;';
    $str_anything .= '<button class="cancel notAJAX push-right btn btn-warning">Batal</button>&nbsp;';
    $str_anything .= '<button class="delete notAJAX push-right btn btn-danger">Hapus Cantuman</button>&nbsp;';
    $str_anything .= '<button class="edit notAJAX push-right btn btn-primary" style="float: right;">Sunting</button>';
    $str_anything .= '</div>';
    $str_anything .= "<script type=\"text/javascript\">
                      $(document).ready(function(){
                        $('.setiadi-form').find('input, textarea, select, .save, .delete, .addSelect, .openPopUp').attr('disabled','disabled');
                        $('#imageFilename a').attr('style', 'pointer-events: none;');
                        $('.dateField a').attr('style', 'pointer-events: none !important;');
                      });
                      </script>";
    $setiadiForm->createAnything($info . $str_anything . $item_ID);
    // GMD
    $setiadiForm->createSelect('GMD Type', $rec_d['gmd_name'], 'gmd_id', $gmd_options);

    // Item Type
    $setiadiForm->createSelect('Item Type', $rec_d['item_type_name'], 'item_type_id', $itemType_options);
    // Title
    $setiadiForm->createText('title', $rec_d['title'], 'title');
    // Author
    if (!$setiadiForm->inPop) {
      $str_anything  = '<span>Author</span>';
      $str_anything .= '<a style="margin-top: 10px;margin-bottom: 10px;" class="notAJAX button btn btn-info openPopUp" href="' . MWB . 'bibliography/pop_author.php?biblioID=' . $rec_d['biblio_id'] . '" title="' . __('Authors/Roles') . '">' . __('Add Author(s)') . '</a>';
      $str_anything .= '<div class="block" style="width: 100%;height: 70px;position: absolute;z-index: 7;"></div><iframe name="authorIframe" id="authorIframe" class="borderAll" style="width: 100%; height: 70px;" src="' . MWB . 'bibliography/iframe_author.php?biblioID=' . $rec_d['biblio_id'] . '&block=1"></iframe>';
      $setiadiForm->createAnything($str_anything);
      $str_anything  = '';
    }

    //EDIT NOMOR POKOK MAHASISWA
    $setiadiForm->createText('Student ID', $rec_d['student_id'], 'student_id');
    //COntact Email
    $setiadiForm->createText('Contact Email', $rec_d['cp_email'], 'cp_email');

    // supervisor
    if (!$setiadiForm->inPop) {
      $str_anything  = '<span>Supervisor</span>';
      $str_anything .= '<a style="margin-top: 10px;margin-bottom: 10px;" class="notAJAX button btn btn-info openPopUp" href="' . MWB . 'bibliography/pop_supervisor.php?biblioID=' . $rec_d['biblio_id'] . '" title="' . __('Supervisors/Roles') . '">' . __('Add Supervisor(s)') . '</a>';
      $str_anything .= '<div class="block" style="width: 100%;height: 70px;position: absolute;z-index: 7;"></div><iframe name="superIframe" id="superIframe" class="borderAll" style="width: 100%; height: 70px;" src="' . MWB . 'bibliography/iframe_supervisor.php?biblioID=' . $rec_d['biblio_id'] . '&block=1"></iframe>';
      $setiadiForm->createAnything($str_anything);
      $str_anything  = '';
    }


    // examiner
    if (!$setiadiForm->inPop) {
      $str_anything  = '<span>examiner</span>';
      $str_anything .= '<a style="margin-top: 10px;margin-bottom: 10px;" class="notAJAX button btn btn-info openPopUp" href="' . MWB . 'bibliography/pop_examiner.php?biblioID=' . $rec_d['biblio_id'] . '" title="' . __('examiners/Roles') . '">' . __('Add Examiner(s)') . '</a>';
      $str_anything .= '<div class="block" style="width: 100%;height: 70px;position: absolute;z-index: 7;"></div><iframe name="examinerIframe" id="examinerIframe" class="borderAll" style="width: 100%; height: 70px;" src="' . MWB . 'bibliography/iframe_examiner.php?biblioID=' . $rec_d['biblio_id'] . '&block=1"></iframe>';
      $setiadiForm->createAnything($str_anything);
      $str_anything  = '';
    }


    // contributor
    if (!$setiadiForm->inPop) {
      $str_anything  = '<span>Contributor</span>';
      $str_anything .= '<a style="margin-top: 10px;margin-bottom: 10px;" class="notAJAX button btn btn-info openPopUp" href="' . MWB . 'bibliography/pop_contributor.php?biblioID=' . $rec_d['biblio_id'] . '" title="' . __('Contributor/Roles') . '">' . __('Add Contributor(s)') . '</a>';
      $str_anything .= '<div class="block" style="width: 100%;height: 70px;position: absolute;z-index: 7;"></div><iframe name="contributorIframe" id="contributorIframe" class="borderAll" style="width: 100%; height: 70px;" src="' . MWB . 'bibliography/iframe_contributor.php?biblioID=' . $rec_d['biblio_id'] . '&block=1"></iframe>';
      $setiadiForm->createAnything($str_anything);
      $str_anything  = '';
    }


    //INSERT CODE MINISTRY 
    // $setiadiForm->createText('Code Ministry PDDIKTI', $rec_d['code_id_ministry'], 'code_id_ministry');
    // $str_prodi  = '<span>Search Code Ministry</span>';

    // $str_prodi .= '<a style="margin-top: 10px;margin-bottom: 10px;" class="notAJAX button btn btn-info openPopUp" style="width: 100%; height: 80%;" href="https://forlap.ristekdikti.go.id/prodi" title="' . __('Search Prodi PPDIKTI') . '">' . __('Search PDDIKTI') . '</a>';
    // $setiadiForm->createAnything($str_prodi);

    $setiadiForm->createSelect('Code Ministry PDDIKTI', $rec_d['name_prodi'], 'code_ministry', $codeMinistry_options);



    // Sor
    // $setiadiForm->createText('Statement of Responsibility', $rec_d['sor'], 'sor');
    // Edition
    //$setiadiForm->createText('edition', $rec_d['edition'], 'edition');
    $array_edition = array('Published', 'In Press', 'Submitted', 'Unpublished');
    $setiadiForm->createRegSelect('Date Type', $rec_d['edition'], 'edition', $array_edition);
    // Bib Att
    // if (!$setiadiForm->inPop) {
    //   $BibAtt = new longBiblioAtt();
    //   // Set Batch Code List
    //   $setList = $BibAtt->setList($dbs);
    //   $str_anything  = '<span>Item Code </span>';
    //   $str_anything .= '<div class="btn-group" style="float:left !important;">';
    //   $str_anything .= '<a style="margin-right:0px" class="notAJAX btn btn-primary openPopUp notIframe" href="'.MWB.'bibliography/pop_pattern.php" height="420px" title="'.__('Add new pattern').'">
    //                 <i class="glyphicon glyphicon-plus"></i> Add New Pattern</a>';
    //   $str_anything .= '<a href="'.MWB.'master_file/item_code_pattern.php" class="notAjax btn btn-default openPopUp" title="'.__('Item code pattern manager.').'"><i class="glyphicon glyphicon-wrench"></i></a>';
    //   $str_anything .= '</div>&nbsp;';
    //   $str_anything .= simbio_form_element::selectList('itemCodePattern', $setList, '', 'style="width: auto"').' &nbsp;';
    //   $str_anything .= '<label id="totalItemsLabel">' . __('Total item(s)').':</label> <input type="text" class="small_input" style="width: 100px;" name="totalItems" value="0" /> &nbsp;';
    //   $str_anything .= simbio_form_element::selectList('collTypeID', $coll_options, '', 'style="width: auto"').' &nbsp;';
    //   $str_anything .= simbio_form_element::selectList('locationID', $loc_options, '', 'style="width: auto"').' &nbsp;';
    //   $str_anything .= '<br><a style="margin-top: 10px; margin-bottom: 10px;" class="notAJAX button btn btn-info openPopUp" href="'.MWB.'bibliography/pop_item.php?inPopUp=true&action=detail&biblioID='.$rec_d['biblio_id'].'" title="'.__('Items/Copies').'" height="500">'.__('Add New Items').'</a>';
    //   $str_anything .= '<div class="block" style="width: 100%;height: 70px;position: absolute;z-index: 7;"></div><iframe name="itemIframe" id="itemIframe" class="borderAll" style="width: 100%; height: 70px;" src="'.MWB.'bibliography/iframe_item_list.php?biblioID='.$rec_d['biblio_id'].'&block=1"></iframe>'."\n";
    //   $setiadiForm->createAnything($str_anything);
    // }
    // Specific Detail Info
    $setiadiForm->createText('Specific Detail Info', $rec_d['spec_detail_info'], 'specDetailInfo');

    //Departement
    $setiadiForm->createText('Departement', $rec_d['departement'], 'departement');
    // ISBN/ISSN
    // $setiadiForm->createText('ISBN/ISSN', $rec_d['isbn_issn'], 'isbn_issn');
    $setiadiForm->createAnything('<label>Legalization</label>' . simbio_form_element::dateField('isbn_issn', $rec_d['isbn_issn']) . ' <b>hh = Tanggal 01 s.d. 31; bb = Bulan 01 s.d. 12; tttt = Tahun contoh: ' . date('Y'));
    // Publisher
    $setiadiForm->createSelect('Publisher', $rec_d['publisher_name'], 'publisherID', $publ_options);
    // Year
    $setiadiForm->createText('Publish Year', $rec_d['publish_year'], 'year');
    // Publishing Place
    $setiadiForm->createSelect('Publishing Place', $rec_d['place_name'], 'placeID', $plc_options);
    // Collation
    $setiadiForm->createText('Collation', $rec_d['collation'], 'collation');
    // language
    $setiadiForm->createSelect('Language', $rec_d['language_name'], 'languageID', $lang_options);

    // COpyright
    $setiadiForm->createSelect('Copyright', $rec_d['copyright_name'], 'copyright_id', $copyright_options);


    // Item Type
    $setiadiForm->createSelect('License', $rec_d['license_name'], 'license_id', $license_options);

    // Cover
    $str_image  = '<label>Upload Image</label>';
    $str_image .= ($rec_d['image']) ? '<div id="imageFilename"><a href="' . SWB . 'images/docs/' . $rec_d['image'] . '" class="openPopUp notAJAX"><strong>' . $rec_d['image'] . '</strong></a> <a href="' . MWB . 'bibliography/index.php" postdata="removeImage=true&bimg=' . $itemID . '&img=' . $rec_d['image'] . '" loadcontainer="imageFilename" class="makeHidden removeImage">' . __('REMOVE IMAGE') . '</a></div>' : NULL;
    $str_image .= simbio_form_element::textField('file', 'image');
    $str_image .= ' Maximum ' . $sysconf['max_image_upload'] . ' KB';
    $setiadiForm->createAnything($str_image);
    // Attachment
    if (!$setiadiForm->inPop) {
      $str_anything  = '<span>Attachment</span>';
      $str_anything .= '<a style="margin-top: 10px;margin-bottom: 10px;" class="notAJAX button btn btn-info openPopUp" href="' . MWB . 'bibliography/pop_attach.php?biblioID=' . $rec_d['biblio_id'] . '" title="' . __('File Attachments') . '">' . __('Add Attachment') . '</a>';
      $str_anything .= '<div class="block" style="width: 100%;height: 70px;position: absolute;z-index: 7;"></div><iframe name="attachIframe" id="attachIframe" class="borderAll" style="width: 100%; height: 70px;" src="' . MWB . 'bibliography/iframe_attach.php?biblioID=' . $rec_d['biblio_id'] . '&block=1"></iframe>';
      $setiadiForm->createAnything($str_anything);
      $str_anything  = '';
    }
    // Subject
    if (!$setiadiForm->inPop) {
      $str_anything  = '<span>Subject</span>';
      $str_anything .= '<a class="notAJAX button btn btn-info openPopUp" href="' . MWB . 'bibliography/pop_topic.php?biblioID=' . $rec_d['biblio_id'] . '" title="' . __('Subjects/Topics') . '">' . __('Add Subject(s)') . '</a>';
      $str_anything .= '<div class="block" style="width: 100%;height: 70px;position: absolute;z-index: 7;"></div><iframe name="topicIframe" id="topicIframe" class="borderAll" style="width: 100%; height: 70px;" src="' . MWB . 'bibliography/iframe_topic.php?biblioID=' . $rec_d['biblio_id'] . '&block=1"></iframe>';
      $setiadiForm->createAnything($str_anything);
      $str_anything = '';
    }

    // Series Title
    $setiadiForm->createTextArea('Abstract', $rec_d['notes'], 'notes');
    // Classification
    $setiadiForm->createText('Classification', $rec_d['classification'], 'class');
    // Collation
    $setiadiForm->createText('Call Number', $rec_d['call_number'], 'callNumber');

    // crossref
    $setiadiForm->createText('URL Crossref', $rec_d['url_crossref'], 'urlcrossref');

    //send email approved
    $array_edition = array('Send', 'Not Send');
    $setiadiForm->createRegSelect('Send Email Approved', '', 'sendMail', $array_edition);



    // Button
    $str_anything  = '<div style="margin-top: 7px;">';
    $str_anything .= '<button name="saveData" class="save btn btn-success">Simpan</button>&nbsp;';
    $str_anything .= '<button class="cancel notAJAX push-right btn btn-warning">Batal</button>&nbsp;';
    $str_anything .= '<button class="delete notAJAX push-right btn btn-danger">Hapus Cantuman</button>&nbsp;';
    $str_anything .= '<button class="edit notAJAX push-right btn btn-primary" style="float: right;">Sunting</button>';
    $str_anything .= '</div>';
    $setiadiForm->createAnything($str_anything . "<div class=\"msg\"></div>");
  } else {
    // Step
    $array_step = array('GMD', 'Bibliografi', 'Approval');
    echo $setiadiForm->createStep($array_step);
    // GMD
    $setiadiForm->setSeparator('gmd', 'activeAppear', '');
    // Publishing Place
    $setiadiForm->createSelect('GMD Type', '', 'gmd_id', $gmd_options);
    // Next Button
    $str_anything = '<a href="#" des="bibliografi" style="margin-top: 20px;" class="nextBtn notAJAX btn btn-success">Next</a>';

    $setiadiForm->createAnything($str_anything);
    // Lash Biblio Sep
    $setiadiForm->setCloseSeparator();
    // Biblio
    $setiadiForm->setSeparator('bibliography', '', 'style="display: none;"');


    // Item Type
    $setiadiForm->createSelect('Item Type', '', 'item_type_id', $itemType_options);



    // Title
    $setiadiForm->createText('Title', '', 'title');
    // Author
    $str_anything  = '<span>Author</span>';
    $str_anything .= '<a style="margin-top: 10px;margin-bottom: 10px;" class="notAJAX button btn btn-info openPopUp" href="' . MWB . 'bibliography/pop_author.php?biblioID=0" title="' . __('Authors/Roles') . '">' . __('Add Author(s)') . '</a>';
    $str_anything .= '<iframe name="authorIframe" id="authorIframe" class="borderAll" style="width: 100%; height: 70px;" src="' . MWB . 'bibliography/iframe_author.php?biblioID=0&block=1"></iframe>';
    $setiadiForm->createAnything($str_anything);





    //INSERT STUDENT ID
    $setiadiForm->createText('Student ID', '', 'student_id');

    $setiadiForm->createText('Contact Email', '', 'cp_email');


    //INSERT SUPERVISOR
    $str_anything  = '<span>Supervisor</span>';
    $str_anything .= '<a style="margin-top: 10px;margin-bottom: 10px;" class="notAJAX button btn btn-info openPopUp" href="' . MWB . 'bibliography/pop_supervisor.php?biblioID=0" title="' . __('Supervisors/Roles') . '">' . __('Add supervisors') . '</a>';
    $str_anything .= '<iframe name="superIframe" id="superIframe" class="borderAll" style="width: 100%; height: 70px;" src="' . MWB . 'bibliography/iframe_supervisor.php?biblioID=0&block=1"></iframe>';
    $setiadiForm->createAnything($str_anything);


    //INSERT PENGUJI
    $str_anything  = '<span>examiner</span>';
    $str_anything .= '<a style="margin-top: 10px;margin-bottom: 10px;" class="notAJAX button btn btn-info openPopUp" href="' . MWB . 'bibliography/pop_examiner.php?biblioID=0" title="' . __('examiner/Roles') . '">' . __('Add examiner') . '</a>';
    $str_anything .= '<iframe name="examinerIframe" id="examinerIframe" class="borderAll" style="width: 100%; height: 70px;" src="' . MWB . 'bibliography/iframe_examiner.php?biblioID=0&block=1"></iframe>';
    $setiadiForm->createAnything($str_anything);

    //INSERT Contributor
    $str_anything  = '<span>Contributor</span>';
    $str_anything .= '<a style="margin-top: 10px;margin-bottom: 10px;" class="notAJAX button btn btn-info openPopUp" href="' . MWB . 'bibliography/pop_contributor.php?biblioID=0" title="' . __('Contributor/Roles') . '">' . __('Add Contributor') . '</a>';
    $str_anything .= '<iframe name="contributorIframe" id="contributorIframe" class="borderAll" style="width: 100%; height: 70px;" src="' . MWB . 'bibliography/iframe_contributor.php?biblioID=0&block=1"></iframe>';
    $setiadiForm->createAnything($str_anything);


    $setiadiForm->createRegSelectNoAdd('Code Ministry PDDIKTI', '', 'code_ministry', $codeMinistry_options);

    // Sor
    // $setiadiForm->createText('Statement Of Responsibility', '', 'sor');
    // Edition
    //$setiadiForm->createText('edition', '', 'edition');
    $array_edition = array('Published', 'In Press', 'Submitted', 'Unpublished');
    $setiadiForm->createRegSelect('Date Type', '', 'edition', $array_edition);
    // Specific Detail Info
    $setiadiForm->createText('Specific Detail Info', '', 'specDetailInfo');
    // Departement
    $setiadiForm->createText('Departement', '', 'departement');

    // Frequency
    // ISBN/ISSN
    //$setiadiForm->createText('Lembar Pengesahan', '', 'isbn_issn');
    $setiadiForm->createAnything('<label>Lembar Pengesahan</label>' . simbio_form_element::dateField('isbn_issn', '') . ' <b>hh = Tanggal 01 s.d. 31; bb = Bulan 01 s.d. 12; tttt = Tahun contoh: ' . date('Y'));
    // Publisher
    $setiadiForm->createSelect('Publisher', '', 'publisherID', $publ_options);
    // Year
    $setiadiForm->createText('Publish Year', '', 'year');
    // Publishing Place
    $setiadiForm->createSelect('Publishing Place', '', 'placeID', $plc_options);
    // Collation
    $setiadiForm->createText('Collation', '', 'collation');

    // language
    $setiadiForm->createSelect('Language', '', 'languageID', $lang_options);

    // copyright
    $setiadiForm->createSelect('Copyright', '', 'copyright_id', $copyright_options);

    // license
    $setiadiForm->createSelect('License', '', 'license_id', $license_options);

   

    // Cover
    $str_image  = '<label>Upload Image</label>';
    $str_image .= simbio_form_element::textField('file', 'image');
    $str_image .= ' Maximum ' . $sysconf['max_image_upload'] . ' KB';
    $setiadiForm->createAnything($str_image);
    // Attachment
    $str_anything  = '<span>File lampiran</span>';
    $str_anything .= '<a style="margin-top: 10px;margin-bottom: 10px;" class="notAJAX button btn btn-info openPopUp" href="' . MWB . 'bibliography/pop_attach.php?biblioID=0" title="' . __('File Attachments') . '">' . __('Add Attachment') . '</a>';
    $str_anything .= '<iframe name="attachIframe" id="attachIframe" class="borderAll" style="width: 100%; height: 70px;" src="' . MWB . 'bibliography/iframe_attach.php?biblioID=0&block=1"></iframe>';
    $setiadiForm->createAnything($str_anything);
    // Subject
    $str_anything  = '<span>Subyek</span>';
    $str_anything .= '<a style="margin-top: 10px;margin-bottom: 10px;" class="notAJAX button btn btn-info openPopUp" href="' . MWB . 'bibliography/pop_topic.php?biblioID=0" title="' . __('Subjects/Topics') . '">' . __('Add Subject(s)') . '</a>';
    $str_anything .= '<iframe name="topicIframe" id="topicIframe" class="borderAll" style="width: 100%; height: 70px;" src="' . MWB . 'bibliography/iframe_topic.php?biblioID=0&block=1"></iframe>';
    $setiadiForm->createAnything($str_anything);
    // Series Title
    $setiadiForm->createTextArea('Abstrak', '', 'notes');
    // Classification
    $setiadiForm->createText('Klasifikasi', '', 'class');
    // Collation
    $setiadiForm->createText('No.Panggil', '', 'callNumber');

     // Doi
     $setiadiForm->createText('URL Crossref', '', 'urlcrossref');
    
    //send email approved
    $array_edition = array('Send', 'Not Send');
    $setiadiForm->createRegSelect('Send Email Approved', '', 'sendMail', $array_edition);



    // Next Button
    $str_anything  = '<a href="#" des="gmd" style="margin-top: 20px;" class="nextBtn notAJAX btn btn-success">Previous</a>';
    $str_anything .= '<a href="#" des="approval" style="margin-left: 10px; margin-top: 20px;" class="nextBtn notAJAX btn btn-success">Next</a>';
    $setiadiForm->createAnything($str_anything);
    // Lash Biblio Sep
    $setiadiForm->setCloseSeparator();
    // Approval
    $setiadiForm->setSeparator('approval', '', 'style="display: none;"');
    // Publishing Place
    $setiadiForm->createButton('Simpan');
    // Lash Approval Sep
    $setiadiForm->setCloseSeparator();
  }
  // Output
  echo $setiadiForm->printOut();
  ?>
  <?php
  // javascript
  ?>
  <script type="text/javascript">
    $(document).ready(function() {
      // Add Select Event
      $('.addSelect').click(function() {
        var data = $(this).attr('data');
        $("#" + data + "2").slideDown();
        $("#" + data).hide();
        $("#" + data).removeAttr('name');
        $("#" + data + "2").attr('name', data);
        $("#" + data + "2").focus();
      });
      // Delete Button Event
      $('.delete').click(function() {
        // Ask
        var ask = confirm('Apakah anda yakin ingin menghapus cantuman ini?');
        // Checking
        if (ask == true) {
          var item_id = $('#updateRecordID').val();
          $.post("<?php echo $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']; ?>", {
            itemID: item_id,
            itemAction: true
          }, function(result) {
            $('.msg').html(result);
          });
        }
      });
      // Cancel Button Event
      $('.cancel').click(function() {
        <?php
        echo ($setiadiForm->inPop) ? "parent.jQuery.colorbox.close();" : "parent.$('#mainContent').simbioAJAX('" . MWB . "bibliography/index.php')";
        ?>
      });
      // Edit Button Event
      $('.edit').click(function() {
        $('.setiadi-form').find('input, textarea, select, iframe, .save, .delete, .addSelect, .openPopUp').removeAttr('disabled');
        $('#imageFilename a').removeAttr('style');
        $('.dateField a').attr('style', 'cursor: pointer;');
        $('.setiadi-form').find('.block').remove();
      });

      $('.nextBtn').on('click', function() {
        var des = $(this).attr('des');
        $('.' + des + 'l').trigger('click');
      });
      // Step Click Event
      $('.li-steps').click(function() {
        var data = $(this).attr('data');
        // set class
        if (data == 'bibliografi') {
          $(this).addClass('visited');
          $('.bibliografil').html('<a class="notAJAX" href="#">Bibliography</a>');
          $('.gmd').attr('style', 'display:none');
          $('.approval').attr('style', 'display:none');
          $('.bibliography').removeAttr('style');
        } else if (data == 'approval') {
          $(this).addClass('visited');
          $('.approvall').html('<a class="notAJAX" href="#">Approval</a>');
          $('.gmd').attr('style', 'display:none');
          $('.bibliography').attr('style', 'display:none');
          $('.approval').removeAttr('style');
        } else {
          $('.gmd').removeAttr('style');
          $('.bibliography').attr('style', 'display:none');
          $('.approval').attr('style', 'display:none');
        }
      });
    });
  </script>
<?php
  /* End Modification */
} else {
  require SIMBIO . 'simbio_UTILS/simbio_tokenizecql.inc.php';
  require MDLBS . 'bibliography/biblio_utils.inc.php';
  require LIB . 'biblio_list_model.inc.php';

  // number of records to show in list
  $biblio_result_num = ($sysconf['biblio_result_num'] > 100) ? 100 : $sysconf['biblio_result_num'];

  // create datagrid
  $datagrid = new simbio_datagrid();

  // index choice
  if ($sysconf['index']['type'] == 'index' ||  $sysconf['index']['type'] == 'sphinx') {
    if ($sysconf['index']['type'] == 'sphinx') {
      require LIB . 'sphinx/sphinxapi.php';
      require LIB . 'biblio_list_sphinx.inc.php';
    } else {
      require LIB . 'biblio_list_index.inc.php';
    }

    // table spec
    $table_spec = 'search_biblio AS `index` LEFT JOIN item ON `index`.biblio_id=item.biblio_id';

    if ($can_read and $can_write) {
      $datagrid->setSQLColumn(
        'index.biblio_id',
        'index.title AS \'' . __('Title') . '\'',
        'index.labels',
        'index.author',
        'index.isbn_issn AS \'' . __('ISBN/ISSN') . '\'',
        // 'IF(COUNT(item.item_id)>0, COUNT(item.item_id), \'<strong style="color: #f00;">'.__('None').'</strong>\') AS \''.__('Copies').'\'',
        'index.last_update AS \'' . __('Last Update') . '\''
      );
      $datagrid->modifyColumnContent(1, 'callback{showTitleAuthors}');
    } else {
      $datagrid->setSQLColumn(
        'index.title AS \'' . __('Title') . '\'',
        'index.author',
        'index.labels',
        'index.isbn_issn AS \'' . __('ISBN/ISSN') . '\'',
        // 'IF(COUNT(item.item_id)>0, COUNT(item.item_id), \'<strong style="color: #f00;">'.__('None').'</strong>\') AS \''.__('Copies').'\'',
        'index.last_update AS \'' . __('Last Update') . '\''
      );
      $datagrid->modifyColumnContent(1, 'callback{showTitleAuthors}');
    }
    $datagrid->invisible_fields = array(1, 2);
    $datagrid->setSQLorder('index.last_update DESC');

    // set group by
    $datagrid->sql_group_by = 'index.biblio_id';
  } else {
    require LIB . 'biblio_list.inc.php';

    // table spec
    $table_spec = 'biblio LEFT JOIN item ON biblio.biblio_id=item.biblio_id';

    if ($can_read and $can_write) {
      $datagrid->setSQLColumn(
        'biblio.biblio_id',
        'biblio.biblio_id AS bid',
        'biblio.title AS \'' . __('Title') . '\'',
        'biblio.isbn_issn AS \'Tanggal Pengesahan\'',
        // 'IF(COUNT(item.item_id)>0, COUNT(item.item_id), \'<strong style="color: #f00;">'.__('None').'</strong>\') AS \''.__('Copies').'\'',
        'biblio.last_update AS \'' . __('Last Update') . '\''
      );
      $datagrid->modifyColumnContent(2, 'callback{showTitleAuthors}');
    } else {
      $datagrid->setSQLColumn(
        'biblio.biblio_id AS bid',
        'biblio.title AS \'' . __('Title') . '\'',
        'biblio.isbn_issn AS \'' . __('ISBN/ISSN') . '\'',
        // 'IF(COUNT(item.item_id)>0, COUNT(item.item_id), \'<strong style="color: #f00;">'.__('None').'</strong>\') AS \''.__('Copies').'\'',
        'biblio.last_update AS \'' . __('Last Update') . '\''
      );
      // modify column value
      $datagrid->modifyColumnContent(1, 'callback{showTitleAuthors}');
    }
    $datagrid->invisible_fields = array(0);
    $datagrid->setSQLorder('biblio.last_update DESC');

    // set group by
    $datagrid->sql_group_by = 'biblio.biblio_id';
  }

  $stopwords = "@\sAnd\s|\sOr\s|\sNot\s|\sThe\s|\sDan\s|\sAtau\s|\sAn\s|\sA\s@i";

  // is there any search
  if (isset($_GET['keywords']) and $_GET['keywords']) {
    $keywords = $dbs->escape_string(trim($_GET['keywords']));
    $keywords = preg_replace($stopwords, ' ', $keywords);
    $searchable_fields = array('title', 'author', 'subject', 'isbn', 'publisher');
    if ($_GET['field'] != '0' and in_array($_GET['field'], $searchable_fields)) {
      $field = $_GET['field'];
      $search_str = $field . '=' . $keywords;
    } else {
      $search_str = '';
      foreach ($searchable_fields as $search_field) {
        $search_str .= $search_field . '=' . $keywords . ' OR ';
      }
      $search_str = substr_replace($search_str, '', -4);
    }

    $biblio_list = new biblio_list($dbs, $biblio_result_num);
    $criteria = $biblio_list->setSQLcriteria($search_str);
  }

  if (isset($criteria)) {
    $datagrid->setSQLcriteria('(' . $criteria['sql_criteria'] . ')');
  }

  // set table and table header attributes
  $datagrid->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
  $datagrid->table_header_attr = 'class="dataListHeader" style="font-weight: bold;"';
  // set delete proccess URL
  $datagrid->chbox_form_URL = $_SERVER['PHP_SELF'];
  $datagrid->debug = true;

  // put the result into variables
  $datagrid_result = $datagrid->createDataGrid($dbs, $table_spec, $biblio_result_num, ($can_read and $can_write));
  if (isset($_GET['keywords']) and $_GET['keywords']) {
    $msg = str_replace('{result->num_rows}', $datagrid->num_rows, __('Found <strong>{result->num_rows}</strong> from your keywords')); //mfc
    echo '<div class="infoBox">' . $msg . ' : "' . $_GET['keywords'] . '"<div>' . __('Query took') . ' <b>' . $datagrid->query_time . '</b> ' . __('second(s) to complete') . '</div></div>'; //mfc
  }

  echo $datagrid_result;
}
/* main content end */
?>