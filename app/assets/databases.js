const STATUS_LABEL = { live: "Live", planned: "Planned" };

const out = document.getElementById("out");
const emptyEl = document.getElementById("empty");
const qEl = document.getElementById("q");

let activeStatus = "all";
let query = "";

function haystack(d) {
  return [d.name, d.engine_name, d.host_machine, d.port, d.purpose, d.notes, STATUS_LABEL[d.status]]
    .filter(Boolean).join(" ").toLowerCase();
}

function matches(d) {
  if (activeStatus !== "all" && d.status !== activeStatus) return false;
  if (!query) return true;
  return haystack(d).includes(query);
}

function card(d) {
  const meta = [];
  if (d.host_machine) meta.push('<span class="kv"><i>on</i> ' + esc(d.host_machine) + "</span>");
  if (d.port)         meta.push('<span class="kv"><i>port</i> ' + esc(d.port) + "</span>");

  let actions = "";
  if (CAN_EDIT || CAN_DELETE) {
    const bits = [];
    if (CAN_EDIT) bits.push('<a class="btn-link" href="database-form.php?id=' + d.id + '">Edit</a>');
    if (CAN_DELETE) {
      bits.push(
        '<form method="post" action="database-delete.php" onsubmit="return confirm(\'Delete ' + esc(d.name).replace(/'/g, "\\'") + '? This can\\\'t be undone.\');">'
        + '<input type="hidden" name="csrf_token" value="' + esc(CSRF_TOKEN) + '">'
        + '<input type="hidden" name="id" value="' + d.id + '">'
        + '<button type="submit" class="btn-link danger">Delete</button>'
        + '</form>'
      );
    }
    actions = '<div class="card-actions">' + bits.join("") + '</div>';
  }

  return '<article class="card ' + esc(d.status) + '">'
    + '<div class="card-top"><div>'
    + '<div class="host">' + esc(d.name) + "</div>"
    + (d.engine_name ? '<div class="fqdn">' + esc(d.engine_name) + "</div>" : "")
    + "</div>"
    + '<span class="pill ' + esc(d.status) + '">' + STATUS_LABEL[d.status] + "</span>"
    + "</div>"
    + (d.purpose ? '<div class="role">' + esc(d.purpose) + "</div>"
                 : '<div class="role none">Unassigned</div>')
    + (meta.length ? '<div class="meta">' + meta.join("") + "</div>" : "")
    + (d.notes ? '<div class="notes">' + esc(d.notes) + "</div>" : "")
    + actions
    + "</article>";
}

function render() {
  const visible = DATABASES.filter(matches);
  out.innerHTML = visible.length
    ? '<div class="grid">' + visible.map(card).join("") + "</div>"
    : "";

  if (DATABASES.length === 0) {
    emptyEl.textContent = CAN_EDIT
      ? "No databases logged yet. Use “Add database” above to add the first one."
      : "No databases have been logged yet.";
    emptyEl.classList.add("show");
  } else {
    emptyEl.textContent = "Nothing matches that search.";
    emptyEl.classList.toggle("show", visible.length === 0);
  }
}

qEl.addEventListener("input", () => {
  query = qEl.value.trim().toLowerCase();
  render();
});

wireChips("statusFilters", "status", v => { activeStatus = v; render(); });
wireStickyControls("controls");

render();
