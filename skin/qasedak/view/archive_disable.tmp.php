<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?=tr('Archive')?> - <?= Elf::Settings('title') ?></title>
    <?= css('chat') ?>
    <?= css('override') ?>
    <style type="text/css">
        body {
            overflow: auto;
        }
    </style>
</head>
<body>
<!-- wrap -->
<div id="wrap" class="wrap">

    <div class="chatwindow">
        <div class="top">
            <span class="title"><?=tr('Archive')?></span>
        </div>

        <table class="body">
           <tr>
                <td class="chat">
                    <?=tr('Archives disabled by the administrator.')?>
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
