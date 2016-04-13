<? $view->layout('wrap'); ?>
<? $view->layout()->title = tr('Ban') . ' - ' . Elf::Settings('title') ?>

<? if(isset($ban)){ $view->layout()->begin('javascript'); ?>
    <script type="text/javascript">
        parent.Chat.send({
            type: 'ban',
            user_id: <?= escape($ban->user_id)?>,
            reason: "<?= escape($ban->reason) ?>",
            for_time: "<?= escape($ban->time_title())?>"
        });
    </script>
<? $view->layout()->end(); } ?>

<? if(isset($info)): ?>
<div class="info">
<?=escape($info)?>
</div>
<? endif; ?>
<?=$content?>
