//get dobord_session_id from url
var value = gup('dobord_session_id',window.location.href)

function gup( name, url ) {
    if (!url) url = location.href;
    name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    var regexS = "[\\?&]"+name+"=([^&#]*)";
    var regex = new RegExp( regexS );
    var results = regex.exec( url );
    return results == null ? null : results[1];
}

// check exist dobord_session_id & set cookie
value !== null?setCookie('dbrdsid',value,1):''


function setCookie(name,value,exHour)
{
    var d = new Date();
    d.setTime(d.getTime() + (exHour * 60 * 60 * 1000));
    var c_value=escape(value) + ((d==null)
            ? "" : "; expires="+d.toUTCString())
        + "; path=/";
    document.cookie=name + "=" + c_value;
}