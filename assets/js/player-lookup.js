(function (window) {
    const searchUrl = document.body.dataset.playerSearchUrl || '/players/search';

    function qs(root, sel) {
        return root.querySelector(sel);
    }

    function resultsEl(root) {
        return root._resultsEl || qs(root, '.player-results');
    }

    function formatMeta(p) {
        return p.skill_label || p.skill_level || '';
    }

    /** Move dropdown to body so it always stacks above other search fields */
    function mountResults(root) {
        const el = resultsEl(root);
        if (!el || el.parentElement === document.body) {
            return;
        }
        if (!root._resultsAnchor) {
            root._resultsAnchor = document.createComment('player-results-anchor');
            el.parentNode.insertBefore(root._resultsAnchor, el);
        }
        document.body.appendChild(el);
    }

    function unmountResults(root) {
        const el = resultsEl(root);
        if (!el || !root._resultsAnchor || el.parentElement !== document.body) {
            return;
        }
        root._resultsAnchor.parentNode.insertBefore(el, root._resultsAnchor.nextSibling);
    }

    function positionResults(root) {
        const input = qs(root, '.player-search-input');
        const el = resultsEl(root);
        if (!input || !el) {
            return;
        }
        const rect = input.getBoundingClientRect();
        el.style.position = 'fixed';
        el.style.top = Math.round(rect.bottom + 4) + 'px';
        el.style.left = Math.round(rect.left) + 'px';
        el.style.width = Math.round(Math.max(rect.width, 220)) + 'px';
        el.style.zIndex = '10050';
    }

    function showResults(root) {
        const el = resultsEl(root);
        if (!el) {
            return;
        }
        mountResults(root);
        positionResults(root);
        el.classList.remove('d-none');
        el.classList.add('is-open');
    }

    function hideResults(root) {
        const el = resultsEl(root);
        if (!el) {
            return;
        }
        el.classList.add('d-none');
        el.classList.remove('is-open');
        unmountResults(root);
    }

    function renderResults(root, players) {
        const el = resultsEl(root);
        el.innerHTML = '';
        if (!players.length) {
            const empty = document.createElement('div');
            empty.className = 'list-group-item text-muted small py-3';
            empty.textContent = 'No players found';
            el.appendChild(empty);
            showResults(root);
            return;
        }
        players.forEach(p => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'list-group-item list-group-item-action player-option';
            btn.dataset.id = p.id;
            btn.dataset.name = p.player_name || '';
            btn.dataset.code = p.player_code || '';
            btn.dataset.skill = p.skill_label || p.skill_level || '';
            btn.dataset.games = p.games_played ?? '0';
            const title = document.createElement('div');
            title.className = 'fw-semibold';
            title.textContent = p.player_name || '';
            const meta = document.createElement('small');
            meta.className = 'text-muted d-block';
            meta.textContent = formatMeta(p);
            btn.appendChild(title);
            btn.appendChild(meta);
            el.appendChild(btn);
        });
        showResults(root);
    }

    function getExcludedIds(excludeRoot) {
        const scope = document.getElementById('queueForm') || document;
        const ids = new Set();
        scope.querySelectorAll('.player-lookup').forEach(lookup => {
            if (lookup === excludeRoot) {
                return;
            }
            const hidden = lookup.querySelector('input[type="hidden"]');
            if (!hidden || hidden.disabled) {
                return;
            }
            const value = (hidden.value || '').trim();
            if (value) {
                ids.add(value);
            }
        });
        return Array.from(ids);
    }

    function filterPlayers(root, players) {
        const excluded = new Set(getExcludedIds(root));
        return players.filter(p => !excluded.has(String(p.id)));
    }

    function fetchPlayers(root, q) {
        const params = new URLSearchParams({ q });
        const exclude = getExcludedIds(root);
        if (exclude.length) {
            params.set('exclude', exclude.join(','));
        }
        fetch(searchUrl + '?' + params.toString())
            .then(r => r.json())
            .then(players => renderResults(root, filterPlayers(root, players)))
            .catch(() => {
                const el = resultsEl(root);
                el.innerHTML = '<div class="list-group-item text-danger small py-3">Search failed</div>';
                showResults(root);
            });
    }

    function notifySelectionChange() {
        document.dispatchEvent(new CustomEvent('player-lookup:changed'));
    }

    function refreshActiveLookups() {
        document.querySelectorAll('.player-lookup.is-active').forEach(root => {
            const selected = qs(root, '.player-selected');
            if (selected && !selected.classList.contains('d-none')) {
                return;
            }
            const input = qs(root, '.player-search-input');
            if (input) {
                fetchPlayers(root, input.value.trim());
            }
        });
    }

    function selectPlayer(root, id, name, code, skill, games) {
        const hidden = qs(root, 'input[type="hidden"]');
        hidden.value = id;
        qs(root, '.player-selected-name').textContent = name;
        const metaEl = qs(root, '.player-selected-meta');
        if (skill) {
            metaEl.textContent = skill;
            metaEl.classList.remove('d-none');
        } else {
            metaEl.textContent = '';
            metaEl.classList.add('d-none');
        }
        qs(root, '.player-search-wrap').classList.add('d-none');
        qs(root, '.player-selected').classList.remove('d-none');
        hideResults(root);
        root.classList.remove('is-active');
        notifySelectionChange();
    }

    function clearSelection(root) {
        const hidden = qs(root, 'input[type="hidden"]');
        hidden.value = '';
        const input = qs(root, '.player-search-input');
        input.value = '';
        qs(root, '.player-search-wrap').classList.remove('d-none');
        qs(root, '.player-selected').classList.add('d-none');
        hideResults(root);
        root.classList.remove('is-active');
        notifySelectionChange();
        input.focus();
    }

    function closeAllResults(exceptRoot) {
        document.querySelectorAll('.player-lookup').forEach(el => {
            if (el !== exceptRoot) {
                el.classList.remove('is-active');
                hideResults(el);
            }
        });
    }

    function setActive(root) {
        closeAllResults(root);
        root.classList.add('is-active');
    }

    function repositionOpenResults() {
        document.querySelectorAll('.player-lookup.is-active').forEach(root => {
            const el = resultsEl(root);
            if (el && el.classList.contains('is-open')) {
                positionResults(root);
            }
        });
    }

    function initRoot(root) {
        let debounceTimer = null;
        const input = qs(root, '.player-search-input');
        const el = qs(root, '.player-results');
        root._resultsEl = el;

        el.addEventListener('click', function (e) {
            const btn = e.target.closest('.player-option');
            if (!btn) {
                return;
            }
            selectPlayer(root, btn.dataset.id, btn.dataset.name, btn.dataset.code, btn.dataset.skill, btn.dataset.games);
        });

        input.addEventListener('input', function () {
            setActive(root);
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchPlayers(root, this.value.trim()), 250);
        });

        input.addEventListener('focus', function () {
            setActive(root);
            if (!el.innerHTML) {
                fetchPlayers(root, this.value.trim());
            } else {
                showResults(root);
            }
        });

        qs(root, '.player-clear-btn').addEventListener('click', () => clearSelection(root));
    }

    document.querySelectorAll('.player-lookup').forEach(initRoot);

    document.addEventListener('player-lookup:changed', refreshActiveLookups);

    window.addEventListener('resize', repositionOpenResults);
    window.addEventListener('scroll', repositionOpenResults, true);

    document.addEventListener('click', function (e) {
        const inLookup = e.target.closest('.player-lookup');
        const inResults = e.target.closest('.player-results');
        if (!inLookup && !inResults) {
            closeAllResults(null);
        }
    });

    window.PlayerLookup = {
        restore(prefix, id, name, code, skill, games) {
            const root = document.querySelector('.player-lookup[data-lookup-prefix="' + prefix + '"]');
            if (root) {
                selectPlayer(root, String(id), name, code || '', skill || '', games ?? 0);
            }
        },
        clear(prefix) {
            const root = document.querySelector('.player-lookup[data-lookup-prefix="' + prefix + '"]');
            if (!root) {
                return;
            }
            const hidden = qs(root, 'input[type="hidden"]');
            if (hidden) {
                hidden.value = '';
            }
            const selected = qs(root, '.player-selected');
            if (selected && !selected.classList.contains('d-none')) {
                clearSelection(root);
                return;
            }
            qs(root, '.player-search-wrap')?.classList.remove('d-none');
            selected?.classList.add('d-none');
            const input = qs(root, '.player-search-input');
            if (input) {
                input.value = '';
            }
            hideResults(root);
            root.classList.remove('is-active');
            notifySelectionChange();
        },
    };
})(window);
