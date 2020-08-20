<?php

// tooltip image slideshow
//query content 1
$query_content = $dbs->query("SELECT content_title,content_desc,content_path,is_news FROM content WHERE is_news IS NOT NULL ORDER BY content.input_date DESC LIMIT 0,1");
$_content = $query_content->fetch_row();
$contDesc = $_content[1];
if (strlen($contDesc) > 150){
    $contDesc = substr($contDesc, 0, 150);
    while (substr($contDesc, -1)!=' ') $contDesc = substr($contDesc, 0, -1);
    $contDesc = trim($contDesc).' ... <a href="index.php?p='.$_content[2].'">Read more.</a>';
    $tooltip_1 = '<h3><a href="index.php?p='.$_content[2].'">'.$_content[0].'</a></h3><p>'.$contDesc.'</p>';
}else{
    $tooltip_1 = '<h3>'.$_content[0].'</h3><p>'.$_content[1].'</p>';
}

//query content 2
$query_content = $dbs->query("SELECT content_title,content_desc,content_path FROM content WHERE is_news IS NOT NULL ORDER BY content.input_date DESC LIMIT 1,1");
$_content = $query_content->fetch_row();
$contDesc = $_content[1];
if (strlen($contDesc) > 150){
    $contDesc = substr($contDesc, 0, 150);
    while (substr($contDesc, -1)!=' ') $contDesc = substr($contDesc, 0, -1);
    $contDesc = trim($contDesc).' ... <a href="index.php?p='.$_content[2].'">Read more.</a>';
    $tooltip_2 = '<h3><a href="index.php?p='.$_content[2].'">'.$_content[0].'</a></h3><p>'.$contDesc.'</p>';
}else{
    $tooltip_2 = '<h3>'.$_content[0].'</h3><p>'.$_content[1].'</p>';
}

//query content 3
$query_content = $dbs->query("SELECT content_title,content_desc,content_path FROM content WHERE is_news IS NOT NULL ORDER BY content.input_date DESC LIMIT 2,1");
$_content = $query_content->fetch_row();
$contDesc = $_content[1];
if (strlen($contDesc) > 150){
    $contDesc = substr($contDesc, 0, 150);
    while (substr($contDesc, -1)!=' ') $contDesc = substr($contDesc, 0, -1);
    $contDesc = trim($contDesc).' ... <a href="index.php?p='.$_content[2].'">Read more.</a>';
    $tooltip_3 = '<h3><a href="index.php?p='.$_content[2].'">'.$_content[0].'</a></h3><p>'.$contDesc.'</p>';
}else{
    $tooltip_3 = '<h3>'.$_content[0].'</h3><p>'.$_content[1].'</p>';
}

?>
                    <div class="card">
                        <div class="card-body">

                            <div id="carouselExampleCaption" class="carousel slide" data-ride="carousel">
                                <ol class="carousel-indicators">
                                    <li data-target="#carouselExampleCaption" data-slide-to="0" class="active"></li>
                                    <li data-target="#carouselExampleCaption" data-slide-to="1"></li>
                                    <li data-target="#carouselExampleCaption" data-slide-to="2"></li>
                                </ol>

                                <div class="carousel-inner" role="listbox">
                                    <div class="carousel-item active">
                                        <img src="<?php echo $sysconf['template']['dir'].'/'.$sysconf['template']['theme']; ?>/img/carousel/c-1.jpg" alt="First slide">
                                        <div class="carousel-caption">
                                            <?php echo $tooltip_1; ?>
                                        </div>
                                    </div>
                                    <div class="carousel-item">
                                        <img src="<?php echo $sysconf['template']['dir'].'/'.$sysconf['template']['theme']; ?>/img/carousel/c-2.jpg" alt="Second slide">
                                        <div class="carousel-caption">
                                            <?php echo $tooltip_2; ?>
                                            </div>
                                    </div>
                                    <div class="carousel-item">
                                        <img src="<?php echo $sysconf['template']['dir'].'/'.$sysconf['template']['theme']; ?>/img/carousel/c-3.jpg" alt="Third slide">
                                        <div class="carousel-caption">
                                            <?php echo $tooltip_3; ?>
                                            </div>
                                    </div>
                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleCaption" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleCaption" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                </a>
                            </div>
                        </div>
                    </div>