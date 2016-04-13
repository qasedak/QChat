<? foreach($skins as $key => $skin): ?>
<label>
    <div class="cell"><input type="radio" name="<?=$id?>" value="<?=$key?>" <?=$key==$value?'checked':''?>></div>
    <div class="cell <?=$key==$value?'select_skin':''?>">
        <h2><?=$skin->title?></h2>
        <?=tr('Author')?>: <?=$skin->author_name?><br>
        <?=tr('Email')?>: <?=$skin->author_email?><br>
        <?=tr('Site')?>: <a href="<?=$skin->author_site?>" target="_black"><?=$skin->author_site?></a>
    </div>
</label>
<? endforeach; ?>

