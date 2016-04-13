<?
// In MVC view can comunicate with model.
include_once 'models/Group.php';
$groups = Group::model()->select('g.id AS id, g.title AS title');

if(!isset ($group_filtr))
    $group_filtr = -1;
?>
<table class="subnav" width="100%">
    <tr>
        <td width="220px" class="menu r4">
            <a href="users.php" class="r4 <?=($filtr == 'allusers') ? 'menuselect' : ''?>"><img src="skin/img/user.png" alt=""> <?=tr('All users')?></a>
            <a href="admins.php" class="r4 <?=($filtr == 'admins') ? 'menuselect' : ''?>"><img src="skin/img/crown.png" alt=""> <?=tr('Admins')?></a>
            <a href="users.php?<?=url_params(array('filtr'=>'moders'))?>" class="r4 <?=($filtr == 'moders') ? 'menuselect' : ''?>">
                <img src="skin/img/balance.png" alt=""> <?=tr('Moderators')?>
            </a>
            <a href="users.php?<?=url_params(array('filtr'=>'find'))?>" class="r4 <?=($filtr == 'find') ? 'menuselect' : ''?>">
                <img src="skin/img/magnifier.png" alt=""> <?=tr('Find')?>
            </a>
            <h1><?=tr('Groups')?></h1>
            <? foreach($groups as $key => $group): ?>
            <a href="users.php?<?=url_params(array('filtr'=>'groups', 'group_filtr' => $group['id']))?>" class="r4 <?=($group_filtr == $group['id']) ? 'menuselect' : ''?>">
                    <?=$group['title']?>
            </a>
            <? endforeach; ?>
        </td>

        <td class="content">
            <? if(isset($find)): ?>
            <?=$find?>
            <? endif; ?>
            <?=$content?>
        </td>
    </tr>
</table>
