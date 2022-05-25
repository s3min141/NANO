const register_loginBtn = document.querySelector("#loginBtn"),
    register_usernameInput = document.querySelector("#usernameInput"),
    register_passwordInput = document.querySelector("#passwordInput");

function handleClickRegisterBtn(event) {
    event.preventDefault();

    $.ajax({
        url: "/NANO/register/src/php/register.php",
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        type: "POST",
        data: {
            username: register_usernameInput.value,
            password: register_passwordInput.value,
        },
        dataType: "json",
        success: function (response) {
            alert(response.message);
        }
    });
}

function init() {
    register_loginBtn.addEventListener("click", handleClickRegisterBtn);
}
init();

//https://obfuscator.io/