<style>
:root {
  --mantras-accent: #2c4da5;
  --mantras-accent-dark: #19378a;
  --mantras-card-radius: 16px;
  --mantras-muted: #6b7280;
}

.mantras-page-row {
  align-items: flex-start;
}

.mantras-filter-col {
  align-self: flex-start;
  height: fit-content;
}

.mantras-filter-col .theiaStickySidebar {
  width: 100%;
}

.mantras-filter-panel {
  max-height: calc(100vh - 100px);
  display: flex;
  flex-direction: column;
  margin-top: 0;
}

.mantras-filter-panel .nav-tabs .nav-link {
  color: var(--mantras-accent-dark);
  font-weight: 600;
  font-size: 0.9rem;
}

.mantras-filter-panel .nav-tabs .nav-link.active {
  color: var(--mantras-accent);
  border-color: #dee2e6 #dee2e6 #fff;
}

.mantras-filter-panel .tab-content {
  flex: 1;
  min-height: 0;
}

.mantras-filter-panel .scroll-box {
  max-height: calc(100vh - 240px);
  overflow-y: auto;
}

.mantras-filter-panel .item-box {
  padding: 5px 0;
  border-radius: 0;
  border: none;
  background: #fff;
  margin-bottom: 0;
  display: flex;
  gap: 12px;
  align-items: center;
  font-weight: 500;
  font-size: 14px;
  border-bottom: 1px solid #f2f2f2;
  cursor: pointer;
}

.mantras-filter-panel .item-box .filter-count {
  color: var(--mantras-muted);
  font-weight: 500;
  margin-left: 4px;
}

.mantras-filter-panel .section-title {
  color: var(--mantras-accent-dark);
  font-weight: 600;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 1.1rem;
}

.mantras-filter-panel.section-box {
  background: #ffffff;
  border-radius: var(--mantras-card-radius);
  padding: 22px;
  margin-top: 10px;
  box-shadow: 0 4px 15px rgba(35, 95, 200, 0.08);
  border-left: 4px solid var(--mantras-accent);
  transition: transform .18s ease, box-shadow .18s ease;
}

.mantras-filter-panel.section-box:hover {
  transform: none;
  box-shadow: 0 4px 15px rgba(35, 95, 200, 0.08);
}

.god-accordion {
  margin-top: 8px;
}

.god-accordion-head-row {
  display: flex;
  align-items: center;
  gap: 8px;
  border-bottom: 1px solid #f2f2f2;
  padding: 4px 0;
}

.god-accordion-head-row .item-box {
  flex: 1;
  margin-bottom: 0;
  font-weight: 700;
  font-size: 14px;
}

.god-accordion-toggle {
  border: none;
  background: transparent;
  color: #1f2c47;
  font-size: 18px;
  line-height: 1;
  padding: 6px 8px;
  cursor: pointer;
}

.god-accordion-toggle:hover {
  color: var(--mantras-accent);
}

.god-accordion-body {
  padding: 4px 0 8px 8px;
}

.god-accordion-body .item-box {
  font-weight: 500;
}

.mantra-item {
  padding: 10px;
}

.mantra-card {
  display: block;
  background: #fff;
  border: 10px solid rgba(246, 222, 22, 0.7);
  border-radius: 12px;
  padding: 18px 20px;
  text-align: center;
  text-decoration: none;
  transition: 0.25s ease;
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
}

.mantra-card:hover,
.mantra-card.mantra-card--active {
  transform: translateY(-4px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
  border-color: rgba(246, 222, 22, 1);
  background: #fffaf1;
}

.mantra-title {
  font-size: 20px;
  color: #4b341a;
  font-weight: 600;
  font-family: "Tiro Devanagari Hindi", serif;
}

@media (max-width: 991px) {
  .mantras-filter-col {
    position: static;
  }

  .mantras-filter-panel {
    max-height: none;
    margin-bottom: 1rem;
  }

  .mantras-filter-panel .scroll-box {
    max-height: 280px;
  }
}
</style>
