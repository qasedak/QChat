<h1><?=escape($title)?></h1>
<form action="<?=$action?>" method="POST">
    <input type="hidden" name="_actionpost" value="<?=$_actionpost_hash?>">
    <? foreach ($hiddens as $hidden): ?>
        <?=$hidden->render()?>
    <? endforeach; ?>
    <table class="contable" width="100%">
        <col width="300px">
        <col>
        <? $i=1; foreach ($inputs as $id => $input): ?>
        <tr <?=($i%2==0)?'':' class="dark"'?>>
            <td>
                <?=$input->title?>
            </td>
            <td>
                <?=$input->render()?>
            </td>
        </tr>
        <? $i++; endforeach; ?>
        <tr>
            <td colspan="2" align="center">
                <input type="submit" value="<?=$submit?>">
                <? if($reset) { ?><input type="reset" value="<?=tr('Reset')?>"><? } ?>
            </td>
        </tr>
    </table>
</form>
