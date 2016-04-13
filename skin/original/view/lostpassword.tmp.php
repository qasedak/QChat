<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?= tr('Password recovery') ?> - <?= Elf::Settings('title') ?></title>
        <?= css('login') ?>
        <script type="text/javascript" src="js/min/jquery.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $(".bigbutton").hover(
                function () {
                    $(this).addClass("bigbutton_over");
                },
                function () {
                    $(this).removeClass("bigbutton_over");
                })
                .mousedown(function () {
                    $(this).addClass("bigbutton_down");
                })
                .mouseup(function () {
                    $(this).removeClass("bigbutton_down");
                });

                $("#password_recovery").click(function () {
                    $("#password_recovery_form").submit();
                    return false;
                });


                $("#email").focus();
  
            });
        </script>
    </head>
    <body>
        <div class="wrap">

            <h1><?= tr('Password recovery') ?></h1>

            <div class="loginbox">
                <? if (isset($done) && $done): ?>

                    <div class="alert info"><?= tr('New password sended to your email.') ?></div>
                    <div class="bigbuttonholder">
                        <a href="index.php">
                            <div class="bigbutton"><?= tr('Entry to chat') ?></div>
                        </a>
                    </div>

                <? else: ?>

                        <form action="lostpassword.php" method="post" id="password_recovery_form">
                        <input type="hidden" name="_actionpost" value="<?=$actionpost_hash?>">
                        <label>
                        <?= tr('Email') ?>
                            <input type="text" name="email" id="email" value="<?=$inputs['email']->GetValue()?>">
                            <? if ($inputs['email']->has_errors()):  ?>
                                <div class="alert error relative"><div class="uparrow"></div><?= $inputs['email']->get_first_error() ?></div>
                            <? endif; ?>
                        </label>


                        <div align="center"><img src="lib/kcaptcha/image.php?name=signup&<?= session_name() ?>=<?= session_id() ?>" alt=""/></div>

                        <label>
                        <?= tr('Captcha') ?>
                            <input type="text" name="captcha" id="captcha" value="">
                            <? if ($inputs['captcha']->has_errors()): ?>
                                <div class="alert error relative"><div class="uparrow"></div><?= $inputs['captcha']->get_first_error() ?></div>
                            <? endif; ?>
                        </label>
                        
                        <input type="submit" class="hidden_submit">
                        </form>

                        <div class="bigbuttonholder">
                            <a id="password_recovery" href="#">
                                <div class="bigbutton"><?= tr('Recovery') ?></div>
                            </a>
                        </div>
                <? endif; ?>
            </div>
            <div class="copyright"><?= $copyright ?></div>
        </div>
    </body>
</html>
