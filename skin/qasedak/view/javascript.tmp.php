<script type="text/javascript" src="js/min/jquery.js"></script>
<script type="text/javascript" src="js/min/jquery.ui.js"></script>
<script type="text/javascript" src="js/min/json2.js"></script>
<script type="text/javascript" src="js/min/jquery.hotkeys.js"></script>
<script type="text/javascript" src="js/min/swfobject.js"></script>

<script type="text/javascript" src="js/min/audio.js"></script>
<script type="text/javascript" src="js/min/jquery.tmpl.js"></script>
<script type="text/javascript" src="js/chat.js"></script>
<script type="text/javascript" src="js/skin.js"></script>

<script type="text/javascript" src="transmitter/<?=Elf::Settings('transmitter')?>/client.js"></script>

<script type="text/javascript" src="smiles/smiles.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        window.Resize = new KeepSize(["#chat", "#online"], {saveSize: 20});
        window.Smiles = new SmilesClass(SMILES, "smiles/");
        window.UI = new UIClass();
        window.Sound = new SoundClass([{play: 'login', src: 'sound/online.mp3'}, {play: 'beep', src: 'sound/beep.mp3'}, {play: 'media', src: ''}], {swf: 'js/audio/Player.swf'});
        window.Rooms = new RoomsListClass();
        window.Messages = new MessagesClass();
        window.Users = new UsersListClass();
        window.Users.me_id = <?=$user->id?>;
        <? if(Elf::Settings('transmitter') == 'websocket'): ?>
        var transmitter = new WebSocketTransmitterClass("ws://<?= Elf::Settings('websocket_server') ?>:<?= Elf::Settings('websocket_port') ?><?= Elf::Settings('websocket_path') ?>");
        <? else: ?>
        var transmitter = new AjaxTransmitterClass("<?= Elf::Settings('ajax_server') ?>?", <?= Elf::Settings('ajax_delay') ?>);
        <? endif; ?>
        window.Chat = new ChatClass(transmitter);       
    });
    window.Lang = {
        your_status: "<?= tr('Enter your status:') ?>",
        room_password: "<?= tr('To enter this room need to type a password:') ?>",
        user_settings: "<?=tr('User settings')?>",
        avatar: "<?=tr('Avatar')?>",
        moderator: "<?=tr('Moderator')?>"
    };

    window.Settings = {
        show_tooltip: <?=($user->settings->show_tooltip?'true':'false')?>,
        show_images: <?=($user->settings->show_images?'true':'false')?>,
        play_immediately: <?=($user->settings->play_immediately?'true':'false')?>
    }
    
</script>
