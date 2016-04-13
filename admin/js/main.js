$(document).ready(function() {
    $(".navelem").hover(
        function () {
            if(!$(this).is(".navselect")) {
                $(this).addClass("navover");
            }
        },
        function () {
            if(!$(this).is(".navselect")) {
                $(this).removeClass("navover");
            }
        });

    $(".contable tr").hover(
        function () {
            $(this).addClass("over");
        },
        function() {
            $(this).removeClass("over");
        });
});

function OrderSerialise(table)
{
    divs = table.get();
    var a = [];
    for (var i = 0; i < divs.length; i++) {
        var div = $(divs[i]);
        if(!div.hasClass('top'))
            a.push(div.attr('id') + '=' + i);
    }
    return a.join(";");
}

function OrderTable(table, url) {
    $(table).tableDnD({
        onDrop:
        function() {
            var orderline = OrderSerialise($(table + " tr"));
            $.post(url, {
                'orderline': orderline
            },
            function (r) { },
            'json');
        },
        dragHandle: "dragHandle"
    });
}

$.fn.crud_delete = function (url) {
    var elem = this;
    if(window.confirm(Lang.delete_this)) {
        $.getJSON(url, function (r) {
            if(r.success) {                
                elem.fadeOut();
            } else {
                window.alert(r.message);
            }
        });
    }
}
