<script type="text/html" id="view_room">		
    <div class="room" id="room_{=room_id}">
        <div class="box {=(current==true)?'overbox':''}">
            <div style="float: right" class="toggler toggler_collapse" onclick="$('#room_{=room_id}').roomtoggle(); return false;"></div>
            {if lock == true}
                <img src="<?=imgpath()?>/lock.png" alt=""/>
            {/if}
            <a href="javascript:set_room({=room_id});" tooltip="{if current==true}<?=tr('Current room.')?>{else}<?=tr('Click here to enter this room.') ?>{/if}">{=title}</a>
        </div>
        <div class="box_content users_list">
            {html users}
        </div>
    </div>
</script>
