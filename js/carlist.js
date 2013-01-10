
function showAutocheck(element,vin)
{
    $.getJSON("/public/", {mod:"autocheck", sw:"get", vincode:vin}, function(json)
    {

        var userAgent = $.browser;

            $.fancybox({
                //'orig'			: $(this),
                'padding'		: 0,
                'href'			: element.href,
                'transitionIn'	: 'elastic',
                'transitionOut'	: 'elastic'
            });
    });
}