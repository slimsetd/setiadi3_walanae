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

$main_template_path = $sysconf['template']['dir'].'/'.$sysconf['template']['theme'].'/login_template.inc.php';

?>

<div class="loginForm container">
                      <div class="row team">
                        <div class="col-md-6 col-lg-6">
                            <header>
                              <h1><?php echo __('Visitor Counter'); ?></h1>
                              <span><h3><?php echo $sysconf['library_name']; ?> | <?php echo $sysconf['library_subname']; ?></h3></span>
                            </header>
                            <div class="card card--inverse widget-signups">
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

                            <h4 class="card-title"><?php echo __('10 most active members'); ?> of <?php echo count_member_sum(); ?> </h4>
                            <h6 class="card-subtitle">Let's not lose to them!, keep visiting the library, there will be interesting prizes for visitors</h6>

                            <div class="widget-signups__list">
                              <?php
                                // 10 most active member
                                $report_q = $dbs->query('
                                SELECT m.member_name, m.member_image,m.member_id, COUNT(visitor_id) FROM visitor_count AS l
                                  INNER JOIN member AS m ON m.member_id=l.member_id
                                  WHERE TO_DAYS(expire_date)>TO_DAYS(\''.date('Y-m-d').'\')
                                  GROUP BY l.member_id ORDER BY COUNT(visitor_id) DESC LIMIT 10');
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
                                  echo '<a data-toggle="tooltip" title="'.$last_name.'" href=""><img src="'.SWB.'images/'.$img.'" class="avatar-img"  alt=""></a>';
                                  echo '';
                                  }
                                } else {
                                  echo '<p>No Librarian data yet</p>';
                                }
                            ?>
                            </div>
                        </div>
                    </div>
                            <div class="float-center">
                            <div class="q-a__stat hidden-sm-down">
                                    <?php
                                      function count_visitor() {
                                      global $dbs;
                                      $count_visitor = $dbs->query('SELECT COUNT(visitor_id) AS visitor FROM visitor_count
                                      WHERE checkin_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY)
                                                                    ');
                                      $counter = $count_visitor->fetch_assoc();
                                      $num_counter = $counter['visitor'];
                                      return $num_counter;
                                    } 
                                    ?>
                                    <?php
                                      function count_visitor_month() {
                                      global $dbs;
                                      $count_visitor_month = $dbs->query('SELECT COUNT(visitor_id) AS visitor FROM visitor_count
                                      WHERE checkin_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 31 DAY)
                                                                    ');
                                      $counter = $count_visitor_month->fetch_assoc();
                                      $num_counter = $counter['visitor'];
                                      return $num_counter;
                                    } 
                                    ?>
                                    <?php
                                      function count_visitor_year() {
                                      global $dbs;
                                      $count_visitor_year = $dbs->query('SELECT COUNT(visitor_id) AS visitor FROM visitor_count
                                      WHERE checkin_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 365 DAY)
                                                                    ');
                                      $counter = $count_visitor_year->fetch_assoc();
                                      $num_counter = $counter['visitor'];
                                      return $num_counter;
                                    } 
                                    ?>
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
                                    <!-- <span>
                                        <strong><?php echo count_visitor_month(); ?></strong>
                                        <small>Month</small>
                                    </span>

                                    <span>
                                        <strong><?php echo count_visitor_year(); ?></strong>
                                        <small>Year</small>
                                    </span> -->

                                    <span class="hidden-md-down">
                                        <strong><?php echo count_visitor_sum(); ?></strong>
                                        <small>Total Visitor</small>
                                    </span>
                                    <span class="hidden-md-down">
                                        <strong><?php echo count_member_sum(); ?></strong>
                                        <small>Total Member</small>
                                    </span>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="card-green team__item">
                                <img  id="visitorCounterPhoto" src="./images/persons/photo.png" class="team__img" alt="">

                                <div class="card-body">
                                    <h4 style="color:#FFF;"class="card-title">Selamat Datang Di Perpustakaan</h4>
                                    <h4 id="counterInfo" class="card-subtitle"><?php echo __('Ketik NIM untuk Anggota. Jika Tamu, Ketik nama dan Institusi'); ?></h4>
                                      </br>
                                      <form action="index.php?p=visitor" name="visitorCounterForm" id="visitorCounterForm" method="post">

                                      <div class="row"> 

                                        <div class="col-lg-6 col-sm-6 col-xs-12">
                                          <div class="form-group"> 
                                            <input type="text" name="memberID" id="memberID"  class="form-control input-lg" />
                                            <label for="memberID"><?php echo __('NIM / Nama (Jika Bukan Anggota)'); ?></label>
                                          </div> 
                                        </div>

                                        <div class="col-lg-6 col-sm-6 col-xs-12">
                                          <div class="form-group"> 
                                            <input type="text" name="institution" id="institution" class="form-control input-lg" />
                                            <label for="institution"><?php echo __('Institution'); ?></label>
                                          </div> 
                                        </div>
                                      
                                        <div class="clearfix"></div>

                                        <div class="col-lg-12 col-sm-12 col-xs-12">
                                          <button type="submit" id="counter" name="counter" class="btn btn-info btn--icon"><i class="zmdi zmdi-check"></i></button>
                                        </div>
                                      </div>

                                    </form>

                                    <div class="team__social">
                                        <h2>
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
                                      </h2>

                                    </div>
                                </div>
                            </div>
                        </div>
                      </div>

</div>
<script>
  $('#login-page, .s-login').attr('style','margin:0;')
  $('.s-login-content').removeClass('animated flipInY').addClass('animated fadeInUp')
</script>
