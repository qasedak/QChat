<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?=tr('Archive')?> - <?= Elf::Settings('title') ?></title>
    <?= css('chat') ?>
    <?= css('override') ?>
    <script type="text/javascript" src="js/min/jquery.js"></script>
    <script type="text/javascript" src="js/min/swfobject.js"></script>
    <script type="text/javascript" src="js/min/audio.js"></script>
    <script type="text/javascript" src="js/chat.js"></script>
    <script type="text/javascript" src="smiles/smiles.js"></script>
    <script type="text/javascript">
        window.Smiles = new SmilesClass(SMILES, "smiles/");
        window.Messages = new MessagesClass();
        $(function () {
            window.Sound = new SoundClass([{play: 'login', src: 'sound/online.mp3'}, {play: 'beep', src: 'sound/beep.mp3'}, {play: 'media', src: ''}], {swf: 'js/audio/Player.swf'});
        });
    </script>
    <link rel="stylesheet" media="screen" type="text/css" href="js/datepicker/css/datepicker.css"/>
    <script type="text/javascript" src="js/datepicker/js/datepicker.js"></script>
    <style type="text/css">
        body {
            overflow: auto;
        }

        .archive_nav {
            background: #cccccc;
            padding: 10px;
        }

        #date {
            width: 70px;
        }
    </style>
    <script type="text/javascript">
        $(function () {
            $('#nav').submit(function () {
                if ($('#password').val() == '') {
                    if ($('option:selected').attr('password') == 'true') {
                        var password = window.prompt("<?= tr('To enter this room need to type a password:') ?>");
                        $('#password').val(password);
                    }
                }
            });

            $('#date').DatePicker({
                format:'d.m.Y',
                date: $('#date').val(),
                current: $('#date').val(),
                starts: 1,
                position: 'r',
                onBeforeShow: function() {
                    $('#date').DatePickerSetDate($('#date').val(), true);
                },
                onChange: function(formated, dates) {
                    $('#date').val(formated);
                }
            });
        });

        window.Settings = {
            show_tooltip: false,
            show_images: true,
            play_immediately:false
        }
    </script>
</head>
<body>
<?
function msg($msg)
{
$msg = addslashes($msg);
$js = <<<HTML
    <script type="text/javascript">
        document.write(new MessageProcessing('{$msg}').process().message());
    </script>
HTML;
return $js;
}
?>
<!-- wrap -->
<div id="wrap" class="wrap">

    <div class="chatwindow">
        <div class="top">
            <span class="title"><?=tr('Archive')?></span>
        </div>

        <table class="body">
            <tr>
                <td class="archive_nav">
                    <form id="nav" action="archive.php" method="get">
                        <input type="hidden" name="password" id="password" value="<?=$password?>">
                        <?=tr('Chat Rooms')?>:
                        <select name="room">
                            <? foreach ($rooms as $room): ?>
                            <option id="rooms" value="<?=$room->id?>"
                                <?= $room->id == $roomId ? 'selected' : '' ?>
                                <? if ($room->password != ''): ?>
                                    password="true"
                                <? endif; ?>
                                    >
                                <?=$room->title?>
                            </option>
                            <? endforeach; ?>
                        </select>
                         <?=tr('Day')?>: <input type="text" name="date" id="date" value="<?=$date?>">
                        <input type="submit" value="<?=tr('Go')?>">
                        <span style="float:right;">
                            <input type="submit" name="prev" value="<?=tr('Back')?>" onclick="$('#date').val('<?=date('d.m.Y', $starttime-86400)?>');">
                            <input type="submit" name="today" value="<?=tr('Today')?>" onclick="$('#date').val('<?=date('d.m.Y')?>');">
                            <input type="submit" name="next" value="<?=tr('Next')?>" onclick="$('#date').val('<?=date('d.m.Y', $starttime+86400)?>');">
                        </span>
                    </form>
                </td>
            </tr>
            <tr>
                <td class="chat">
                    <div id="chat">
                        <!-- chat body -->
                        <? if (isset($bad)): ?>
                        <div class="message">
                            <span class="text"><?=tr('You have entered an incorrect password.')?></span>
                        </div>
                        <? else: ?>
                        <? foreach ($messages as $message): ?>
                            <div class="message">
                                <span class="time"><?=$message->time?></span><span class="user"><a
                                    class="name"><?=$message->name?></a>:&nbsp;</span><span
                                    style="color:<?=$message->color?>;" class="text"><?=msg($message->msg)?></span>
                            </div>
                            <? endforeach; ?>
                        <? endif; ?>
                        <!-- chat body end -->
                    </div>
                </td>
            </tr>
        </table>

    </div>
    <!-- chatwindow -->

    <div class="copy"><?=$copyright?></div>

</div>
<!-- wrap end -->

</body>
</html>
