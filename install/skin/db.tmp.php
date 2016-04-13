<? $view->layout('wrap'); ?>
<? if(isset($created)): ?>

    <? if(empty ($errors)) { ?>
        <h1 class="info"><?=tr('Tables created successfully.')?></h1>
        <div class="center">
            <form action="index.php" method="get">
                <input type="hidden" name="act" value="settings">
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
        <h1 class="info"><?=tr('Connection is established. Everything is ready to create tables.')?></h1>
        <div class="center">
            <form action="index.php" method="get">
                <input type="hidden" name="act" value="db">
                <input type="submit" name="create" value="<?= tr('Create tables') ?>">
            </form>
        </div>
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
