const login_loginBtn = document.querySelector("#loginBtn"),
    login_usernameInput = document.querySelector("#usernameInput"),
    login_passwordInput = document.querySelector("#passwordInput");

function handleClickLoginBtn(event) {
    event.preventDefault();

    $.ajax({
        url: "/NANO/login/src/php/login.php",
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        type: "POST",
        data: {
            username: login_usernameInput.value,
            password: login_passwordInput.value,
        },
        dataType: "json",
        success: function (response) {
            if (response.result === "success") {
                location.href = "/NANO";
            }
            else {
                alert(response.message);
            }
        }
    });
}

function init() {
    login_loginBtn.addEventListener("click", handleClickLoginBtn);
}
init();

//https://obfuscator.io/