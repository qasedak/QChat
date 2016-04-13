<? $pagecount = ceil($count / $perpage); ?>
<? if($page-1 > 0) { ?>
<a href="<?=url($url + array('page' => $page-1))?>" class="button r4"><?=tr('Back')?></a>
<? } ?>

<? if($page-$shift > 1) { ?>
<a href="<?=url($url + array('page' => 1))?>" class="button r4">1</a>...
<? } ?>

<? $s = $page-$shift; $s = ($s > 0)? $s : 1; ?>
<? for ($i=$s; $i<=$page-1 && $i>0; $i++) { ?>
<a href="<?=url($url + array('page' => $i))?>" class="button r4"><?=$i?></a><?=''?>
<? } ?>
<a href="<?=url($url + array('page' => $page))?>" class="button r4 buttonover"><?=$page?></a><?=''?>
<? for ($i=$page+1; $i<=$page+$shift && $i<=$pagecount; $i++) { ?>
<a href="<?=url($url + array('page' => $i))?>" class="button r4"><?=$i?></a><?=''?>
<? } ?>

<? if($page+$shift < $pagecount) { ?>
...<a href="<?=url($url + array('page' => $pagecount))?>" class="button r4"><?=$pagecount?></a>
<? } ?>

<? if($page+1 <= $pagecount) { ?>
<a href="<?=url($url + array('page' => $page+1))?>" class="button r4"><?=tr('Next')?></a>
<? } ?>
