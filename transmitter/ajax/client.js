function AjaxTransmitterClass(host, delay)
{
    var $transmitter = this;
    this.onready = null;
    this.onmessage = null;
    this.host = host;
    this.delay = delay;
    this.last_msg_id = 0;
    var ids = [];
    var interval;

    var button = $('#exit');
    var exit_href = button.attr('href');
    button.attr('href', '#');
    button.click(function () {
        $.get($transmitter.host + 'act=logout' + random(), function () {
            window.document.location.href = exit_href;
        });
        return false;
    });

    function push_ids(id)
    {
        ids.push(id);
        if(ids.length > 30)
            ids.shift();
    }

    function in_ids(id)
    {
        for(var i in ids)
            if(ids[i] == id)
                return true;
        return false;
    }

    this.init = function ()
    {
        this.onready();
        this.poll();
        interval = setInterval(function () {
            $transmitter.poll.call($transmitter);
        }, this.delay);
    }

    this.poll = function ()
    {
        getJSON(this.host + 'act=load&last=' + this.last_msg_id, function (data) {
            for(var i in data.msg)
            {
                if(!in_ids(data.msg[i].msg_id))
                {
                    push_ids(data.msg[i].msg_id);
                    $transmitter.onmessage(data.msg[i]);                    
                }
            }
            $transmitter.last_msg_id = data.last || $transmitter.last_msg_id;
        });
    }

    this.open = function ()
    {
        
    }

    this.send = function (msg)
    {
        $.post(this.host + 'act=send' + random(), msg, function (data) {
            $transmitter.poll();
        }, "json");
    }
}


