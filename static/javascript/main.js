var IS_SIGNED = false;
var CURRENT_TAB = "login";
var CURRENT_USER = "";

function redirect(url) {
    window.location.hash = '#' + url;
    check_is_signed();
}

var set_html = function (target, html, append = false) {
    if (!html) {
        html = "";
    }

    if (!append) {
        $(target).html(html);
    } else {
        $(target).append(html);
    }
}

var content_alert = function (type = "primary", title = "null", message = "null") {
    $("#alert-box").empty();
    var alertHtml = `<div id="alert-element" class="alert alert-${type}" role="alert" style="opacity: 500; display: none;"><h4>${title}</h4>${message}</div>`;
    set_html("#alert-box", alertHtml, true);

    $(`#alert-element`).fadeTo(3500, 500);
}

var show_confirm = function (body = "null", callback, justyes = false) {
    $("#confirm-body").text(body);

    if (justyes == true) {
        $("#confirm-no-button").attr("hidden", true);
    }

    $("#confirm-yes-button").on("click", function () {
        $("#confirm-yes-button").attr("hidden", false);
        $("#confirm-no-button").attr("hidden", false);
        callback();
    });

    $("#confirmModal").modal("show");
}

var show_error = function (errorCode) {
    set_html("#content-wrapper", `<img src="/static/image/error.jpg" style="width: 100%;height: 100%;object-fit: cover;">`);
    switch (errorCode) {
        case "unauthorized":
            content_alert("danger", "Alert", "You tried to access an unauthorized page!");
            break;
        case "already-authorized":
            content_alert("danger", "Alert", "You alreay logined!");
            break;
        case "not-found":
            content_alert("danger", "Alert", "Accessing to nonexist tab!");
            break;
        default:
            content_alert("danger", "Alert", "Unknown error!");
            break;
    }
    $("#loading-icon").attr("hidden", true);
}

var generate_sidebar = function(pageId = "", pageTxt = "", pageIcon = "", isFirst = false) {
    var sideBarHtml = `<li page-id="${pageId}"><a href="#/${pageId}" class="filter-item">${pageTxt}<span class="octicon octicon-${pageIcon} icon-right"></span></a></li>`;;
    if (pageId == "alert") {
        sideBarHtml = `<li page-id="${pageId}"><a class="filter-item">${pageTxt}<span class="octicon octicon-${pageIcon} icon-right"></span></a></li>`;
    }

    if (isFirst) {
        sideBarHtml += "<hr>";
    }
    return sideBarHtml;
}

function render_sidebar() {
    var sideBarHtml = "";
    if (!IS_SIGNED) {
        sideBarHtml += generate_sidebar("login", "Sign In", "sign-in", true);
        sideBarHtml += generate_sidebar("register", "Sign Up", "plus", false);
    } else {
        sideBarHtml += generate_sidebar("logout", "Logout", "circle-slash", true);
        sideBarHtml += generate_sidebar("main", "Main", "search", false);
    }

    set_html("#sidebar-menu", sideBarHtml);
    $(`#sidebar-menu>li[page-id="${CURRENT_TAB}"]>a`).addClass("selected");
}

function render_contents() {
    $("#alert-box").empty();
    $("#loading-icon").removeAttr("hidden");
    set_html("#modal-list", '<div class="modal fade" id="confirmModal" data-bs-backdrop="static" tabindex="-1"> <div class="modal-dialog modal-xl"> <div class="modal-content"> <div class="modal-header"> <span id="confirm-head">Confirmation</span> </div><div class="modal-body"> <span id="confirm-body"></span> </div><div class="modal-footer"> <button id="confirm-no-button" type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button> <button id="confirm-yes-button" type="button" class="btn btn-primary" data-bs-dismiss="modal">Yes</button> </div></div></div></div>');

    switch (CURRENT_TAB) {
        case "login":
            if (IS_SIGNED) {
                show_error("already-authorized");
                return;
            }

            set_html("#content-wrapper", `<form onsubmit="return user_login();"><div id="output-message" class="mb-2"></div><div class="input-group mb-3"><label class="input-group-text" for="username-input">Username</label><input class="form-control input-block" tabindex="1" name="username" id="username-input" type="text" placeholder="ex) user1234" required></div><div class="input-group mb-3"><label class="input-group-text" for="password-input">Password</label><input class="form-control input-block" tabindex="2" id="password-input" name="password" placeholder="Password" type="password" required></div><div class="input-group mb-3"><div class="input-group-text"><span style="margin-right:5px;">Remember username</span><input class="form-checkbox" id="remember-username" type="checkbox"></div><button class="btn btn-sm btn-primary" tabindex="3" id="signin_button "type="submit">Sign In</button></div></form>`, false);
            load_saved_usename();
            $("#loading-icon").attr("hidden", true);
            break;
        case "register":
            if (IS_SIGNED) {
                show_error("already-authorized");
                return;
            }

            set_html("#content-wrapper", `<form onsubmit="return user_register();"> <div id="output-message" class="mb-2"></div><div class="input-group mb-3"><label class="input-group-text" for="username-input">Username</label><input class="form-control input-block" tabindex="1" name="username" id="username-input" type="text" placeholder="Blackboard username" required></div><div class="input-group mb-3"><label class="input-group-text" for="email-input">Email</label><input class="form-control input-block" tabindex="2" name="email" id="email-input" type="email" placeholder="ex) user1234@email.com" required></div><div class="input-group mb-3"><label class="input-group-text" for="password-input">Password</label><input class="form-control input-block" tabindex="3" id="password-input" name="password" placeholder="Blackboard password" type="password" required> <label class="input-group-text" for="password-input-check">Password Check</label><input class="form-control input-block" tabindex="4" id="password-input-check" placeholder="Password check" name="password-check" type="password" required> </div><div class="input-group mb-3"><button class="btn btn-sm btn-primary" tabindex="5" id="signup_button " type="submit">Sign Up</button> </div></form>`, false); $("#loading-icon").attr("hidden", true);
            $("#loading-icon").attr("hidden", true);
            break;
        case "logout":
            if (!IS_SIGNED) {
                show_error("unauthorized");
                return;
            }

            $.post("/user/logout", function (data) {
                redirect("/login");
            });
            break;
        case "main":
            if (!IS_SIGNED) {
                show_error("unauthorized");
                return;
            }

            set_html("#content-wrapper", `<div class="card" id="lecture-list-card"> <div class="card-body" style="position: relative;min-height: 150px;max-height: 600px;overflow: auto;"> <table class="table table-hover mb-0"> <thead> <tr> <th scope="col">Name</th> <th scope="col">Id</th> </tr></thead> <tbody id="lecture-list"> </tbody> </table> </div></div>`, false);
            load_lecture_list();
            break;  
    }
}

function user_login() {
    event.preventDefault();

    var username = $("#username-input").val();
    var password = $("#password-input").val();

    if (username == "" || password == "") {
        content_alert("danger", "Alert", "Invalid Creditionals");
        return;
    }

    var parameter = {
        username: username,
        password: password
    };

    $.post("/user/login", parameter, function (data) {
        if (!data) {
            content_alert("danger", "Alert", "Failed to login");
            return;
        }

        if ($("#remember-username").prop("checked") == true) {
            localStorage.setItem("saved-username", username);
        }

        redirect("/main");
    });
}

function user_register() {
    event.preventDefault();
    show_confirm("", function () {
        var email = $("#email-input").val();
        var username = $("#username-input").val();
        var password = $("#password-input").val();
        var password_check = $("#password-input-check").val();
        
        if (username == "" || password == "" || email == "") {
            content_alert("danger", "Alert", "Invalid Creditionals");
            return;
        }
    
        if (password != password_check) {
            content_alert("danger", "Alert", "Please check password");
            return;
        }
    
        var parameter = {
            username: username,
            password: password,
            email: email
        };
    
        $.post("/user/register", parameter, function (data) {
            if (!data) {
                content_alert("danger", "Alert", "Failed to login");
                return;
            }
    
            if ($("#remember-username").prop("checked") == true) {
                localStorage.setItem("saved-username", username);
            }
            
            show_confirm("Successfully registered!", redirect("/login"));
        });
    });
}

function load_lecture_list()
{
    $("#loading-icon").removeAttr("hidden");
    $.post("/lecture/list", function (data) {
        if (!data) {
            content_alert("danger", "Alert", "Failed to load lecture list");
            return;
        }

        $.each(data, function (key, data) {
            set_html("#lecture-list", `<tr id="${data}lecture" onclick="load_lecture_status('${data}')" style="cursor: pointer;"> <td> <span>${key}</span> </td><td class="align-middle"> <h6 class="mb-0"><span class="badge bg-primary">${data}</span></h6> </td></tr>`, true);
        });
        $("#loading-icon").attr("hidden", true);
    });
}

function load_lecture_status(lecture_id)
{
    var parameter = {
        lectureid: lecture_id
    };

    $("#loading-icon").removeAttr("hidden");
    $.post("/lecture/status", parameter, function (data) {
        if (!data) {
            content_alert("danger", "Alert", "Failed to load lecture status");
            return;
        }
        
        show_confirm("", undefined, true);
        set_html(".modal-body", data, false);
        $("#loading-icon").attr("hidden", true);
    });
}

function check_nowtab() {
    var splitedUrl = window.location.href.split("#");

    if (splitedUrl.length >= 2) {
        CURRENT_TAB = splitedUrl[1].replace("/", "");
    }
}

function check_is_signed() {
    $.post("/user/profile", function (data) {
        if (data == false) {
            IS_SIGNED = false;
            load_layout();
            return;
        }

        IS_SIGNED = true;
        CURRENT_USER = data;
        CURRENT_TAB = "main";
        load_layout();
    });
}

function load_saved_usename() {
    var username = localStorage.getItem("saved-username");

    if (username == "") {
        return;
    }
    $("#username-input").val(username);
    $("#remember-username").prop("checked", true);
}

function load_layout() {
    check_nowtab();
    render_contents();
    render_sidebar();
}

function main() {
    check_is_signed();
}

$(document).ready(main);
$(window).on("hashchange", function () {
    load_layout();
});