(() => {
  const base = document.body?.dataset.base || "";

  document.querySelectorAll("[data-drop]").forEach((item) => {
    const btn = item.querySelector(".nav__btn");
    if (!btn) return;
    btn.addEventListener("click", (e) => {
      const open = item.classList.contains("is-open");
      document.querySelectorAll("[data-drop].is-open").forEach((el) => {
        el.classList.remove("is-open");
        el.querySelector(".nav__btn")?.setAttribute("aria-expanded", "false");
      });
      item.classList.toggle("is-open", !open);
      btn.setAttribute("aria-expanded", String(!open));
      e.stopPropagation();
    });
  });

  document.addEventListener("click", () => {
    document.querySelectorAll("[data-drop].is-open").forEach((el) => {
      el.classList.remove("is-open");
      el.querySelector(".nav__btn")?.setAttribute("aria-expanded", "false");
    });
  });

  document.addEventListener("keydown", (e) => {
    if (e.key !== "Escape") return;
    document.querySelectorAll("[data-drop].is-open").forEach((el) => {
      el.classList.remove("is-open");
      el.querySelector(".nav__btn")?.setAttribute("aria-expanded", "false");
    });
  });

  const mapRoot = document.querySelector(".dentamap");
  const mapJson = document.querySelector("#map-data");
  if (!mapRoot || !mapJson) return;

  let data = { teeth: {}, services: {} };
  try {
    data = JSON.parse(mapJson.textContent || "{}");
  } catch {
    /* keep defaults */
  }

  const title = mapRoot.querySelector("[data-map-title]");
  const text = mapRoot.querySelector("[data-map-text]");
  const links = mapRoot.querySelector("[data-map-links]");
  const book = mapRoot.querySelector("[data-map-book]");

  function serviceHref(slug) {
    const name = data.services?.[slug]?.name || slug;
    return `${base}services/service_template.php?service=${encodeURIComponent(name)}`;
  }

  function bookingHref(slug) {
    const name = data.services?.[slug]?.name || "";
    const q = name ? `?service=${encodeURIComponent(name)}` : "";
    return `${base}index.php${q}#booking`;
  }

  function renderTooth(id) {
    const item = data.teeth?.[id];
    mapRoot.querySelectorAll(".mouth-tooth").forEach((el) => {
      el.classList.toggle("is-active", el.dataset.id === id);
    });
    if (!item) return;
    if (title) title.textContent = `Зуб № ${id} — ${item.name}`;
    if (text) text.textContent = item.hint || item.group || "";
    if (links) {
      links.innerHTML = "";
      (item.services || []).forEach((slug) => {
        const svc = data.services?.[slug];
        if (!svc) return;
        const a = document.createElement("a");
        a.href = serviceHref(slug);
        a.textContent = svc.name;
        links.appendChild(a);
      });
    }
    if (book) {
      book.hidden = false;
      book.href = bookingHref((item.services || [])[0] || "treatment");
    }
  }

  mapRoot.addEventListener("click", (e) => {
    const target = e.target.closest("[data-kind='tooth']");
    if (!target) return;
    renderTooth(target.dataset.id);
  });

  mapRoot.addEventListener("keydown", (e) => {
    if (e.key !== "Enter" && e.key !== " ") return;
    const target = e.target.closest("[data-kind='tooth']");
    if (!target) return;
    e.preventDefault();
    renderTooth(target.dataset.id);
  });
})();
