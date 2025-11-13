/**
 * categories.js
 * Standalone JS for Inventory > Categories management:
 * - List categories
 * - Create new category
 * - Edit existing category
 * - Delete category (blocked if inventory items reference it)
 *
 * Uses window.UIS_API (injected by Header) or falls back to '/api'.
 */

document.addEventListener('DOMContentLoaded', function() {
  const API = (typeof window.UIS_API === 'string' ? window.UIS_API : '') || '/api';

  const tableBody = document.querySelector('#catTable tbody');
  const form = document.getElementById('catForm');
  const catId = document.getElementById('catId');
  const catName = document.getElementById('catName');
  const catDesc = document.getElementById('catDesc');
  const saveBtn = document.getElementById('saveBtn');
  const resetBtn = document.getElementById('resetBtn');
  const msg = document.getElementById('catMsg');

  function setMsg(text, ok = true) {
    msg.textContent = text || '';
    msg.style.color = ok ? 'var(--modeColorOPP)' : 'var(--accent)';
  }

  async function api(method, url, body) {
    const res = await fetch(url, {
      method,
      headers: { 'Accept':'application/json', 'Content-Type':'application/json' },
      body: body ? JSON.stringify(body) : undefined
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok || data.success === false) {
      throw new Error((data && (data.error || data.message)) || ('HTTP ' + res.status));
    }
    return data;
  }

  function escapeHtml(s){
    return (s ?? '').replace(/[&<>"']/g, ch => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
    })[ch]);
  }

  async function load() {
    tableBody.innerHTML = '<tr><td colspan="4">Loading…</td></tr>';
    try {
      const out = await api('GET', API + '/categories.php');
      const arr = out.data || [];
      if (!arr.length) {
        tableBody.innerHTML = '<tr><td colspan="4">No categories yet</td></tr>';
        return;
      }
      tableBody.innerHTML = '';
      for (const c of arr) {
        const tr = document.createElement('tr');
        tr.innerHTML =
          '<td>'+ c.id +'</td>' +
          '<td>'+ escapeHtml(c.name) +'</td>' +
          '<td>'+ escapeHtml(c.description || "") +'</td>' +
          '<td>' +
            '<button class="btn btn-primary btn-sm" data-act="edit" data-id="'+ c.id +'">Edit</button> '+
            '<button class="btn btn-danger btn-sm" data-act="del" data-id="'+ c.id +'">Delete</button>'+
          '</td>';
        tableBody.appendChild(tr);
      }
    } catch(e) {
      tableBody.innerHTML = '<tr><td colspan="4">Failed to load: '+ escapeHtml(e.message) +'</td></tr>';
    }
  }

  tableBody.addEventListener('click', async (ev) => {
    const btn = ev.target.closest('button[data-act]');
    if (!btn) return;

    const id = parseInt(btn.getAttribute('data-id'), 10);
    const act = btn.getAttribute('data-act');

    if (act === 'edit') {
      try {
        const out = await api('GET', API + '/categories.php?id=' + id);
        const c = out.data;
        catId.value = c.id;
        catName.value = c.name || '';
        catDesc.value = c.description || '';
        saveBtn.textContent = 'Save Changes';
        setMsg('Editing category #' + c.id);
      } catch(e) {
        setMsg('Load failed: ' + e.message, false);
      }
    } else if (act === 'del') {
      if (!confirm('Delete this category? This will fail if any inventory items use it.')) return;
      try {
        await api('DELETE', API + '/categories.php', { id });
        setMsg('Category deleted.');
        await load();
        if (parseInt(catId.value || '0', 10) === id) resetForm();
      } catch(e) {
        setMsg('Delete failed: ' + e.message, false);
      }
    }
  });

  function resetForm() {
    catId.value = '';
    catName.value = '';
    catDesc.value = '';
    saveBtn.textContent = 'Add Category';
    setMsg('');
  }

  resetBtn.addEventListener('click', (e) => { e.preventDefault(); resetForm(); });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const name = (catName.value || '').trim();
    const desc = (catDesc.value || '').trim();
    if (!name) { setMsg('Name is required.', false); return; }

    try {
      if (catId.value) {
        await api('PUT', API + '/categories.php', { id: parseInt(catId.value, 10), name, description: desc });
        setMsg('Category updated.');
      } else {
        await api('POST', API + '/categories.php', { name, description: desc });
        setMsg('Category created.');
      }
      resetForm();
      await load();
    } catch(e2) {
      setMsg('Save failed: ' + e2.message, false);
    }
  });

  load();
});