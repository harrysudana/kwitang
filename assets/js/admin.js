/**
 * Kwitang Admin script
 *
 * Iyan Kushardiansah <iyank4@gmail.com>
 */

// subnav
$(document).scroll(function(){
    // we don't do this on small screen
    if ($(window).width() < 980) return;

    var nav_height = $('.navbar').height();
    var scroll_top = $(this).scrollTop();
    $(".subnav").each(function() {
        if ( ! $(this).attr('data-top')) {
            $(this).attr('data-top', $(this).offset().top);
        }

        if((scroll_top + nav_height) > $(this).attr("data-top")) {
            if ( ! $(this).hasClass("subnav-fixed")) {
                $(this).css('top', nav_height).addClass("subnav-fixed");
            }
            nav_height += $(this).height();
        } else {
            $(this).removeClass("subnav-fixed");
        }
    });
    fix_body_margin();
});


$(document).ready(function() {
    fix_body_margin();
    $(window).resize(function() {
        fix_body_margin();
    });

    // hide .helpLink if there is no #helpScreen on the page
    if ($("#helpScreen").length == 0) {
        $("#helpLink").hide();
    }

    // show main menu on hover
    $('.dropdown').hover(function() {
        $('.dropdown').removeClass('open');
        $(this).addClass('open');
    }, function() {
        $('.dropdown').removeClass('open');
    });

    // date time picker
    if ($.fn.datetimepicker) {
        $('.datetimepicker').datetimepicker({
            format: 'yyyy-MM-dd hh:mm:ss'
        });
        $('.justdatepicker').datetimepicker({
            format: 'yyyy-MM-dd',
            pickTime: false
        });
        $('.justtimepicker').datetimepicker({
            format: 'hh:mm:ss',
            pickDate: false
        });
    }

    // slug/unique name autocomplete
    $('.form_title').keyup(function (key) {
        $('.form_slug').val(slugify($(this).val()));
    });
    $('.form_slug').change(function() {
        $(this).val(slugify($(this).val()));
    });

    /*
    common validator

    this script sweeps all field on the form,
    reading the 'rel' attribute,
    'rel' attribute hold validation scheme identifier
    skip if rel is not defined!
    */
    $('form').submit(function(e) {
        var msg = '';
        var errMsg = '';
        var fieldError;
        var req_str = '';

        $(this).find('input, textarea').each(function() {
            msg = $(this).attr('data-required');
            if (typeof msg != 'undefined') {
                if(! $(this).val() || $(this).val() == '') {
                    errMsg += '- ' + msg + "\r\n";
                    if (typeof fieldError != 'object')
                        fieldError = $(this);
                }
            }
        });

        if(errMsg !== '') {
            e.preventDefault();
            alert('Silakan lengkapi kolom berikut ini:\r\n' + errMsg);
            fieldError.focus();
        }
    })

    /* common delete confirmation */
    $('.askdelete').click(function (e) {
        e.preventDefault();
        cf = confirm('Apakah anda yakin untuk menghapus "' + $(this).attr('title') + '" ?');
        if(cf) {
            document.location.href = $(this).attr('href');
        }
        return false;
    });

    if ($.fn.tooltip) {
        $('.tips').tooltip();
    }
    if ($.fn.popover) {
        $("a[rel=popover]").popover({html:true}).click(function(e) { e.preventDefault() });
    }

    // custom multi language input
    $('.lang-control a').click(function(e) {
        e.preventDefault();
        $(this).parent().find('a').removeClass('active');
        $(this).addClass('active');

        var domid = $(this).addClass('active').attr('href');
        $(this).parents('.lang-input').find('.lang-content').children().each(function() {
            $(this).removeClass('active');
        });
        $(domid).addClass('active');
    }).hover(function() {
        var domid = $(this).attr('href');
        $(this).attr('data-original-title', $(domid).val());
    });

    // Integer input
    $(".input-digit").keyup(function(e) {
        var tmp_val = $(this).val() * 1;
        var tmp_min = 0;
        var tmp_max = 99999999;

        var min_attr = $(this).attr('data-min');
        var max_attr = $(this).attr('data-max');
        if (typeof min_attr !== 'undefined' && min_attr !== false && $.isNumeric(min_attr)) {
            tmp_min = min_attr;
        }
        if (typeof max_attr !== 'undefined' && max_attr !== false && $.isNumeric(max_attr)) {
            tmp_max = max_attr;
        }

        if (e.keyCode == 38 || e.keyCode == 39) {
            tmp_val = tmp_val + 1;
        } else if (e.keyCode == 37 || e.keyCode == 40) {
            tmp_val = tmp_val - 1;
        }

        if (tmp_val > tmp_max) {
            tmp_val = tmp_max;
        }
        if (tmp_val < tmp_min) {
            tmp_val = tmp_min;
        }

        // last check
        tmp_val = parseInt(tmp_val);
        if ( ! $.isNumeric(tmp_val)) {
            tmp_val = tmp_min;
        }

        $(this).val(tmp_val);
    });
});

// -- FUNCTIONS ---------------------------------------------------------------

function slugify(text) {
    text = text.trim();
    text = text.replace(/[^a-zA-Z0-9\-_\s]+/ig, '');
    text = text.replace(/\s+/gi, "-");
    if (text.substr(text.length-1,1) == '-') {
        text = text.substr(0, text.length-1);
    }

    return text.toLowerCase();
}

function notify(message, autoclose, delay) {
    $("#notify").hide();
    autoclose = typeof autoclose !== 'undefined' ? autoclose : true;
    delay     = typeof delay     !== 'undefined' ? delay : 3000;
    if ( autoclose) {
        $("#notify").html(message).fadeIn().delay(delay).fadeOut();
    } else {
        message = '<div style="float: left; margin-right:14px;">'+message+'</div><a href="#" onclick="javascript:$(this).parent().fadeOut();return false;" class="pull-right"><i class="icon-remove icon-red"></i></a>';
        $("#notify").html(message).fadeIn();
    }
}

function fix_body_margin() {
    var sub_height = 0;
    $(".subnav-fixed").each(function() {
        sub_height += $(this).height();
    });
    $("body").css("margin-top", $(".navbar").outerHeight() + 10 + sub_height);
}

function getCookie(c_name) {
    var c_value = document.cookie;
    var c_start = c_value.indexOf(" " + c_name + "=");

    if (c_start == -1) {
      c_start = c_value.indexOf(c_name + "=");
    }

    if (c_start == -1) {
        c_value = null;
    } else {
        c_start = c_value.indexOf("=", c_start) + 1;
        var c_end = c_value.indexOf(";", c_start);

        if (c_end == -1) {
            c_end = c_value.length;
        }
        c_value = unescape(c_value.substring(c_start,c_end));
    }

    return c_value;
}
