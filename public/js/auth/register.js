$(function () {
    // Check if input has value on page load
    $(".input-group input").each(function () {
        if ($(this).val() !== "") {
            $(this).parent().addClass('is-filled');
        }
    });

    // Check on input change
    $(".input-group input").on('focus blur input', function () {
        if ($(this).val() !== "") {
            $(this).parent().addClass('is-filled');
        } else {
            $(this).parent().removeClass('is-filled');
        }
    });

    // Add animation to register button
    $(".register-btn").hover(
        function () {
            $(this).addClass("btn-pulse");
        },
        function () {
            $(this).removeClass("btn-pulse");
        }
    );
});
