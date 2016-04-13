<?php

require_once 'init.php';
require_once 'controller/FormController.php';

$skin = new Skin(ELFCHAT_ROOT . '/skin/special/default');
View::set_skin($skin);

class Avatar extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->unauthorized_entry = false;
    }

    public function action_index()
    {
        $this->view = new View('user/avatar');

        $avatars = array();
        $handle = opendir(ELFCHAT_ROOT . '/avatars');
        if($handle)
        {
            while(false !== ($name = readdir($handle)))
            {
                $src = 'avatars/' . $name;
                if(is_file($src) && $name != "." && $name != "..")
                {
                    $avatars[] = $src;
                }
            }
            closedir($handle);
        }

        if(_GET('select')->is_set())
        {
            $select = _GET('select')->low();
            if($select == 'user_avatar')
            {
                $this->view->info = tr('Avatar have been uploaded.');
            }
            else
            {
                $select_number = intval($select);

                $this->auth->user->avatar = $avatars[$select_number];
                $this->auth->user->save();

                $this->view->info = tr('Avatar have been changed.');
            }

            $this->view->changed = true;            
        }

        $this->view->x = Elf::Settings('avatar_size_width');
        $this->view->y = Elf::Settings('avatar_size_height');
        $this->view->user = $this->auth->user;
        $this->view->avatars = $avatars;
        $this->display();
    }

    public function action_upload()
    {
        include_once 'lib/class.upload/class.upload.php';

        $file = new upload($_FILES['file']);
        if($file->uploaded)
        {
            // save uploaded image with a new name,
            // resized to 32x32
            $file->allowed = array('image/*');
            $file->file_overwrite = true;
            $file->file_new_name_body = 'avatar_' . $this->auth->user->id;
            $file->image_resize = true;
            $file->image_x = Elf::Settings('avatar_size_width');
            $file->image_y = Elf::Settings('avatar_size_height');
            $file->process(ELFCHAT_ROOT . '/upload');
            if($file->processed)
            {                
                $file->clean();

                $this->auth->user->avatar = 'upload/' . $file->file_dst_name;
                $this->auth->user->save();
                $this->redirect('avatar.php?select=user_avatar');
            }
            else
            {
                $file->clean();
                echo '<b>Upload error: ' . $file->error . '</b>';
            }
        }
    }

}

$avatar = new Avatar();
$avatar->run();
?>
