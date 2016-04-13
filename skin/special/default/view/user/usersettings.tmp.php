<? $view->layout('wrap'); ?>
<? $view->layout()->title = tr('User settings') . ' - ' . Elf::Settings('title'); ?>
<? if(isset($settings)){ $view->layout()->begin('javascript'); ?>
    <script type="text/javascript">
        var settings = <?=$settings?>;
        parent.Settings = settings;
    </script>     
<? $view->layout()->end(); } ?>

<? if(isset($settings)): ?>
    <div class="info">
        <?= tr('Settings have been saved.') ?>
    </div>
<? endif; ?>
<?= $content ?>
