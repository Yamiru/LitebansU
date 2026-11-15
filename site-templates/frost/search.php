<?php $h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); ?>
<h1><i class="fas fa-search"></i><?= $h($lang->get('search.title')) ?></h1>

<form id="search-form" autocomplete="off">
    <div class="inline">
        <input type="search" id="search-input" maxlength="36" minlength="1" placeholder="<?= $h($lang->get('search.placeholder')) ?>" aria-label="<?= $h($lang->get('search.title')) ?>">
        <button class="btn primary" type="submit"><i class="fas fa-search"></i><?= $h($lang->get('search.button')) ?></button>
    </div>
    <p class="small muted" style="margin-top:0.5rem"><?= $h($lang->get('search.help')) ?></p>
</form>

<div id="search-results" style="margin-top:2rem"></div>

<script>
(function () {
    var form = document.getElementById('search-form'), input = document.getElementById('search-input'), out = document.getElementById('search-results');
    var base = <?= json_encode(url('detail')) ?>, action = <?= json_encode(url('search')) ?>, timer;
    var dec = function (s) { var t = document.createElement('textarea'); t.innerHTML = String(s == null ? '' : s); return t.value.replace(/<br\s*\/?>/gi, ' '); };
    var el = function (tag, cls, text) { var e = document.createElement(tag); if (cls) e.className = cls; if (text != null) e.textContent = text; return e; };
    var notice = function (cls, text) { out.replaceChildren(el('div', 'notice ' + cls, text)); };

    function render(data) {
        if (!data.punishments || !data.punishments.length) return notice('', <?= json_encode($lang->get('punishments.no_data')) ?>);
        var wrap = el('div', 'tablewrap'), table = el('table'), body = el('tbody');
        data.punishments.forEach(function (p) {
            var single = String(p.type).replace(/s$/, ''), tr = el('tr');
            tr.addEventListener('click', function () { location.href = base + '?type=' + encodeURIComponent(single) + '&id=' + encodeURIComponent(p.id); });
            tr.style.cursor = 'pointer';
            var name = el('td', null, dec(p.player_name) + ' ');
            name.appendChild(el('span', 'tag', single));
            tr.appendChild(name);
            tr.appendChild(el('td', 'hide-m', dec(p.reason).slice(0, 60)));
            tr.appendChild(el('td', 'hide-m', dec(p.staff)));
            tr.appendChild(el('td', 'hide-s small', p.date));
            var st = el('td');
            st.appendChild(el('span', 'tag ' + (p.active ? 'danger' : 'ok'), p.active ? <?= json_encode($lang->get('status.active')) ?> : <?= json_encode($lang->get('status.expired')) ?>));
            tr.appendChild(st);
            body.appendChild(tr);
        });
        table.appendChild(body); wrap.appendChild(table);
        var head = el('h2', null, <?= json_encode($lang->get('search.title')) ?> + ': ' + data.player);
        out.replaceChildren(head, wrap);
    }

    async function run() {
        var q = input.value.trim();
        if (!q) { out.replaceChildren(); return; }
        out.replaceChildren(el('p', 'muted', '...'));
        var fd = new FormData(); fd.append('query', q); fd.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);
        try {
            var r = await fetch(action, { method: 'POST', body: fd, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            var data = await r.json();
            if (!r.ok || data.error) throw new Error(data.error || 'Search failed');
            render(data);
        } catch (e) { notice('error', e.message); }
    }
    form.addEventListener('submit', function (e) { e.preventDefault(); run(); });
    input.addEventListener('input', function () { clearTimeout(timer); timer = setTimeout(run, 500); });
})();
</script>
