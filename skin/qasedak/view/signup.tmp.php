<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?= tr('Signup') ?> - <?= Elf::Settings('title') ?></title>
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

                $("#guest_login").click(function () {
                    $("#guest_login_form").submit();
                    return false;
                });

                $("#signup").click(function () {
                    $("#signup_form").submit();
                    return false;
                });

                $("#name").focus();
  
            });
        </script>
    </head>
    <body>
        <div class="wrap">

            <h1><?=tr('Signup')?></h1>

            <? if (isset($done) && $done): ?>
                <div class="loginbox">
                    <img src="<?=imgpath()?>/User.png" class="imgclip" alt="">
                    <div class="alert info"><?=tr('Registration complete.')?></div>
                    <div class="bigbuttonholder">
                        <a href="index.php">
                            <div class="bigbutton"><?=tr('Entry')?></div>
                        </a>
                    </div>
                </div>
            <? else: ?>
                    <div class="loginbox">
                        <img src="<?=imgpath()?>/User.png" class="imgclip" alt="">
                        <form action="signup.php" method="post" id="signup_form">
                        <input type="hidden" name="_actionpost" value="<?=$actionpost_hash?>">
                        <label>
                        <?=tr('Name') ?>
                        <input type="text" name="name" id="name" value="<?=$inputs['name']->GetValue()?>">
                        <? if ($inputs['name']->has_errors()): ?>
                            <div class="alert error relative"><div class="uparrow"></div><?=$inputs['name']->get_first_error()?></div>
                        <? endif; ?>
                        </label>

                        <label>
                            <div><?=tr('Password')?></div>
                            <input type="password" name="password" id="password" value="<?=$inputs['password']->GetValue()?>">
                        <? if ($inputs['password']->has_errors()): ?>
                                <div class="alert error relative"><div class="uparrow"></div><?= $inputs['password']->get_first_error() ?></div>
                        <? endif; ?>
                        </label>

                       <label>
                           <div><?= tr('Retype password') ?></div>
                           <input type="password" name="retype_password" id="retype_password" value="<?=$inputs['retype_password']->GetValue()?>">
                           <? if ($inputs['retype_password']->has_errors()): ?>
                               <div class="alert error relative"><div class="uparrow"></div><?= $inputs['retype_password']->get_first_error() ?></div>
                           <? endif; ?>
                        </label>

                        <label>
                        <?= tr('Email') ?>
                            <input type="text" name="email" id="email" value="<?=$inputs['email']->GetValue()?>">
                            <? if ($inputs['email']->has_errors()):  ?>
                                <div class="alert error relative"><div class="uparrow"></div><?= $inputs['email']->get_first_error() ?></div>
                            <? endif; ?>
                        </label>


                        <div align="center"><img src="lib/kcaptcha/image.php?name=signup&<?= session_name() ?>=<?= session_id() ?>" alt="Captcha"/></div>

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
                           <a id="signup" href="signup.php">
                               <div class="bigbutton"><?= tr('Signup') ?></div>
                           </a>
                       </div>

                   </div>
            <? endif; ?>
            <div class="copyright"><?= $copyright ?></div>
        </div>
    </body>
</html>
