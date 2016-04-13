<table class="subnav" width="100%">
    <tr>
        <td width="220px" class="menu r4">
            <?
            $items = array(
                'main' => tr('Main settings'),
                'transmitter' => tr('Transmitter'),
                'archive' => tr('Archive'),
                'mail' => tr('E-mail'),
                'skin' => tr('Skin'),
                'groups' => tr('Groups'),
                'filters' => tr('Filters'),
                'date' => tr('Date & Time'),
                'reset' => tr('Reset all')
            );
            foreach($items as $k => $v):
            ?>
                <a href="<?= url('act', $k) ?>" class="r4 <?= ($act == $k) ? 'menuselect' : '' ?>"><?= $v ?></a>
            <? endforeach; ?>
        </td>

        <td class="content">
            <? if($message != ''): ?>
                <div id="message" class="message">
                    <?= escape($message) ?>
                </div>
            <? endif; ?>
            <?= $content ?>
        </td>
    </tr>
</table>
