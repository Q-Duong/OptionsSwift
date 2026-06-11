FilePond.registerPlugin(FilePondPluginImagePreview);
FilePond.setOptions({
    server: {
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        process: url_file_process,
        revert: url_file_revert,
    },
});

FilePond.create(document.querySelector('input[type="file"]'), {
    labelIdle: `Kéo và thả tập tin của bạn hoặc <span class="filepond--label-action">Trình duyệt</span>`,
    credits: false,
    onaddfilestart: () => {
        $(".button-submit").attr("disabled", true);
    },
    onprocessfile: () => {
        $(".button-submit").removeAttr("disabled");
    },
});

if (typeof files !== "undefined") {
    FilePond.setOptions({
        files,
    });
}

function deleteFileOrder(n, p, i) {
    if (confirm("Do you want to delete this file?")) {
        $(".loader-over").fadeIn();
        $(".button-submit").attr("disabled", true);
        $.ajax({
            url: url_file_delete_order,
            type: "DELETE",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                name: n,
                path: p,
                id: i,
            },
            success: function (data) {
                if (data.html) {
                    $(".section-file").html(data.html);
                } else {
                    $(".section-file").html("").addClass("hidden");
                }
                $(".loader-over").fadeOut();
                $(".button-submit").removeAttr("disabled");
                successMsg(data.message);
            },
        });
    }
}

$(document).on("click", ".del-total-file", function () {
    var n = $("input[name=file]").val();
    var p = $("input[name=path]").val();
    var i = $("input[name=id]").val();
    $(".loader-over").fadeIn();
    $.ajax({
        url: url_file_delete_total,
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
            name: n,
            path: p,
            id: i,
        },
        success: function () {
            $(".loader-over").fadeOut();
            $(".event-total-file").html('');
            $(".total-file").removeClass("hidden");
            successMsg("Xoá file thành công");
        },
    });
});