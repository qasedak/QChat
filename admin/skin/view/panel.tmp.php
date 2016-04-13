<? $view->layout('wrap'); ?>
<div class="wrap r10">
<h1 class="title"><?=tr('ElfChat Control Center')?></h1>
<div class="navbar">
<a href="index.php?exit" class="navelem right" title="<?=Format(tr('Sign in as: %%'), $name)?>">
	<img src="skin/img/exit.png" alt=""><br>
	<span><?=tr('Exit')?></span>
</a> 
<!-- Menu on Left --> 
<? foreach ($navigation as $key=>$val) { ?>
<a href="<?=$val['link']?>"  class="navelem <?=($select == $key) ? 'navselect' : ''?>">
	<img src="<?=$val['img']?>" alt=""><br>
	<span><?=$val['text']?></span> 
</a> 
<? } ?>
</div>
<div class="main r4">
<!-- main body -->
<?=$content?>
<!-- main body end -->
</div>
<div class="copy"><?=$copyright?></div>
</div>
