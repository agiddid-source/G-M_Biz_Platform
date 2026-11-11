(() => {
    document.querySelectorAll('[data-ght-search-target]').forEach((ghtSearch) => {
        const ghtTarget = document.querySelector(ghtSearch.dataset.ghtSearchTarget || '');
        if (!ghtTarget) return;

        const ghtRows = Array.from(ghtTarget.querySelectorAll('[data-ght-search-row]'));
        const ghtEmptyRow = document.querySelector(ghtSearch.dataset.ghtSearchEmpty || '');
        const ghtCount = document.querySelector(ghtSearch.dataset.ghtSearchCount || '');
        ghtSearch.addEventListener('input', () => {
            const ghtQuery = ghtSearch.value.trim().toLocaleLowerCase();
            let ghtVisibleCount = 0;
            ghtRows.forEach((ghtRow) => {
                const ghtMatches = (ghtRow.dataset.search || '').includes(ghtQuery);
                ghtRow.hidden = !ghtMatches;
                if (ghtMatches) ghtVisibleCount += 1;
            });
            if (ghtEmptyRow) ghtEmptyRow.hidden = ghtVisibleCount !== 0;
            if (ghtCount) {
                const ghtSingular = ghtCount.dataset.singular || 'item';
                const ghtPlural = ghtCount.dataset.plural || (ghtSingular + 's');
                ghtCount.textContent = ghtVisibleCount + ' ' + (ghtVisibleCount === 1 ? ghtSingular : ghtPlural);
            }
        });
    });

    const ghtPosForm = document.querySelector('[data-ght-pos-form]');
    if (ghtPosForm) {
        const ghtQuantities = Array.from(ghtPosForm.querySelectorAll('.ght-admin-pos-quantity'));
        const ghtTotal = document.getElementById('ght-admin-pos-total');
        const ghtLineCount = document.getElementById('ght-admin-pos-line-count');
        const ghtUpdatePosTotal = () => {
            let ghtAmount = 0;
            let ghtItems = 0;
            ghtQuantities.forEach((ghtInput) => {
                const ghtQuantity = Math.max(0, Number.parseInt(ghtInput.value || '0', 10) || 0);
                ghtAmount += ghtQuantity * Number(ghtInput.dataset.price || 0);
                if (ghtQuantity > 0) ghtItems += ghtQuantity;
            });
            if (ghtTotal) ghtTotal.textContent = '\u20a6' + new Intl.NumberFormat('en-NG', { maximumFractionDigits: 0 }).format(ghtAmount);
            if (ghtLineCount) ghtLineCount.textContent = ghtItems + ' ' + (ghtItems === 1 ? 'item' : 'items');
        };
        ghtQuantities.forEach((ghtInput) => ghtInput.addEventListener('input', ghtUpdatePosTotal));
        ghtUpdatePosTotal();
    }
})();
