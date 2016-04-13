<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
<style type="text/css">
.auto-style1 {
	text-align: center;
}
</style>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <? if(Elf::Settings('exit_url') != ''): ?>
        <meta http-equiv="refresh" content="2;url=<?=Elf::Settings('exit_url')?>">
        <? endif; ?>
        <title><?= Elf::Settings('title') ?></title>
        <?= css('chat') ?>
    </head>
    <body>
        <div class="wrap">
            <div class="info">
                <table>
                    <tr>
                        <td class="img"><img src="<?=imgpath()?>/Log-out-48.png" alt=""></td>
                        <td class="msg auto-style1">
                            <h1><?=tr('You have successfully logged out')?></h1>
                            <div class="buttonbox"> <center>
                            <a href="<?=Elf::Settings('chat_url')?>" class="button r4"><?=tr('بازگشت')?></a>
                             </center>
                          </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </body>
</html>
