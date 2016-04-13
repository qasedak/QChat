<? $view->layout('wrap'); ?>
<? if(isset($ok) && $ok): ?>
    <h1 class="info"><?=tr('Everything is ready to install.')?></h1>
<? else: ?>
    <h1><?=tr('Before continuing the installation:')?></h1>
    <?=tr('The following directories must exist and be writable:')?>
    <ul>
    <? foreach($dirs as $dir): ?>
        <li><b><?=$dir?></b></li>
    <? endforeach; ?>
    </ul>
    <?=format(tr('To do this, set the permission %% of these directories.'), '0777')?>
<? endif; ?>
<div class="center">
    <form action="index.php" method="get">
        <input type="hidden" name="act" value="config">
        <input type="submit" value="<?= tr('Install') ?>">
    </form>
</div>
