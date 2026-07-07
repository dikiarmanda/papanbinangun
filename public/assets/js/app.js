document.addEventListener("DOMContentLoaded", () => {
  initMobileNav();
  initHeroBanner();
  initTestimonialCarousel();
  initLightbox();
});

function initMobileNav() {
  const toggle = document.getElementById("navToggle");
  const nav = document.getElementById("siteNav");
  if (!toggle || !nav) return;

  const icon = toggle.querySelector("i");

  const setOpen = (open) => {
    nav.classList.toggle("open", open);
    toggle.setAttribute("aria-expanded", open ? "true" : "false");
    document.body.classList.toggle("nav-open", open);
    if (icon) {
      icon.className = open ? "fa-solid fa-xmark" : "fa-solid fa-bars";
    }
  };

  toggle.setAttribute("aria-expanded", "false");
  toggle.setAttribute("aria-controls", "siteNav");

  toggle.addEventListener("click", () => {
    setOpen(!nav.classList.contains("open"));
  });

  nav.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => setOpen(false));
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") setOpen(false);
  });
}

function initHeroBanner() {
  const root = document.getElementById("heroBanner");
  if (!root) return;

  const slides = Array.from(root.querySelectorAll(".hero-banner-slide"));
  const dots = Array.from(root.querySelectorAll(".hero-banner-dot"));
  const prevBtn = root.querySelector(".hero-banner-prev");
  const nextBtn = root.querySelector(".hero-banner-next");
  const intervalMs = parseInt(root.dataset.interval || "5000", 10);

  if (slides.length < 2) return;

  let index = 0;
  let timer = null;
  let touchStartX = 0;

  const goTo = (nextIndex) => {
    slides[index].classList.remove("is-active");
    dots[index]?.classList.remove("is-active");
    dots[index]?.setAttribute("aria-selected", "false");

    index = (nextIndex + slides.length) % slides.length;

    slides[index].classList.add("is-active");
    dots[index]?.classList.add("is-active");
    dots[index]?.setAttribute("aria-selected", "true");
  };

  const start = () => {
    stop();
    timer = setInterval(() => goTo(index + 1), intervalMs);
  };

  const stop = () => {
    if (timer) clearInterval(timer);
    timer = null;
  };

  prevBtn?.addEventListener("click", () => {
    goTo(index - 1);
    start();
  });

  nextBtn?.addEventListener("click", () => {
    goTo(index + 1);
    start();
  });

  dots.forEach((dot) => {
    dot.addEventListener("click", () => {
      goTo(parseInt(dot.dataset.index, 10));
      start();
    });
  });

  root.addEventListener("mouseenter", stop);
  root.addEventListener("mouseleave", start);
  root.addEventListener("focusin", stop);
  root.addEventListener("focusout", start);

  root.addEventListener(
    "touchstart",
    (e) => {
      touchStartX = e.changedTouches[0].screenX;
      stop();
    },
    { passive: true },
  );

  root.addEventListener(
    "touchend",
    (e) => {
      const delta = e.changedTouches[0].screenX - touchStartX;
      if (Math.abs(delta) > 40) {
        goTo(delta < 0 ? index + 1 : index - 1);
      }
      start();
    },
    { passive: true },
  );

  start();
}

function initTestimonialCarousel() {
  const root = document.getElementById("testimonialCarousel");
  if (!root) return;

  const viewport = root.querySelector(".testimonial-carousel-viewport");
  const track = root.querySelector(".testimonial-carousel-track");
  const slides = Array.from(root.querySelectorAll(".testimonial-card"));
  const dotsContainer = root.querySelector(".testimonial-carousel-dots");
  const prevBtn = root.querySelector(".testimonial-carousel-prev");
  const nextBtn = root.querySelector(".testimonial-carousel-next");
  const intervalMs = parseInt(root.dataset.interval || "6000", 10);

  if (slides.length < 2 || !track || !viewport) return;

  let index = 0;
  let timer = null;
  let touchStartX = 0;
  let perView = 1;
  let maxIndex = 0;
  let dots = [];

  const getPerView = () => {
    const w = window.innerWidth;
    if (w >= 992) return 3;
    if (w >= 640) return 2;
    return 1;
  };

  const buildDots = () => {
    dotsContainer.innerHTML = "";
    dots = [];
    for (let i = 0; i <= maxIndex; i++) {
      const dot = document.createElement("button");
      dot.type = "button";
      dot.className = "testimonial-carousel-dot" + (i === index ? " is-active" : "");
      dot.dataset.index = String(i);
      dot.setAttribute("aria-label", "Testimoni halaman " + (i + 1));
      dot.setAttribute("aria-selected", i === index ? "true" : "false");
      dot.addEventListener("click", () => {
        goTo(i);
        start();
      });
      dotsContainer.appendChild(dot);
      dots.push(dot);
    }
  };

  const updateLayout = () => {
    perView = getPerView();
    maxIndex = Math.max(0, slides.length - perView);
    if (index > maxIndex) index = maxIndex;
    buildDots();
    applyTransform(false);
  };

  const applyTransform = (animate = true) => {
    track.style.transition = animate ? "transform 0.45s ease" : "none";
    const slideWidth = slides[0].offsetWidth;
    const gap = parseFloat(getComputedStyle(track).gap) || 0;
    const offset = index * (slideWidth + gap);
    track.style.transform = "translateX(-" + offset + "px)";

    dots.forEach((dot, i) => {
      const active = i === index;
      dot.classList.toggle("is-active", active);
      dot.setAttribute("aria-selected", active ? "true" : "false");
    });
  };

  const goTo = (nextIndex) => {
    index = Math.max(0, Math.min(nextIndex, maxIndex));
    applyTransform();
  };

  const start = () => {
    stop();
    if (maxIndex > 0) {
      timer = setInterval(() => goTo(index >= maxIndex ? 0 : index + 1), intervalMs);
    }
  };

  const stop = () => {
    if (timer) clearInterval(timer);
    timer = null;
  };

  prevBtn?.addEventListener("click", () => {
    goTo(index <= 0 ? maxIndex : index - 1);
    start();
  });

  nextBtn?.addEventListener("click", () => {
    goTo(index >= maxIndex ? 0 : index + 1);
    start();
  });

  root.addEventListener("mouseenter", stop);
  root.addEventListener("mouseleave", start);
  root.addEventListener("focusin", stop);
  root.addEventListener("focusout", start);

  root.addEventListener(
    "touchstart",
    (e) => {
      touchStartX = e.changedTouches[0].screenX;
      stop();
    },
    { passive: true },
  );

  root.addEventListener(
    "touchend",
    (e) => {
      const delta = e.changedTouches[0].screenX - touchStartX;
      if (Math.abs(delta) > 40) {
        goTo(delta < 0 ? (index >= maxIndex ? 0 : index + 1) : index <= 0 ? maxIndex : index - 1);
      }
      start();
    },
    { passive: true },
  );

  let resizeTimer;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(updateLayout, 150);
  });

  updateLayout();
  start();
}

function initLightbox() {
  const lightbox = document.getElementById("lightbox");
  if (!lightbox) return;

  const img = lightbox.querySelector("img");
  const closeBtn = lightbox.querySelector(".lightbox-close");

  document.querySelectorAll("[data-lightbox]").forEach((el) => {
    el.addEventListener("click", (e) => {
      e.preventDefault();
      img.src = el.getAttribute("href");
      img.alt = el.getAttribute("title") || "";
      lightbox.hidden = false;
      document.body.style.overflow = "hidden";
    });
  });

  const close = () => {
    lightbox.hidden = true;
    img.src = "";
    document.body.style.overflow = "";
  };

  closeBtn.addEventListener("click", close);
  lightbox.addEventListener("click", (e) => {
    if (e.target === lightbox) close();
  });
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && !lightbox.hidden) close();
  });
}
