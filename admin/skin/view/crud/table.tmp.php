<? if($message != ''): ?>
<div id="message" class="message">
<?=escape($message)?>
</div>
<? endif; ?>

<? if($editable): ?>
<div class="buttonbox" style="float: right;">
<a href="<?=url($url_params + array($action_trigger=>'create'))?>" class="button r4"><img src="skin/img/add.png" alt=""> <?=tr('Add new')?></a>
</div>
<? endif; ?>

<? if($pageable): ?>
<div class="buttonbox" style="text-align: left; padding: 8px;">
<?
echo $pagenavigation = call('crud/pagenavigation', array('page' => $page,
                                                          'perpage' => $perpage,
                                                          'shift' => $shift,
                                                          'count' => $count,
                                                          'url' => $url_params
                                                         ));
?>
</div>
<? endif; ?>

<table id="<?=$tableid?>" class="contable" width="100%">

    <? foreach($columns as $col): ?>
        <col<? if($col['width'] != "") { ?> width="<?=$col['width']?>"<? } ?>>
    <? endforeach; ?>
    <? if($editable): ?><col width="70px"><? endif; ?>



    <tr class="top">
        <? foreach ($columns as $col): ?>
        <td><?=$col['title']?></td>
        <? endforeach; ?>
        <? if($editable): ?><td> </td><? endif; ?>
    </tr>



    <? $i=1; foreach ($rows as $row): ?>
    <tr <?=($i%2==0)?'':'class="dark"'?>  id="<?=$row->getPrimaryValue()?>">

        <? foreach ($columns as $name => $col): ?>            
            <?
                echo call($col['view'], array( 'item' => $row->$name ) );
            ?>
        <? endforeach; ?>

        <? if($editable): ?>
        <td align="center">
            <a href="<?=url($url_params + array($action_trigger => 'update', 'id' => $row->getPrimaryValue(), 'page' => $page))?>" class="button r4"><img src="skin/img/pencil.png" alt=""></a><a onclick="$('#<?=$row->getPrimaryValue()?>').crud_delete('<?=url(array('type' => 'ajax', $action_trigger => 'delete', 'id' => $row->getPrimaryValue()))?>'); return false;" href="<?=url(array($action_trigger => 'delete', 'id' => $row->getPrimaryValue()))?>" class="button r4"><img src="skin/img/cross.png" alt=""></a>
        </td>
        <? endif; ?>
        
    </tr>
    <? $i++; endforeach; ?>
    
</table>

<? if($pageable): ?>
<div class="buttonbox" style="text-align: left; padding: 8px;">
<?=$pagenavigation?>
</div>
<? endif; ?>
