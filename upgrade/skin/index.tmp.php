<? $view->layout('wrap'); ?>
<h1 class="info"><?=tr('Upgrade was successfully completed.')?></h1>
<i><?=tr('Do not forget to delete or rename the upgrade folder!')?></i><br>
<?= tr('Watch for updates on') ?> <a href="http://socialtools.ir" target="_black"><?=tr('the official site')?></a>.<br>
<?= tr('Follow as on') ?> <a href="https://forums.socialtools.ir" target="_black">پشتیبانی</a>.
<script type="text/javascript" src="http://elfchat.ru/event/upgrade/?serial=<?=$serial?>&url=<?=$chat_url?>"></script>
