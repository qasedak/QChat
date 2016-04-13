<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?=$title?></title>
        <?= css('main') ?>
        <script type="text/javascript" src="js/min/jquery.js"></script>
        <? if(isset($javascript)) echo $javascript; ?>
    </head>
    <body>
        <?=$content?>
    </body>
</html>
