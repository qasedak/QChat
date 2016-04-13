<select name="<?=$id?>">
<?
foreach($options as $opt)
    echo "<option ".($value==$opt['value']?'selected':'')." value='".$opt['value']."'>".escape ($opt['title'])."</option>";
?>
</select>
