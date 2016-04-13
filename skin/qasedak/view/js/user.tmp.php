<script type="text/html" id="view_user">	
    <div class="user user_{=user.id}">
        <? if (Elf::Settings('use_avatars')): ?>
        <img class="avatar" src="{=(user.avatar==''?'<?=imgpath()?>/guest.png':user.avatar)}" width="32" height="32">
        <? endif; ?>
        <div>
            <span class="onlineicon"><img src="{=(user.icon==''?'<?=imgpath()?>/bullet_blue.png':user.icon)}" tooltip="{=user.group_title}" alt=""></span>
            <a href="#" class="name" user_id="{=user.id}">{html user.get_name()}</a><span class="user_menu" user_id="{=user.id}"></span>
        </div>
        <div class="status_line"><span class="status">{html user.status}</span></div>
    </div>
</script>
