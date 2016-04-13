<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <title><?=( $title ) ? $title.' - ' : ''?><?=tr('ElfChat Control Center')?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="icon" href="<?=imgpath()?>/favicon.ico" type="image/ico">
        <?=css('main')?>
        <?=css('spec')?>
        <link type="text/css" href="css/ui-lightness/jquery-ui-1.8.2.custom.css" rel="stylesheet" />
        <script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8.2.custom.min.js"></script>
        <script type="text/javascript" src="js/jquery.tablednd_0_5.js"></script>
        <script type="text/javascript" src="js/main.js"></script>
        <script type="text/javascript">
            var Lang = {
                delete_this: "<?=tr('Do you want to delete this?')?>"
            };
        </script>
    </head>
    <body>
        <?php
        echo $content;
        ?>
    </body>
</html>
