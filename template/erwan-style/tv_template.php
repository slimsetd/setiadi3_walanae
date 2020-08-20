<?php
/**
 * Template for visitor counter
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

$main_template_path = $sysconf['template']['dir'].'/'.$sysconf['template']['theme'].'/tv_skin_template.inc.php';

?>
<body data-ma-theme="blue">
        <main class="main animated fadeInUp delay9">
            

            <header class="header">
                <div class="navigation-trigger hidden-xl-up" data-ma-action="aside-open" data-ma-target=".sidebar">
                    <div class="navigation-trigger__inner">
                        <i class="navigation-trigger__line"></i>
                        <i class="navigation-trigger__line"></i>
                        <i class="navigation-trigger__line"></i>
                    </div>
                </div>
                <div class="header__logo hidden-sm-down">
                     <a href="index.php"><img width="150px" src="<?php echo $sysconf['template']['dir'].'/'.$sysconf['template']['theme']; ?>/img/library.png" alt=""></a>
                </div>
                  <?php
                  $query_content = $dbs->query("SELECT content_title,content_desc,content_path,is_news FROM content WHERE content_path='newsticker'");
                  $_content = $query_content->fetch_row();
                  $contDesc = $_content[1];
                  if (strlen($contDesc) > 150){
                      $contDesc = substr($contDesc, 0, 150);
                      while (substr($contDesc, -1)!=' ') $contDesc = substr($contDesc, 0, -1);
                      $contDesc = trim($contDesc).' ... <a href="index.php?p='.$_content[2].'">Read more.</a>';
                      $tooltip_1 = ''.$_content[1].'';
                  }else{
                      $tooltip_1 = ''.$_content[1].'';
                  }
                  ?>
              <form class="search">
                    <div class="search__inner">
                      <marquee direction="scroll"><?php echo $tooltip_1; ?></marquee>
                    </div>
                </form>

                <div class="header__logo hidden-sm-down">
                    <h1><a href="#">
                    <script type="text/javascript">        
                                            function tampilkanwaktu(){         //fungsi ini akan dipanggil di bodyOnLoad dieksekusi tiap 1000ms = 1detik    
                                            var waktu = new Date();            //membuat object date berdasarkan waktu saat 
                                            var sh = waktu.getHours() + "";    //memunculkan nilai jam, //tambahan script + "" supaya variable sh bertipe string sehingga bisa dihitung panjangnya : sh.length    //ambil nilai menit
                                            var sm = waktu.getMinutes() + "";  //memunculkan nilai detik    
                                            var ss = waktu.getSeconds() + "";  //memunculkan jam:menit:detik dengan menambahkan angka 0 jika angkanya cuma satu digit (0-9)
                                            document.getElementById("clock").innerHTML = (sh.length==1?"0"+sh:sh) + ":" + (sm.length==1?"0"+sm:sm) + ":" + (ss.length==1?"0"+ss:ss);
                                            }
                                        </script>

                                        <body onload="tampilkanwaktu();setInterval('tampilkanwaktu()', 1000);">        
                                        <span id="clock"></span> 
                                      </br>
                                        <?php
                                        $hari = date('l');
                                        /*$new = date('l, F d, Y', strtotime($Today));*/
                                        if ($hari=="Sunday") {
                                         echo "Minggu";
                                        }elseif ($hari=="Monday") {
                                         echo "Senin";
                                        }elseif ($hari=="Tuesday") {
                                         echo "Selasa";
                                        }elseif ($hari=="Wednesday") {
                                         echo "Rabu";
                                        }elseif ($hari=="Thursday") {
                                         echo("Kamis");
                                        }elseif ($hari=="Friday") {
                                         echo "Jum'at";
                                        }elseif ($hari=="Saturday") {
                                         echo "Sabtu";
                                        }
                                        ?>,

                                        <?php
                                        $tgl =date('d');
                                        echo $tgl;
                                        $bulan =date('F');
                                        if ($bulan=="January") {
                                         echo " Januari ";
                                        }elseif ($bulan=="February") {
                                         echo " Februari ";
                                        }elseif ($bulan=="March") {
                                         echo " Maret ";
                                        }elseif ($bulan=="April") {
                                         echo " April ";
                                        }elseif ($bulan=="May") {
                                         echo " Mei ";
                                        }elseif ($bulan=="June") {
                                         echo " Juni ";
                                        }elseif ($bulan=="July") {
                                         echo " Juli ";
                                        }elseif ($bulan=="August") {
                                         echo " Agustus ";
                                        }elseif ($bulan=="September") {
                                         echo " September ";
                                        }elseif ($bulan=="October") {
                                         echo " Oktober ";
                                        }elseif ($bulan=="November") {
                                         echo " November ";
                                        }elseif ($bulan=="December") {
                                         echo " Desember ";
                                        }
                                        $tahun=date('Y');
                                        echo $tahun;
                                        ?>

                    </a></h1>
                </div>


            </header>

            

            <section class="content content--full">
                <header class="content__title">
                    <h1>Welcome To <?php echo $sysconf['library_name']; ?> | <?php echo $sysconf['library_subname']; ?></h1>

                    <div class="actions">
                        <a href="" class="actions__item zmdi zmdi-trending-up"></a>
                        <a href="" class="actions__item zmdi zmdi-check-all"></a>

                        <div class="dropdown actions__item">
                            <i data-toggle="dropdown" class="zmdi zmdi-more-vert"></i>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="" class="dropdown-item">Refresh</a>
                                <a href="" class="dropdown-item">Manage Widgets</a>
                                <a href="" class="dropdown-item">Settings</a>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="row quick-stats">
                    <div class="col-sm-6 col-md-3">
                        <div class="quick-stats__item bg-blue">
                            <div class="checkbox checkbox--char todo__item">
                                    <input type="checkbox" id="todo-1">
                                    <label for="todo-1" class="bg-red checkbox__char">A</label>
                                    <?php
                                    function count_author_sum() {
                                    global $dbs;
                                    $count_author_sum = $dbs->query('SELECT COUNT(author_id) AS author FROM mst_author
                                    ');
                                    $counter = $count_author_sum->fetch_assoc();
                                    $num_counter = $counter['author'];
                                    return $num_counter;
                                    } 
                                    ?>

                                    <div class="listview__content">
                                        <div class="listview__heading"><?php echo count_author_sum(); ?></div>
                                        <div class="listview__heading"><?php echo __('Author'); ?></div>
                                    </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <div class="quick-stats__item bg-amber">
                            <div class="checkbox checkbox--char todo__item">
                                    <input type="checkbox" id="todo-1">
                                    <label for="todo-1" class="bg-red checkbox__char">P</label>
                                    <?php
                                    function count_pub_sum() {
                                    global $dbs;
                                    $count_pub_sum = $dbs->query('SELECT COUNT(publisher_id) AS pub FROM mst_publisher
                                    ');
                                    $counter = $count_pub_sum->fetch_assoc();
                                    $num_counter = $counter['pub'];
                                    return $num_counter;
                                    } 
                                    ?>

                                    <div class="listview__content">
                                        <div class="listview__heading"><?php echo count_pub_sum(); ?></div>
                                        <div class="listview__heading"><?php echo __('Publisher'); ?></div>
                                    </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <div class="quick-stats__item bg-purple">
                            <div class="checkbox checkbox--char todo__item">
                                    <input type="checkbox" id="todo-1">
                                    <label for="todo-1" class="bg-red checkbox__char">G</label>
                                    <?php
                                    function count_gmdi_sum() {
                                    global $dbs;
                                    $count_gmdi_sum = $dbs->query('SELECT COUNT(gmd_id) AS gmdi FROM mst_gmd
                                    ');
                                    $counter = $count_gmdi_sum->fetch_assoc();
                                    $num_counter = $counter['gmdi'];
                                    return $num_counter;
                                    } 
                                    ?>

                                    <div class="listview__content">
                                        <div class="listview__heading"><?php echo count_gmdi_sum(); ?></div>
                                        <div class="listview__heading"><?php echo __('GMD'); ?></div>
                                    </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <div class="quick-stats__item bg-red">
                            <div class="checkbox checkbox--char todo__item">
                                    <input type="checkbox" id="todo-1">
                                    <label for="todo-1" class="bg-red checkbox__char">I</label>
                                    <?php
                                    function count_item_sum() {
                                    global $dbs;
                                    $count_item_sum = $dbs->query('SELECT COUNT(item_id) AS it FROM item
                                    ');
                                    $counter = $count_item_sum->fetch_assoc();
                                    $num_counter = $counter['it'];
                                    return $num_counter;
                                    } 
                                    ?>

                                    <div class="listview__content">
                                        <div class="listview__heading"><?php echo count_item_sum(); ?></div>
                                        <div class="listview__heading"><?php echo __('Items'); ?></div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <br>

                <div class="row stats">
                    <div class="col-sm-6 col-md-6">
                        <div class="card">
                                <div class="toolbar toolbar--inner">
                                    <div class="toolbar__label">New Books</div>
                                </div>

                                <div class="listview listview--bordered">

                                    <?php
                                       $current_book = "SELECT b.biblio_id, b.title, b.notes, b.image, b.gmd_id, b.publish_year, b.publisher_id, pa.publisher_name, 
                                                          b.publish_place_id, pl.place_name,pl.place_id,
                                                          ba.biblio_id, ba.author_id, g.gmd_id, g.gmd_name,
                                                          ma.author_id, ma.author_name FROM biblio AS b 
                                                          LEFT JOIN biblio_author AS ba ON ba.biblio_id = b.biblio_id 
                                                          LEFT JOIN mst_author AS ma ON ma.author_id = ba.author_id
                                                          LEFT JOIN mst_gmd AS g ON g.gmd_id = b.gmd_id
                                                          LEFT JOIN mst_publisher AS pa ON pa.publisher_id = b.publisher_id
                                                          LEFT JOIN mst_place AS pl ON pl.place_id = b.publish_place_id
                                                          ORDER BY b.biblio_id DESC LIMIT 0,3";
                                       $current_book_q = $dbs->query($current_book);
                                       while ($current_book_d = $current_book_q->fetch_assoc()) {
                                         if (is_null($current_book_d['image'])) {
                                            $img = 'default/image.png';
                                         } else {
                                            $img = 'docs/'.$current_book_d['image'];
                                         }

                                         if (is_null($current_book_d['notes'])) {
                                            $notes = '<i style="color:#f00;">No Description</i>';
                                         } else {
                                            $notes = substr($current_book_d['notes'],0, 80).'... (<a href="?p=show_detail&id='.$current_book_d['biblio_id'].'">Read More</a>)';
                                            $titlecut = substr($current_book_d['title'],0, 1).'';
                                         
                                         }

                                         $html_str  = '<div class="listview__item">
                                                        <div class="checkbox checkbox--char todo__item">
                                                            <input type="checkbox" id="custom-checkbox-1">
                                                            <label class="checkbox__char bg-blue" for="custom-checkbox-1"><img class="avatar-img" src="'.SWB.'images/'.$img.'" alt=""></label>
                                                            
                                                            <div class="listview__content">
                                                                <div class="listview__heading">'.$current_book_d['title'].'</div>
                                                                <p>'.$current_book_d['author_name'].'</p>
                                                                <p>'.$current_book_d['publisher_name'].': '.$current_book_d['place_name'].', '.$current_book_d['publish_year'].'</p>
                                                            </div>

                                                            <div class="listview__attrs">
                                                                <span>'.$current_book_d['gmd_name'].'</span>
                                                                <span><a href="?p=show_detail&id='.$current_book_d['biblio_id'].'">See Detail</a></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- end -->';

                                        // Set PrintOut
                                        echo $html_str;
                                       }        
                                    ?>
                                </div>
                            </div>
                    </div>

                    

                    <div class="col-sm-6 col-md-3">
                        <div class="stats__item bg-cyan">
                            <div class="card-body">
                              <?php
                              function count_member_sum() {
                              global $dbs;
                              $count_member_sum = $dbs->query('SELECT COUNT(member_id) AS member FROM member
                              ');
                              $counter = $count_member_sum->fetch_assoc();
                              $num_counter = $counter['member'];
                              return $num_counter;
                              } 
                              ?>

                            <h4 style="color:white" class="card-title"><?php echo __('5 most active members'); ?> of <?php echo count_member_sum(); ?> </h4>

                            <div class="row">
                              <?php
                                // 10 most active member
                                $report_q = $dbs->query('
                                SELECT m.member_name, m.inst_name,m.member_image,m.member_id, COUNT(loan_id) FROM loan AS l
                                  INNER JOIN member AS m ON m.member_id=l.member_id
                                  WHERE TO_DAYS(expire_date)>TO_DAYS(\''.date('Y-m-d').'\')
                                  GROUP BY l.member_id ORDER BY COUNT(loan_id) DESC LIMIT 5');
                                if ($report_q->num_rows > 0) {

                                 while ($member_name = $report_q->fetch_assoc()) {
                                    $aut = $member_name['member_name'];
                                    $r_aut = str_replace(',',' ',$aut);
                                    
                                    $value = 3;
                                    $last_name = implode(" ", array_slice(explode(" ", $r_aut),0,$member_name));
                                    
                                    if (is_null($member_name['member_image'])) {
                                            $img = 'persons/photo.png';
                                         } else {
                                            $img = 'persons/'.$member_name ['member_image'];
                                         }
                                  echo '';
                                  echo '<div class="listview__item">
                                                        <div class="checkbox checkbox--char todo__item">
                                                            <input type="checkbox" id="custom-checkbox-1">
                                                            <label class="checkbox__char bg-blue" for="custom-checkbox-1"><img class="avatar-img" src="'.SWB.'images/'.$img.'" class="avatar-img" alt=""></label>
                                                            
                                                            <div class="listview__content">
                                                                <div style="color:white" class="listview__heading">'.$last_name.'</div>
                                                                <p style="color:white" >'.$member_name ['inst_name'].'</p>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                    <!-- end -->';
                                  echo '';
                                  }
                                } else {
                                  echo '<p>No Librarian data yet</p>';
                                }
                            ?>
                            </div>
                        </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <div class="card card--inverse widget-profile">
                        <div class="card-body text-center">
                            <h4 class="card-title mb-0">Statistics</h4>
                            <?php
                                $page = (isset($_GET['page'])) ? $_GET['page'] : 1;
                                $startpoint = ($page - 1)*10;
                                // $startpoint = $page + 9;
                                // echo $page;
                                $cihuy = $dbs->query('SELECT b.biblio_id, b.title, ma.author_name 
                                FROM biblio AS b 
                                LEFT JOIN biblio_author AS ba ON b.biblio_id=ba.biblio_id
                                LEFT JOIN mst_author AS ma ON ba.author_id=ma.author_id ORDER by b.last_update DESC LIMIT '.$startpoint.',10');
                                $rowc = $dbs->query('SELECT COUNT(biblio_id) AS row FROM biblio');
                                $iuh = $rowc->fetch_row();
                                $aa = floor($iuh['0']/10)+1;
                                $rowcount = 0;
                                ?>
                                <!-- Query visitor -->
                                <?php
                                function count_visitor_sum() {
                                global $dbs;
                                $count_visitor_sum = $dbs->query('SELECT COUNT(visitor_id) AS visitor FROM visitor_count
                                ');
                                $counter = $count_visitor_sum->fetch_assoc();
                                $num_counter = $counter['visitor'];
                                return $num_counter;
                                } 
                                ?>
                                <!-- Query GMD -->
                                <?php
                                function count_gmd_sum() {
                                global $dbs;
                                $count_gmd_sum = $dbs->query('SELECT COUNT(gmd_id) AS gmd FROM mst_gmd
                                ');
                                $counter = $count_gmd_sum->fetch_assoc();
                                $num_counter = $counter['gmd'];
                                return $num_counter;
                                } 
                                ?>
                                <!-- Query Collection Type -->
                                <?php
                                function count_col_sum() {
                                global $dbs;
                                $count_col_sum = $dbs->query('SELECT COUNT(coll_type_id) AS col FROM mst_coll_type
                                ');
                                $counter = $count_col_sum->fetch_assoc();
                                $num_counter = $counter['col'];
                                return $num_counter;
                                } 
                                ?>
                                <!-- Query Member -->
                                <?php
                                function count_member_sumarry() {
                                global $dbs;
                                $count_member_sumarry = $dbs->query('SELECT COUNT(member_id) AS member FROM member
                                ');
                                $counter = $count_member_sumarry->fetch_assoc();
                                $num_counter = $counter['member'];
                                return $num_counter;
                                } 
                                ?>
                                <!-- Query Transaction -->
                                <?php
                                function count_trans_sum() {
                                global $dbs;
                                $count_trans_sum = $dbs->query('SELECT COUNT(loan_id) AS trans FROM loan
                                ');
                                $counter = $count_trans_sum->fetch_assoc();
                                $num_counter = $counter['trans'];
                                return $num_counter;
                                } 
                                ?>
                                <!-- Query Librarian-->
                                <?php
                                function count_librarian_sum() {
                                global $dbs;
                                $count_librarian_sum = $dbs->query('SELECT COUNT(user_id) AS librarian FROM user
                                ');
                                $counter = $count_librarian_sum->fetch_assoc();
                                $num_counter = $counter['librarian'];
                                return $num_counter;
                                } 
                                ?>
                                <?php
                                function count_checkout_sum() {
                                global $dbs;
                                $count_checkout_sum = $dbs->query('SELECT COUNT(item_id) FROM item AS i LEFT JOIN loan AS l ON i.item_code=l.item_code
                                WHERE is_lent=1 AND is_return=0');
                                $counter = $count_checkout_sum->fetch_assoc();
                                $num_counter = $counter['loan'];
                                return $num_counter;
                                } 
                                ?>
                        </div>

                        <div class="widget-profile__list">
                            <div class="media">
                                <div class="avatar-char"><i class="zmdi zmdi-account zmdi-hc-fw"></i></div>
                                <div class="media-body">
                                    <strong><?php echo count_member_sumarry(); ?></strong>
                                    <small><?php echo __('Members'); ?></small>
                                </div>
                            </div>

                            <div class="media">
                                <div class="avatar-char"><i class="zmdi zmdi-book zmdi-hc-fw"></i></div>
                                <div class="media-body">
                                    <strong><?php echo $iuh['0'];?></strong>
                                    <small><?php echo __('Collection'); ?></small>
                                </div>
                            </div>

                            <div class="media">
                                <div class="avatar-char"><i class="zmdi zmdi-sign-in zmdi-hc-fw"></i></div>
                                <div class="media-body">
                                    <strong><?php echo count_visitor_sum(); ?></strong>
                                    <small><?php echo __('Visitor'); ?></small>
                                </div>
                            </div>

                            <div class="media">
                                <div class="avatar-char"><i class="zmdi zmdi-shopping-basket zmdi-hc-fw"></i></div>
                                <div class="media-body">
                                    <strong><?php echo count_trans_sum(); ?></strong>
                                    <small><?php echo __('Loan Data Summary'); ?></small>
                                </div>
                            </div>

                            <div class="media">
                                <div class="avatar-char"><i class="zmdi zmdi-bookmark zmdi-hc-fw"></i></div>
                                <div class="media-body">
                                    <strong><?php echo count_col_sum(); ?></strong>
                                    <small><?php echo __('Collection Type'); ?></small>
                                </div>
                            </div>
                        </div>
                    </div>


                    </div>
                </div>

                <hr>
                <br>
                <br>

                
            </section>
        </main>

        
    </body>

<script>
  $('#login-page, .s-login').attr('style','margin:0;')
</script>
