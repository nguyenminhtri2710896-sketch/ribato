var auth = {
    signIn: function () {
        $("#authenticator-check").click(function () {
            if ($(this).is(":checked")) {
                $(".input-auth").show();
            } else {
                $(".input-auth").hide();
            }
        })

    },
};