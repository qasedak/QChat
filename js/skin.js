function UIClass()
{
    var $ui = this;
    this.chat = null;
    this.online = null;
    this.textarea = null;
    this.button = null;
    this.connecting_div = null;
    
    this.init = function () {
        $ui.set_chat("#chat");
        $ui.set_online("#list");
        $ui.set_textarea("#msg");
        $ui.set_button("#send");
        $ui.connecting("#chat_is_loading");
        $ui.message_quoting(".time", "div.message");
        // Color init
        $ui.color_picker('menu_colors', {
            default_color: '#000000'
        });
        $('#colors').menu('#menu_colors');

        // Smiles init
        $("#smiles").one('click', function () {
            $ui.smiles('menu_smiles', Smiles.main(), Smiles.path());
            $("#smiles").menu("#menu_smiles");
        });
        
        // Switch images
        $('[on]').each(function () {
            var $this = $(this);
            $this.attr('src', $this.attr('on'));
        });

        // Switch sound init
        $('#switch_sounds').click(function () {
            var $this = $(this);
            var $img = $this.find('img');
            var turnon = $this.data('turnon');
            if(turnon == undefined) turnon = true;
            turnon = !turnon;
            
            if(turnon)
            {
                $img.attr('src', $img.attr('on'));
                Sound.enable = true;
            }
            else
            {
                $img.attr('src', $img.attr('off'));
                Sound.enable = false;
                Sound.stop_all();
            }
            
            $this.data('turnon', turnon);
        });
       

        // Menu init
        $ui.user_menu();
        $ui.settings_menu();
        $ui.bbcodes_menu();
        $ui.media_menu();

        // Over UI initialisation
        $(".buttons").buttons();
        $("[tooltip]").tooltip();
        

        // Popup Window
        $ui.popup = new PopupClass();

        // Moderator
        $ui.moderator();

        // Browser's hack.
        if(!$.browser.opera)
            $("#list").draggable({
                axis: 'y',
                revert: true
            });
        $('.button,.sep').table_cell();

        // #-href
        $('[href="#"]').live('click', function () {
            return false;
        });

        if($.browser.msie)
        {
            window.onerror = function ()
            {
                return true;
            }
        }
    }

    this.view = function (name)
    {
        return $("#view_" + name);
    }

    this.set_chat = function (hold)
    {
        this.chat = $(hold);
    }

    this.set_online = function (hold)
    {
        this.online = $(hold);
    }

    this.set_textarea = function (text)
    {
        this.textarea = $(text);
        this.textarea.bind('keydown', 'return', function (evt) {
            var text = $ui.textarea.val();
            $ui.textarea.val('');
            Chat.send_message(text);
            if($.browser.opera)
            {
                // I could not clear the event for opera
                setTimeout(function () {
                    $ui.textarea.val('');
                }, 1);
            }
            return false;
        });
    }

    this.set_button = function (ok)
    {
        this.button = $(ok);
        this.button.mouseup(function () {
            var text = $ui.textarea.val();
            $ui.textarea.val('');
            $ui.textarea.focus();
            Chat.send_message(text);
            return false;
        });
    }

    this.connecting = function (div)
    {
        if(div != undefined)
        {
            this.connecting_div = $(div);
        }
        if(this.connecting_div != undefined)
        {
            function get_pos()
            {
                var at = $ui.chat;
                var chat_size = {
                    width: at.width(),
                    height: at.height()
                };
                var chat_pos = at.position();
                var pos = {
                    top: chat_pos.top + chat_size.height/2 + 'px',
                    left: chat_pos.left + chat_size.width/2 + 'px'
                };
                return pos;
            }
            this.connecting_div.css(get_pos()).show();
        }
    }

    this.connected = function()
    {
        if(this.connecting_div != null)
        {
            this.connecting_div.hide();
        }
    }
    
    this.message_quoting = function (time, message)
    {
        var self = this;
        self.remove_backlight = function ()
        {
            if(self.last_quote != undefined)
                self.last_quote.removeClass('backlight');
            if(self.last_message != undefined)
                self.last_message.removeClass('backlight');
        }
        $(time).live('click', function ()
        {
            var msg = $(this).parent(message);
            var quote = '[quote' +
            ' msg="' + msg.attr('id').replace(/[^0-9]*/, '') + '"' +
            ' time="' + msg.find('.time').first().text() + '"' +
            ' name="' + msg.find('.name').first().text() + '"' +
            ']' + msg.find('.text').text() +
            '[/quote]';
            self.textarea.insertAtCaret(' ' + quote + ' ');
        });
        $('blockquote').live('click', function ()
        {
            self.remove_backlight();
            self.last_quote = $(this);
            self.last_quote.addClass('backlight');
            var msg_id = self.last_quote.attr('ref');
            var msg = $('#message_' + msg_id);
            if(msg.length)
            {
                self.last_message = msg;
                self.last_message.addClass('backlight');                               
            }
            $('body').one('click', self.remove_backlight); 
        });        
    }

    this.toggle_online = function (img, src1, src2)
    {
        if($('.online').is(':visible'))
        {
            $('.online').hide();
            $(img).attr('src', src1);
        }
        else
        {
            $('.online').show();
            $(img).attr('src', src2);
        }
    }
    
    this.color_menu = function (id, callback)
    {
        var menu = $('<table class="menu menu_colors" id="'+id+'">');
        var line_size = 7;
        var colors = [
        "#ffffff",        "#cccccc",        "#c0c0c0",        "#999999",        "#666666",        "#333333",        "#000000",

        "#ffcccc",        "#ff6666",        "#ff0000",        "#cc0000",        "#990000",        "#660000",        "#330000",

        "#ffcc99",        "#ff9966",        "#ff9900",        "#ff6600",        "#cc6600",        "#993300",        "#663300",

        "#ffff99",        "#ffff66",        "#ffcc66",        "#ffcc33",        "#cc9933",        "#996633",        "#663333",

        "#ffffcc",        "#ffff33",        "#ffff00",        "#ffcc00",        "#999900",        "#666600",        "#333300",

        "#99ff99",        "#66ff99",        "#33ff33",        "#33cc00",        "#009900",        "#006600",        "#003300",

        "#99FFFF",        "#33FFFF",        "#66CCCC",        "#00CCCC",        "#339999",        "#336666",        "#003333",

        "#CCFFFF",        "#66FFFF",        "#33CCFF",        "#3366FF",        "#3333FF",        "#000099",        "#000066",

        "#CCCCFF",        "#9999FF",        "#6666CC",        "#6633FF",        "#6600CC",        "#333399",        "#330099",

        "#FFCCFF",        "#FF99FF",        "#CC66CC",        "#CC33CC",        "#993399",        "#663366",        "#330033"
        ];
        var html = '';
        for(var i = 0; i < colors.length;)
        {
            html += '<tr>';            
            for(var j = 0; j < line_size; j++, i++)
            {
                html += '<td><a href="#" ' 
                + (callback != undefined ? callback(colors[i]) : '')
                + ' style="background-color:'+colors[i]+'"></a></td>';
            }
            html += '</tr>';
        }
        menu.append(html);
        return menu;
    }


    this.color_picker = function (id, options)
    {
        var options = $.extend({
            default_color: "#000000"
        }, options);

        var menu = $ui.color_menu(id, function (color) {
            return (options.default_color == color?'class="current"':'');
        });
        
        menu.appendTo('body');
        menu.find('a').each(function () {
            $(this).click(function () {
                menu.find('.current').removeClass('current');
                $(this).addClass('current');
                var color = rgb2hex($(this).css('background-color'));
                if(options.default_color == color)
                    Chat.color = null;
                else
                    Chat.color = color;
                $ui.textarea.css({
                    'color': Chat.color
                });
            });
        });
    }

    this.smiles = function (id, smiles, path)
    {
        var line_size = 7;
        var menu = $('<table class="menu" id="'+id+'">');
        var html = '';
        var i = 0;
        for(var key in smiles)
        {
            if(i == 0)
            {
                html += '<tr>';
            }
            html += '<td><a href="#" title="' + key.replace(/"/g, '\"') + '"><img src="' + path + smiles[key] + '" alt=""></a></td>';
            i++;
            if(i >= line_size)
            {
                html += '</tr>';
                i = 0;
            }
        }
        menu.append(html);
        menu.appendTo('body');
        menu.find('a').each(function () {
            $(this).click(function () {
                var key = $(this).attr('title');
                $ui.textarea.insertAtCaret(' ' + key + ' ');
            });
        });
    }
    
    this.media_menu = function () 
    {
        var button_id = 'media';
        var menu_id = 'menu_media';
        // Media init
        $('#' + button_id).one('click', function () {            
            $.getScript('media/media.js', function () {
                // MEDIA is loaded
                // Create menu html code
                var html = '<ul class="menu" id="' + menu_id + '">';
                for(var i in MEDIA)
                {
                    if(MEDIA[i].menu == undefined)
                        html += '<li><a href="#" media="' + MEDIA[i].file + '">' + MEDIA[i].title + '</a></li>';
                    else
                    {
                        html += '<li class="submenu"><a href="#"><div>' + MEDIA[i].title + '</div></a>';
                        html += '<ul class="menu">';
                        for(var j in MEDIA[i].menu)
                        {
                            html += '<li><a href="#" media="' + MEDIA[i].menu[j].file + '">' + MEDIA[i].menu[j].title + '</a></li>';
                        }
                        html += '</ul>';
                        html += '</li>';
                    }
                }
                html += '</ul>';
                $(document.body).append(html);
                $('[media]').click(function () {
                    var file = $(this).attr('media');
                    var title = $(this).text();
                    $ui.textarea.insertAtCaret(' [media=' + file + ']' + title + '[/media] ');
                });
                $('#' + button_id).menu('#' + menu_id).trigger('click');
            });
        });
    }

    this.user_menu = function ()
    {
        var $user_menu = this;
        this.user_id = 0;
        this.user_name = '%username%';

        $('#chat .name, #online .name').live('click', function () {
            $ui.textarea.insertAtCaret(' ' + $(this).text() + ', ');
        });

        $(".user_menu").menu("#menu_user", {
            onshow: function () {
                $user_menu.user_id = $(this).attr('user_id');
                $user_menu.user_name = $(this).text();
                $('#ignore').attr('checked', Users.is_ignore($user_menu.user_id));
            }
        });

        $('#personal_for').click(function () {
            var user = Users.by_id($user_menu.user_id);
            if(user != null)
            {
                Chat.personal = user.id;
                $('#personal').find('.name').text(user.name);
                $('#personal').show('blind',{
                    direction: "horizontal"
                });
                $ui.textarea.focus();
            }
        });

        $('#personal').find('.button').click(function () {
            Chat.personal = null;
            $('#personal').hide('blind',{
                direction: "horizontal"
            });
            $ui.textarea.focus();
        });

        $('#ignore').click(function () {
            var ignore = $(this).attr('checked');
            if(ignore)
                Users.add_ignore($user_menu.user_id);
            else
                Users.remove_ignore($user_menu.user_id);
        });

        $('#kill').click(function () {
            Chat.send({
                type: 'kill',
                user_id: $user_menu.user_id
            });
        });

        $('#ban').click(function () {
            $ui.popup.show(Lang.moderator, 'moderator.php?act=ban&id='+$user_menu.user_id);
        });

        $('#silence').click(function () {
            Chat.send({
                type: 'silence',
                user_id: $user_menu.user_id
            });
        });
    }
    
    this.settings_menu = function () 
    {
        $("#settings").menu("#menu_settings");
        
        $('#scrollable').click(function () {
            var scroll = $(this).attr('scroll');
            scroll = scroll == 'true' ? 'false' : 'true';
            $(this).attr('scroll', scroll);
            if(scroll == 'true')
            {
                Messages.scrollable = true;
                Messages.scroll();
            }
            else
            {
                Messages.scrollable = false;
            }
        });

        $('.status_list').click(function () {
            Chat.send({
                'type': 'status',
                'status': $(this).text()
            });
        });

        $('.your_status').click(function () {
            var status = window.prompt(Lang.your_status, '');
            Chat.send({
                'type': 'status',
                'status': status
            });
        });

        $('#edit_settings').click(function () {
            $ui.popup.show(Lang.user_settings, 'usersettings.php');
        });

        $('#edit_avatar').click(function () {
            $ui.popup.show(Lang.avatar, 'avatar.php');
        });
    }

    this.bbcodes_menu = function ()
    {
        $("#bbcodes").menu("#menu_bbcodes");

        $('.bbcode').click(function () {
            $ui.textarea.insertAtCaret($(this).attr('bbcode'));
        });
    }

    this.moderator = function ()
    {
        if($('#moderator').length)
        {
            $('#moderator').click(function () {
                $ui.popup.show(Lang.moderator, 'moderator.php');
            });
        
            $('.message').live('mouseover', function () {
                if(!$(this).data('appended'))
                {
                    $(this).css({
                        position: 'relative'
                    })
                    .append('<a href="#" class="delete_message">x</a>')
                    .data('appended', true);
                }
                $(this).find('.delete_message').show();
            });
            $('.message').live('mouseout', function () {
                $(this).find('.delete_message').hide();
            });

            $('.delete_message').live('click', function () {
                var id = $(this).parent().attr('id').replace(/[^0-9]*/, '');
                Chat.send({
                    type: 'delete_message',
                    msg_id: id
                });
            });
        }
    }

    // call init
    this.init();
}

function PopupClass()
{
    var popup = $('.popup');

    popup.draggable({
        handle: '.top'
    });

    popup.bind("mouseenter", function() {
        popup.stop().animate({
            opacity: 1
        }) ;
    });

    popup.bind("mouseleave", function() {
        popup.stop().animate({
            opacity: 0.5
        }) ;
    });

    popup.find('.close_popup').click(function () {
        popup.hide();
    });

    popup.find('.expand_popup').click(function () {
        popup.find('.content').toggler(this);
    });

    this.show = function (title, url)
    {        

        var at = $('body');
        var popup_size = {
            width: popup.width(),
            height: popup.height()
        };
        var body_size = {
            width: at.width(),
            height: at.height()
        };

        var css = {
            top: body_size.height/2 - popup_size.height/2 + 'px',
            left: body_size.width/2 - popup_size.width/2 + 'px'
        };

        popup.css(css).find('iframe').attr('src', url);
        popup.find('.title').text(title);
        popup.show();
    }
}


//  RGB to HEX format
function rgb2hex(rgb) {
    if(rgb.match(/^#[a-f0-9]{6}$/i))
        return rgb;
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    function hex(x) {
        return ("0" + parseInt(x).toString(16)).slice(-2);
    }
    return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}

// table-cell IE hack.
$.fn.table_cell = function ()
{
    if($.browser.msie && $.browser.version.substr(0,1) <= 7) {
        $(this).each(function () {
            var elem = $(this);           
            elem.css({
                'display': 'block',
                'float': 'left'
            });
        });
    }
    return this;
}

/**
 * Z-Index var
 */
$.z_index = 1;
$.zIndex = function ()
{
    $.z_index += 1;
    return $.z_index;
}

/**
 * Buttons
 */
$.fn.buttons = function (options) {
    var opt = $.extend({
        button: 'button',
        sep: 'sep'
    }, options);
    var normal = function(button) {
        button.removeClass("over");
        button.removeClass("down");
    }
    var over = function(button) {
        button.addClass("over");
    }
    var down = function(button) {
        button.removeClass("over");
        button.addClass("down");
    }
    var connect = function (_from, _to) {
        var from, to;
        from = _from;
        to = _to;
        
        from.hover(function () {
            over(to);
        },
        function () {
            normal(to);
        })
        .mousedown(function () {
            down(to);
        })
        .mouseup(function () {
            normal(to);
        })
        .mouseleave(function () {
            normal(to);
        })
        .focus(function () {
            $(this).blur();
        });
    }
    this.each(function () {
        // buttons events connect
        $(this).find('.' + opt.button)
        .last().addClass('last').end()
        .first().addClass('first').end()
        .each(function () {
            var $this = $(this);
            connect($this, $this);
        });

        // one
        $(this).find('.first.last').addClass('one').removeClass('first').removeClass('last')
        .before('<div class="' + opt.button + ' first"></div>')
        .after('<div class="' + opt.button + ' last"></div>');

        // one events
        var one = $(this).find('.one');
        if(one.length > 0) {
            connect(one, one.prev('.first'));
            connect(one, one.next('.last'));
        }
    });
    return this;
}

$.fn.roomtoggle = function () {
    return this.each(function(i){
        var room = $(this);
        var box_content = room.find(".box_content");
        var box = room.find(".box");
        if(box_content.is(":hidden")) {
            box.removeClass("round_bottom");
            box_content.slideToggle("slow");
            room.find(".toggler").addClass("toggler_collapse");
            room.find(".toggler").removeClass("toggler_expand");
        } else {
            box_content.slideToggle("slow", function () {
                box.addClass("round_bottom");
            });
            room.find(".toggler").removeClass("toggler_collapse");
            room.find(".toggler").addClass("toggler_expand");
        }
    });
}

$.fn.toggler = function (button) {
    if(button == undefined)
        button = false;
    else
        button = $(button);
    return this.each(function ()
    {
        var toggle = $(this);
        if(toggle.is(':hidden'))
        {
            toggle.slideToggle("slow");
            if(button)
                button.removeClass("expand");
        }
        else
        {
            toggle.slideToggle("slow");
            if(button)
                button.addClass("expand");
        }
    });
}

jQuery.tooltip = { 
    hideTimeout: false,
    show: function () {
        if(!Settings.show_tooltip)
            return;
		
        var elem = $(this);
        var arrow_offset = {
            top: 8,
            left: 16
        };
        var wrapSize = {
            width: $("#wrap").width(),
            height: $("#wrap").height()
        };
        var offset = getOffset(elem.get(0));
        var size = {
            width: elem.outerWidth(),
            height: elem.outerHeight()
        };
	    
        if(elem.attr('tooltip') == "")
            return;
	    
        $("#tooltip span").html( elem.attr('tooltip').replace(/\\n/g, '<br>') );
	    
        clearTimeout($.tooltip.hideTimeout);
	    
        // set to right
	    
        $("#tooltip_arrow")
        .removeClass("tooltip_arrow_left")
        .removeClass("tooltip_arrow_right")
        .removeClass("tooltip_arrow_up")
        .removeClass("tooltip_arrow_down")
        .addClass("tooltip_arrow_up")
        .addClass("tooltip_arrow_left");
	    
	    
        var pos = {
            top: size.height + offset.top + arrow_offset.top,
            left: offset.left + size.width/2 - arrow_offset.left
        };

        //$("#tooltip").css({top: pos.top, left: pos.left}).show();
	    
        var tooltip_size = {
            width: $("#tooltip").width(),
            height: $("#tooltip").height()
        };
	    
        if(pos.left + tooltip_size.width > wrapSize.width || elem.hasClass("tooltip_left")) // set to left
        {
            $("#tooltip_arrow").removeClass("tooltip_arrow_left").addClass("tooltip_arrow_right");
		
            pos.left = offset.left - tooltip_size.width + size.width/2 + arrow_offset.left;
        }
	    
        if(pos.top + tooltip_size.height > wrapSize.height || elem.hasClass("tooltip_top")) // set to top
        {
            $("#tooltip_arrow").removeClass("tooltip_arrow_up").addClass("tooltip_arrow_down");
		
            pos.top = offset.top - tooltip_size.height - arrow_offset.top;
        }
	    
        $("#tooltip").css({
            top: pos.top,
            left: pos.left,
            'z-index': $.zIndex()
        }).fadeIn('fast');
    },
    hide:  function () {
        $.tooltip.hideTimeout = setTimeout(function () {
            $("#tooltip").fadeOut("fast");
        }, 500);
    },
    click: function () {
        $("#tooltip").hide();
    }
};
jQuery.fn.tooltip = function () {
    $("body").append('<div id="tooltip"><div id="tooltip_relative"><span></span><div id="tooltip_arrow" class="tooltip_arrow_up tooltip_arrow_left"></div></div></div>');
    $(this).live("mouseover", $.tooltip.show);
    $(this).live("mouseout", $.tooltip.hide);
    $(this).live("click", $.tooltip.click);
}


/**
 * Png Animation JS
 */

jQuery.fn.PngAnimation = function (url, options) {
    var options = jQuery.extend({
        count: 10,
        height: 16,
        speed: 100
    }, options);
    return this.each(function () {
        var elem = $(this);
        if(url == "") {
            clearInterval(this.timer);
            elem.css({
                "background-image": elem.data("img"),
                "backgroundPosition": '0px 0px'
            });
        } else {
            elem.data("img", elem.css("background-image"));
            elem.css({
                "background-image": "url("+url+")"
            });
            var i = 0;
            this.timer = setInterval(function () {
                if(i >= options.count) i = 0;
                elem.css({
                    "backgroundPosition": '0px -' + i * options.height + 'px'
                });
                i++;
            }, options.speed);
        }
    });    
}


/**
 * Menu JS
 */

jQuery.menu = {
    show: function (_elem, _menu) {
        var menu = $(_menu);
        var elem = $(_elem);
            
        var wrapSize = {
            width: $("#wrap").width(),
            height: $("#wrap").height()
        };
        var offset = getOffset(elem.get(0));
        var size = {
            width: elem.outerWidth(),
            height: elem.outerHeight()
        };
	    
        var pos = {
            top: size.height + offset.top,
            left: offset.left
        };
	    
        var menu_size = {
            width: menu.width(),
            height: menu.height()
        };
	    
        if(pos.top + menu_size.height > wrapSize.height || elem.hasClass("menu_top")) // set to top
        {
            pos.top = offset.top - menu_size.height;
        }
        menu.css({
            top: pos.top,
            left: pos.left,
            'z-index': $.zIndex()
        }).fadeIn('fast');
	    
        if(menu.data('showed') != true) {
            $(document).one('mouseup', function () {
                $.menu.hide(_menu);
            });
        }
	    
        menu.data('showed', true);
    },
    hide: function (_menu) {
        var menu = $(".menu");
        menu.fadeOut('fast');
        menu.data('showed', false);
    },
    showsubmenu: function (_elem) {
        var elem = $(_elem);
        var menu = $(elem.find(".menu").get(0));
        var wrapSize = {
            width: $("#wrap").width(),
            height: $("#wrap").height()
        };
        var offset = elem.position();
        var size = {
            width: elem.outerWidth(),
            height: elem.outerHeight()
        };
	    
        var pos = {
            top: offset.top,
            left: offset.left + size.width - 5
        };

        var offsetGlobal = getOffset(elem.get(0));
        var menu_size = {
            width: menu.width(),
            height: menu.height()
        };
 	    
        if(offsetGlobal.top + menu_size.height > wrapSize.height || elem.hasClass("menu_top")) // set to top
        {
            pos.top = offset.top - menu_size.height + size.height;
        }

        if(offsetGlobal.left + menu_size.width + size.width > wrapSize.width) // set to left
        {
            pos.left = -menu_size.width;
        }
            
        menu.css({
            top: pos.top,
            left: pos.left
        }).fadeIn('fast');
 
    },
    hidesubmenu: function (_elem) {
        var elem = $(_elem);
        var menu = elem.find(".menu");
        menu.fadeOut('fast');
    }
}
jQuery.fn.menu = function (menu, options) {
    var opt = $.extend({
        onshow: function () {}
    }, options);
    $(this).live("click", function () {
        $.menu.show(this, menu);
        opt.onshow.call(this);
        return false;
    });
    $(menu).find("li.submenu").hover(function () {
        $.menu.showsubmenu(this);
    },
    function () {
        $.menu.hidesubmenu(this);
    });
    return this;
}


/**
 * Over JS for Skin
 */

function getOffset(elem) {
    if (elem.getBoundingClientRect) {
        // "правильный" вариант
        return getOffsetRect(elem)
    } else {
        // пусть работает хоть как-то
        return getOffsetSum(elem)
    }
}

function getOffsetSum(elem) {
    var top=0, left=0
    while(elem) {
        top = top + parseInt(elem.offsetTop)
        left = left + parseInt(elem.offsetLeft)
        elem = elem.offsetParent
    }

    return {
        top: top,
        left: left
    }
}

function getOffsetRect(elem) {
    // (1)
    var box = elem.getBoundingClientRect()

    // (2)
    var body = document.body
    var docElem = document.documentElement

    // (3)
    var scrollTop = window.pageYOffset || docElem.scrollTop || body.scrollTop
    var scrollLeft = window.pageXOffset || docElem.scrollLeft || body.scrollLeft

    // (4)
    var clientTop = docElem.clientTop || body.clientTop || 0
    var clientLeft = docElem.clientLeft || body.clientLeft || 0

    // (5)
    var top  = box.top +  scrollTop - clientTop
    var left = box.left + scrollLeft - clientLeft

    return {
        top: Math.round(top),
        left: Math.round(left)
    }
}






