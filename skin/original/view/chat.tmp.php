<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?= Elf::Settings('title') ?></title>
        <?= css('chat') ?>
        <?= css('override') ?>
        <?= call('javascript', array('user' => $user)) ?>
        <?= call('js/room') ?>
        <?= call('js/user') ?>
        <?= call('js/message') ?>        
    </head>
<body>
<!-- wrap -->
<div id="wrap" class="wrap">

<div class="chatwindow">
<div class="top">
    <div class="buttons" style="float:right;">
        <!--<div class="button" tooltip="<?=tr('Help')?>"><img src="<?=imgpath()?>/question_frame.png" alt=""/></div>-->
        <!--<div class="sep"></div>-->
        <div onclick="UI.toggle_online('#img_toggle_onlnie', '<?=imgpath()?>/layer-2.png', '<?=imgpath()?>/layer.png');" class="button"  tooltip="<?=tr('Show/Hide online list.')?>"><img id="img_toggle_onlnie" src="<?=imgpath()?>/layer.png" alt=""></div>
        <div class="sep"></div>
        <? if(Elf::Settings('archive_enable')) { ?>
        <div class="button" tooltip="<?=tr('Archive')?>"><a href="archive.php" target="chat_archive"><img src="<?=imgpath()?>/drawer.png" alt=""/></a></div>
        <div class="sep"></div>
        <? } ?>
        <a class="button" id="exit" href="exit.php" tooltip="<?=tr('To exit the chat, click on this button.')?>"><img src="<?=imgpath()?>/cross_circle_frame.png"  alt=""/><span>&nbsp;<?=tr('Exit')?></span></a>
        </div>
     <span class="title" tooltip="<?=tr('Welcome to the chat!')?>"><?=Elf::Settings('title')?></span>
</div>

<table class="body">
<tr>
<?
$chat = <<<CHAT
    <td class="chat">
    <div id="chat">
    <!-- chat body -->
        &nbsp;
    <!-- chat body end -->
    </div>
    </td>
CHAT;
?>
<? if( ! Elf::Settings('online_on_left') ): ?>
<?=$chat?>
<? endif; ?>
    <td class="online">
    <div id="online">
    <div id="list">
    <!-- online list -->

    <!-- online list end -->
    </div>
    </div>
    </td>
<? if( Elf::Settings('online_on_left') ): ?>
<?=$chat?>
<? endif; ?>

</tr>
</table>

<div class="footer">
<div class="buttons">
    <div id="colors" class="button tooltip_top" tooltip="<?=tr('Message color')?>"><img src="<?=imgpath()?>/color.png" alt=""/></div>
    <div class="sep"></div>
    <div id="smiles" class="button tooltip_top menu_top" tooltip="<?=tr('Smiles')?>"><img src="<?=imgpath()?>/smiley-cool.png" alt=""/></div>
    <div class="sep"></div>
    <div id="media" class="button tooltip_top" tooltip="<?=tr('Sounds')?>"><img src="<?=imgpath()?>/music_beam.png" alt=""/></div>
</div>

<div class="buttons">
    <div id="switch_sounds" class="button tooltip_top" tooltip="<?=tr('Switch sounds')?>"><img src="" on="<?=imgpath()?>/sound.png" off="<?=imgpath()?>/sound_none.png" alt=""/></div>
    <div class="sep"></div>
    <div id="bbcodes" class="button tooltip_top" tooltip="<?=tr('BBCode')?>"><img src="<?=imgpath()?>/balloon_quote.png" alt=""/></div>
    <div class="sep"></div>
    <div id="settings" class="button tooltip_top" tooltip="<?=tr('Settings')?>"><img src="<?=imgpath()?>/gear.png" alt=""/></div>
</div>

<? if($user->is_moder()): ?>
<div class="buttons">
    <div id="moderator" class="button"><img src="<?=imgpath()?>/balance.png" title="" alt=""/><span>&nbsp;<?=tr('Moderator')?></span></div>
</div>
<? endif; ?>

    <div id="personal">        
        <div class="buttons blue">            
            <div class="button img_left tooltip_top" tooltip="<?=tr('Messages that you send only visible to the user.\n To send a message to all, click this button.')?>">
                <img class="free_left" src="<?= imgpath() ?>/cross.png" title="" alt=""/><span class="name"></span>
            </div>
        </div>
        <?=tr('to')?>&nbsp;
    </div>


<div class="buttons" style="float: right">
    <div id="send" class="button tooltip_top" tooltip="<?=tr('To send a message, press the «Enter».\nTo move to a new line «Shift + Enter».')?>"><i id="loading"></i><span>&nbsp;<?=tr('Send')?></span></div>
</div>

<textarea id="msg" name="msg"></textarea>
</div>

</div><!-- chatwindow -->
    
<div class="copy"><?=$copyright?></div>

</div>
<!-- wrap end -->

<!-- Over elements -->
<div id="chat_is_loading"><img src="<?=imgpath()?>/loading-white.gif"> <?=tr('Connecting...')?></div>

<!-- Menus -->
<ul class="menu" id="menu_settings">
    <li><a href="#" id="edit_settings"><?=tr('Edit settings')?></a></li>
    <li><a href="#" id="edit_avatar"><?=tr('Avatar')?></a></li>
    <li class="submenu">
        <a href="#"><div><?=tr('Status')?></div></a>
        <ul class="menu">
        <?
        $status_list = explode(',', Elf::Settings('status_list'));
        foreach($status_list as $status):
        ?>
            <li><a href="#" class="status_list"><?=trim($status)?></a></li>
        <? endforeach; ?>
        <li><a href="#" class="your_status"><?=tr('Your status...')?></a></li>
        </ul>
    </li>
    <li><a href="#" id="scrollable" scroll="true"><?= tr('Scroll') ?></a></li>
</ul>

<ul class="menu" id="menu_bbcodes">
    <li><a href="#" class="bbcode" bbcode="[quote][/quote]"><?=tr('Quote')?></a></li>
    <li><a href="#" class="bbcode" bbcode="[b][/b]"><?=tr('Bold')?></a></li>
    <li><a href="#" class="bbcode" bbcode="[i][/i]"><?=tr('Italic')?></a></li>
    <li><a href="#" class="bbcode" bbcode="[s][/s]"><?=tr('Strikethrough')?></a></li>
    <li><a href="#" class="bbcode" bbcode="[color=black][/color]"><?=tr('Text Color')?></a></li>
    <li><a href="#" class="bbcode" bbcode="[bg=white][/bg]"><?=tr('Background Color')?></a></li>
</ul>

<ul class="menu" id="menu_user">
    <li><a href="#" id="personal_for"><?=tr('Personal message')?></a></li>
    <li><a href="#" tooltip="<?=tr('Do not show messages from this user.')?>"><label><input type="checkbox" id="ignore" value=""> <?=tr('Ignore')?></label></a></li>
    <!--<li><a href="#" id="vote_ban" tooltip="<?=tr('If enough users vote for the ban, the user will be banned.')?>"><?=tr('Vote for ban')?></a></li>-->
        <? if($user->is_moder()): ?>
        <li class="submenu"><a href="#"><div><?=tr('Moderating')?></div></a>
            <ul class="menu">
               <li><a href="#" id="silence"><?=tr('Silence')?></a></li>
               <li><a href="#" id="kill"><?=tr('Kill')?></a></li>
               <li><a href="#" id="ban"><?=tr('Ban')?></a></li>
            </ul>
        </li>
        <? endif; ?>
</ul>

<div class="popup">
    <div class="top">        
        <div class="buttons" style="float:right;">
            <a href="#" class="button expand_popup"><div class="img"></div></a>
            <div class="sep"></div>
            <a href="#" class="button close_popup"><img src="<?= imgpath() ?>/cross_circle_frame.png"  alt=""></a>
        </div>
        <span class="title"></span>&nbsp;
    </div>
    <div class="content">
        <iframe></iframe>
    </div>
</div>

</body>
</html>
