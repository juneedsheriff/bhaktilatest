$(document).ready(function () {

     function loadCategories() {
        $.post("module/forum-api.php", { action: "list_categories" }, function (res) {
            if (!res.status) return;

            let html = '<option value="">Select Category</option>';
            res.categories.forEach(c => {
                html += `<option value="${c.category_id}">${c.category_name}</option>`;
            });

            $("#topic_category").html(html);
            $("#newTopicCategory").html(html);
        }, "json");
    }

    $(document).on("click", "#refreshCats", function () {
        loadCategories();
    });



    loadCategories(); // Load categories on page load

    /* ============================================================
        OPEN NEW TOPIC MODAL
    ============================================================ */
    $("#openNewTopicBtn").click(function () {
        $("#newTopicForm")[0].reset();
        $("#newTopicModal").modal("show");
    });

    /* ============================================================
        ADD TOPIC (WITH VALIDATION + MODAL)
    ============================================================ */
    $("#newTopicForm").submit(function (e) {
        e.preventDefault();

        let category = $("#newTopicCategory").val();
        let title = $("#newTopicTitle").val().trim();
        let content = $("#newTopicContent").val().trim();

        if (!category) return alert("Please select a category.");
        if (title === "") return alert("Title cannot be empty.");
        if (content === "") return alert("Content cannot be empty.");

        let formData = $(this).serialize() + "&action=add_topic";

        $.post("module/forum-api.php", formData, function (res) {
            alert(res.message);

            if (res.status) {
                $("#newTopicModal").modal("hide");
                loadTopics();
            }

        }, "json");
    });


    /* ============================================================
        DELETE TOPIC
    ============================================================ */
    $(document).on("click", ".deleteTopicBtn", function () {
        let id = $(this).data("id");

        if (!confirm("Delete topic?")) return;

        $.post("module/forum-api.php", {
            action: "delete_topic",
            topic_id: id
        }, function (res) {
            alert(res.message);
            if (res.status) loadTopics();
        }, "json");
    });


    /* ============================================================
        ADD REPLY (VALIDATION)
    ============================================================ */
    $("#addReplyBtn").click(function () {

        let reply = $("#replyContent").val().trim();
        let topic_id = $("#topic_id").val();

        if (reply === "") return alert("Reply cannot be empty.");

        $.post("module/forum-api.php", {
            action: "add_reply",
            topic_id: topic_id,
            reply_text: reply
        }, function (res) {
            alert(res.message);

            if (res.status) {
                viewTopic(topic_id);
                $("#replyContent").val("");
            }

        }, "json");
    });


    /* ============================================================
        DELETE REPLY
    ============================================================ */
    $(document).on("click", ".deleteReplyBtn", function () {
        let id = $(this).data("id");

        $.post("module/forum-api.php", {
            action: "delete_reply",
            reply_id: id
        }, function (res) {
            alert(res.message);
            if (res.status) viewTopic($("#topic_id").val());
        }, "json");
    });


    /* ============================================================
        LIKE TOPIC
    ============================================================ */
    $(document).on("click", "#likeTopicBtn", function () {
        $.post("module/forum-api.php", {
            action: "like_topic",
            topic_id: $(this).data("id")
        }, function (res) { alert(res.message); }, "json");
    });


    /* ============================================================
        LIKE REPLY
    ============================================================ */
    $(document).on("click", ".likeReplyBtn", function () {
        $.post("module/forum-api.php", {
            action: "like_reply",
            reply_id: $(this).data("id")
        }, function (res) { alert(res.message); }, "json");
    });


    /* ============================================================
        LOAD TOPICS
    ============================================================ */
    function loadTopics() {
        $.post("module/forum-api.php", {
            action: "list_topics"
        }, function (res) {
            if (res.status) renderTopicList(res.topics);
        }, "json");
    }

    loadTopics();


    /* ============================================================
        RENDER TOPIC LIST
    ============================================================ */
    function renderTopicList(topics) {
        let html = "";

        topics.forEach(t => {
        html += `
            <div class="forum-topic topic-row">
                <h4 class="topic-title">${t.topic_title}</h4>
                <small class="muted">${t.created_at}</small>
                <br>
                <button class="btn btn-primary btn-sm mt-2" onclick="viewTopic(${t.topic_id})">View</button>
                ${IS_LOGGED_IN ? `
                    <button class="btn btn-danger btn-sm mt-2 deleteTopicBtn" 
                        data-id="${t.topic_id}">
                        Delete
                    </button>
                ` : ""}
            </div>
        `;
    });

    $("#topicList").html(html);
    }


    /* ============================================================
        VIEW SINGLE TOPIC
    ============================================================ */
    window.viewTopic = function (topic_id) {

        $.post("module/forum-api.php", {
            action: "view_topic",
            topic_id: topic_id
        }, function (res) {

            if (!res.status) return;

            let t = res.topic;

            // Switch layout
            $("#topicsArea").hide();
            $("#topicViewArea").show();

            // Fill topic header
            $("#topicHeader").html(`
                <h3>${t.topic_title}</h3>
                <p>${t.topic_content}</p>
                <button id="likeTopicBtn" data-id="${t.topic_id}" class="btn btn-success btn-sm">Like</button>
                <input type="hidden" id="topic_id" value="${t.topic_id}">
                <hr>
            `);

            // Render replies
            renderReplies(res.replies);

        }, "json");
    }


    /* ============================================================
        RENDER REPLIES
    ============================================================ */
    function renderReplies(replies) {
        let html = "";

        replies.forEach(r => {
            html += `
                <div class="reply-box post">
                    <p>${r.reply_text}</p>
                    <small class="muted">${r.created_at}</small>
                    <br>
                    <button class="btn btn-sm btn-outline-success likeReplyBtn" data-id="${r.reply_id}">Like</button>
                    <button class="btn btn-sm btn-danger deleteReplyBtn" data-id="${r.reply_id}">Delete</button>
                </div>
            `;
        });

        $("#postList").html(html);
    }


    /* ============================================================
        BACK BUTTON
    ============================================================ */
    $("#backToTopics").click(function () {
        $("#topicViewArea").hide();
        $("#topicsArea").show();
    });


});
