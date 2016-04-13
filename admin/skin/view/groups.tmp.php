<div class="content">    
    <? if($title != '') echo "<h1>$title</h1>"; ?>
    <?=$content?>
    <br>
    <a href="<?=url(array('act' => 'reset'))?>" class="button r4 right"><?=tr('Reset all groups')?></a>
</div>
