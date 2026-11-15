<?php $h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); ?>
<div class="columns is-centered">
<div class="column is-8-desktop">
    <h1 class="title is-2 has-text-centered"><i class="fas fa-search has-text-primary mr-2"></i><?= $h($lang->get('search.title')) ?></h1>

    <form id="search-form" class="mb-4" autocomplete="off">
        <div class="field has-addons">
            <p class="control has-icons-left is-expanded">
                <input class="input is-large" type="text" id="search-input" maxlength="36" minlength="1" placeholder="<?= $h($lang->get('search.placeholder')) ?>">
                <span class="icon is-left"><i class="fas fa-user"></i></span>
            </p>
            <p class="control"><button class="button is-primary is-large" type="submit"><i class="fas fa-search mr-2"></i><?= $h($lang->get('search.button')) ?></button></p>
        </div>
        <p class="help"><?= $h($lang->get('search.help')) ?></p>
    </form>

    <div id="search-results"></div>
</div>
</div>

<script>
(function () {
    var form = document.getElementById('search-form'), input = document.getElementById('search-input'), out = document.getElementById('search-results');
    var base = <?= json_encode(url('detail')) ?>, action = <?= json_encode(url('search')) ?>, timer;
    // Names and reasons arrive HTML-escaped from the server: turn them back into plain text, then set them as text
    var dec = function (s) { var t = document.createElement('textarea'); t.innerHTML = String(s == null ? '' : s); return t.value.replace(/<br\s*\/?>/gi, ' '); };
    var el = function (tag, cls, text) { var e = document.createElement(tag); if (cls) e.className = cls; if (text != null) e.textContent = text; return e; };
    var notice = function (cls, text) { out.replaceChildren(el('div', 'notification ' + cls, text)); };

    function render(data) {
        if (!data.punishments || !data.punishments.length) return notice('is-info is-light', <?= json_encode($lang->get('punishments.no_data')) ?>);
        var wrap = el('div', 'table-container'), table = el('table', 'table is-fullwidth is-hoverable'), body = el('tbody');
        data.punishments.forEach(function (p) {
            var single = String(p.type).replace(/s$/, ''), tr = el('tr', 'is-clickable');
            tr.addEventListener('click', function () { location.href = base + '?type=' + encodeURIComponent(single) + '&id=' + encodeURIComponent(p.id); });
            var nameTd = el('td', null, dec(p.player_name));
            nameTd.appendChild(el('span', 'tag ml-2', single));
            tr.appendChild(nameTd);
            tr.appendChild(el('td', 'is-hidden-mobile', dec(p.reason).slice(0, 60)));
            tr.appendChild(el('td', 'is-hidden-touch', dec(p.staff)));
            tr.appendChild(el('td', 'is-hidden-mobile', p.date));
            var st = el('td'); st.appendChild(el('span', 'tag ' + (p.active ? 'is-danger' : 'is-success'), p.active ? <?= json_encode($lang->get('status.active')) ?> : <?= json_encode($lang->get('status.expired')) ?>));
            tr.appendChild(st);
            body.appendChild(tr);
        });
        table.appendChild(body); wrap.appendChild(table);
        var head = el('h2', 'title is-5'); head.textContent = <?= json_encode($lang->get('search.title')) ?> + ': ' + data.player;
        out.replaceChildren(head, wrap);
    }

    async function run() {
        var q = input.value.trim();
        if (!q) { out.replaceChildren(); return; }
        out.replaceChildren(el('progress', 'progress is-small is-primary'));
        var fd = new FormData(); fd.append('query', q); fd.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);
        try {
            var r = await fetch(action, { method: 'POST', body: fd, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            var data = await r.json();
            if (!r.ok || data.error) throw new Error(data.error || 'Search failed');
            render(data);
        } catch (e) { notice('is-danger', e.message); }
    }
    form.addEventListener('submit', function (e) { e.preventDefault(); run(); });
    input.addEventListener('input', function () { clearTimeout(timer); timer = setTimeout(run, 500); });
})();
</script>
