$(document).ready(function () {

    // ------------------------------
    // OPEN LOGIN MODAL
    // ------------------------------
    $("#openLoginBtn").click(function () {
        $("#authModalLabel").text("Login");
        $("#loginSection").show();
        $("#registerSection").hide();
        $("#authModal").modal("show");
    });

    // ------------------------------
    // OPEN REGISTER MODAL
    // ------------------------------
    $("#openRegisterBtn").click(function () {
        $("#authModalLabel").text("Register");
        $("#loginSection").hide();
        $("#registerSection").show();
        $("#authModal").modal("show");
    });

    // Switch Login → Register
    $("#goRegister").click(function () {
        $("#authModalLabel").text("Register");
        $("#loginSection").hide();
        $("#registerSection").show();
    });

    // Switch Register → Login
    $("#goLogin").click(function () {
        $("#authModalLabel").text("Login");
        $("#registerSection").hide();
        $("#loginSection").show();
    });

    // ------------------------------
    // LOGIN SUBMIT
    // ------------------------------
    $("#loginForm").submit(function (e) {
        e.preventDefault();

        let formData = $(this).serialize() + "&action=login";

        $.ajax({
            url: "module/register.php",
            type: "POST",
            data: formData,
            dataType: "json",

            success: function (res) {

                if (res.status === true) {
                    alert(res.message);

                    $("#loginForm")[0].reset();

                    location.reload();

                } else {
                    alert(res.message);
                }
            },

            error: function () {
                alert("Something went wrong during login.");
            }
        });
    });

    // ------------------------------
    // REGISTER SUBMIT
    // ------------------------------
    $("#registerForm").submit(function (e) {
        e.preventDefault();

        let formData = $(this).serialize() + "&action=register";

        $.ajax({
            url: "module/register.php",
            type: "POST",
            data: formData,
            dataType: "json",

            success: function (res) {
                if (res.status === true) {
                    alert(res.message);

                    // Switch to login after success
                    $("#loginModal").modal("show");

                } else {
                    alert(res.message);
                }
            },

            error: function () {
                alert("Something went wrong during registration.");
            }
        });
    });

    $(document).on("click", "#logoutBtn", function () {
        let formData = $(this).serialize() + "&action=logout";
        $.ajax({
                url: "module/register.php",
                type: "POST",
                data: formData,
                dataType: "json",

                success: function (res) {
                    if (res.status === true) {
                        location.reload();

                    } else {
                        alert(res.message);
                    }
                },

                error: function () {
                    alert("Something went wrong during registration.");
                }
            });
    });


    /* ============================================================
        Helper: Get CSRF Token
    ============================================================ */
    function csrf() {
        return $("#csrf_token").val();
    }


    /* ============================================================
        1. SAVE JOURNAL ENTRY
    ============================================================ */
    $("#saveJournalBtn").click(function () {
        let note = $("#journalText").val().trim();
        let date = $("#journalDate").val().trim();

        if (note === "") {
            alert("Please write something before saving.");
            return;
        }

        $.ajax({
            url: "module/journal_action.php",
            type: "POST",
            data: {
                action: "save",
                note: note,
                date: date,
                csrf_token: csrf()
            },
            dataType: "json",
            success: function (res) {
                if (res.status) {
                    $("#saveJournalForm")[0].reset(); 
                    loadJournals();
                }
                alert(res.message);
            },
            error: function () {
                alert("Error saving journal.");
            }
        });
    });



    /* ============================================================
        2. LOAD JOURNAL LIST
    ============================================================ */
    function loadJournals() {
        $.ajax({
            url: "module/journal_action.php",
            type: "POST",
            data: { 
                action: "list",
                csrf_token: csrf()
            },
            dataType: "json",
            success: function (res) {
                if (res.status) {
                    renderJournalList(res.journals);
                }
            }
        });
    }

    loadJournals();



    /* ============================================================
        3. RENDER JOURNAL HTML
    ============================================================ */
   function renderJournalList(journals) {

    let html = "";

    if (journals.length === 0) {
        html = `<p>No journal entries found.</p>`;
    } else {
        journals.forEach((journal, index) => {

            let num = index + 1;

            html += `
                <div class="journal-item styled-journal">
                
                    <div class="journal-index">${num}</div>

                    <div class="journal-content">
                        <p class="journal-note">${journal.note.replace(/\n/g, "<br>")}</p>
                        <small class="journal-date">${journal.action_date}</small>

                        <div class="journal-actions">
                            <button class="btn btn-sm btn-danger deleteJournalBtn" 
                                data-id="${journal.id}">
                                Delete
                            </button>
                        </div>
                    </div>

                </div>
            `;
        });
    }

    $("#journalSavedList").html(html);
}


    /* ============================================================
        4. DELETE JOURNAL ENTRY
    ============================================================ */
    $(document).on("click", ".deleteJournalBtn", function () {
        let id = $(this).data("id");

        if (!confirm("Delete this journal entry?")) return;

        $.ajax({
            url: "module/journal_action.php",
            type: "POST",
            data: {
                action: "delete",
                journal_id: id,
                csrf_token: csrf()
            },
            dataType: "json",
            success: function (res) {
                alert(res.message);
                if (res.status) loadJournals();
            }
        });
    });


    $(document).ready(function () {

    /* ============================================================
        1. SAVE GOAL ENTRY
    ============================================================ */
    $("#saveGoalBtn").click(function (e) {
        e.preventDefault();
        let goal = $("#goalInput").val().trim();
     
        let date = $("#goalDate").val().trim();

        if (goal === "") {
            alert("Please enter your goal.");
            return;
        }

        $.ajax({
            url: "module/goal_action.php",
            type: "POST",
            data: {
                action: "save",
                goal: goal,
                date: date,
                csrf_token: csrf()
            },
            dataType: "json",
            success: function (res) {
                if (res.status) {
                    $("#saveGoalForm")[0].reset();
                    loadGoals();
                }
                alert(res.message);
            },
            error: function () {
                alert("Error saving goal.");
            }
        });

    });



    /* ============================================================
        2. LOAD GOAL LIST
    ============================================================ */
    function loadGoals() {
        $.ajax({
            url: "module/goal_action.php",
            type: "POST",
            data: {
                action: "list",
                csrf_token: csrf()
            },
            dataType: "json",
            success: function (res) {
                if (res.status) {
                    renderGoalList(res.goals);
                }
            }
        });
    }

    loadGoals(); // auto load on page ready



    /* ============================================================
        3. RENDER GOALS WITH STYLING
    ============================================================ */
    function renderGoalList(goals) {

        let html = "";

        if (goals.length === 0) {
            html = `<p>No goals saved.</p>`;
        } else {

            goals.forEach((goal, index) => {

                let num = index + 1;

                html += `
                    <div class="goal-item styled-journal">

                        <div class="journal-index">${num}</div>

                        <div class="journal-content">

                            <p class="journal-note">
                                <strong>Goal:</strong> ${goal.goal_text}<br>
                                <strong>Target:</strong> ${goal.target_date ? goal.target_date : "—"}<br>
                            </p>

                            <small class="journal-date">Created on : ${goal.created_at}</small>

                            <div class="journal-actions">
                                <button class="btn btn-sm btn-danger deleteGoalBtn"
                                    data-id="${goal.id}">
                                    Delete
                                </button>
                            </div>

                        </div>

                    </div>
                `;
            });

        }

        $("#goalSavedList").html(html);
    }



    /* ============================================================
        4. DELETE GOAL ENTRY
    ============================================================ */
    $(document).on("click", ".deleteGoalBtn", function () {

        let id = $(this).data("id");

        if (!confirm("Delete this goal?")) return;

        $.ajax({
            url: "module/goal_action.php",
            type: "POST",
            data: {
                action: "delete",
                goal_id: id,
                csrf_token: csrf()
            },
            dataType: "json",
            success: function (res) {
                alert(res.message);
                if (res.status) loadGoals();
            }
        });

    });


});

$(document).ready(function () {

    /* ============================================================
        1. SAVE VOWS ENTRY
    ============================================================ */
    $("#saveVowsBtn").click(function (e) {
        e.preventDefault();

        let vows = $("#vowsText").val().trim();
        let date = $("#vowsDate").val().trim();

        if (vows === "") {
            alert("Please enter your vows.");
            return;
        }

        $.ajax({
            url: "module/vows_action.php",
            type: "POST",
            data: {
                action: "save",
                vows: vows,
                date: date,
                csrf_token: csrf()
            },
            dataType: "json",
            success: function (res) {
                alert(res.message);
                if (res.status) {
                    $("#saveVowsForm")[0].reset();
                    loadVows();
                }
            },
            error: function () {
                alert("Error saving vows.");
            }
        });
    });


    /* ============================================================
        2. LOAD VOWS LIST
    ============================================================ */
    function loadVows() {
        $.ajax({
            url: "module/vows_action.php",
            type: "POST",
            data: {
                action: "list",
                csrf_token: csrf()
            },
            dataType: "json",
            success: function (res) {
                if (res.status) {
                    renderVowsList(res.vows);
                }
            }
        });
    }

    loadVows(); // auto load on page ready


    /* ============================================================
        3. RENDER VOWS WITH STYLING
    ============================================================ */
    function renderVowsList(vows) {

        let html = "";

        if (vows.length === 0) {
            html = `<p>No vows saved.</p>`;
        } else {

            vows.forEach((item, index) => {

                let num = index + 1;

                html += `
                    <div class="goal-item styled-journal">

                        <div class="journal-index">${num}</div>

                        <div class="journal-content">

                            <p class="journal-note">
                                <strong>Vows:</strong> ${item.vows_text}<br>
                                <strong>Date:</strong> ${item.vows_date ? item.vows_date : "—"}
                            </p>

                            <small class="journal-date">
                                Created on : ${item.created_at}
                            </small>

                            <div class="journal-actions">
                                <button class="btn btn-sm btn-danger deleteVowsBtn"
                                    data-id="${item.id}">
                                    Delete
                                </button>
                            </div>

                        </div>

                    </div>
                `;
            });

        }

        $("#VowsSavedList").html(html);
    }


    /* ============================================================
        4. DELETE VOWS ENTRY
    ============================================================ */
    $(document).on("click", ".deleteVowsBtn", function () {

        let id = $(this).data("id");

        if (!confirm("Delete this vow?")) return;

        $.ajax({
            url: "module/vows_action.php",
            type: "POST",
            data: {
                action: "delete",
                vows_id: id,
                csrf_token: csrf()
            },
            dataType: "json",
            success: function (res) {
                alert(res.message);
                if (res.status) loadVows();
            }
        });

    });

});



$(document).ready(function () {

    /* ============================================================
        1. SAVE MANTRA NOTE
    ============================================================ */
    $("#saveMantraBtn").click(function (e) {
        e.preventDefault();

        let deity     = $("#deitySelect").val().trim();
        let mantra    = $("#mantraInput").val().trim();
        let goalType  = $("#goalType").val().trim(); // count / days / target
        let goalValue = $("#goalValue").val().trim();
        let output    = $("#outputType").val().trim(); // offline / online

        if (deity === "" || mantra === "") {
            alert("Please select deity and enter mantra.");
            return;
        }

        if (goalType === "" || goalValue === "") {
            alert("Please set your mantra goal.");
            return;
        }

        $.ajax({
            url: "module/mantra_action.php",
            type: "POST",
            data: {
                action: "save",
                deity: deity,
                mantra: mantra,
                goal_type: goalType,
                goal_value: goalValue,
                output: output,
                csrf_token: csrf()
            },
            dataType: "json",
            success: function (res) {
                if (res.status) {
                    $("#saveMantraForm")[0].reset();
                    loadMantras();
                }
                alert(res.message);
            },
            error: function () {
                alert("Error saving mantra note.");
            }
        });
    });


    /* ============================================================
        2. LOAD MANTRA NOTES
    ============================================================ */
    function loadMantras() {
        $.ajax({
            url: "module/mantra_action.php",
            type: "POST",
            data: {
                action: "list",
                csrf_token: csrf()
            },
            dataType: "json",
            success: function (res) {
                if (res.status) {
                    renderMantraList(res.mantras);
                }
            }
        });
    }

    loadMantras(); // auto load on page ready


    /* ============================================================
        3. RENDER MANTRA NOTES
    ============================================================ */
    function renderMantraList(mantras) {

        let html = "";

        if (mantras.length === 0) {

            html = `<p>No mantra notes found.</p>`;

        } else {

            mantras.forEach((item, index) => {

                let num = index + 1;

                html += `
                    <div class="goal-item styled-journal">

                        <div class="journal-index">${num}</div>

                        <div class="journal-content">

                            <p class="journal-note">
                                <strong>Deity:</strong> ${item.deity}<br>
                                <strong>Mantra:</strong> ${item.mantra_text}<br>
                                <strong>Goal:</strong> ${item.goal_type} (${item.goal_value})<br>
                                <strong>Mode:</strong> ${item.output_mode}
                            </p>

                            <small class="journal-date">
                                Created on : ${item.created_at}
                            </small>

                            <div class="journal-actions">
                                <button class="btn btn-sm btn-danger deleteMantraBtn"
                                    data-id="${item.id}">
                                    Delete
                                </button>
                            </div>

                        </div>

                    </div>
                `;
            });
        }

        $("#mantraSavedList").html(html);
    }


    /* ============================================================
        4. DELETE MANTRA NOTE
    ============================================================ */
    $(document).on("click", ".deleteMantraBtn", function () {

        let id = $(this).data("id");

        if (!confirm("Delete this mantra note?")) return;

        $.ajax({
            url: "module/mantra_action.php",
            type: "POST",
            data: {
                action: "delete",
                mantra_id: id,
                csrf_token: csrf()
            },
            dataType: "json",
            success: function (res) {
                alert(res.message);
                if (res.status) loadMantras();
            }
        });

    });

});

});


