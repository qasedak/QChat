<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?=tr('Login to control center')?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="icon" href="<?=imgpath()?>/favicon.ico" type="image/ico">
<style type="text/css">
table td {
	padding: 5px !important;
}
</style>
<?=css('main')?>
</head>
<body>
<div style="padding: 100px;">
<div class="wrap r10" style="width: 400px; margin: 0 auto;">
    <h1 class="title"><?=tr('ElfChat Control Center')?></h1>
<div class="main r4">
<!-- main body -->
<h1><img src="<?=imgpath()?>/elf.png" alt="">  <?=tr('Login to control center')?></h1>
<?=($wrong_password)?'<div class="warning r4">'.tr('Name or password is wrong.').'</div>':''?>
    <form method="POST" action="index.php">
    <table width="90%">
	<tr><td style="width: 60px;"><?=tr('Name')?>:</td><td><input type="text" name="admin" value="" style="width: 100%"></td></tr>
	<tr><td><?=tr('Password')?>:</td><td><input type="password" name="password" style="width: 100%"></td></tr>
	<tr><td colspan="2" align="center"><input type="submit" value="<?=tr('Login')?>"></td></tr>
	</table>
	</form>
<!-- main body end -->
</div>
<div class="copy"><?=$copyright?></div>
</div>
</div>
</body>
</html>
