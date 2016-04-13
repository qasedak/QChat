<? $view->layout('wrap'); ?>
<? $view->layout()->title = tr('Avatar') . ' - ' . Elf::Settings('title'); ?>
<? if(isset($changed)){ $view->layout()->begin('javascript'); ?>
    <script type="text/javascript">
        parent.Chat.send({
            type: 'avatar'
        });
    </script>
<? $view->layout()->end(); } ?>

<? if(isset($info)): ?>
<div class="info">
    <?=$info?>
</div>
<? endif; ?>
<div class="float">
    <h1><?=tr('Your avatar')?></h1>
    <p>
        <? if($user->avatar != ''): ?>
        <a href="avatar.php?select=user_avatar" class="avatar selected"><img src="<?=$user->avatar?>" width="<?=$x?>" height="<?=$y?>" alt=""></a>
        <? else: ?>
        <?=tr('No avatar.')?>
        <? endif; ?>
    </p>
</div>
<div class="float">
    <h1><?=tr('Upload avatar')?></h1>
    <form method="post" action="avatar.php?act=upload" enctype="multipart/form-data">
        <input type="file" name="file">
        <input type="submit" value="<?=tr('Upload')?>">
    </form>
</div>

<h1><?=tr('Avatars gallery')?></h1>
<p>
<? foreach($avatars as  $n => $src): ?>
<a href="avatar.php?select=<?=$n?>" class="avatar"><img src="<?=$src?>" width="<?=$x?>" height="<?=$y?>" alt=""></a>
<? endforeach; ?>
</p>
