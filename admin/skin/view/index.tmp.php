<div class="content">
    
    <table class="container" width="100%">
        <tr>
            <td width="50%">
                <h1><?=tr('ElfChat')?></h1>
                <div class="about">
                    <div><?=tr('Version')?>: <span id="version"><?=$version?></span></div>
                    <div><?=tr('Language')?>: <span id="lang" title="ru"><?=$lang?></span></div>
                    <div><?=tr('Skin')?>: <span id="lang" title="ru"><?=$skin?></span></div>
                </div>

                <br>

                <h1><?=tr('News')?> <a href="http://www.omsh.ir/2013-10-01-20-44-41/prj/mahsoolat/130-web-script/scripts/129-5qchat.html" target="_black"><img alt="" src="<?=imgpath()?>/twitter-icon.png"></a></h1>
                <div id="news" class="news"></div>

                <br>

            </td>

            <td width="50%">
                <h1><?=tr('Admin\'s logs')?></h1>
                <?=call('logs/list', array( 'logs' => $admins_logs ))?>
                <br>
                <h1><?=tr('Moder\'s logs')?></h1>
                <?=call('logs/list', array( 'logs' => $moders_logs ))?>
            </td>

        </tr>

    </table>

</div>

<script src="http://twitterjs.googlecode.com/svn/trunk/src/twitter.min.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8">
getTwitters('news', {
  id: 'elfchat',
  count: 10,
  enableLinks: true,
  ignoreReplies: true,
  clearContents: true,
  withFriends: false,
  template: '%text%'
});
</script>
