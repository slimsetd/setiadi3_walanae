<?php
/**
 *
 * Copyright (C) 2007,2008  Arie Nugraha (dicarve@yahoo.com)
 * Modified for Excel output (C) 2010 by Wardiyono (wynerst@gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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

/* Report By Titles */

// key to authenticate
define('INDEX_AUTH', '1');

// main system configuration
require '../../../../sysconfig.inc.php';
// IP based access limitation
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-reporting');
// start the session
require SB.'admin/default/session.inc.php';
require SB.'admin/default/session_check.inc.php';
// privileges checking
$can_read = utility::havePrivilege('reporting', 'r');
$can_write = utility::havePrivilege('reporting', 'w');

if (!$can_read) {
    die('<div class="errorBox">'.__('You don\'t have enough privileges to access this area!').'</div>');
}

require SIMBIO.'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO.'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO.'simbio_GUI/form_maker/simbio_form_element.inc.php';
require SIMBIO.'simbio_DB/datagrid/simbio_dbgrid.inc.php';
require MDLBS.'reporting/report_dbgrid.inc.php';

$page_title = 'Titles Report';
$reportView = false;
$num_recs_show = 20;
if (isset($_GET['reportView'])) {
    $reportView = true;
}

if (!$reportView) {
?>
    <!-- filter -->
    <fieldset>
    <div class="per_title">
    	<h2><?php echo __('Reporting ETD'); ?></h2>
	  </div>
    <div class="infoBox">
    <?php echo __('Report Filter'); ?>
    </div>
    <div class="sub_section">
    <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" target="reportView">
    <div id="filterForm">
        <div class="divRow">
            <div class="divRowLabel"><?php echo __('Title'); ?></div>
            <div class="divRowContent">
            <?php echo simbio_form_element::textField('text', 'title', '', 'style="width: 50%"'); ?>
            </div>
        </div>
        <div class="divRow">
            <div class="divRowLabel"><?php echo __('Author'); ?></div>
            <div class="divRowContent">
            <?php echo simbio_form_element::textField('text', 'author', '', 'style="width: 50%"'); ?>
            </div>
        </div>
        <div class="divRow">
            <div class="divRowLabel"><?php echo __('Classification'); ?></div>
            <div class="divRowContent">
            <?php echo simbio_form_element::textField('text', 'class', '', 'style="width: 50%"'); ?>
            </div>
        </div>
        <div class="divRow">
            <div class="divRowLabel"><?php echo __('Item Type'); ?></div>
            <div class="divRowContent">
            <?php
            $itemtype_q = $dbs->query('SELECT item_type_id, item_type_name FROM mst_item_type');
            $itemtype_options[] = array('0', __('ALL'));
            while ($itemtype_d = $itemtype_q->fetch_row()) {
                $itemtype_options[] = array($itemtype_d[0], $itemtype_d[1]);
            }
            echo simbio_form_element::selectList('itemtype[]', $itemtype_options, '','multiple="multiple" size="5"');
            ?> <?php echo __('Press Ctrl and click to select multiple entries'); ?>
            </div>
        </div>
        <div class="divRow">
         

            <div class="divRowLabel"><?php echo __('Contributor Name'); ?></div>
            <div class="divRowContent">
            <?php echo simbio_form_element::textField('text', 'contributorName', '', 'style="width: 50%"'); ?>
            </div>

               <div class="divRowLabel"><?php echo __('Contributor Type'); ?></div>
            <div class="divRowContent">
            <?php
            $contributor_options[] = array('0', __('ALL'));
            foreach ($sysconf['authority_level_contributor'] as $level_id => $level) {
                //echo '<option value="'.$level_id.'">'.$level_id.'</option>';
                $contributor_options[] = array($level_id, $level);
            }
            echo simbio_form_element::selectList('contributor[]', $contributor_options, '','multiple="multiple" size="5"');
            ?> <?php echo __('Press Ctrl and click to select multiple entries'); ?>
            </div>
        </div>
        <div class="divRow">
            <div class="divRowLabel"><?php echo __('Subject'); ?></div>
            <div class="divRowContent">
            <?php echo simbio_form_element::textField('text', 'subject', '', 'style="width: 50%"'); ?>
            </div>
        </div>
        <div class="divRow">
            <div class="divRowLabel"><?php echo __('Record each page'); ?></div>
            <div class="divRowContent"><input type="text" name="recsEachPage" size="3" maxlength="3" value="<?php echo $num_recs_show; ?>" /> <?php echo __('Set between 20 and 200'); ?></div>
        </>
    </div>
    <div style="padding-top: 10px; clear: both;">
    <input type="button" name="moreFilter" value="<?php echo __('Show More Filter Options'); ?>" />
    <input type="submit" name="applyFilter" value="<?php echo __('Apply Filter'); ?>" />
    <input type="hidden" name="reportView" value="true" />
    </div>
    </form>
	</div>
    </fieldset>
    <!-- filter end -->
    <div class="dataListHeader" style="padding: 3px;"><span id="pagingBox"></span></div>
    <iframe name="reportView" id="reportView" src="<?php echo $_SERVER['PHP_SELF'].'?reportView=true'; ?>" frameborder="0" style="width: 100%; height: 500px;"></iframe>
<?php
} else {
    ob_start();
    // create datagrid
    $reportgrid = new report_datagrid();
    $reportgrid->setSQLColumn('b.biblio_id', 'b.title AS \''.__('Title').'\'',
		'mit.item_type_name AS \''.__('item type').'\'',
		'mt.topic AS \''.__('Subject').'\'',
        'mc.contributor_name AS \'Contributor\'',
        'b.publish_year AS \''.__('Tahun').'\'');
    $reportgrid->setSQLorder('b.title ASC');
    $reportgrid->invisible_fields = array(0);

    // is there any search
    $criteria = 'bsub.biblio_id IS NOT NULL ';
    $outer_criteria = 'b.biblio_id > 0 ';
    if (isset($_GET['title']) AND !empty($_GET['title'])) {
        $keyword = $dbs->escape_string(trim($_GET['title']));
        $words = explode(' ', $keyword);
        if (count($words) > 1) {
            $concat_sql = ' AND (';
            foreach ($words as $word) {
                $concat_sql .= " (bsub.title LIKE '%$word%' OR bsub.isbn_issn LIKE '%$word%') AND";
            }
            // remove the last AND
            $concat_sql = substr_replace($concat_sql, '', -3);
            $concat_sql .= ') ';
            $criteria .= $concat_sql;
        } else {
            $criteria .= ' AND (bsub.title LIKE \'%'.$keyword.'%\' OR bsub.isbn_issn LIKE \'%'.$keyword.'%\')';
        }
    }
    // AUTHOR
    if (isset($_GET['author']) AND !empty($_GET['author'])) {
        $author = $dbs->escape_string($_GET['author']);
        $criteria .= ' AND ma.author_name LIKE \'%'.$author.'%\'';
    }
    // END AUTHOR
  // START CONTRIBUTOR
    if (isset($_GET['contributor']) AND !empty($_GET['contributor'])) {
        $contributor_IDs = '';
        foreach ($_GET['contributor'] as $id) {
            $id = (integer)$id;
            if ($id) {
                $contributor_IDs .= "$id,";
            }
        }
        $contributor_IDs = substr_replace($contributor_IDs, '', -1);
        if ($contributor_IDs) {
            $outer_criteria .= " AND bt.level IN($contributor_IDs)";
        }
    }
    // CLOSE CONTRIBUTOR TYPE
     // START ITEM TYPE
    if (isset($_GET['itemtype']) AND !empty($_GET['itemtype'])) {
        $itemtype_IDs = '';
        foreach ($_GET['itemtype'] as $id) {
            $id = (integer)$id;
            if ($id) {
                $itemtype_IDs .= "$id,";
            }
        }
        $itemtype_IDs = substr_replace($itemtype_IDs, '', -1);
        if ($itemtype_IDs) {
            $outer_criteria .= " AND b.item_type_id IN($itemtype_IDs)";
        }
    }
    // END ITEM TYPE
    // START SUBJECT
    if (isset($_GET['subject']) AND !empty($_GET['subject'])) {
        $subject = $dbs->escape_string($_GET['subject']);
        $criteria .= ' AND mt.topic LIKE \'%'.$subject.'%\'';
    }
    // CLOSE SUBJECT
    // START CONTRIBUTOR NAME
    if (isset($_GET['contributorName']) AND !empty($_GET['contributorName'])) {
        $contributorName = $dbs->escape_string($_GET['contributorName']);
        $criteria .= ' AND mc.contributor_name LIKE \'%'.$contributorName.'%\'';
    }
    // CLOSE CONTRIBUTOR NAME
    

    
    
    if (isset($_GET['recsEachPage'])) {
        $recsEachPage = (integer)$_GET['recsEachPage'];
        $num_recs_show = ($recsEachPage >= 20 && $recsEachPage <= 200)?$recsEachPage:$num_recs_show;
    }

    // subquery/view string
    $subquery_str = '(SELECT DISTINCT bsub.biblio_id,    
        bsub.title, 
        bsub.isbn_issn, 
        bsub.call_number, 
        bsub.classification, 
        bsub.language_id,
        bsub.publish_place_id, 
        bsub.publisher_id,
        bsub.publish_year,
        bsub.item_type_id,
        mc.contributor_name
        FROM biblio AS bsub
        LEFT JOIN biblio_author AS ba ON bsub.biblio_id = ba.biblio_id
        LEFT JOIN mst_item_type AS mit ON bsub.item_type_id = mit.item_type_id
        LEFT JOIN mst_author AS ma ON ba.author_id = ma.author_id
        LEFT JOIN biblio_topic AS bt ON bsub.biblio_id = bt.biblio_id
        LEFT JOIN mst_topic AS mt ON bt.topic_id = mt.topic_id 
        LEFT JOIN biblio_contributor AS bc ON bsub.biblio_id = bc.biblio_id
        LEFT JOIN mst_contributor AS mc ON bc.contributor_id = mc.contributor_id
        WHERE '.$criteria.')';

    // table spec
    $table_spec = $subquery_str.' AS b
        LEFT JOIN item AS i ON b.biblio_id=i.biblio_id
        LEFT JOIN mst_item_type AS mit ON b.item_type_id = mit.item_type_id
		LEFT JOIN mst_place AS pl ON b.publish_place_id=pl.place_id
        LEFT JOIN mst_publisher AS pb ON b.publisher_id=pb.publisher_id
        LEFT JOIN biblio_topic AS bt ON b.biblio_id = bt.biblio_id
        LEFT JOIN mst_topic AS mt ON bt.topic_id = mt.topic_id
        LEFT JOIN biblio_contributor AS bc ON b.biblio_id = bc.biblio_id
        LEFT JOIN mst_contributor AS mc ON bc.contributor_id = mc.contributor_id';
    //echo $subquery_str;
    // set group by
    $reportgrid->sql_group_by = 'b.biblio_id';
    $reportgrid->setSQLCriteria($outer_criteria);

    // callback function to show title and authors
    function showTitleAuthors($obj_db, $array_data)
    {
        // author name query
        $_biblio_q = $obj_db->query('SELECT b.title, a.author_name FROM biblio AS b
            LEFT JOIN biblio_author AS ba ON b.biblio_id=ba.biblio_id
            LEFT JOIN mst_author AS a ON ba.author_id=a.author_id
            WHERE b.biblio_id='.$array_data[0]);
        $_authors = '';
        while ($_biblio_d = $_biblio_q->fetch_row()) {
            $_title = $_biblio_d[0];
            $_authors .= $_biblio_d[1].' - ';
        }
        $_authors = substr_replace($_authors, '', -3);
        $_output = $_title.'<br /><i>'.$_authors.'</i>'."\n";
        return $_output;
    }
    function showTitleSubjects($obj_db, $array_data)
    {
        // subject name query
        $_biblio_q = $obj_db->query('SELECT b.title, a.topic FROM biblio AS b
            LEFT JOIN biblio_topic AS bt ON b.biblio_id = bt.biblio_id
            LEFT JOIN mst_topic AS a ON bt.topic_id = a.topic_id
            WHERE b.biblio_id='.$array_data[0]);
        $_subjects = '';
        while ($_biblio_d = $_biblio_q->fetch_row()) {
            $_title = $_biblio_d[0];
            $_subjects .= $_biblio_d[1].' - ';
        }
        $_subjects = substr_replace($_subjects, '', -3);
        $_output = $_title.'<br /><i>'.$_subjects.'</i>'."\n";
        return $_output;
    }
    // modify column value
    $reportgrid->modifyColumnContent(1, 'callback{showTitleAuthors}');
    $reportgrid->modifyColumnContent(1, 'callback{showTitleSubjects}');

    // put the result into variables
    echo $reportgrid->createDataGrid($dbs, $table_spec, $num_recs_show);

    echo '<script type="text/javascript">'."\n";
    echo 'parent.$(\'#pagingBox\').html(\''.str_replace(array("\n", "\r", "\t"), '', $reportgrid->paging_set).'\');'."\n";
    echo '</script>';

	$xlsquery = 'SELECT b.biblio_id, b.title AS \''.__('Title').'\', mit.item_type_name \''.__('Item Type').'\''.
		', mt.topic AS \''.__('Subject').'\''.
		', mc.contributor_name AS \''.__('Contributor').'\''.
		', b.publish_year AS \''.__('tahun').'\', b.call_number AS \''.__('Call Number').'\' FROM '.
		$table_spec . ' WHERE '. $outer_criteria . ' group by b.biblio_id';
	//	echo $xlsquery;
		unset($_SESSION['xlsdata']); 
		$_SESSION['xlsquery'] = $xlsquery;
		$_SESSION['tblout'] = "title_list";

	echo '<p><a href="../xlsoutput.php" class="button">'.__('Export to spreadsheet format').'</a></p>';

    $content = ob_get_clean();
    // include the page template
    require SB.'/admin/'.$sysconf['admin_template']['dir'].'/printed_page_tpl.php';
}
