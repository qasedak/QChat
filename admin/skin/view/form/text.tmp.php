<? if($error != ''): ?>
<div class="warning">
<? endif; ?>
    <input type="text" name="<?=$id?>" value="<?=escape($value)?>">
<? if($error != ''): ?>
<br>
<?=escape($error)?>
</div>
<? endif; ?>
