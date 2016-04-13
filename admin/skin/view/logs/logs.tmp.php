<table class="subnav" width="100%">
<tr>
<td width="220px" class="menu r4">
    <a href="<?=url(array('type' => 'moderator'))?>" class="r4 <?=($type == 'moderator') ? 'menuselect' : ''?>"><?=tr('Moderator\'s Log')?></a>
    <a href="<?=url(array('type' => 'admin'))?>" class="r4 <?=($type == 'admin') ? 'menuselect' : ''?>"><?=tr('Admin\'s Log')?></a>
</td>

<td class="content">
<?=$content?>
</td>
</tr>
</table>
