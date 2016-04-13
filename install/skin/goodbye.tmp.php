<? $view->layout('wrap'); ?>
<? if(isset($success) && $success): ?>
    <h1 class="info"><?=tr('Installation was successfully completed.')?></h1>
<? else: ?>
    <h1 class="center"><?=tr('Installation has been completed, but there may be problems.')?></h1>
<? endif; ?>
    <i><?=tr('Do not forget to delete or rename the install folder!')?></i><br>
    <?=tr('Watch for updates on')?> <a href="http://socialtools.ir" target="_black"><?=tr('the official site')?></a>.<br>
    <?=tr('Follow as on')?> <a href="https://forums.socialtools.ir" target="_black">پشتیبانی</a>.
<div class="center">
    <h1><a href="<?=$chat_url?>/admin"><?=tr('Login to Admin Control Center')?></a></h1>
</div>
<script type="text/javascript" src="http://elfchat.ru/event/install/?serial=<?=$serial?>&url=<?=$chat_url?>"></script>
