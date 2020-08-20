<?php
// biblio/record detail
// output the buffer
ob_start(); /* <- DONT REMOVE THIS COMMAND */

// include printed settings configuration file
require SB.'admin'.DS.'admin_template'.DS.'printed_settings.inc.php';
// load print settings from database to override value from printed_settings file
loadPrintSettings($dbs, 'qrcode');
// Gen JS
function genJS($data_id, $fid, $bid, $server_addr) {
   global $sysconf;
   $genJS = '<script type="text/javascript">var qrcode = new QRCode(document.getElementById("qrcode'.$data_id.'"), {width : '.$sysconf['print']['qrcode']['qrcode_width'].',height : '.$sysconf['print']['qrcode']['qrcode_height'].'});function makeCode() {qrcode.makeCode("'.$server_addr.'?p=fstream&fid='.$fid.'&bid='.$bid.'");} makeCode();</script>';
   return $genJS;
}
?>
<script type="text/javascript" src="<?php echo JWB;?>qrcodejs/qrcode.js"></script>
<div class="s-detail animated delay9 fadeInUp" itemscope itemtype="http://schema.org/Book" vocab="http://schema.org/" typeof="Book">
  
  <!-- Book Cover
  ============================================= -->
  <div class="cover">
    {image}
  </div>
  
  <!-- Title 
  ============================================= -->
  <h3 class="s-detail-type">{gmd_name}</h3>
  <h4 class="s-detail-title" itemprop="name" property="name">{title}</h4>
  {social_shares}
  <a target="_blank" href="index.php?p=show_detail&inXML=true&id=<?php echo $_GET['id'];?>" class="btn btn-mini btn-danger">XML</a>
          <?php
                $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                
                echo '<a href="https://www.mendeley.com/import/?url='.urlencode($actual_link).'" target="_blank"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/81/Mendeley_Logo_Vertical.png/600px-Mendeley_Logo_Vertical.png" width="30px" height="30px" alt="" /></a>
                ';
                ?>
  <br>
  <div class="s-detail-author" itemprop="author" property="author" itemscope itemtype="http://schema.org/Person">
  {authors}
  <br>
  </div>

  <!-- Abstract 
  ============================================= -->
  <hr>
  
    <div class="s-detail-abstract" itemprop="description" property="description">
      <p>{notes}</p>
      <hr/>
    </div>


  <!-- Item Details
  ============================================= -->  
  <h3><i class="fa fa-circle-o"></i> <?php echo __('Detail Information'); ?></h3>
  <div class="row">
  <div class="col-lg-12">  
  <table class="s-table">
    <tbody>      
      <!-- ============================================= -->  
      <tr>
        <th><?php print __('Item Type'); ?></th>
        <td>
          <div itemprop="alternativeHeadline" property="alternativeHeadline">{item_type_name}</div>
        </td>
      </tr>
     
      <tr>
        <th><?php print __('Penulis'); ?></th>
        <td>
          <div>{authors}</div>
        </td>
      </tr>
    

       <!-- ============================================= -->  
      <tr>
        <th><?php print __('Student ID'); ?></th>
        <td>
          <div itemprop="numberOfPages" property="numberOfPages">{student_id}</div>
        </td>
      </tr>
      <!-- ============================================= -->  
      <tr>
        <th><?php print __('Dosen Pembimbing'); ?></th>
        <td>
          <div>{supervisors}</div>
        </td>
      </tr>

      <tr>
        <th><?php print __('Penguji'); ?></th>
        <td>
          <div>{examiners}</div>
        </td>
      </tr>
      
      
       <tr>
        <th><?php print __('Kode Prodi PDDIKTI'); ?></th>
        <td>
          <div>{code_ministry}</div>
        </td>
      </tr>
      
       <!-- ============================================= -->  
       <tr>
        <th><?php print __('Edition'); ?></th>
        <td>
          <div>{edition}</div>
        </td>
      </tr>


      <tr>
        <th><?php print __('Departement'); ?></th>
        <td>
          <div>{departement}</div>
        </td>
      </tr>

      <tr>
        <th><?php print __('Contributor'); ?></th>
        <td>
          <div>{contributors}</div>
        </td>
      </tr>

      <tr>
        <th><?php echo __('Language'); ?></th>
        <td>
          <div><meta itemprop="inLanguage" property="inLanguage" content="<?php echo $language_name ?>" />{language_name}</div>
        </td>
      </tr>
      <!-- ============================================= -->  
      <!--<tr>
        <th><?php echo __('Tanggal Publikasi'); ?></th>
        <td>
          <div itemprop="isbn" property="isbn">{isbn_issn}</div>
        </td>
      </tr> -->
      

      <!-- ============================================= -->  
   
      <!-- ============================================= -->  
      <tr>
        <th><?php echo __('Publisher'); ?></th>
        <td>
          <span itemprop="publisher" property="publisher" itemtype="http://schema.org/Organization" itemscope>{publisher_name}</span> :
          <span itemprop="publisher" property="publisher">{publish_place}</span>.,
          <span itemprop="datePublished" property="datePublished">{publish_year}</span>
        </td>
      </tr>
      <!-- ============================================= -->  
      <tr>
        <th><?php echo __('Edition'); ?></th>
        <td>
          <div itemprop="bookEdition" property="bookEdition">{edition}</div>
        </td>
      </tr>
      <!-- ============================================= -->  
      <tr>
        <th><?php echo __('Subject(s)'); ?></th>
        <td>
          <div class="s-subject" itemprop="keywords" property="keywords">{subjects}</div>
        </td>
      </tr>
      <!-- ============================================= -->  
       <tr>  
        <th><?php echo __('No Panggil'); ?></th>
        <td>
          <div>{call_number}</div>
        </td>
      </tr>
      <!-- ============================================= -->  
      
      <tr>
        <th><?php print __('Copyright'); ?></th>
        <td>
          <div>{copyright_name}</div>
        </td>
      </tr>
      
      <tr>
        <th><?php print __('Doi'); ?></th>
        <td>
          <div>{url_crossref}</div>
        </td>
      </tr>
	 

    </tbody>
  </table>
  </div>
  </div>


  <!-- Attachment
  ============================================= -->  
  <h3><i class="fa fa-arrow-circle-o-down"></i> <?php echo __('File Attachment'); ?></h3>
  <div itemprop="associatedMedia">
    <div class="s-download">
      <div style="padding:30px;">
        {file_att}
      </div>
    </div> 
  </div>

<div class="span1" style="
    width: 100%;"> 
<!-- <div class="tagline"> Citation </div> -->
  <div class="form-horizontal">
  <div class="control-group">
        <!--<label class="control-label key"><?php print __('APA Style'); ?></label>
        <div class="controls">{author_name}. ({publish_year}).<i>{title}</i>({edition}).{publish_place}:{publisher_name}</div></br>-->
    
    <!--<label class="control-label key"><?php print __('Chicago Style'); ?></label>
        <div class="controls">{authors_single}.<i>{title}</i>({edition}).{publish_place}:{publisher_name},{publish_year}.{gmd_name}</div></br>-->
    
   <!-- <label class="control-label key"><?php print __('MLA Style'); ?></label>
        <div class="controls">{authors_single}.<i>{title}</i>({edition}).{publish_place}:{publisher_name},{publish_year}.{gmd_name}</div></br> -->
    
    <!--<label class="control-label key"><?php print __('Turabian Style'); ?></label>
        <div class="controls">{authors_single}.<i>{title}</i>({edition}).{publish_place}:{publisher_name},{publish_year}.{gmd_name}</div></br> -->
    </div> 
  </div> 
  <br>
  <div class="clearfix"></div>
  <?php echo showComment($detail_id); ?>
</div></div>
<?php
// put the buffer to template var
$detail_template = ob_get_clean();


