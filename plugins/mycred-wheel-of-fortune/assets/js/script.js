jQuery(document).ready(function() {
    jQuery('.fortunewheel-fortunewheel-bar span.cancel').on('click', function() {
        jQuery('.fortunewheel-fortunewheel-bar').hide();
        setCookie_fortunewheel('fortunewheel-cross-couponbar', 1);
    });
});

function setCookie_fortunewheel(cname, cvalue, exptime, duration_type) {

    var d = new Date();
    if (duration_type === undefined) {
        d.setTime(d.getTime() + (exptime * 24 * 60 * 60 * 1000));
    } else if( duration_type == 'hour' ) {
        d.setTime(d.getTime() + (exptime * 60 * 60 * 1000) );
    } else if( duration_type == 'day' ) {
        d.setTime(d.getTime() + (exptime * 24 * 60 * 60 * 1000));
    }
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
