<?php
$mantrasFilterActiveGodKey = $mantrasFilterActiveGodKey ?? '';
$mantrasFilterActiveMantraId = isset($mantrasFilterActiveMantraId) ? (int) $mantrasFilterActiveMantraId : 0;
$mantrasFilterDefaultTab = $mantrasFilterDefaultTab ?? 'gods';
$mantrasFilterVisibleTabs = $mantrasFilterVisibleTabs ?? 'both';
$mantrasFilterCheckAllGods = ($mantrasFilterActiveGodKey === '');

$showGodsPanel = in_array($mantrasFilterVisibleTabs, ['gods', 'both'], true);
$showMantrasPanel = in_array($mantrasFilterVisibleTabs, ['mantras', 'both'], true);
$showFilterTabs = $showGodsPanel && $showMantrasPanel;

if ($showGodsPanel && !$showMantrasPanel) {
    $godsTabActive = true;
    $mantrasTabActive = false;
} elseif ($showMantrasPanel && !$showGodsPanel) {
    $godsTabActive = false;
    $mantrasTabActive = true;
} else {
    $godsTabActive = ($mantrasFilterDefaultTab !== 'mantras');
    $mantrasTabActive = ($mantrasFilterDefaultTab === 'mantras');
}
?>
<div class="col-lg-3 col-md-4 mantras-filter-col mantras-sidebar">
  <div class="mantras-filter-panel section-box">
    <?php if ($showFilterTabs) { ?>
      <ul class="nav nav-tabs mb-3" id="mantraFilterTab" role="tablist">
        <?php if ($showGodsPanel) { ?>
          <li class="nav-item" role="presentation">
            <button class="nav-link<?php echo $godsTabActive ? ' active' : ''; ?>" id="gods-tab" data-bs-toggle="tab" data-bs-target="#gods" type="button">
              <i class="fa-solid fa-gopuram"></i> Gods
            </button>
          </li>
        <?php } ?>
        <?php if ($showMantrasPanel) { ?>
          <li class="nav-item" role="presentation">
            <button class="nav-link<?php echo $mantrasTabActive ? ' active' : ''; ?>" id="mantra-tab" data-bs-toggle="tab" data-bs-target="#allmantras" type="button">
              <i class="fa-solid fa-book"></i> Mantras
            </button>
          </li>
        <?php } ?>
      </ul>
    <?php } ?>

    <div class="tab-content">
      <?php if ($showGodsPanel) { ?>
        <div class="tab-pane fade<?php echo ($godsTabActive || !$showFilterTabs) ? ' show active' : ''; ?>" id="gods" role="tabpanel">
          <h4 class="section-title"><i class="fa-solid fa-gopuram"></i> Gods</h4>
          <div class="scroll-box">
            <?php if (!empty($godCategoryFilters)) { ?>
              <label class="item-box">
                <input type="checkbox" class="godCheck" value="all" data-title="all"<?php echo $mantrasFilterCheckAllGods ? ' checked' : ''; ?>>
                <span>All <span class="filter-count">(<?php echo (int) $totalGodCount; ?>)</span></span>
              </label>
              <div class="god-accordion">
                <?php foreach ($godCategoryFilters as $categoryIndex => $category) {
                    $categoryLabel = htmlspecialchars($category['label'], ENT_QUOTES, 'UTF-8');
                ?>
                  <div class="god-accordion-item">
                    <div class="god-accordion-head-row">
                      <label class="item-box">
                        <input type="checkbox" class="godCheck godCategoryCheck" value="category-<?php echo $categoryIndex; ?>" data-category-index="<?php echo $categoryIndex; ?>">
                        <span><?php echo $categoryLabel; ?> <span class="filter-count">(<?php echo (int) $category['count']; ?>)</span></span>
                      </label>
                      <button type="button" class="god-accordion-toggle" data-accordion-target="god-accordion-<?php echo $categoryIndex; ?>" aria-label="Expand <?php echo $categoryLabel; ?>">+</button>
                    </div>
                    <div class="god-accordion-body" id="god-accordion-<?php echo $categoryIndex; ?>" style="display: none;">
                      <?php foreach ($category['gods'] as $god) {
                          $godTitle = htmlspecialchars($god['title'], ENT_QUOTES, 'UTF-8');
                          $godTitleKey = htmlspecialchars($god['title_key'], ENT_QUOTES, 'UTF-8');
                          $godDetailsUrl = htmlspecialchars(getMantraDetailsUrl($god['title']), ENT_QUOTES, 'UTF-8');
                          $isActiveGod = ($mantrasFilterActiveGodKey !== '' && $mantrasFilterActiveGodKey === $god['title_key']);
                      ?>
                        <label class="item-box">
                          <input type="checkbox" class="godCheck" value="<?php echo $godTitleKey; ?>" data-god-title="<?php echo $godTitle; ?>" data-god-url="<?php echo $godDetailsUrl; ?>"<?php echo $isActiveGod ? ' checked' : ''; ?>>
                          <span><?php echo $godTitle; ?></span>
                        </label>
                      <?php } ?>
                    </div>
                  </div>
                <?php } ?>
              </div>
            <?php } else { ?>
              <div class="text-muted">No gods found.</div>
            <?php } ?>
          </div>
        </div>
      <?php } ?>

      <?php if ($showMantrasPanel) { ?>
        <div class="tab-pane fade<?php echo ($mantrasTabActive || !$showFilterTabs) ? ' show active' : ''; ?>" id="allmantras" role="tabpanel">
          <h4 class="section-title"><i class="fa-solid fa-book"></i> All Mantras</h4>
          <div class="scroll-box">
            <?php if (!empty($mantraTitleFilters)) { ?>
              <?php foreach ($mantraTitleFilters as $mantraIndex => $mantraFilter) {
                  $title2 = htmlspecialchars($mantraFilter['title'], ENT_QUOTES, 'UTF-8');
                  $filterId = (int) $mantraFilter['index_id'];
                  $isActiveMantra = ($mantrasFilterActiveMantraId > 0 && $mantrasFilterActiveMantraId === $filterId);
                  $isFirstMantra = ($mantraIndex === 0);
                  $isCheckedMantra = $isActiveMantra || ($mantrasFilterActiveMantraId === 0 && $isFirstMantra && ($mantrasTabActive || !$showGodsPanel));
              ?>
                <label class="item-box">
                  <input class="mantras-lst" type="radio" name="mantraTitl" value="<?php echo $filterId; ?>" data-keyword="<?php echo $title2; ?>" id="mantras<?php echo $filterId; ?>"<?php echo $isCheckedMantra ? ' checked' : ''; ?>>
                  <span><?php echo $title2; ?></span>
                </label>
              <?php } ?>
            <?php } else { ?>
              <p class="text-center text-muted">No Mantras or Stotras found in this category.</p>
            <?php } ?>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</div>
