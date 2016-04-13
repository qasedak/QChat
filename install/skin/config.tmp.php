<? $view->layout('wrap'); ?>
<? if(isset($saved)): ?>
<h1 class="info"><?=tr('Configuration saved.')?></h1>
<div class="center">
    <form action="index.php" method="get">
        <input type="hidden" name="act" value="db">
        <input type="submit" value="<?= tr('Continue') ?>">
    </form>
</div>
<? else: ?>
<form action="index.php?act=config" method="post">
    <h1><?= tr('Database Configuration') ?></h1>
    <table class="form">
        <tr>
            <td>
                <?= tr('MySQL Host') ?>
            </td>
            <td>
                <input type="text" name="hostname" value="<?=$config['hostname']?>">
            </td>
        </tr>
        <tr>
            <td>
                <?= tr('MySQL User') ?>
            </td>
            <td>
                <input type="text" name="username" value="<?=$config['username']?>">
            </td>
        </tr>
        <tr>
            <td>
                <?= tr('MySQL password') ?>
            </td>
            <td>
                <input type="text" name="password" value="<?=$config['password']?>">
            </td>
        </tr>
        <tr>
            <td>
                <?= tr('MySQL database') ?>
            </td>
            <td>
                <input type="text" name="dbname" value="<?=$config['dbname']?>">
            </td>
        </tr>
        <tr>
            <td>
                <?= tr('DB prefix') ?>
            </td>
            <td>
                <input type="text" name="prefix" value="<?=$config['prefix']?>">
            </td>
        </tr>
    </table>

    <h1><?= tr('Chat Configuration') ?></h1>
    <table class="form">
        <tr>
            <td>
                <?= tr('Title') ?>
            </td>
            <td>
                <input type="text" name="title" value="<?=$settings['title']?>">
            </td>
        </tr>
        <tr>
            <td>
                <?= tr('Chat URL') ?>
            </td>
            <td>
                <input type="text" name="chat_url" value="<?=$settings['chat_url']?>">
            </td>
        </tr>
    </table>
    <div style="text-align: center;"><input type="submit" name="save" value="<?= tr('Save') ?>"></div>
</form>
<? endif; ?>
