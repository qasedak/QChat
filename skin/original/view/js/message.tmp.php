<script type="text/html" id="view_message">
    {if type == 'normal'}
    {js var elfs = (message.substr(0,6) == 'elf://');}
    <div class="message {=(personal != null)?'personal':''} {=is_to_me?'to_me':''}" id="message_{=message_id}">
        <span class="time">{=time}</span>
        <span class="user">
            {if elfs}
            <a href="#" class="name" user_id="{=user.id}"><i><?=tr('Chat Spirit')?></i></a>
            {js message = message.substr(6);}
            {else}
            <a href="#" class="name" user_id="{=user.id}">{html user.get_name()}</a>
            {/if}
            {if personal != null}
            <?= tr('personal for') ?> <a href="#" class="name" user_id="{=personal.id}">{html personal.get_name()}</a>
            {/if}
            :&nbsp;
        </span>
        <span style="color:{=color};" class="text">{html message}</span>
    </div>
    {elseif type == 'connect'}
    <div class="message information" id="message_{=message_id}">
        <span class="time">{=time}</span>
        <span class="text"><?=tr('In chat comes')?> <a href="#" class="name">{html user.get_name()}</a></span>
    </div>
    {elseif type == 'disconnect'}
    <div class="message information" id="message_{=message_id}">
        <span class="time">{=time}</span>
        <span class="text"><a href="#" class="name">{html user.get_name()}</a> <?=tr('comes out')?></span>
    </div>
    {elseif type == 'warning'}
    <div class="message warning" id="message_{=message_id}">
        <span class="time">{=time}</span>
        <span class="text">{html message}</span>
    </div>
    {/if}    
</script>
