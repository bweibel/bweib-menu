(() => {
  const root = document.querySelector(".bweib-menu-display");
  if (!root) return;

  const resultsEl = root.querySelector("[data-results]");
  const loadingEl = root.querySelector("[data-loading]");

  const tabs = Array.from(root.querySelectorAll(".bweib-tab"));
  const chips = Array.from(root.querySelectorAll(".bweib-chip"));

  const searchInput = root.querySelector(".bweib-menu-search");
  const availableToggle = root.querySelector(".bweib-menu-available");

  const state = {
    section: "",
    q: "",
    available: true,
    dietary: new Set(),
    badges: new Set(),
    spice_level: "",
  };

  const setLoading = (on) => {
    if (!loadingEl) return;
    loadingEl.hidden = !on;
  };

  const buildQueryString = () => {
    const params = new URLSearchParams();

    if (state.section) params.set("section", state.section);
    if (state.q) params.set("q", state.q);

    params.set("available", state.available ? "1" : "0");

    for (const f of state.dietary) params.append("dietary[]", f);
    for (const b of state.badges) params.append("badges[]", b);

    if (state.spice_level) params.set("spice_level", state.spice_level);

    return params.toString();
  };

  const renderItems = (items) => {
    // Group by first section slug (MVP)
    const bySection = new Map();
    for (const item of items) {
      const sectionSlug = item.section?.[0] ?? "uncategorized";
      if (!bySection.has(sectionSlug)) bySection.set(sectionSlug, []);
      bySection.get(sectionSlug).push(item);
    }

    const sectionTitleFromSlug = (slug) =>
      slug.replace(/-/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());

    let html = "";
    for (const [sectionSlug, sectionItems] of bySection.entries()) {
      html += `<section class="bweib-menu-section" data-section="${sectionSlug}">
        <div><h3 class="bweib-menu-section-title">${sectionTitleFromSlug(sectionSlug)}</h3></div>
        <div class="bweib-menu-items">
      `;

      for (const it of sectionItems) {
        html += `
          <article class="bweib-menu-item">
            <header class="bweib-menu-item-header">
              <h4 class="bweib-menu-item-name">${escapeHtml(it.title)}</h4>
              <div class="bweib-menu-item-prices">
                ${it.price_display ? `<span class="bweib-price">${escapeHtml(it.price_display)}</span>` : ""}
                ${it.price_secondary ? `<span class="bweib-price-secondary">${escapeHtml(it.price_secondary)}</span>` : ""}
              </div>
            </header>

            ${it.description ? `<div class="bweib-menu-item-desc"><p>${escapeHtml(it.description)}</p></div>` : ""}
            ${it.modifiers ? `<div class="bweib-menu-item-mods"><p>${escapeHtml(it.modifiers)}</p></div>` : ""}

            <div class="bweib-menu-item-flags">
              ${(it.dietary_flags || []).map(f => `<span class="bweib-pill">${escapeHtml(String(f).toUpperCase())}</span>`).join("")}
              ${(it.spice_level && it.spice_level !== "none") ? `<span class="bweib-pill">${escapeHtml(it.spice_level)}</span>` : ""}
              ${(it.badges || []).map(b => `<span class="bweib-pill">${escapeHtml(String(b))}</span>`).join("")}
            </div>
          </article>
        `;
      }

      html += `</div></section>`;
    }

    resultsEl.innerHTML = html || `<p class="bweib-menu-empty">No items match your filters.</p>`;
  };

  const escapeHtml = (str) =>
    str.replace(/[&<>"']/g, (ch) => ({
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      "\"": "&quot;",
      "'": "&#039;",
    }[ch]));

  let fetchAbort = null;

  const run = async () => {
    if (!resultsEl) return;

    const qs = buildQueryString();
    const url = `${window.location.origin}/wp-json/bweib-menu/v1/items?${qs}`;

    if (fetchAbort) fetchAbort.abort();
    fetchAbort = new AbortController();

    setLoading(true);

    try {
      const res = await fetch(url, { signal: fetchAbort.signal });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);

      const data = await res.json();
      renderItems(data.items || []);
    } catch (e) {
      if (e.name === "AbortError") return;
      resultsEl.innerHTML = `<p class="bweib-menu-error">Could not load menu items.</p>`;
    } finally {
      setLoading(false);
    }
  };

  // Tabs (section)
  tabs.forEach((btn) => {
    btn.addEventListener("click", () => {
      tabs.forEach(b => b.classList.remove("is-active"));
      btn.classList.add("is-active");
      state.section = btn.dataset.section || "";
      run();
    });
  });

  // Chips (dietary/badges)
  chips.forEach((btn) => {
    btn.addEventListener("click", () => {
      btn.classList.toggle("is-active");

      const type = btn.dataset.filterType;
      const val = btn.dataset.filterValue;

      if (type === "dietary") {
        btn.classList.contains("is-active") ? state.dietary.add(val) : state.dietary.delete(val);
      }
      if (type === "badges") {
        btn.classList.contains("is-active") ? state.badges.add(val) : state.badges.delete(val);
      }
      run();
    });
  });

  // Search (debounced)
  let t = null;
  searchInput?.addEventListener("input", () => {
    clearTimeout(t);
    t = setTimeout(() => {
      state.q = searchInput.value.trim();
      run();
    }, 200);
  });

  // Available toggle
  availableToggle?.addEventListener("change", () => {
    state.available = !!availableToggle.checked;
    run();
  });

  // Default section preselect
  const defaultSection = root.dataset.defaultSection;
  if (defaultSection) {
    const match = tabs.find(t => t.dataset.section === defaultSection);
    if (match) match.click();
  }
})();
