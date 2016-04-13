<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?=tr('Exit')?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="icon" href="skin/img/favicon.ico" type="image/ico">
<?=css('main')?>
</head>
<body>
<div style="padding: 100px;">
<div class="wrap r10" style="width: 400px; margin: 0 auto;">
<h1 class="title"><?=tr('ElfChat Control Center')?></h1>
<div class="main r4" style="padding:5px;">
<h1><?=tr('You exited')?></h1>

<div class="buttonbox">
<a href="index.php" class="button r4"><?=tr('Login again')?></a>
<a href="<?=Elf::Settings('chat_url')?>" class="button r4"><?=tr('Enter to chat')?></a>
<a href="http://socialtools.ir" class="button r4"><?=tr('ElfChat Site')?></a>

</div>

</div>
<div class="copy"><?=$copyright?></div>
</div>
</div>
</body>
</html>
