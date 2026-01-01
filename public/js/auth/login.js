$(function () {
    var text_val = $(".input-group input").val();
    if (text_val === "") {
        $(".input-group").removeClass('is-filled');
    } else {
        $(".input-group").addClass('is-filled');
    }
});
