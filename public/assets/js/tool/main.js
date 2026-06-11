"use strict";
//Notifications
function successMsg(msg) {
    $(".notifications-popup-success").addClass('active');
    $('.notifications-icon').html('<i class="fas fa-solid fa-check notifications-success"></i>')
    $(".message-text").text(msg);
    setTimeout(function() {
        $('.notifications-popup-success').removeClass('active');
    }, 3000);
    $('.notifications-close').click(function() {
        $('.notifications-popup-success').removeClass('active');
    });
}

function errorMsg(msg) {
    $(".notifications-popup-error").addClass('active');
    $('.notifications-icon').html('<i class="fas fa-times notifications-error"></i>')
    $(".message-text").text(msg);
    setTimeout(function() {
        $('.notifications-popup-error').removeClass('active');
    }, 3000);
    $('.notifications-close').click(function() {
        $('.notifications-popup-error').removeClass('active');
    });
}

//Handle Slug
function ChangeToSlug() {
    var slug;

    //Lấy text từ thẻ input title 
    slug = document.getElementById("slug").value;
    slug = slug.toLowerCase();
    //Đổi ký tự có dấu thành không dấu
    slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
    slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
    slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
    slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
    slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
    slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
    slug = slug.replace(/đ/gi, 'd');
    //Xóa các ký tự đặt biệt
    slug = slug.replace(/\`|\~|\!|\@|\#|\||\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\?|\>|\<|\'|\"|\:|\;|_/gi, '');
    //Đổi khoảng trắng thành ký tự gạch ngang
    slug = slug.replace(/ /gi, "-");
    //Đổi nhiều ký tự gạch ngang liên tiếp thành 1 ký tự gạch ngang
    //Phòng trường hợp người nhập vào quá nhiều ký tự trắng
    slug = slug.replace(/\-\-\-\-\-/gi, '-');
    slug = slug.replace(/\-\-\-\-/gi, '-');
    slug = slug.replace(/\-\-\-/gi, '-');
    slug = slug.replace(/\-\-/gi, '-');
    //Xóa các ký tự gạch ngang ở đầu và cuối
    slug = '@' + slug + '@';
    slug = slug.replace(/\@\-|\-\@|\@/gi, '');
    //In slug ra textbox có id “slug”
    document.getElementById('convert_slug').value = slug;
}

//Handle Money and Quantity
function formatNumber(n) {
    return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
}

function formatNumber1(n) {
    return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, "")
}

function formatCurrency(input, blur) {
    var input_val = input.val();
    if (input_val === "") {
        return;
    }
    var original_len = input_val.length;
    var caret_pos = input.prop("selectionEnd");
    if (input_val.indexOf(",") >= 0) {
        var decimal_pos = input_val.indexOf(",");
        var left_side = input_val.substring(0, decimal_pos);
        var right_side = input_val.substring(decimal_pos);

        left_side = formatNumber(left_side);
        right_side = formatNumber(right_side);

        input_val = left_side;

    } else {
        input_val = formatNumber(input_val);
    }

    input.val(input_val);


    var updated_len = input_val.length;
    caret_pos = updated_len - original_len + caret_pos;
    input[0].setSelectionRange(caret_pos, caret_pos);
}

function formatQuantity(input) {
    var input_val = input.val();
    if (input_val === "") {
        return;
    }
    var original_len = input_val.length;
    var caret_pos = input.prop("selectionEnd");
    input_val = formatNumber1(input_val);
    input.val(input_val);
    var updated_len = input_val.length;
    caret_pos = updated_len - original_len + caret_pos;
    input[0].setSelectionRange(caret_pos, caret_pos);
}

(function ($) {
    
    $(window).on("load", function () {
        $(".loader").fadeOut();
        $("#preloder").fadeOut("slow");
    });

    // Tool select2
    $(document).ready(function() {
        $('.select-2').select2();
    });

    // Handle Notifications
    $(document).ready(function() {
        setTimeout(function() {
            $('.notifications-popup-success').removeClass('active');
        }, 3000);
        setTimeout(function() {
            $('.notifications-popup-error').removeClass('active');
        }, 3000);
    });

})(jQuery);
