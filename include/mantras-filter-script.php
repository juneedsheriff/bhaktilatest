<?php
$mantrasFilterMode = $mantrasFilterMode ?? 'listing';
$mantrasFilterGodCategoryKeysMap = $mantrasFilterGodCategoryKeysMap ?? [];
$mantrasFilterGodsPageData = $mantrasFilterGodsPageData ?? [];
$mantrasFilterMantraItemsData = $mantrasFilterMantraItemsData ?? [];
$mantrasFilterMantraKeywordsMap = $mantrasFilterMantraKeywordsMap ?? [];
$mantrasTitleDetailCurrentId = isset($mantrasTitleDetailCurrentId) ? (int) $mantrasTitleDetailCurrentId : 0;
?>
<script>
const mantrasFilterMode = <?php echo json_encode($mantrasFilterMode, JSON_UNESCAPED_UNICODE); ?>;
let mantrasTitleDetailCurrentId = <?php echo (int) $mantrasTitleDetailCurrentId; ?>;
const godsPageData = <?php echo json_encode($mantrasFilterGodsPageData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
const mantraItemsData = <?php echo json_encode($mantrasFilterMantraItemsData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
const mantraKeywordsMap = <?php echo json_encode($mantrasFilterMantraKeywordsMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
const godCategoryKeysMap = <?php echo json_encode($mantrasFilterGodCategoryKeysMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
let currentMantraFilterId = null;

function isGodNavigateMode() {
    return mantrasFilterMode === "navigate" || mantrasFilterMode === "detail";
}

function getGodFilterUrl($input) {
    return $input.attr("data-god-url") || "";
}

function getMantraKeyword($input) {
    return String($input.attr("data-keyword") || $input.data("keyword") || "").toLowerCase().trim();
}

function getMantraFilterKeyword(filterId) {
    const id = String(filterId);
    return String(mantraKeywordsMap[id] || mantraKeywordsMap[Number(id)] || "").toLowerCase().trim();
}

function findMantraItemById(itemId) {
    const targetId = Number(itemId);
    return mantraItemsData.find(function (item) {
        return Number(item.index_id) === targetId;
    }) || null;
}

function scrollToMantraTitleDetailSection() {
    const scrollTarget = document.getElementById("mantraTitleDetailView") || document.getElementById("second-section");
    if (!scrollTarget) {
        return;
    }

    const top = scrollTarget.getBoundingClientRect().top + window.pageYOffset - 100;
    window.scrollTo({
        top: Math.max(top, 0),
        behavior: "smooth"
    });
}

function renderMantraTitleDetail(item, shouldScroll) {
    const $view = $("#mantraTitleDetailView");
    if (!$view.length) {
        return;
    }

    if (!item) {
        $view.html('<p class="text-muted mb-0">No Mantra available for this filter.</p>');
        if (shouldScroll) {
            scrollToMantraTitleDetailSection();
        }
        return;
    }

    mantrasTitleDetailCurrentId = Number(item.index_id);

    let audioHtml = "";
    if (item.audio) {
        const audioSrc = "app/uploads/mantras_audio/" + item.audio;
        audioHtml = `
            <div style="width: 300px; height: 50px; display: inline-block; text-align: center; padding: 10px;">
                <audio controls style="width: 100%;">
                    <source src="${audioSrc}" type="audio/ogg">
                    <source src="${audioSrc}" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
            </div>
        `;
    }

    $view.html(`
        <h3 class="fw-bold text-primary font-caveat page-header-title mb-3">${item.title_clean || ""}</h3>
        ${audioHtml}
        <div class="sth-text text-dark">${item.content || ""}</div>
    `);

    $("#mantraTitleList .mantra-card").removeClass("mantra-card--active");
    $(`#mantraTitleList .mantra-card[data-mantra-id="${mantrasTitleDetailCurrentId}"]`).addClass("mantra-card--active");

    if (shouldScroll) {
        requestAnimationFrame(function () {
            scrollToMantraTitleDetailSection();
        });
    }
}

function renderGodCards(items) {
    if (!items.length) {
        $("#mantraList").html('<div class="muted col-12">No Mantras available.</div>');
        return;
    }

    let html = "";
    items.forEach(function (item) {
        const photoSrc = item.photo_src || 'assets/images/default-image.png';
        const title = item.title_clean || '';
        const detailsUrl = item.details_url || ('mantras-details.php?godname=' + encodeURIComponent(item.title || ''));
        html += `
            <div class="col-lg-4 col-sm-6 iconic-featured-col">
                <div class="product-item1">
                    <a href="${detailsUrl}">
                        <img class="iconic-featured-card-image" src="${photoSrc}" alt="${title}">
                        <div class="iconic-featured-card-title">
                            <span class="shiny" style="margin: 0">
                                <span style="margin: 0">${title}</span>
                            </span>
                        </div>
                    </a>
                </div>
            </div>
        `;
    });

    $("#mantraList").html(html);
}

function getMantraTitleListContainer() {
    if ($("#mantraTitleList").length) {
        return $("#mantraTitleList");
    }

    return $("#mantraList");
}

function renderMantraTitleCards(items) {
    const $container = getMantraTitleListContainer();

    if (!items.length) {
        $container.html('<div class="text-muted col-12">No Mantras available.</div>');
        return;
    }

    let html = "";
    items.forEach(function (item) {
        const isActive = mantrasTitleDetailCurrentId > 0 && Number(item.index_id) === mantrasTitleDetailCurrentId;
        const parentQuery = currentMantraFilterId
            ? `&mantraFilter=${encodeURIComponent(currentMantraFilterId)}`
            : "";
        const cardHref = mantrasFilterMode === "title_detail"
            ? "#"
            : `mantras_title_details.php?id=${item.index_id}${parentQuery}`;
        html += `
            <div class="mantra-item col-md-4 col-sm-6">
                <a href="${cardHref}" data-mantra-id="${item.index_id}" class="mantra-card${isActive ? ' mantra-card--active' : ''}">
                    <div class="mantra-title">${item.title_clean}</div>
                </a>
            </div>
        `;
    });

    $container.html(html);
}

function normalizeTitleKey(title) {
    return (title || "").trim().toLowerCase().replace(/\s+/g, " ");
}

function getSelectedGodTitleKeys() {
    const selectedSet = new Set();

    $(".godCategoryCheck:checked").each(function () {
        const categoryIndex = $(this).data("category-index");
        (godCategoryKeysMap[categoryIndex] || []).forEach(function (titleKey) {
            selectedSet.add(String(titleKey));
        });
    });

    $(".godCheck:checked").not(".godCategoryCheck").not("[value='all']").each(function () {
        selectedSet.add(String($(this).val()));
    });

    return selectedSet;
}

function syncCategoryCheckboxes() {
    $(".godCategoryCheck").each(function () {
        const $category = $(this);
        const categoryIndex = $category.data("category-index");
        const $childChecks = $category.closest(".god-accordion-item").find(".god-accordion-body .godCheck");
        const checkedCount = $childChecks.filter(":checked").length;
        const totalCount = $childChecks.length;

        $category.prop("checked", totalCount > 0 && checkedCount === totalCount);
        $category.prop("indeterminate", checkedCount > 0 && checkedCount < totalCount);
    });
}

function filterGodsClient() {
    if (mantrasFilterMode !== "listing") {
        return;
    }

    $(".mantras-lst").prop("checked", false);

    if ($(".godCheck[value='all']").is(":checked")) {
        renderGodCards(godsPageData);
        return;
    }

    const selectedSet = getSelectedGodTitleKeys();

    if (!selectedSet.size) {
        renderGodCards(godsPageData);
        return;
    }

    const filtered = godsPageData.filter(function (item) {
        const titleKey = item.title_key || normalizeTitleKey(item.title);
        return selectedSet.has(titleKey);
    });

    renderGodCards(filtered);
}

function filterMantrasByKeyword(filterId, shouldScroll) {
    currentMantraFilterId = filterId;
    const keyword = getMantraFilterKeyword(filterId);
    if (!keyword) {
        getMantraTitleListContainer().html('<div class="text-muted col-12">No Mantras available.</div>');
        if (mantrasFilterMode === "title_detail") {
            renderMantraTitleDetail(null, shouldScroll);
        }
        return;
    }

    const filtered = mantraItemsData.filter(function (item) {
        return (item.title || "").toLowerCase().includes(keyword);
    });

    renderMantraTitleCards(filtered);

    if (mantrasFilterMode === "title_detail") {
        const currentItem = filtered.find(function (item) {
            return Number(item.index_id) === mantrasTitleDetailCurrentId;
        });
        renderMantraTitleDetail(currentItem || filtered[0] || null, shouldScroll);
    }
}

function filterMantrasClient(filterId) {
    if (mantrasFilterMode !== "listing") {
        return;
    }

    $(".godCheck").prop("checked", false);
    filterMantrasByKeyword(filterId);
}

function showGodDetailView() {
    $("#godDetailView, #godBannerSection").show();
    $("#mantraListView").addClass("d-none");
}

function showMantraListingView() {
    $("#godDetailView, #godBannerSection").hide();
    $("#mantraListView").removeClass("d-none");
}

function navigateFromGodFilter($input) {
    if ($input.val() === "all") {
        window.location.href = "mantras-new.php";
        return;
    }

    if ($input.hasClass("godCategoryCheck")) {
        return;
    }

    const godUrl = getGodFilterUrl($input);
    if (godUrl) {
        window.location.href = godUrl;
    }
}

function navigateFromMantraFilter(filterId) {
    window.location.href = "mantras-new.php?mantraFilter=" + encodeURIComponent(filterId);
}

function applyMantraFilterSelection($input) {
    if (mantrasFilterMode === "detail") {
        const $mantraTab = $("#mantra-tab");
        const tabAlreadyActive = $mantraTab.hasClass("active");

        if ($mantraTab.length && !tabAlreadyActive && typeof bootstrap !== "undefined") {
            bootstrap.Tab.getOrCreateInstance($mantraTab[0]).show();
            return;
        }

        showMantraListingView();
        filterMantrasByKeyword($input.val());
        return;
    }

    if (mantrasFilterMode === "title_detail") {
        filterMantrasByKeyword($input.val(), true);
        return;
    }

    if (mantrasFilterMode === "navigate") {
        navigateFromMantraFilter($input.val());
        return;
    }

    filterMantrasClient($input.val());
}

$(document).on("click", ".mantras-filter-panel #gods .scroll-box > label.item-box", function (e) {
    if (!isGodNavigateMode()) {
        return;
    }

    const $input = $(this).find('.godCheck[value="all"]').first();
    if (!$input.length) {
        return;
    }

    e.preventDefault();
    window.location.href = "mantras-new.php";
});

$(document).on("click", ".mantras-filter-panel .god-accordion-body label.item-box", function (e) {
    if (!isGodNavigateMode()) {
        return;
    }

    const $input = $(this).find(".godCheck").first();
    if (!$input.length || $input.hasClass("godCategoryCheck")) {
        return;
    }

    const godUrl = getGodFilterUrl($input);
    if (!godUrl) {
        return;
    }

    e.preventDefault();
    window.location.href = godUrl;
});

$(document).on("change", ".godCheck", function () {
    const $input = $(this);

    if (isGodNavigateMode()) {
        if ($input.hasClass("godCategoryCheck")) {
            const isChecked = $input.is(":checked");
            $input.closest(".god-accordion-item").find(".god-accordion-body .godCheck").prop("checked", isChecked);
            $input.prop("indeterminate", false);
        }
        return;
    }

    if ($input.val() === "all" && $input.is(":checked")) {
        $(".godCheck").not($input).prop("checked", false);
        $(".godCategoryCheck").prop("indeterminate", false);
    } else if ($input.val() !== "all" && $input.is(":checked")) {
        $(".godCheck[value='all']").prop("checked", false);
    }

    if ($input.hasClass("godCategoryCheck")) {
        const isChecked = $input.is(":checked");
        $input.closest(".god-accordion-item").find(".god-accordion-body .godCheck").prop("checked", isChecked);
        $input.prop("indeterminate", false);
    } else if (!$input.hasClass("godCategoryCheck") && $input.closest(".god-accordion-body").length) {
        syncCategoryCheckboxes();
    }

    filterGodsClient();
});

$(document).on("change", ".mantras-lst", function () {
    if (!$(this).is(":checked")) {
        return;
    }

    applyMantraFilterSelection($(this));
});

$(document).on("click", ".mantras-filter-panel #allmantras label.item-box", function () {
    if (mantrasFilterMode === "listing") {
        return;
    }

    const $input = $(this).find(".mantras-lst").first();
    if (!$input.length) {
        return;
    }

    $input.prop("checked", true);
    applyMantraFilterSelection($input);
});

$(document).on("click", "#mantraTitleList .mantra-card", function (e) {
    if (mantrasFilterMode !== "title_detail") {
        return;
    }

    e.preventDefault();

    const itemId = Number($(this).data("mantra-id"));
    const item = findMantraItemById(itemId);
    if (item) {
        renderMantraTitleDetail(item, true);
    }
});

$("#gods-tab").on("shown.bs.tab", function () {
    if (mantrasFilterMode === "detail") {
        showGodDetailView();
        return;
    }

    if (mantrasFilterMode !== "listing") {
        return;
    }

    $(".mantras-lst").prop("checked", false);
    filterGodsClient();
});

$("#mantra-tab").on("shown.bs.tab", function () {
    if (mantrasFilterMode === "navigate") {
        return;
    }

    if (mantrasFilterMode === "detail") {
        showMantraListingView();

        let $selectedMantra = $(".mantras-lst:checked");
        if (!$selectedMantra.length) {
            $selectedMantra = $(".mantras-lst").first().prop("checked", true);
        }

        filterMantrasByKeyword($selectedMantra.val());
        return;
    }

    $(".godCheck").prop("checked", false);

    let $selectedMantra = $(".mantras-lst:checked");
    if (!$selectedMantra.length) {
        $selectedMantra = $(".mantras-lst").first().prop("checked", true);
    }

    filterMantrasClient($selectedMantra.val());
});

$(document).on("click", ".god-accordion-toggle", function (e) {
    e.preventDefault();
    e.stopPropagation();

    const targetId = $(this).data("accordion-target");
    const $body = $("#" + targetId);
    const $icon = $(this);
    const isOpen = $body.is(":visible");

    $(".god-accordion-body").slideUp(180);
    $(".god-accordion-toggle").text("+");

    if (!isOpen) {
        $body.slideDown(180);
        $icon.text("-");
    }
});

function initMantrasFilterSticky() {
    const $sidebar = $(".mantras-sidebar");
    const $content = $(".mantras-content");

    if (!$sidebar.length || !$content.length) {
        return;
    }

    if ($sidebar.data("theiaStickySidebar")) {
        $sidebar.theiaStickySidebar("destroy");
    }

    if (window.innerWidth >= 992) {
        $sidebar.add($content).theiaStickySidebar({
            additionalMarginTop: 90,
            minWidth: 992
        });
    }
}

function openActiveGodAccordion() {
    const $activeGod = $(".god-accordion-body .godCheck:checked").first();
    if (!$activeGod.length) {
        return;
    }

    const $body = $activeGod.closest(".god-accordion-body");
    const $toggle = $body.closest(".god-accordion-item").find(".god-accordion-toggle");

    $body.show();
    $toggle.text("-");
}

$(function () {
    openActiveGodAccordion();
    initMantrasFilterSticky();

    if (mantrasFilterMode === "title_detail") {
        const currentItem = findMantraItemById(mantrasTitleDetailCurrentId);
        if (currentItem) {
            renderMantraTitleDetail(currentItem);
        }

        let $selectedMantra = $(".mantras-lst:checked");
        if (!$selectedMantra.length) {
            $selectedMantra = $(".mantras-lst").first().prop("checked", true);
        }
        if ($selectedMantra.length) {
            filterMantrasByKeyword($selectedMantra.val());
        }
    }

    if (mantrasFilterMode === "listing") {
        const urlParams = new URLSearchParams(window.location.search);
        const mantraFilter = urlParams.get("mantraFilter");

        if (mantraFilter) {
            const $mantraTab = $("#mantra-tab");
            if ($mantraTab.length && typeof bootstrap !== "undefined") {
                bootstrap.Tab.getOrCreateInstance($mantraTab[0]).show();
            }

            const $filterInput = $('.mantras-lst[value="' + mantraFilter + '"]');
            if ($filterInput.length) {
                $filterInput.prop("checked", true);
                currentMantraFilterId = mantraFilter;
                filterMantrasClient(mantraFilter);
            }
        }
    }
});

$(window).on("resize", initMantrasFilterSticky);
</script>
