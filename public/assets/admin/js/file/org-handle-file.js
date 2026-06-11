FilePond.registerPlugin(FilePondPluginImagePreview);
const inputElements = document.querySelectorAll("input.filepond");
Array.from(inputElements).forEach((inputElement) => {
    const pond = FilePond.create(inputElement, {
        credits: false,
        onaddfilestart: () => {
            $(".button-submit").attr("disabled", true);
        },
        onprocessfile: () => {
            $(".button-submit").removeAttr("disabled");
        },
    });
    pond.setOptions({
        server: {
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            process: url_file_process,
            revert: url_file_revert,
        },
    });
});

function deleteFile(e, p, id) {
    if (confirm("Do you want to delete this file?")) {
        $(".loading-container").show();
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: url_file_delete,
            type: "DELETE",
            data: {
                type: e,
                path: p,
                id: id,
            },
            success: function (data) {
                url_file_delete = url_file_delete.replace(p, ":path");
                $("." + e + "_section").addClass("hidden");
                $("." + e).removeClass("hidden");
                $(".loading-container").fadeOut();
                successMsg(data.message);
            },
        });
    }
}
