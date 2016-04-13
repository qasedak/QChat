<? foreach($options as $o): ?>
<label>
    <input type="radio" name="<?=$id?>" value="<?=$o['value']?>" <?=$o['value']==$value?'checked':''?>>
    <?=$o['title']?>
</label>
<? endforeach; ?>
