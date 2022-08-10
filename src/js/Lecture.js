const Lecture__lecturenameinput = document.querySelector("#lecturenameinput"),
    Lecture_lecturenuminput = document.querySelector("#lecturenuminput"),
    Lecture_modalTitle = document.querySelector("#modalTitle"),
    Lecture_modalInner = document.querySelector("#modalInner"),
    Lecture_lecturelist = document.querySelector("#lecturelist");

var Lecture_previousCall = "",
    Lecture_previousResult = "";


function AddLecture() {
    $.ajax({
        url: "/NANO/src/php/Lecture.php",
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        type: "POST",
        data: {
            behavior: "add",
            lecturename: Lecture__lecturenameinput.value,
            lecturenum: Lecture_lecturenuminput.value
        },
        dataType: "json",
        success: function (response) {
            alert(response.message);
            if (response.result === "success") {
                location.reload();
            }
        }
    });
}

function DeleteLecture(lectureName) {
    $.ajax({
        url: "/NANO/src/php/Lecture.php",
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        type: "POST",
        data: {
            behavior: "del",
            lecturename: lectureName
        },
        dataType: "json",
        success: function (response) {
            alert(response.message);
            if (response.result === "success") {
                location.reload();
            }
        }
    });
}

function ClickLecture(event) {
    const lectureId = event.target.dataset.id;

    Lecture_modalTitle.innerText = event.target.textContent.split("Lecture ID: ")[0];
    Lecture_modalInner.innerText = "";
    AddLoadingIcon(Lecture_modalInner);
    $(".modal").show();

    if (Lecture_previousCall != lectureId || Lecture_previousResult == "Unknown Error, please retry") {
        Lecture_previousCall = lectureId;

        $.ajax({
            url: "/NANO/src/php/getLectureStatus.php",
            contentType: "application/x-www-form-urlencoded; charset=UTF-8",
            type: "POST",
            data: {
                lectureId: lectureId
            },
            dataType: "json",
            success: function (response) {
                const template = document.createElement("template");

                template.innerHTML = response.message;
                Lecture_previousResult = response.message;
                Lecture_modalInner.innerHTML = "";
                Lecture_modalInner.appendChild(template.content.firstChild);
            }
        });
    }
    else {
        const template = document.createElement("template");

        template.innerHTML = Lecture_previousResult;
        Lecture_modalInner.innerHTML = "";
        Lecture_modalInner.appendChild(template.content.firstChild);
    }
}

function AddLoadingIcon(target) {
    const template = document.createElement("template");

    template.innerHTML = "<div style='width: 45px;height: 45px;margin: 0 auto;' class='spinner-border ms-auto' role='status'></div>";
    target.appendChild(template.content.firstChild);
}

function initLectureList() {
    const loadingTemplate = document.createElement("template");

    loadingTemplate.innerHTML = "<div id='lectureloadingicon' style='width: 25px;height: 25px;margin: 0 auto;' class='spinner-border ms-auto' role='status'></div>";
    document.querySelector("#lecturelist").appendChild(loadingTemplate.content.firstChild);

    $.ajax({
        url: "/NANO/src/php/getLectureList.php",
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        type: "POST",
        dataType: "json",
        success: function (response) {
            if (response.result = "success") {
                $.each(response.message, function(key, value) {
                    const tempTemplate = document.createElement("template");
                    tempTemplate.innerHTML = `<li class='disabledrag list-group-item list-group-item-action d-flex justify-content-between align-items-center' style='cursor: pointer;' data-id='${value}' ondblclick='ClickLecture(event);'>${key}<div id='moreinfo'><span class='badge bg-primary rounded-pill'>Lecture ID: ${value}</span></div></li>`;
                    Lecture_lecturelist.appendChild(tempTemplate.content.firstChild);
                });
            }
            else {
                alert(response.message);
            }
            $("#lectureloadingicon").remove();
        }
    });
}
initLectureList();