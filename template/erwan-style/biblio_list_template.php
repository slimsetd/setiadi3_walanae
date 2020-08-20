<?php
/**
 * Template for Biblio List
 * name of memberID text field must be: memberID
 * name of institution text field must be: institution
 *
 * Copyright (C) 2015 Arie Nugraha (dicarve@gmail.com)
 * Create by Eddy Subratha (eddy.subratha@slims.web.id)
 * 
 * Slims 8 (Akasia)
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
 */

$label_cache = array();
/**
 *
 * Format bibliographic item list for OPAC display
 *
 * @param   object      $dbs
 * @param   array       $biblio_detail
 * @param   int		$n
 * @param   array       $settings
 * @param   array       $return_back
 *
 * return   string
 *
 */
function biblio_list_format($dbs, $biblio_detail, $n, $settings = array(), &$return_back = array()) {
  global $label_cache, $sysconf;
  // init output var
  $output     = '';

  $title      = $biblio_detail['title'];
  $biblio_id  = $biblio_detail['biblio_id'];
  $detail_url = SWB.'index.php?p=show_detail&id='.$biblio_id.'&keywords='.$settings['keywords'];
  $cite_url   = SWB.'index.php?p=cite&id='.$biblio_id.'&keywords='.$settings['keywords'];  
  $title_link = '<a href="'.$detail_url.'" class="listview__heading" itemprop="name" property="name" title="'.__('View record detail description for this title').'">'.$title.'</a>';

  // label
  if ($settings['show_labels'] AND !empty($biblio_detail['labels'])) {
    $labels = @unserialize($biblio_detail['labels']);
    if ($labels !== false) {
      foreach ($labels as $label) {
        if (!isset($label_cache[$label[0]]['name'])) {
          $label_q = $dbs->query('SELECT label_name, 
            label_desc, label_image FROM mst_label AS lb
            WHERE lb.label_name=\''.$dbs->escape_string($label[0]).'\'');
          $label_d = $label_q->fetch_row();
          $label_cache[$label[0]] = array( 'name'  => $label_d[0], 
            'desc'  => $label_d[1], 
            'image' => $label_d[2]
          );
        }

        if (isset($label[1]) && $label[1]) {
          $title_link .= ' <a itemprop="name" property="name" href="'.$label[1].'" target="_blank"><img src="'.SWB.'lib/minigalnano/createthumb.php?filename=../../'.IMG.'/labels/'.urlencode($label_cache[$label[0]]['image']).'&amp;width=48&amp;height=48" title="'.$label_cache[$label[0]]['desc'].'" alt="'.$label_cache[$label[0]]['desc'].'" align="middle" class="labels" border="0" /></a>';
        } else {
          $title_link .= ' <img src="'.SWB.'lib/minigalnano/createthumb.php?filename=../../'.IMG.'/labels/'.urlencode($label_cache[$label[0]]['image']).'&amp;width=48&amp;height=48" title="'.$label_cache[$label[0]]['desc'].'" alt="'.$label_cache[$label[0]]['desc'].'" align="middle" class="labels" />';
        }
      }
    }
  }
  
  // button
  $xml_button = '';
  $detail_button = '<span><a href="'.$detail_url.'"  title="'.__('View record detail description for this title').'">'.__('Record Detail').'</a></span>';
  if ($settings['xml_detail']) {
    $xml_button = '<span><a href="'.$detail_url.'&inXML=true" title="'.__('View record detail description in XML Format').'" target="_blank">'.__('XML Detail').'</a></span>';
  }
  
  // citation button
  $cite_button = '<span><a href="'.$cite_url.'" class="openPopUp citationLink" title="'.str_replace('{title}', substr($title, 0, 50) , __('Citation for: {title}')).'" target="_blank">'.__('Cite').'</a></span>';
  
  // cover images var
  $image_cover = '';
  if (!empty($biblio_detail['image']) && !defined('LIGHTWEIGHT_MODE')) {
    $biblio_detail['image'] = urlencode($biblio_detail['image']);
    $images_loc = '../../images/docs/'.$biblio_detail['image'];
    if ($sysconf['tg']['type'] == 'minigalnano') {
      $thumb_url = './lib/minigalnano/createthumb.php?filename='.urlencode($images_loc).'&width=120';
      $image_cover = '<img width="40px" height="40px" src="'.$thumb_url.'" class="avatar-img" itemprop="image" alt="'.$title.'" />';
    }
  }
  
  // $alt_list = ($n%2 == 0)?'alterList':'alterList2';
  $output .= '<div class="listview__item" itemscope itemtype="http://schema.org/Book" vocab="http://schema.org/" typeof="Book">
              <div class="checkbox checkbox--char todo__item">
                  <input type="checkbox" id="custom-checkbox-1">
                  <label class="checkbox__char bg-blue" for="custom-checkbox-1">
                    '.$image_cover.'
                    </label>
              </div>
              ';
  $output .= '<div class="listview__content">'.$title_link.'';
  // concat author data
  $_authors = isset($biblio_detail['author'])?$biblio_detail['author']:biblio_list_model::getAuthors($dbs, $biblio_id, true);
  $output .= '<div class="author" itemprop="author" property="author" itemscope itemtype="http://schema.org/Person">';
  if ($_authors) {
    $_authors_string = '';
    if (is_array($_authors)) {
      foreach ($_authors as $author) {
        $_authors_string .= '<p itemprop="name" property="name">'.$author.'</p> - ';  
      }
    } else {
      $_authors_string .= '<p class="author-name" itemprop="name" property="name">'.$_authors.'</p> - ';
    }
    $_authors_string = substr_replace($_authors_string, '', -2);
    $output .= $_authors_string;
    // $output .= '<div class="author" itemprop="author"><b>'.__('Author(s)').'</b> : '.$_authors.'</div>';
  }
  $output .= '</div>';
  
  // checking custom frontpage file
  if ($settings['enable_custom_frontpage'] AND $settings['custom_fields']) {
    foreach ($settings['custom_fields'] as $field => $field_opts) {
      if ($field_opts[0] == 1) {
        if ($field == 'edition') {
          $output .= '<div class="customField editionField" itemprop="bookEdition" property="bookEdition"><b>'.$field_opts[1].'</b> : '.$biblio_detail['edition'].'</div>';
        } else if ($field == 'isbn_issn') {
          $output .= '<div class="customField isbnField" itemprop="isbn" property="isbn"><b>'.$field_opts[1].'</b> : '.$biblio_detail['isbn_issn'].'</div>';
        } else if ($field == 'collation') {
          $output .= '<div class="customField collationField" itemprop="numberOfPages" property="numberOfPages"><b>'.$field_opts[1].'</b> : '.$biblio_detail['collation'].'</div>';
        } else if ($field == 'series_title') {
          $output .= '<div class="customField seriesTitleField" itemprop="alternativeHeadline" property="alternativeHeadline"><b>'.$field_opts[1].'</b> : '.$biblio_detail['series_title'].'</div>';
        } else if ($field == 'call_number') {
          $output .= '<div class="customField callNumberField"><b>'.$field_opts[1].'</b> : '.$biblio_detail['call_number'].'</div>';
        } else if ($field == 'availability' && !$settings['disable_item_data']) {
          // get total number of this biblio items/copies
          $_item_q = $dbs->query('SELECT COUNT(*) FROM item WHERE biblio_id='.$biblio_id);
          $_item_c = $_item_q->fetch_row();
          // get total number of currently borrowed copies
          $_borrowed_q = $dbs->query('SELECT COUNT(*) FROM loan AS l INNER JOIN item AS i'
            .' ON l.item_code=i.item_code WHERE l.is_lent=1 AND l.is_return=0 AND i.biblio_id='.$biblio_id);
          $_borrowed_c = $_borrowed_q->fetch_row();
          // total available
          $_total_avail = $_item_c[0]-$_borrowed_c[0];
          if ($_total_avail < 1) {
            $output .= '<div class="customField availabilityField"><b>'.$field_opts[1].'</b> : <strong style="color: #f00;">'.__('none copy available').'</strong></div>';
          } else {
            $item_availability_message = str_replace('{numberAvailable}' , $_total_avail, __('{numberAvailable} copies available for loan'));
            $output .= '<div class="customField availabilityField"><b>'.$field_opts[1].'</b> : '.$item_availability_message.'</div>';
          }
        } else if ($field == 'node_id' && $settings['disable_item_data']) {
  	    $output .= '<div class="customField locationField"><b>'.$field_opts[1].'</b> : '.$sysconf['node'][$biblio_detail['node_id']]['name'].'</div>';
  	}
      }
    }
  }

  // checkbox for marking collection
  $_i= rand(); // Add By Eddy Subratha
  $_check_mark = (utility::isMemberLogin() && $settings['enable_mark'])?' <input type="checkbox" id="biblioCheck'.$_i.'" name="biblio[]" class="biblioCheck" value="'.$biblio_id.'" /> <label for="biblioCheck'.$_i.'">'.__('mark this').'</label>':'';
  $output .= '<div class="listview__attrs">'.$detail_button.$xml_button.$cite_button.$_check_mark.'</div>';
  
  // social buttons
  if ($sysconf['social_shares']) {
    // share buttons
    $detail_url_encoded = urlencode('http://'.$_SERVER['SERVER_NAME'].$detail_url);
    $_share_btns = "\n".'</br><div class="btn-demo">'.
    ''.__('Share to').': '.
    '<button class="btn btn-light btn--icon"><a href="http://www.facebook.com/sharer.php?u='.$detail_url_encoded.'" title="Facebook" target="_blank"><i class="zmdi zmdi-facebook zmdi-hc-fw"></i></a></button>'.
    '<button class="btn btn-light btn--icon"><a href="http://twitter.com/share?url='.$detail_url_encoded.'&text='.urlencode($title).'" title="Twitter" target="_blank"><i class="zmdi zmdi-twitter zmdi-hc-fw"></i></a></button>'.
    '<button class="btn btn-light btn--icon"><a href="https://plus.google.com/share?url='.$detail_url_encoded.'" title="Google Plus" target="_blank"><i class="zmdi zmdi-google-plus-box zmdi-hc-fw"></i></a></button>'.
    '<button class="btn btn-light btn--icon"><a href="http://www.digg.com/submit?url='.$detail_url_encoded.'" title="Digg It" target="_blank"><i class="zmdi zmdi-twitter zmdi-hc-fw"></i></a></button>'.
    '<button class="btn btn-light btn--icon"><a href="http://www.linkedin.com/shareArticle?mini=true&url='.$detail_url_encoded.'" title="LinkedIn" target="_blank"><i class="zmdi zmdi-linkedin zmdi-hc-fw"></i></a></button>'.
    '</div>'."\n";
    
    $output .= $_share_btns;
  }
  
  $output .= "</div></div>\n";
  return $output;
}