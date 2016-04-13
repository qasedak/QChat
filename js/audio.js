// Audio object
function Audio(src, options)
{
    if(window.audio_count == undefined)
        window.audio_count = 0;
    else
        window.audio_count++;

    var loaded = false;

    options = $.extend({
        swf: 'Player.swf'
    }, options);

    var isFlashLite = function()
    {
        if (!window.navigator || !window.navigator.mimeTypes) return false;
        var mimeType = window.navigator.mimeTypes["application/x-shockwave-flash"];
        if (!mimeType || !mimeType.enabledPlugin || !mimeType.enabledPlugin.filename) return false;
        return mimeType.enabledPlugin.filename.match(/flashlite/i) ? true : false;
    };

    var container = document.createElement("div");
    container.id = "sound_container_" + window.audio_count;
    // Hides Flash box. We cannot use display: none or visibility: hidden because it prevents
    // Flash from loading at least in IE. So we move it out of the screen at (-100, -100).
    // But this even doesn't work with Flash Lite (e.g. in Droid Incredible). So with Flash
    // Lite, we put it at (0, 0). This shows 1x1 box visible at left-top corner but this is
    // the best we can do as far as we know now.
    container.style.position = "absolute";
    if (isFlashLite()) {
        container.style.left = "0px";
        container.style.top = "0px";
    } else {
        container.style.left = "-100px";
        container.style.top = "-100px";
    }
    var holder = document.createElement("div");
    holder.id = "sound_flash_" + window.audio_count;
    container.appendChild(holder);
    document.body.appendChild(container);
    // See this article for hasPriority:
    // http://help.adobe.com/en_US/as3/mobile/WS4bebcd66a74275c36cfb8137124318eebc6-7ffd.html
    swfobject.embedSWF(
        options.swf, holder.id,
        "1" /* width */, "1" /* height */, "9.0.0" /* SWF version */,  null, {},
        {
            quality: "high",
            allowScriptAccess: "always"
        }
        );    

    var id = holder.id;
    var movie = function(id)
    {
        var movie = null;
        if ($.browser.msie) {
            movie =  window[id];
        }
        else {
            movie = document[id];
        }
        return movie;
    };    

    this.play = function (new_src)
    {
        var player = movie(id);
        if(new_src != undefined && new_src != src)
        {
            src = new_src;
            loaded = false;
        }

        // Always load mp3 for dump IE
        if($.browser.msie)
            loaded = false;

        if(loaded)
        {
            player.play();
        }
        else
        {
            player.load(src);
            loaded = true;
        }
    }

    this.stop = function ()
    {
        var player = movie(id);
        player.stop();
    }
}
