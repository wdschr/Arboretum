function esc(s) {
  return String(s == null ? "" : s).replace(/[&<>"']/g, c => ({
    "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;"
  }[c]));
}

function wireChips(containerId, datasetKey, onChange) {
  const el = document.getElementById(containerId);
  if (!el) return;
  el.addEventListener("click", e => {
    const btn = e.target.closest(".chip");
    if (!btn) return;
    onChange(btn.dataset[datasetKey]);
    for (const c of el.querySelectorAll(".chip")) {
      c.setAttribute("aria-pressed", String(c === btn));
    }
  });
}

function wireStickyControls(controlsId) {
  const controls = document.getElementById(controlsId);
  if (!controls) return;
  addEventListener("scroll", () => {
    controls.classList.toggle("stuck", scrollY > 8);
  }, { passive: true });
}
