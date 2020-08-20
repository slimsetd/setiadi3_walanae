<?php
/**
 * Template for OPAC
 *
 * Copyright (C) 2015 Arie Nugraha (dicarve@gmail.com)
 * Create by Eddy Subratha (eddy.subratha@slims.web.id)
 * Modify by Erwan Setyo Budi (erwans818@gmail.com)
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

// be sure that this file not accessed directly

if (!defined('INDEX_AUTH')) {
  die("can not access this file directly");
} elseif (INDEX_AUTH != 1) {
  die("can not access this file directly");
}

?>
<!--
==========================================================================
   ___  __    ____  __  __  ___      __    _  _    __    ___  ____    __
  / __)(  )  (_  _)(  \/  )/ __)    /__\  ( )/ )  /__\  / __)(_  _)  /__\
  \__ \ )(__  _)(_  )    ( \__ \   /(__)\  )  (  /(__)\ \__ \ _)(_  /(__)\
  (___/(____)(____)(_/\/\_)(___/  (__)(__)(_)\_)(__)(__)(___/(____)(__)(__)

==========================================================================
-->
<!DOCTYPE html>
<html lang="<?php echo substr($sysconf['default_lang'], 0, 2); ?>" xmlns="http://www.w3.org/1999/xhtml" prefix="og: http://ogp.me/ns#">
    <head>
        <?php
        // Meta Template
        include "partials/meta.php";
        ?>
    </head>

    <body itemscope="itemscope" itemtype="http://schema.org/WebPage" data-ma-theme="indigo">
        <!--[if lt IE 9]>
        <div class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</div>
        <![endif]-->
        
        <?php
        // Meta Template
        include "partials/meta.php";
        ?>

        </head>

        <body itemscope="itemscope" itemtype="http://schema.org/WebPage">

        <!--[if lt IE 9]>
        <div class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</div>
        <![endif]-->

        <?php
            // Header Template
            include "partials/header.php";

            // Left Side Template
            include "partials/left-side.php";

        ?>


        <?php
        // Content
        ?>
        <?php if(isset($_GET['search']) || isset($_GET['p'])): ?>
        
        <section  id="content" class="content" role="main">

        <!-- Search on Front Page
        ============================================= -->
        <div class="s-main-search">
          <?php
          if(isset($_GET['p'])) {
            switch ($_GET['p']) {
            case ''             : $page_title = __('Collections'); break;
            case 'show_detail'  : $page_title = __("Record Detail"); break;
            case 'member'       : $page_title = __("Member Area"); break;
            case 'member'       : $page_title = __("Member Area"); break;
            default             : $page_title; break; }
          } else {
            $page_title = __('Collections');
          }
          ?>
          
          <form action="index.php" method="get" autocomplete="off">
          </form>
        </div>

        <!-- Main
        ============================================= -->
        <div class="row todo">
            <div class="col-md-8">
                <div class="card">
                    <div class="listview listview--bordered">
                    <div class="toolbar toolbar--inner">
                          <h3><?php echo $page_title ?></h3>
                    </div>

              <?php
                // Generate Output
                // catch empty list
                if(strlen($main_content) == 7) {
                  echo '<h2>' . __('No Result') . '</h2><hr/><p>' . __('Please try again') . '</p>';
                } else {
                  echo $main_content;
                }

                // Somehow we need to hack the layout
                if(isset($_GET['search']) || (isset($_GET['p']) && $_GET['p'] != 'member')){
                  echo '</div>';
                } else {
                  if(isset($_SESSION['mid'])) {
                    echo  '</div></div>';
                  }
                }

              ?>
              </div>
        </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                      <?php if(isset($_GET['search'])) : ?>
                      <h4><?php echo __('Search Result'); ?></h4>
                      <hr>
                      <?php echo $search_result_info; ?>
                      <?php endif; ?>

                      <br>

                      <!-- If Member Logged
                      ============================================= -->
                      <h2><?php echo __('Information'); ?></h2>
                      <hr/>
                      <p><?php echo (utility::isMemberLogin()) ? $header_info : $info; ?></p>
                      <br/>
            
                    <!-- Show if clustering search is enabled
                    ============================================= -->
                    <?php
                      if(isset($_GET['keywords']) && (!empty($_GET['keywords']))) :
                        if (($sysconf['enable_search_clustering'])) : ?>
                        <h2><?php echo __('Search Cluster'); ?></h2>

                        <hr/>

                        <div id="search-cluster">
                          <div class="cluster-loading"><?php echo __('Generating search cluster...');  ?></div>
                        </div>

                        <script type="text/javascript">
                          $('document').ready( function() {
                            $.ajax({
                              url     : 'index.php?p=clustering&q=<?php echo urlencode($criteria); ?>',
                              type    : 'GET',
                              success : function(data, status, jqXHR) { $('#search-cluster').html(data); }
                            });
                          });
                        </script>
                    </div>
                </div>
            </div>

                  <?php endif; ?>
                <?php endif; ?>
            </div>
          </div>
        </div>

      </section>
      <?php
        // Footer Template
        include "partials/footer.php";
      ?>

        <?php else: ?>
        <!-- Homepage
        ============================================= -->
        <main class="main">
            <div class="page-loader">
                <div class="page-loader__spinner">
                    <svg viewBox="25 25 50 50">
                        <circle cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
                    </svg>
                </div>
            </div>

            <section class="content">
                <div class="content__inner">
                    
                    <?php

                    // New Books Template
                    include "partials/new-books.php";
                    
                    
                    // Right Side Template
                    include "partials/right-side.php";
                    ?>
                    
                </div>

                    <?php
                    // Footer Template
                    include "partials/footer.php";
                    ?>
            </section>
        </main>
        <?php endif; ?>

        <?php
        // JS
        include "partials/js.php";
        
        ?>
        <script>
          <?php if(isset($_GET['search']) && (isset($_GET['keywords'])) && ($_GET['keywords'] != ''))   : ?>
          $('.biblioRecord .detail-list, .biblioRecord .title, .biblioRecord .abstract, .biblioRecord .controls').highlight(<?php echo $searched_words_js_array; ?>);
          <?php endif; ?>

          //Replace blank cover
          $('.book img').error(function(){
            var title = $(this).parent().attr('title').split(' ');
            $(this).parent().append('<div class="s-feature-title">' + title[0] + '<br/>' + title[1] + '<br/>... </div>');
            $(this).attr({
              src   : './template/default/img/book.png',
              title : title + title[0] + ' ' + title[1]
            });
          });

          //Replace blank photo
          $('.librarian-image img').error(function(){
            $(this).attr('src','./template/default/img/avatar.jpg');
          });

        </script>
        
    </body>
</html>