<?php 

$count = 0;
$positions = $this->get_sources($ttt_querypositions_slug);
$saved = $this->get($ttt_querypositions_slug);

$gui = $this->get($ttt_querypositions_slug.'_gui');
$posts_per_page = $gui['posts_per_page'];
?>


<form method="post" name="tttqueryposition" action="">

    <input type="hidden" name="slug" value="<?php echo $ttt_querypositions_slug; ?>">

    Posts per page:
    <input type="text" name="posts_per_page" value="<?php echo $gui['posts_per_page']; ?>">
    <br>

    <div class="sources">
        <?php foreach($positions as $_name => $params): ?>
            <div class="source" data-num="<?php echo $count; ?>">
                <?php echo $_name; ?>
            </div>
            <?php $count++; ?>
        <?php endforeach; ?>
    </div>

    <div style="clear:both;"></div>
    <br>

    <?php 

    ?>

    <div class="positions">
    <?php for($i=1; $i <= $posts_per_page; $i++): ?>
        <?php $num = $gui['position'.$i]; ?>
        <div class="position" data-num="<?php echo $num; ?>">
            <input type="hidden" name="position<?php echo $i; ?>" value="<?php echo $num; ?>">
            <em class="num"><?php echo $i; ?></em>
        </div>
    <?php endfor; ?>
    </div>

    <div style="clear:both;"></div>
    <br>
    
    <input type="submit" class="button button-secondary button-large save" value="Save">
    <input type="submit" class="button button-primary button-large preview" value="Preview">

</form>
