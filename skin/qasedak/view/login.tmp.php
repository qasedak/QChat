<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?= tr('Login') ?> - <?= Elf::Settings('title') ?></title>
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

                $("#login").click(function () {
                    $("#login_form").submit();
                    return false;
                });

                $("#guest_name").focus();
                $("#name").focus();
  
            });
        </script>
    </head>
    <body>
        <div class="wrap">

            <h1><?= tr('Entry to chat') ?></h1>

            <? if (Elf::Settings('ownusers')): ?>
                <div class="loginbox">
                    <img src="<?= imgpath() ?>/User.png" class="imgclip" alt="">

                    <form action="index.php" method="post" id="login_form">                 
                        <label>
                            <?= tr('Name') ?>
                            <input type="text" name="name" id="name" tabindex="1" value="">
                            <? if (!empty($error_ownuser)): ?>
                                <div class="alert error relative"><div class="uparrow"></div><?= $error_ownuser ?></div>
                            <? endif; ?>
                        </label>
                        
                        <div class="left small"><a href="lostpassword.php"><?= tr('Forgot?') ?></a></div>
                        <label>
                            <?= tr('Password') ?>
                            <input type="password" name="password" id="password" tabindex="2" value="">
                        </label>

                        <label>
                            <input type="checkbox" name="remember" id="remember" tabindex="3" value="1">
                            <?= tr('Remember me') ?>
                        </label>
                        <input type="submit" class="hidden_submit">
                    </form>

                    <div class="bigbuttonholder">
                        <a id="login" href="#" tabindex="4">
                            <div class="bigbutton"><?= tr('Enter') ?></div>
                        </a>
                        <a id="signup" href="signup.php">
                            <div class="bigbutton"><?= tr('Signup') ?></div>
                        </a>
                    </div>

                </div>
            <? endif; ?>

            <? if (Elf::Settings('guest_enable')): ?>
            <h1><?= tr('Guest entry') ?></h1>
            <div class="loginbox">
            <img src="<?= imgpath() ?>/guest.png" class="imgclip" alt="">
            <form action="index.php" method="post" id="guest_login_form">
                <label>
                    <?= tr('Guest name') ?>
                    <input type="text" name="guest_name" id="guest_name" value="">
                    <? if (!empty($error_guest)): ?>
                        <div class="alert error relative"><div class="uparrow"></div><?= $error_guest ?></div>
                    <? endif; ?>
                </label>
                <input type="submit" class="hidden_submit">
            </form>
                <div class="bigbuttonholder">
                   <a id="guest_login" href="#">
                       <div class="bigbutton"><?= tr('Enter') ?></div>
                   </a>
                </div>

            </div>
            <? endif; ?>
            <div class="copyright"><?= $copyright ?></div>
        </div>
    </body>
</html>
