function show(nos)
{    
    id="#"+nos;
    $.fancybox({
        'autoScale': true,
        'transitionIn': 'elastic',
        'transitionOut': 'elastic',
        'speedIn': 500,
        'speedOut': 300,
        'autoDimensions': true,
        'centerOnScroll': true,
        'href' : id
    });
}
function pinit(nos)
{        
    var reason = "";         
    reason += validateEmail(document.getElementById('email_pin_'+nos)); 
    if (reason != "") 
        {
        reason+="The required field has not been filled in.\n"
        alert("Some fields need correction:\n" + reason);
        return false;
    }
    else
        {
        email="/?u="+encodeURI(document.getElementById('email_pin_'+nos).value);
        url=document.getElementById('url_pin_'+nos).value;
        desc=document.getElementById('desc_pin_'+nos).value;
        src=document.getElementById('src_pin_'+nos).value;
        post_url=document.getElementById('post_url_pin_'+nos).value;
        pin_url=url+post_url+email+'&media='+src+desc;
        top.location.href=pin_url;    
        $.fancybox.close();       
    }
}
function pinit_without(nos)
{                
    url=document.getElementById('url_pin_'+nos).value;
    desc=document.getElementById('desc_pin_'+nos).value;
    src=document.getElementById('src_pin_'+nos).value;
    post_url=document.getElementById('post_url_pin_'+nos).value;
    pin_url=url+post_url+'&media='+src+desc;
    top.location.href=pin_url;    
    $.fancybox.close();       
}
function trim(s)
{
    return s.replace(/^\s+|\s+$/, '');
} 

function validateEmail(fld) {
    var error="";
    var tfld = trim(fld.value);                        // value of field with whitespace trimmed off
    var emailFilter = /^[^@]+@[^@.]+\.[^@]*\w\w$/ ;
    var illegalChars= /[\(\)\<\>\,\;\:\\\"\[\]]/ ;

    if (fld.value == "") {
        fld.style.background = 'Yellow';
        error = "You didn't enter an email address.\n";
    } else if (!emailFilter.test(tfld)) {              //test email for illegal characters
        fld.style.background = 'Yellow';
        error = "Please enter a valid email address.\n";
    } else if (fld.value.match(illegalChars)) {
        fld.style.background = 'Yellow';
        error = "The email address contains illegal characters.\n";
    } else {
        fld.style.background = 'White';
    }
    return error;
}