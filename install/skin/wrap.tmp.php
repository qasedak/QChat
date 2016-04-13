<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?= $title ?></title>
        <?= css('main') ?>
    </head>
    <body>
        <div class="box">
            <table class="bar">
                <tr>
                    <?
                    $items = array(
                        'index' => tr('Welcome#Install'),
                        'config' => tr('Configuration#Install'),
                        'db' => tr('Database#Install'),
                        'settings' => tr('Settings#Install'),
                        'goodbye' => tr('Goodbye#Install')
                        );
                    $class_td = array();
                    $class_sep = array();
                    $over = true;
                    foreach($items as $go => $title)
                    {
                        if($over)
                        {
                            $class_td[$go] = 'over';
                            $class_sep[$go] = ($act == $go) ? 'corner' : 'sep_over';
                        }
                        else
                        {
                            $class_td[$go] = 'normal';
                            $class_sep[$go] = 'sep';
                        }
                        if($act == $go)
                            $over = false;
                    }
                    ?>
                    <? $i = 0; foreach ($items as $go => $title): ?>
                        <td class="<?= $class_td[$go] ?>"><a href="index.php?act=<?=$go?>"><?=$title?></a></td>
                        <? if($i+1 < count($items)) { ?>
                            <td class="<?= $class_sep[$go] ?>"></td>
                        <? } ?>
                    <? $i++; endforeach; ?>
                </tr>
            </table>
            <div class="content">                
                <?=$content?>
            </div>
        </div>
        <div class="copyright"><?= $copyright ?></div>
    </body>
</html>
