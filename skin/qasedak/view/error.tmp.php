<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?= Elf::Settings('title') ?></title>
        <?= css('chat') ?>
    </head>
    <body>
        <div class="wrap">
            <div class="info">
                <table>
                    <tr>
                        <td class="img"><img src="<?=imgpath()?>/warning.png" alt=""></td>
                        <td class="msg">
                            <h1><?= $error ?></h1>
                            <?= $description ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>
