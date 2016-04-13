<? $view->layout('moderator/ban'); ?>
<h1><?=tr('Bans')?></h1>
<table class="contable" width="100%">
    <? $i=0; foreach ($bans as $ban): $i++; ?>
    <tr <?=($i%2==0)?'class="dark"':''?> >
        <td>
            <div class="cell" style="text-align: right; padding-right: 10px;">
                <div><?=tr('Name')?>:</div>
                <div><?=tr('Ban')?>:</div>
                <div><?=tr('Ban for time')?>:</div>                
                <div><?=tr('Ban ends')?>:</div>
                <div><?=tr('Reason')?>:</div>
            </div>
            <div class="cell">
                <div><?=$ban->name?></div>
                <div><?=($ban->ban_id ? tr('ID').':'.$ban->user_id : '')?> <?=($ban->ban_ip ? tr('IP').':'.$ban->ip : '')?></div>
                <div><?=$ban->time_title()?></div>
                <div><?=$ban->ban_ends()?></div>
                <div><?=$ban->reason?></div>
            </div>
        </td>
        <td align="center">
            <a href="moderator.php?act=delete&index=<?=$ban->index?>"><?=tr('Delete')?></a>
        </td>        
    </tr>
    <? endforeach; ?>
    
</table>
