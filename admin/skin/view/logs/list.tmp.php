<table class="contable" width="100%">
    <col width="120px">
    <col width="80px">
    <tr class="top">
        <td><?=tr('Time')?></td>
        <td><?=tr('Name')?></td>
        <td><?=tr('Action')?></td>
    </tr>
    <? $i = 1; foreach ($logs as $log): ?>
    <tr <?= ($i % 2 == 0) ? '' : 'class="dark"' ?> >
        <td><?=date('j.m.Y G:i', $log->time)?></td>
        <td><?=escape($log->name)?></td>
        <td><?=escape($log->doing)?></td>
    </tr>
    <? $i++; endforeach; ?>

</table>
