$(document).ready(function() {
    // Remove no-js tag if javascript is enabled
    $('html').removeClass('no-js');
    $( '.datepicker' ).datepicker({
        dateFormat: 'yy-mm-dd',
        numberOfMonths: 3,
        showButtonPanel: true
    });

    function animateDropDown(obj) {
        $this = $(obj);

        $('.sublinks').stop(false, true).hide();
        $('.subdroplinks').stop(false, true).hide();

        var submenu = $this.next();

        submenu.css({
            position:'absolute',
            top: ($this.offset().top + $this.height()) + 'px',
            left: $this.offset().left + 'px',
            zIndex: 999
        });

        submenu.stop().slideDown(300);

        submenu.mouseleave(function(){
            $(this).slideUp(300);
        });

        $this.parent().mouseleave(function(){
            submenu.slideUp(300);
        });

    }

    $('.dropdown').mouseenter(function(){
        animateDropDown(this);
    });

    $('.dropdown').live('mouseenter', function(){
        animateDropDown(this);
    });

    $('.subdropdown').mouseenter(function(){
        $('.subdroplinks').stop(false, true).hide();

        var subdropmenu = $(this).next();

        subdropmenu.css({
            position:'absolute',
            top: '0px',
            left: '150px',
            zIndex:1000
        });

        subdropmenu.stop().slideDown(300);

        subdropmenu.mouseleave(function(){
            $(this).slideUp(300);
        });
    });

    $(".scroll").click(function(event){
        //prevent the default action for the click event
        event.preventDefault();

        //get the full url - like mysitecom/index.htm#home
        var full_url = this.href;

        //split the url by # and get the anchor target name - home in mysitecom/index.htm#home
        var parts = full_url.split("#");
        var trgt = parts[1];

        //get the top offset of the target anchor
        var target_offset = $("#"+trgt).offset();
        var target_top = target_offset.top;

        //goto that anchor by setting the body scroll top to anchor top
        $('html, body').animate({scrollTop:target_top}, 500);
    });


    // Site Specific Functions

    $(".galleryImagesContainer a").nmtModal({});

    $("#slidingbuttonnextpanel").click(function(){
        if ($('#slidingpanelsheet').position().left > -1356) {
            $('#slidingpanelsheet').animate({left:$('#slidingpanelsheet').position().left - 678},1000);
        }

        return false;
    });

    $("#slidingbuttonpreviouspane").click(function(){
        if ($('#slidingpanelsheet').position().left !== 0) {
            $('#slidingpanelsheet').animate({left:$('#slidingpanelsheet').position().left + 678},1000);
        }

        return false;
    });
});

var listingTitles = new Array();
listingTitles['i'] = "Interior";
listingTitles['e'] = "Engine";
listingTitles['r'] = "Rear";
listingTitles['b'] = "Back";
listingTitles['f'] = "Front";

var dayOfWeek = new Array();
dayOfWeek[0] = "Sunday";
dayOfWeek[1] = "Monday";
dayOfWeek[2] = "Tuesday";
dayOfWeek[3] = "Wednesday";
dayOfWeek[4] = "Thursday";
dayOfWeek[5] = "Friday";
dayOfWeek[6] = "Saturday";

function daysInMonth(iMonth, iYear)
{
    return 32 - new Date(iYear, iMonth, 32).getDate();
}

function getMonday(d) {
  var day = d.getDay(),
      diff = d.getDate() - day + (day == 0 ? -6:1); // adjust when day is sunday
  return new Date(d.setDate(diff));
}

function showLoadingPopup(force){
    if(((loading_attempt < loading_attempt_limit)
        && ((url.search(/dashboard/g) != -1)
        || (url.search(/ads/g) != -1)))
        || (force == true))
    {
        if((loading_scripts == false) || (force == true))
        {
            $('#loading_notice').dialog({
                modal: true,
                height: 130,
                width: 100,
                autoOpen: true,
                buttons: {
                    "Close": function(){
                        $(this).dialog("close");
                    }
                },
                position: ['top', 'center']
            });
            $(".ui-dialog-titlebar").hide();
               loading_scripts = true;
        }

        loading_attempt++;
    }

}

function hideLoadingPopup(){
    $('#loading_notice').dialog('close');

}

function formatNumber(num)
{
    var n = num.toString();
    var nums = n.split('.');
    var newNum = "";
    if (nums.length > 1)
    {
        var dec = nums[1].substring(0,2);
        newNum = nums[0] + "." + dec + '%';
    }
    else
    {
        newNum = num;
    }
    return newNum;
}

function CurrencyFormatted(amount)
{
    var i = parseFloat(amount);
    if(isNaN(i)) { i = 0.00; }
    var minus = '';
    if(i < 0) { minus = '-'; }
    i = Math.abs(i);
    i = parseInt((i + .005) * 100);
    i = i / 100;
    s = new String(i);
    if(s.indexOf('.') < 0) { s += '.00'; }
    if(s.indexOf('.') == (s.length - 2)) { s += '0'; }
    s = minus + s;
    return s;
}

// this fixes an issue with the old method, ambiguous values
// with this test document.cookie.indexOf( name + "=" );
function Get_Cookie( check_name ) {
    // first we'll split this cookie up into name/value pairs
    // note: document.cookie only returns name=value, not the other components
    var a_all_cookies = document.cookie.split( ';' );
    var a_temp_cookie = '';
    var cookie_name = '';
    var cookie_value = '';
    var b_cookie_found = false; // set boolean t/f default f

    for ( i = 0; i < a_all_cookies.length; i++ )
    {
        // now we'll split apart each name=value pair
        a_temp_cookie = a_all_cookies[i].split( '=' );


        // and trim left/right whitespace while we're at it
        cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');

        // if the extracted name matches passed check_name
        if ( cookie_name == check_name )
        {
            b_cookie_found = true;
            // we need to handle case where cookie has no value but exists (no = sign, that is):
            if ( a_temp_cookie.length > 1 )
            {
                cookie_value = unescape( a_temp_cookie[1].replace(/^\s+|\s+$/g, '') );
            }
            // note that in cases where cookie is initialized but no value, null is returned
            return cookie_value;
            break;
        }
        a_temp_cookie = null;
        cookie_name = '';
    }
    if ( !b_cookie_found )
    {
        return null;
    }
}

$.fn.hasAttr = function (attr) {
    for (var i = 0; i < this[0].attributes.length; i++) {
        if (this[0].attributes[i].nodeName == attr) {return true}
    }
   return false;
};

function convertObjectToArray(object){
    var newArray = []
    for (var key in object) {
        newArray.push(object[key]);
    }

    return newArray;
}

function popUp(URL) {
    day = new Date();
    id = day.getTime();
    eval(
        "page"
        + id + " = window.open(URL, '"
        + id + "', 'toolbar=0, scrollbars=1, location=0, statusbar=1, menubar=0, resizable=0, width=300, height=250, left = 595, top = 350');"
    );
}