<? $view->layout('wrap'); ?>
<? if(isset($saved)): ?>

    <? if(empty ($errors)) { ?>
        <h1 class="info"><?=tr('Queries to the database successfully executed.')?></h1>
        <div class="center">
            <form action="index.php" method="get">
                <input type="hidden" name="act" value="goodbye">
                <input type="submit" value="<?= tr('Continue') ?>">
            </form>
        </div>
    <? } else { ?>
        <h1 class="warning"><?=tr('When creating tables were errors.')?></h1>
        <ol>
            <li>
            <?=implode('</li><li>', $errors)?>
            </li>
        </ol>
        <div class="center">
            <form action="index.php" method="get">
                <input type="hidden" name="act" value="db">
                <input type="submit" value="<?= tr('Back') ?>">
            </form>
        </div>
    <? } ?>

<? else: ?>

    <? if(isset($connected)){ ?>

       <form action="index.php?act=settings" method="post">
       <h1><?= tr('Admin Account') ?></h1>
       <?=tr('Account to log into the admin panel.')?>
       <table class="form">
            <tr>
                <td>
                    <?= tr('Admin Name') ?>
                </td>
                <td>
                    <input type="text" name="name" value="">
                </td>
            </tr>
            <tr>
                <td>
                    <?= tr('Admin Password') ?>
                </td>
                <td>
                    <input type="text" name="password" value="">
                </td>
            </tr>
        </table>
       <h1><?= tr('Chat Rooms') ?></h1>
       <table class="form">
            <tr>
                <td>
                    <?= tr('Default room name') ?>
                </td>
                <td>
                    <input type="text" name="room_title" value="">
                </td>
            </tr>
        </table>
        <div style="text-align: center;"><input type="submit" name="save" value="<?= tr('Save') ?>"></div>
        </form>

    <? } else { ?>

        <h1 class="warning"><?=tr('Could not establish a connection to database.')?></h1>
        <?=tr('Go back and check your entries.')?>
        <div class="center">
            <form action="index.php" method="get">
                <input type="hidden" name="act" value="config">
                <input type="submit" name="create" value="<?= tr('Back') ?>">
            </form>
        </div>
        
    <? } ?>

<? endif; ?>
