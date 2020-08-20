<?php
function news_list_tpl($title, $path, $date, $summary) {
?>
<div class="card">
    <div class="card-body">
        <h4 class="card-title"><?php echo $title ?></h4>
        <h6 class="card-subtitle"><?php echo $date ?></h6>
        <p class="card-text"><?php echo $summary ?></p>
        <a href="<?php echo SWB.'index.php?p='.$path ?>" class="card-link"><?php echo __('Read More') ?></a>
    </div>
</div>

<?php
}