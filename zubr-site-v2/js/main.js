(() => {
  const header = document.querySelector("[data-header]");
  const burger = document.querySelector("[data-burger]");
  const mobileMenu = document.querySelector("[data-mobile-menu]");
  const form = document.querySelector("#booking-form");
  const doctorSelect = document.querySelector("[data-doctor-select]");
  const serviceSelect = document.querySelector("[data-service-select]");
  const statusEl = document.querySelector("[data-form-status]");
  const calTitle = document.querySelector("[data-cal-title]");
  const calGrid = document.querySelector("[data-cal-grid]");
  const calPrev = document.querySelector("[data-cal-prev]");
  const calNext = document.querySelector("[data-cal-next]");
  const dateInput = document.querySelector("[data-date-input]");
  const timeInput = document.querySelector("[data-time-input]");
  const selectedDateEl = document.querySelector("[data-selected-date]");
  const timeSlotsEl = document.querySelector("[data-time-slots]");
  const reviewsScroller = document.querySelector("[data-reviews-scroller]");

  const apiBase = (() => {
    const fromBody = (document.body?.dataset.base || "").replace(/\\/g, "/");
    if (fromBody) return fromBody.replace(/\/?$/, "/") + "api";
    const path = window.location.pathname.replace(/\\/g, "/");
    if (path.includes("/services/")) return "../api";
    return "api";
  })();

  let weekdaySlots = ["09:00", "10:00", "11:00", "12:30", "14:00", "15:30", "17:00", "18:30"];
  let saturdaySlots = ["10:00", "11:30", "13:00", "15:00"];
  let weekdays = [1, 2, 3, 4, 5, 6];
  const exceptions = new Map();

  const monthNames = [
    "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь",
    "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь",
  ];

  const view = new Date();
  view.setDate(1);
  view.setHours(0, 0, 0, 0);

  let selectedDate = null;
  let selectedTime = null;

  function todayStart() {
    const d = new Date();
    d.setHours(0, 0, 0, 0);
    return d;
  }

  function toISO(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, "0");
    const d = String(date.getDate()).padStart(2, "0");
    return `${y}-${m}-${d}`;
  }

  function formatLong(date) {
    return new Intl.DateTimeFormat("ru-RU", {
      weekday: "long",
      day: "numeric",
      month: "long",
    }).format(date);
  }

  function isoDow(date) {
    const d = date.getDay();
    return d === 0 ? 7 : d;
  }

  function isDisabledDay(date) {
    if (date < todayStart()) return true;
    const iso = toISO(date);
    if (exceptions.has(iso)) {
      const slots = exceptions.get(iso);
      return !slots || slots.length === 0;
    }
    return !weekdays.includes(isoDow(date));
  }

  function slotsFor(date) {
    const iso = toISO(date);
    if (exceptions.has(iso)) return exceptions.get(iso) || [];
    return isoDow(date) === 6 ? saturdaySlots : weekdaySlots;
  }

  function renderCalendar() {
    if (!calGrid || !calTitle) return;

    const year = view.getFullYear();
    const month = view.getMonth();
    calTitle.textContent = `${monthNames[month]} ${year}`;
    calGrid.innerHTML = "";

    const firstDay = new Date(year, month, 1);
    let startWeekday = firstDay.getDay();
    startWeekday = startWeekday === 0 ? 6 : startWeekday - 1;

    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrev = new Date(year, month, 0).getDate();

    for (let i = 0; i < startWeekday; i += 1) {
      const dayNum = daysInPrev - startWeekday + i + 1;
      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "calendar__day is-muted";
      btn.textContent = String(dayNum);
      btn.tabIndex = -1;
      btn.disabled = true;
      calGrid.appendChild(btn);
    }

    const today = todayStart();

    for (let day = 1; day <= daysInMonth; day += 1) {
      const date = new Date(year, month, day);
      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "calendar__day";
      btn.textContent = String(day);
      btn.dataset.iso = toISO(date);

      if (date.getTime() === today.getTime()) btn.classList.add("is-today");
      if (isDisabledDay(date)) btn.disabled = true;

      if (selectedDate && toISO(date) === toISO(selectedDate)) {
        btn.classList.add("is-selected");
      }

      btn.addEventListener("click", () => selectDate(date));
      calGrid.appendChild(btn);
    }

    const totalCells = calGrid.children.length;
    const remainder = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
    for (let i = 1; i <= remainder; i += 1) {
      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "calendar__day is-muted";
      btn.textContent = String(i);
      btn.disabled = true;
      calGrid.appendChild(btn);
    }
  }

  function renderSlots() {
    if (!timeSlotsEl || !selectedDateEl) return;
    timeSlotsEl.innerHTML = "";

    if (!selectedDate) {
      selectedDateEl.textContent = "Сначала выберите дату в календаре";
      timeSlotsEl.innerHTML = '<p class="slots__empty">Слоты появятся после выбора даты</p>';
      return;
    }

    selectedDateEl.textContent = formatLong(selectedDate);
    const slots = slotsFor(selectedDate);

    if (!slots.length) {
      timeSlotsEl.innerHTML = '<p class="slots__empty">На эту дату нет свободных слотов</p>';
      return;
    }

    slots.forEach((slot) => {
      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "slot";
      btn.textContent = slot;
      if (selectedTime === slot) btn.classList.add("is-active");
      btn.addEventListener("click", () => {
        selectedTime = slot;
        if (timeInput) timeInput.value = slot;
        renderSlots();
        if (statusEl) {
          statusEl.textContent = "";
          statusEl.className = "form__status";
        }
      });
      timeSlotsEl.appendChild(btn);
    });
  }

  function selectDate(date) {
    if (isDisabledDay(date)) return;
    selectedDate = date;
    selectedTime = null;
    if (dateInput) dateInput.value = toISO(date);
    if (timeInput) timeInput.value = "";
    renderCalendar();
    renderSlots();
  }

  function closeMenu() {
    if (!burger || !mobileMenu) return;
    burger.setAttribute("aria-expanded", "false");
    burger.setAttribute("aria-label", "Открыть меню");
    mobileMenu.hidden = true;
  }

  function toggleMenu() {
    if (!burger || !mobileMenu) return;
    const open = burger.getAttribute("aria-expanded") === "true";
    burger.setAttribute("aria-expanded", String(!open));
    burger.setAttribute("aria-label", open ? "Открыть меню" : "Закрыть меню");
    mobileMenu.hidden = open;
  }

  function setInvalid(el, invalid) {
    el?.classList.toggle("is-invalid", invalid);
  }

  function validate(formEl) {
    let ok = true;
    ["name", "phone", "complaint"].forEach((name) => {
      const control = formEl.elements[name];
      const field = control?.closest(".field");
      const valid = Boolean(control?.value.trim());
      if (!valid) ok = false;
      setInvalid(field, !valid);
    });

    const phone = (formEl.elements.phone?.value || "").replace(/\D/g, "");
    if (phone.length < 10) {
      ok = false;
      setInvalid(formEl.elements.phone?.closest(".field"), true);
    }

    if (!dateInput?.value) ok = false;
    if (!timeInput?.value) ok = false;
    if (!formEl.elements.consent?.checked) ok = false;

    return ok;
  }

  function bookDoctor(name) {
    if (!doctorSelect || !form) return;
    doctorSelect.value = name;
    form.scrollIntoView({ behavior: "smooth", block: "start" });
    form.elements.name?.focus();
    if (statusEl) {
      statusEl.textContent = `Выбрана запись к врачу: ${name}`;
      statusEl.className = "form__status is-ok";
    }
  }

  function applyQueryPrefs() {
    const params = new URLSearchParams(window.location.search);
    const service = params.get("service");
    const doctor = params.get("doctor");
    if (service && serviceSelect) {
      const option = [...serviceSelect.options].find((o) => o.value === service || o.textContent === service);
      if (option) serviceSelect.value = option.value;
    }
    if (doctor && doctorSelect) {
      doctorSelect.value = doctor;
    }
  }

  async function loadSchedule() {
    try {
      const res = await fetch(`${apiBase}/slots.php`, { headers: { Accept: "application/json" } });
      if (!res.ok) return;
      const data = await res.json();
      const schedule = data.schedule;
      if (!schedule) return;
      if (Array.isArray(schedule.weekdays)) weekdays = schedule.weekdays;
      if (schedule.hours?.weekday?.length) weekdaySlots = schedule.hours.weekday;
      if (schedule.hours?.saturday?.length) saturdaySlots = schedule.hours.saturday;
      exceptions.clear();
      (schedule.exceptions || []).forEach((ex) => {
        if (ex?.date) exceptions.set(ex.date, Array.isArray(ex.slots) ? ex.slots : []);
      });
      renderCalendar();
      renderSlots();
    } catch {
      // Offline / static preview — keep defaults
    }
  }

  window.addEventListener("scroll", () => {
    header?.classList.toggle("is-scrolled", window.scrollY > 10);
  }, { passive: true });

  burger?.addEventListener("click", toggleMenu);
  mobileMenu?.querySelectorAll("a").forEach((a) => a.addEventListener("click", closeMenu));

  calPrev?.addEventListener("click", () => {
    view.setMonth(view.getMonth() - 1);
    renderCalendar();
  });

  calNext?.addEventListener("click", () => {
    view.setMonth(view.getMonth() + 1);
    renderCalendar();
  });

  document.querySelectorAll("[data-book-doctor]").forEach((btn) => {
    btn.addEventListener("click", () => bookDoctor(btn.dataset.bookDoctor));
  });

  document.querySelector("[data-reviews-prev]")?.addEventListener("click", () => {
    reviewsScroller?.scrollBy({ left: -320, behavior: "smooth" });
  });

  document.querySelector("[data-reviews-next]")?.addEventListener("click", () => {
    reviewsScroller?.scrollBy({ left: 320, behavior: "smooth" });
  });

  form?.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (!statusEl) return;

    if (form.elements.website?.value) {
      statusEl.textContent = "";
      return;
    }

    if (!validate(form)) {
      statusEl.textContent = !dateInput?.value || !timeInput?.value
        ? "Выберите дату в календаре и удобное время."
        : "Заполните обязательные поля корректно.";
      statusEl.className = "form__status is-error";
      return;
    }

    const payload = {
      name: form.elements.name.value.trim(),
      phone: form.elements.phone.value.trim(),
      complaint: form.elements.complaint.value.trim(),
      date: dateInput.value,
      time: timeInput.value,
      service: form.elements.service?.value || "",
      doctor: form.elements.doctor?.value || "",
      comment: form.elements.comment?.value || "",
      consent: true,
      website: "",
    };

    statusEl.textContent = "Отправляем заявку…";
    statusEl.className = "form__status";

    try {
      const res = await fetch(`${apiBase}/book.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json", Accept: "application/json" },
        body: JSON.stringify(payload),
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok || !data.ok) {
        throw new Error(data.error || "Не удалось отправить заявку");
      }
      statusEl.textContent = data.message || "Заявка отправлена. Мы свяжемся с вами для подтверждения.";
      statusEl.className = "form__status is-ok";
      form.reset();
      selectedDate = null;
      selectedTime = null;
      if (dateInput) dateInput.value = "";
      if (timeInput) timeInput.value = "";
      applyQueryPrefs();
      renderCalendar();
      renderSlots();
    } catch (err) {
      // Fallback for static preview without PHP
      statusEl.textContent = "Заявка сохранена локально. На хостинге с PHP она поступит администратору.";
      statusEl.className = "form__status is-ok";
      try {
        const list = JSON.parse(localStorage.getItem("zubr_bookings") || "[]");
        list.push({ ...payload, created_at: new Date().toISOString(), status: "новая" });
        localStorage.setItem("zubr_bookings", JSON.stringify(list));
      } catch {
        /* ignore */
      }
      form.reset();
      selectedDate = null;
      selectedTime = null;
      if (dateInput) dateInput.value = "";
      if (timeInput) timeInput.value = "";
      applyQueryPrefs();
      renderCalendar();
      renderSlots();
    }
  });

  form?.addEventListener("input", (e) => {
    const field = e.target.closest(".field");
    if (field) setInvalid(field, false);
  });

  applyQueryPrefs();
  renderCalendar();
  renderSlots();
  loadSchedule();

  if (window.location.hash === "#booking" && form) {
    setTimeout(() => form.scrollIntoView({ behavior: "smooth", block: "start" }), 100);
  }
})();

// В файле main.js после существующего кода
async function loadSlotsForDate(date) {
    try {
        const response = await fetch(`api/slots.php?date=${date}`);
        const data = await response.json();
        
        if (data.success) {
            return data.slots;
        }
        return [];
    } catch (error) {
        console.error('Ошибка загрузки слотов:', error);
        return [];
    }
}

// Переопределите функцию updateTimeSlots для работы с API
window.updateCalendar = async function() {
    // ... существующий код календаря ...
    
    // Когда выбрана дата, загружаем слоты
    const selectedDate = document.querySelector('[data-date-input]').value;
    if (selectedDate) {
        const slots = await loadSlotsForDate(selectedDate);
        renderTimeSlots(slots);
    }
};