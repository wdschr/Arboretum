const TIERS = [
  { key: "machine", title: "Machines", note: "Physical hosts and virtual machines." },
  { key: "webapp",  title: "Web apps", note: "Anything with a web interface someone opens. Each one is a CNAME onto whichever machine runs it." },
  { key: "service", title: "Services", note: "Daemons and background workers. Most have no interface worth visiting." }
];

const STATUS_LABEL = { live: "Live", planned: "Planned" };

const out = document.getElementById("out");
const emptyEl = document.getElementById("empty");
const qEl = document.getElementById("q");

let activeStatus = "all";
let activeTier = "all";
let query = "";

function haystack(s) {
  return [s.hostname, s.role, s.runs, s.notes, s.why, s.ip, s.port, s.on_machine, STATUS_LABEL[s.status]]
    .filter(Boolean).join(" ").toLowerCase();
}

function matches(s) {
  if (activeStatus !== "all" && s.status !== activeStatus) return false;
  if (activeTier !== "all" && s.tier !== activeTier) return false;
  if (!query) return true;
  return haystack(s).includes(query);
}

function card(s) {
  const fqdn = s.hostname + "." + DOMAIN;
  const isLive = s.status === "live";
  const name = isLive
    ? '<a href="https://' + esc(fqdn) + '/" rel="noreferrer" target="_blank">' + esc(s.hostname) + "</a>"
    : esc(s.hostname);

  const meta = [];
  if (s.on_machine) meta.push('<span class="kv"><i>on</i> ' + esc(s.on_machine) + "</span>");
  if (s.ip)         meta.push('<span class="kv"><i>ip</i> ' + esc(s.ip) + "</span>");
  if (s.port)       meta.push('<span class="kv"><i>port</i> ' + esc(s.port) + "</span>");

  let actions = "";
  if (CAN_EDIT || CAN_DELETE) {
    const bits = [];
    if (CAN_EDIT) bits.push('<a class="btn-link" href="host-form.php?id=' + s.id + '">Edit</a>');
    if (CAN_DELETE) {
      bits.push(
        '<form method="post" action="host-delete.php" onsubmit="return confirm(\'Delete ' + esc(s.hostname).replace(/'/g, "\\'") + '? This can\\\'t be undone.\');">'
        + '<input type="hidden" name="csrf_token" value="' + esc(CSRF_TOKEN) + '">'
        + '<input type="hidden" name="id" value="' + s.id + '">'
        + '<button type="submit" class="btn-link danger">Delete</button>'
        + '</form>'
      );
    }
    actions = '<div class="card-actions">' + bits.join("") + '</div>';
  }

  return '<article class="card ' + esc(s.status) + '">'
    + '<div class="card-top"><div>'
    + '<div class="host">' + name + "</div>"
    + '<div class="fqdn">' + esc(fqdn) + "</div>"
    + "</div>"
    + '<span class="pill ' + esc(s.status) + '">' + STATUS_LABEL[s.status] + "</span>"
    + "</div>"
    + (s.role ? '<div class="role">' + esc(s.role) + "</div>"
              : '<div class="role none">Unassigned</div>')
    + (s.runs ? '<div class="runs">' + esc(s.runs) + "</div>" : "")
    + (meta.length ? '<div class="meta">' + meta.join("") + "</div>" : "")
    + (s.notes ? '<div class="notes">' + esc(s.notes) + "</div>" : "")
    + (s.why ? '<div class="why">' + esc(s.why) + "</div>" : "")
    + actions
    + "</article>";
}

function render() {
  const visible = SERVERS.filter(matches);
  let html = "";

  for (const tier of TIERS) {
    const rows = visible.filter(s => s.tier === tier.key);
    if (!rows.length) continue;
    html += "<section>"
      + '<div class="sec-head"><h2>' + tier.title + "</h2>"
      + '<span class="count">' + rows.length + " of " + SERVERS.filter(s => s.tier === tier.key).length + "</span></div>"
      + '<p class="sec-note">' + tier.note + "</p>"
      + '<div class="grid">' + rows.map(card).join("") + "</div>"
      + "</section>";
  }

  out.innerHTML = html;

  if (SERVERS.length === 0) {
    emptyEl.textContent = CAN_EDIT
      ? "No hostnames yet. Use “Add hostname” above to add the first one."
      : "No hostnames have been added yet.";
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
wireChips("tierFilters", "tier", v => { activeTier = v; render(); });
wireStickyControls("controls");

render();
