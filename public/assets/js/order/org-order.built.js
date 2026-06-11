$(".btn-submit-order").prop("disabled", true);
$(".confirm").click(function () {
    $(".confirm").is(":checked") ? $(".btn-submit-order").prop("disabled", false) : "";
});

$("input[name='ord_film']").click(function () {
    console.log($(this).val());
    if ($(this).val() === "Bình thường") {
        $(".block-type-of-film").removeClass("hidden");
        $(".block-type-of-film-abnormal").addClass("hidden");
        $(".block-type-of-film input").first().prop("checked", true);
    }

    if ($(this).val() === "Bất thường") {
        $(".block-type-of-film").addClass("hidden");
        $(".block-type-of-film-abnormal").removeClass("hidden");
        $(".block-type-of-film-abnormal input").first().prop("checked", true);
    }

    if ($(this).val() === "") {
        $(".block-type-of-film").addClass("hidden");
        $(".block-type-of-film-abnormal").addClass("hidden");
    }
});
