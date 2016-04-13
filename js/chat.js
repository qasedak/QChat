function random ()
{
    return '&_random=' + Math.random();
}

function getJSON(url, callback)
{
    $.getJSON(url + this.random(), function (data) {
        if(data.type == 'redirect')
        {
            window.location.href = data.url;
        }
        else
        {
            callback(data);
        }
    });
}

function ChatClass(transmitter)
{
    var $chat = this;
    this.transmitter = transmitter;
    this.color = null;
    this.personal = null;

    this.init = function ()
    {
        // set events to transmitter
        this.transmitter.onready = this.ready;
        this.transmitter.onmessage = this.message;
        this.transmitter.init();
    };

    this.send = function (msg)
    {
        this.transmitter.send(msg);
    };

    this.send_message = function(text)
    {
        if(text == '')
            return;
        var data = {
            type: 'msg',
            msg: text
        };
        if(this.color != null)
            data['color'] = this.color;
        if(this.personal != null)
            data['personal'] = this.personal;
        this.send(data);
    };

    this.message = function(data)
    {
        switch(data.type)
        {
            case 'disconnect':
                Users.disconnect(data.id);
                break;
            case 'connect':
                Users.connect(data.user);
                Sound.play('login');
                break;
            case 'change_room':
                Users.change_room(data.user_id, data.room_id);
                break;
            case 'msg':
                Messages.add(data);
                Sound.play('beep');
                Messages.scroll();
                break;
            case 'status':
                Users.status(data);
                break;
            case 'avatar':
                Users.avatar(data);
                break;
            case 'alert':
                window.alert(data.msg);
                break;
            case 'redirect':
                window.location.href = data.url;
                break;
            case 'delete_message':
                Messages.delete_message(data.delete_id);
                break;
            default:
                break;
        }

    };

    this.ready = function ()
    {
        UI.connected();
        $chat.load_users_list();
        $chat.last_messages();
    };

    this.load_users_list = function ()
    {
        getJSON('chat.php?act=list', function (data) {
            Rooms.rooms = data['rooms'];
            Users.set(data['users']);
            Rooms.display();
        });
    };

    this.last_messages = function (room_id)
    {
        var in_room = '';
        if(room_id != undefined)
            in_room = 'room=' + room_id;
        getJSON('chat.php?act=last_messages&' + in_room, function (data) {
            Users.add_users(data.users);
            Messages.add_array(data.messages);
        });
    };

    // Start init only here, because js need to declare all functions
    this.init();
}

function MessagesClass()
{
    var $messages = this;
    this.scrollable = true;

    this.add = function(data, options)
    {
        try
        {
            if(data.type == 'msg')
            {
                if(Users.is_ignore(data.user_id))
                    return;
                var type = data.msg_type || 'normal';
                var user = Users.get(data.user_id, {
                    id: data.user_id,
                    name: data.name || '%username%'
                });
                var personal = (data.personal == undefined) ? null : Users.get(data.personal, {
                    id: data.personal,
                    name: data.personal_name || '%username%'
                });
                var is_to_me = !(data.msg.indexOf(Users.me().name) == -1);
                var msg = (new MessageProcessing(data.msg, options)).process().message();
                var vars = {
                    'type': type,
                    'message_id': data.msg_id,
                    'time': data.time,
                    'color': data.color,
                    'user': user,
                    'message': msg,
                    'personal': personal,
                    'is_to_me': is_to_me
                };
                UI.view('message').render(vars).appendTo(UI.chat);
            }
        }
        catch (e)
        {}
    }

    this.add_array = function(data)
    {
        $messages.clear();
        for(var i in data)
        {
            $messages.add(data[i], {
                play: false
            });
        }
        $messages.scroll(10);
    }

    this.scroll = function (_speed)
    {
        if(this.scrollable)
        {
            var distance = UI.chat.attr('scrollHeight');
            var time = 1000;
            UI.chat.stop(true).animate({
                'scrollTop': distance
            }, time);
        }
    }
    this.clear = function ()
    {
        UI.chat.html('&nbsp;');
    }

    this.delete_message = function (msg_id)
    {
        $("#message_" + msg_id).fadeOut("slow", function () {
            $(this).remove();
        });
    }

}

function MessageProcessing(_msg, _options)
{
    var opt = $.extend({
        smiles: true,
        bbcode: true,
        images: Settings.show_images,
        play: Settings.play_immediately,
        scroll: true,
        max_images: 2,
        max_words_length: 100
    }, _options);
    var msg = _msg;
    var images_count = 0;
    this.message = function ()
    {
        return msg;
    }
    this.tag_explode = function (html, map)
    {
        // First always is not tag.
        var length = html.length;
        var array = [''];
        var n = 0;
        for(var i = 0; i < length; i++)
        {
            if(html.charAt(i) == '<')
            {
                array.push('');
                n++;
            }
            array[n] += html.charAt(i);
            if(html.charAt(i) == '>')
            {
                array.push('');
                n++;
            }
        }
        if(map == undefined)
            return array;
        else
        {
            map = $.extend({
                tag: function (val) {
                    return val;
                },
                text: function (val) {
                    return val;
                }
            }, map);
            return $.map(array, function(val, i) {
                if(i%2 == 1)
                    return map.tag(val);
                else
                    return map.text(val);
            }).join('');
        }
    }
    this.process = function ()
    {

        // SMILES
        if(opt.smiles)
        {
            msg = Smiles.parse(msg);
        }

        // bbcode
        if(opt.bbcode)
        {
            // background
            msg = msg.replace(/\[bg=([#0-9a-z]{1,20})\]((?:.(?!\[bg))*)\[\/bg\]/ig, '<span style="background-color:$1;">$2</span>');

            // color
            msg = msg.replace(/\[color=([#0-9a-z]{1,20})\]((?:.(?!\[color))*)\[\/color\]/ig, '<span style="color:$1;">$2</span>');

            // bold, italic, ext.
            msg = msg.replace(/\[b\]((?:.(?!\[b\]))*)\[\/b\]/ig, '<b>$1</b>');
            msg = msg.replace(/\[i\]((?:.(?!\[i\]))*)\[\/i\]/ig, '<i>$1</i>');
            msg = msg.replace(/\[s\]((?:.(?!\[s\]))*)\[\/s\]/ig, '<s>$1</s>');

            //marquee:
            msg = msg.replace(/\[m\]((?:.(?!\[s\]))*)\[\/m\]/ig, '<marquee>$1</marquee>');

            // blockquote
            function quote_callback(m, p1, p2)
            {
                var info = '';
                var quote = '';

                var msg = p1.match(/msg=&quot;([0-9]*)&quot;/);
                if(msg != null && msg[1] != '')
                    quote = ' ref="' + msg[1] + '"';

                var time = p1.match(/time=&quot;([\.:0-9]*)&quot;/);
                if(time != null && time[1] != '')
                    info += '<i>' + time[1] + '</i> ';

                var name = p1.match(/name=&quot;(.*)&quot;/);
                if(name != null && name[1] != '')
                    info += name[1];

                if(info != '')
                    info = '&copy; ' + info + ': ';
                return '<blockquote' + quote + '>' + info + p2 + '</blockquote>';
            }
            msg = msg.replace(/\[quote([^\]]*)\]((?:.(?!\[quote))*)\[\/quote\]/ig, quote_callback);


            // Media
            function media_callback(all, file, title)
            {
                var local = file.match(/(:\/\/)/);
                if(local == null)
                {
                    var player = 'media';
                    if(opt.play)
                    {
                        Sound.play(player, file);
                    }
                    return '<a href="javascript:;" class="play" player="' + player + '" src="' + file + '">' + title + "</a>";
                }
                else
                {
                    return file + ' - ' + title;
                }
            }
            msg = msg.replace(/\[media=([^\]]*)\]((?:.(?!\[media))*)\[\/media\]/ig, media_callback);

            // URI
            var regexUrl = /(https?):\/\/((?:[a-z0-9.-]|%[0-9A-F]{2}){3,})(?::(\d+))?((?:\/(?:[a-z0-9-._~!$&'()*+,;=:@]|%[0-9A-F]{2})*)*)(?:\?((?:[a-z0-9-._~!$&'()*+,;=:\/?@]|%[0-9A-F]{2})*))?(?:#((?:[a-z0-9-._~!$&'()*+,;=:\/?@]|%[0-9A-F]{2})*))?/i;
            function UriCallback(m,p1,p2,p3,p4,p5,p6,p7,p8,p9)
            {
                this.uri_count = ++this.uri_count || 0;
                var a_uri = '#uri_' + this.uri_count;
                if(opt.images)
                {
                    var ext = m.match(/\.([a-z0-9]+)$/i);
                    if(ext != null)
                    {
                        if( ext[1] == "jpg" || ext[1] == "jpeg" || ext[1] == "gif" || ext[1] == "png" )
                        {
                            var img = new load_image(m);
                            img.load = function () {
                                var w = img.width;
                                var h = img.height;
                                var max = 300;
                                if(w > max)
                                {
                                    w = max;
                                    var p = img.width / max;
                                    h = img.height / p;
                                }
                                if(h > max)
                                {
                                    h = max;
                                    var p = img.height / max;
                                    w = img.width / p;
                                }
                                var img_html = '<img src="'+m+'" style="width:'+w+'px; height:'+h+'px;">';
                                if(images_count++ < opt.max_images)
                                {
                                    setTimeout(function () {
                                        $(a_uri).html(img_html);
                                        if(opt.scroll)
                                            Messages.scroll(1);
                                    }, 1);
                                }
                            }
                        }
                    }
                }
                return '<a id="uri_'+this.uri_count+'" href="'+m+'" target="_blank">'+m+'</a>';
            }
            msg = msg.replace(regexUrl, UriCallback);
        }

        // loo...oong words
        msg = this.tag_explode(msg, {
            text: function (text)
            {
                return text.replace(new RegExp('[^\\s]{'+opt.max_words_length+',}', 'g'), function (all) {
                    return all.substr(0, 20) + '...' + all.substr(all.length - 20, all.length);
                });
            }
        });

        // a lot of spaces
        msg = msg.replace(/([\s]{100,})/g, function (all) {
            return all.substr(0, 100);
        })

        // empty lines:
        msg = msg.replace(/(\n){3,}/g, "\n");

        // max lines count:
        var lines_count = 0;
        msg = msg.replace(/[\n\r\t]/g, function (all) {
            if(++lines_count < 20)
                return "\n";
            else
                return " ";
        });

        return this;
    }
}

function SmilesClass(smiles, path)
{
    var $smiles = this;
    this.count = 0;
    var all = [];
    for(var key in smiles.main)
        all[key] = smiles.main[key];
    for(var key in smiles.hidden)
        all[key] = smiles.hidden[key];

    this.main = function ()
    {
        return smiles.main;
    }

    this.hidden = function ()
    {
        return smiles.hidden;
    }

    this.path = function ()
    {
        return path;
    }

    this.get = function(key)
    {
        $smiles.count += 1;
        if($smiles.count <= 10)
            return '<img src="' + path + all[key] + '" alt="">';
        else
            return key;
    }

    this.parse = function (msg)
    {
        $smiles.count = 0;
        for(var key in all)
        {
            msg = msg.replace(new RegExp(this.escape(key), 'g'), $smiles.get);
        }
        return msg;
    }

    this.escape = function(text) {
        return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
    }

}

function RoomsListClass()
{
    var $ui = UI;
    this.rooms = [];

    this.by_id = function (id)
    {
        for (var i in this.rooms)
        {
            if(this.rooms[i].id == id)
                return this.rooms[i];
        }
        return null;
    }

    this.current_room = function()
    {
        for (var id in this.rooms)
        {
            if(this.rooms[id].current == true)
                return this.rooms[id];
        }
        return 0;
    }

    this.set_current_room = function(room_id)
    {
        for (var id in this.rooms)
        {
            if(id == room_id)
                this.rooms[id].current = true;
            else
                this.rooms[id].current = false;
        }
    }

    this.display = function ()
    {
        // First all time will be current room
        $ui.online.html('');
        var cr = this.current_room();
        $ui.view('room').render({
            room_id: cr.id,
            title: cr.title,
            users: Users.in_room(cr.id),
            current: true,
            lock: cr.lock
        }).appendTo($ui.online);

        // All rooms after
        for (var id in this.rooms)
        {
            if(this.rooms[id].current == false)
            {
                var room = this.rooms[id];
                $ui.view('room').render({
                    room_id: room.id,
                    title: room.title,
                    users: Users.in_room(room.id),
                    current: false,
                    lock: room.lock
                }).appendTo($ui.online);
            }
        }// End room's for
    }
}

function UsersListClass()
{
    var $userslist = this;
    this.me_id = 0;
    this.users = {};
    this.list = [];
    var ignore = {};
    this.set = function(datas)
    {
        this.users = {};
        this.list = [];
        for (var i in datas)
        {
            var user = new UserClass(datas[i]);
            this.users[user.id] = user;
            this.list.push(user.id);
        }
    }
    this.add_users = function (users)
    {
        for (var i in users)
        {
            if(this.users[users[i].id] == undefined)
            {
                var user = new UserClass(users[i]);
                this.users[user.id] = user;
            }
        }
    }
    this.html = function(user)
    {
        return UI.view('user').render({
            'user': user
        }, true);
    }
    this.in_room = function (room_id)
    {
        var html = '';
        for (var i in this.list)
        {
            var user = this.by_id(this.list[i]);
            if(user.room == room_id)
                html += this.html(user);
        }
        return html;
    }

    this.by_id = function(user_id)
    {
        if(this.users[user_id] != undefined)
            return this.users[user_id];
        else
            return null;
    }
    this.get = function(user_id, data)
    {
        var user = this.by_id(user_id);
        if(user != null)
            return user;
        else
            return (data == undefined) ? null : new UserClass(data);
    }
    this.me = function ()
    {
        return this.by_id(this.me_id);
    }
    this.remove_from_list = function(user_id)
    {
        for (var i in this.list)
        {
            if(this.list[i] == user_id)
                delete this.list[i];
        }
    }
    this.get_name = function (id, defname)
    {
        var user = this.by_id(id);
        if(user != null && user.name != undefined)
            return user.get_name();
        else
            return (defname == undefined) ? null : defname;
    }
    this.disconnect = function (id)
    {
        var user = this.by_id(id);
        if(user != null)
        {
            this.remove_from_list(id);
            $('.user_' + user.id).hide('blind', {}, 200, function () {
                $(this).remove();
            });
        }
    }
    this.connect = function (user_data)
    {
        if(this.by_id(user_data.id) != null)
        {
            this.disconnect(user_data.id);
        }
        var user = new UserClass(user_data);
        this.users[user.id] = user;
        this.list.push(user.id);
        var html = this.html(user);
        $('#room_' + user.room + ' .users_list').append(html);
        $('.user_' + user.id).hide().show('blind');
    }
    this.change_room = function (user_id, room_id)
    {
        if(user_id == this.me_id)
        {
            Rooms.set_current_room(room_id);
            Rooms.display();
            Chat.last_messages(room_id);
        }
        var user = this.by_id(user_id);
        user.room = room_id;
        this.connect(user);
    }

    this.status = function (data)
    {
        var user = this.by_id(data.user_id);
        user.set_status(data.status);
    }

    this.avatar = function (data)
    {
        var user = this.by_id(data.user_id);
        user.set_avatar(data.avatar);
    }

    this.add_ignore = function (user_id) {
        ignore[user_id] = true;
    }

    this.remove_ignore = function(user_id) {
        delete ignore[user_id];
    }

    this.is_ignore = function(user_id) {
        return ignore[user_id] == true;
    }
}

function UserClass(data)
{
    this.id = 0;
    this.name = '%username%';
    this.mask = '';
    this.room = 0;
    this.status = '%status%';
    this.avatar = '%avatar%';
    this.icon = '%icon%';
    this.group_title = '%group_title%';
    this.group_settings = {
        bbcode_status: false
    };
    for (var i in data)
    {
        if(typeof(data[i]) != 'function')
            this[i] = data[i];
    }

    this.init = function ()
    {
        this.set_status(this.status);
    }

    this.get_name = function()
    {
        if(this.mask == '')
        {
            return this.name;
        }
        else
        {
            return this.mask.replace(/\*/g, this.name);
        }
    }

    this.set_status = function (string)
    {
        this.status = (
            new MessageProcessing(string, {
                smiles: false,
                bbcode: this.group_settings.bbcode_status,
                images: false,
                play: false,
                max_words_length: 40
            })
            ).process().message();
        $('.user_' + this.id + ' .status').html(this.status)
        .addClass('backlight', 1000).removeClass('backlight', 1000);
    }

    this.set_avatar = function (src)
    {
        this.avatar = src;
        $('.user_' + this.id + ' .avatar').attr({
            'src': src
        });
    }

    this.init();
}

function SoundClass(sounds, options)
{
    var $sound = this;
    options = $.extend({}, options);
    this.players = {};
    this.enable = true;

    // media links init
    $('a.play').live('click', function () {
        Sound.play($(this).attr('player'), $(this).attr('src'));
    });


    this.init = function ()
    {
        for(var i in sounds)
        {
            this.create(sounds[i]);
        }
    }

    this.create = function (obj)
    {
        if(this.players[obj.play] == undefined)
        {
            var audio = new Audio(obj.src, options);
            this.players[obj.play] = audio;
        }
    }

    this.play = function (name, src)
    {
        if(this.enable && this.players[name] != undefined)
        {
            var player = this.players[name];
            try
            {
                player.play(src);
            }
            catch (e) {}
        }
    }

    this.stop = function (name)
    {
        if(this.players[name] != undefined)
        {
            var player = this.players[name];
            try
            {
                player.stop();
            }
            catch (e) {}
        }
    }

    this.stop_all = function ()
    {
        for(var i in this.players)
        {
            try
            {
                this.players[i].stop();
            }
            catch (e) {}
        }
    }

    this.init();
}

function set_room(id)
{
    var room = Rooms.by_id(id);
    var pass = '';
    if(room.lock)
        pass = window.prompt(Lang.room_password, '');
    Chat.send({
        type: 'set_room',
        room_id: id,
        password: pass
    });
}


function load_image(url)
{
    this.load = null;
    this.width = 0;
    this.height = 0;
    var self = this;
    var img = new Image();
    img.onload = function () {
        self.width = this.width;
        self.height = this.height;
        if(self.load != null)
            self.load(self);
    }
    img.src = url;
}

function KeepSize(elements, options) {
    var options = jQuery.extend({
        saveSize: 20,
        speed: 50,
        wrap: $("#wrap")
    }, options);

    var chat = $(elements[0]);
    var i = 0;
    this.Resize = function () {
        var windowHeight = $(window).height();
        var wrapHeight = options.wrap.height();
        var chatHeight = chat.height();

        var appendHeight = (windowHeight - wrapHeight - options.saveSize);
        var newHeight = chatHeight + appendHeight;

        if(Math.abs(appendHeight) > 2) {
            for (i = 0; i < elements.length; i++)
                $(elements[i]).height(newHeight);
        }

        if(i++>10) clearInterval(timer);
    }
    $(window).resize(this.Resize);
    var timer = setInterval(this.Resize, options.speed);
    this.Resize();
}

/**
* Get the value of a cookie with the given name.
*
* @example $.cookie('the_cookie');
* @desc Get the value of a cookie.
*
* @param String name The name of the cookie.
* @return The value of the cookie.
* @type String
*
* @name $.cookie
* @cat Plugins/Cookie
* @author Klaus Hartl/klaus.hartl@stilbuero.de
*/
jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options = $.extend({}, options); // clone object since it's unexpected behavior if the expired property were changed
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // NOTE Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};


/**
* Insert content at caret position (converted to jquery function)
* @link http://alexking.org/blog/2003/06/02/inserting-at-the-cursor-using-javascript
*/
$.fn.insertAtCaret = function (myValue) {
    return this.each(function(){
        //IE support
        if (document.selection) {
            this.focus();
            sel = document.selection.createRange();
            sel.text = myValue;
            this.focus();
        }
        //MOZILLA/NETSCAPE support
        else if (this.selectionStart || this.selectionStart == '0') {
            var startPos = this.selectionStart;
            var endPos = this.selectionEnd;
            var scrollTop = this.scrollTop;
            this.value = this.value.substring(0, startPos)
            + myValue
            + this.value.substring(endPos, this.value.length);
            this.focus();
            this.selectionStart = startPos + myValue.length;
            this.selectionEnd = startPos + myValue.length;
            this.scrollTop = scrollTop;
        } else {
            this.value += myValue;
            this.focus();
        }
    });

};

