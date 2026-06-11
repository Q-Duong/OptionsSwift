/*------------------
               Select Month Schedule
            --------------------*/
$(document).on("change", ".select-month", function () {
    var month = $(this).val();
    var year = $(".select-year").val();
    $(".schedule-search").removeClass("search-show").val("");
    $(".btn-search").html(
        '<button class="btn-schedule-search"><i class="fas fa-search"></i></button>'
    );
    $(".search-results").removeClass("search-results-show").html("");
    $(".loader-over").fadeIn();
    $.ajax({
        url: url_select_details,
        method: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            year: year,
            month: month,
        },
        success: function (data) {
            pushCurrentTime(month, year);
            schedule(data.day);
            $(".schedule").html(data.html);
            $(".loader-over").fadeOut();
        },
        error: function (textStatus) {
            popupNotificationSessionExpired();
        },
        complete: function () {
            $(".loader-over").fadeOut();
        },
    });
});

/*------------------
                Set month when year change
                --------------------*/
$(document).on("change", ".select-year", function () {
    $(".define-month").prop("selected", true);
});
$(document).on("click", ".tile-overlay-toggle", function () {
    var id = $(this).removeClass("form-textbox-entered");
    if ($(this).is(":checked")) {
        $(this).parent().addClass("expanded");
        $(this)
            .parent()
            .find(".tile-button-text")
            .attr("aria-expanded", "true");
        $(this)
            .parent()
            .find(".tile-overlay-content")
            .attr("aria-hidden", "false");
    } else {
        $(this).parent().removeClass("expanded");
        $(this)
            .parent()
            .find(".tile-button-text")
            .attr("aria-expanded", "false");
        $(this)
            .parent()
            .find(".tile-overlay-content")
            .attr("aria-hidden", "true");
    }
});
/*------------------
               Handle Task
            --------------------*/
function loadTask() {
    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        url: url_load_task,
        method: "GET",
        success: function (data) {
            $(".cd-task").html(data.html);
        },
    });
}

$(document).on("click", ".create-task-btn", function () {
    $("[data-core-overlay]").fadeIn(200);
    $('input[name="department"]').val($(this).attr("data-department"));
    $('input[name="type"]').val("create");
    $(".typography-headline-reduced").text("Tạo nhiệm vụ");
    $(".form-button").text("Tạo");
    $("body").css("overflow", "hidden");
});

$(document).on("click", ".rc-overlay-close", function () {
    $("[data-core-overlay]").fadeOut(200);
    $("#task")[0].reset();
    $(".form-textbox-input").removeClass("form-textbox-entered");
    $("body").css("overflow", "");
});

$(document).on("click", ".edit-task-btn", function () {
    $("[data-core-overlay]").fadeIn(200);
    $('input[name="id"]').val($(this).attr("id"));
    $('input[name="type"]').val("update");
    $('input[name="task_name"]').val(
        $(this).parent().parent().find(".tile-card-headline").text().trim()
    );
    $('textarea[name="task_description"]').val(
        $(this).parent().parent().find(".tile-overlay-copy").text().trim()
    );
    $(".typography-headline-reduced").text("Cập nhật nhiệm vụ");
    $(".form-button").text("Cập nhật");
    $(".form-textbox-input").addClass("form-textbox-entered");
    $("body").css("overflow", "hidden");
});

$(document).on("click", ".rs-lookup-submit", function () {
    var formData = new FormData($("#task")[0]);
    $(".form-textbox").removeClass("is-error");
    $(".rs-lookup-submit").attr("disabled", true);
    $(".loader-over").fadeIn();
    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        url: url_create_or_update,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            if (data.errors) {
                $.each(data.validator, (k, v) => {
                    errorsMsgInput(k, v);
                });
            } else {
                successMsg(data.message);
                loadTask();
            }
            $("[data-core-overlay]").fadeOut(200);
            $("body").css("overflow", "");
            $(".loader-over").delay(200).fadeOut("slow");
            $(".rs-lookup-submit").removeAttr("disabled");
        },
        error: function (textStatus) {
            popupNotificationSessionExpired();
        },
        complete: function () {
            $("[data-core-overlay]").fadeOut(200);
            $("#task")[0].reset();
            $(".form-textbox-input").removeClass("form-textbox-entered");
            $(".loader-over").delay(200).fadeOut("slow");
            $(".rs-lookup-submit").removeAttr("disabled");
        },
    });
});

$(document).on("click", ".done-task-btn", function () {
    var id = $(this).attr("id");
    $(".loader-over").fadeIn();
    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        url: url_update_status,
        method: "PATCH",
        data: {
            id: id,
        },
        success: function (data) {
            loadTask();
        },
        error: function (textStatus) {
            popupNotificationSessionExpired();
        },
        complete: function () {
            $(".loader-over").delay(200).fadeOut("slow");
        },
    });
});
