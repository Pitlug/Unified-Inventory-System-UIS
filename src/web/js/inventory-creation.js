/**
 * inventory-creation.js
 * - Stepper (increment/decrement)
 * - Load categories into <select>
 * - If editing (?id=), load item via GET
 * - Submit with JSON via POST (create) or PUT (edit)
 */

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById('itemForm');
  const nameEl = document.getElementById('itemName');
  const descEl = document.getElementById('itemDesc');
  const qtyWrapper = document.getElementById('itemQuantity');
  const qtyInput = document.getElementById('quantity');
  const categorySelect = document.getElementById('categorySelect');
  const msg = document.getElementById('formMessage');
  const submitBtn = document.getElementById('submitBtn');
  const idHidden = document.getElementById('inventoryID');
  const isEdit = !!idHidden;

  // ----- Stepper -----
  function adjustQty(delta) {
    const v = parseInt(qtyInput.value || '0', 10);
    const next = Math.max(0, v + delta);
    qtyInput.value = next;
  }
  const decBtn = qtyWrapper.getElementsByTagName('button')[0];
  const incBtn = qtyWrapper.getElementsByTagName('button')[1];
  decBtn.addEventListener('click', () => adjustQty(-1));
  incBtn.addEventListener('click', () => adjustQty(1));

  // ----- Helpers -----
  function setMessage(text, ok = true) {
    msg.textContent = text || '';
    msg.style.color = ok ? 'var(--modeColorOPP)' : 'var(--accent)';
  }

  async function apiGet(url) {
    const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
    const data = await res.json().catch(() => ({}));
    if (!res.ok || data.success === false) {
      const err = (data && (data.error || data.message)) || `HTTP ${res.status}`;
      throw new Error(err);
    }
    return data;
  }

  async function apiSend(url, method, payload) {
    const res = await fetch(url, {
      method,
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(payload)
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok || data.success === false) {
      const err = (data && (data.error || data.message)) || `HTTP ${res.status}`;
      throw new Error(err);
    }
    return data;
  }

  const API = (typeof window.UIS_API === 'string' ? window.UIS_API : '') || '/api';
  const BASE = (typeof window.UIS_BASE === 'string' ? window.UIS_BASE : '') || '';

  // ----- Load categories -----
  (async function loadCategories() {
    try {
      const out = await apiGet(`${API}/categories.php`);
      const arr = out.data || out; // tolerate shape
      categorySelect.innerHTML = '<option value="" disabled selected>Select a category</option>';
      for (const c of arr) {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = c.name;
        categorySelect.appendChild(opt);
      }
    } catch (e) {
      categorySelect.innerHTML = '<option value="" disabled selected>Failed to load categories</option>';
      setMessage(`Error loading categories: ${e.message}`, false);
    }
  })();

  // ----- If editing, load item -----
  (async function loadItemIfEditing() {
    if (!isEdit) return;
    try {
      const id = idHidden.value;
      const out = await apiGet(`${API}/inventory.php?inventoryID=${encodeURIComponent(id)}`);
      const item = out.data || out;
      nameEl.value = item.name || '';
      descEl.value = item.description || '';
      qtyInput.value = typeof item.quantity === 'number' ? item.quantity : (parseInt(item.quantity, 10) || 0);
      if (item.categoryID) {
        const setCat = () => {
          const opt = [...categorySelect.options].find(o => parseInt(o.value, 10) === parseInt(item.categoryID, 10));
          if (opt) opt.selected = true;
        };
        setCat();
        setTimeout(setCat, 300);
      }
    } catch (e) {
      setMessage(`Error loading item: ${e.message}`, false);
    }
  })();

  // ----- Submit -----
  form.addEventListener('submit', async (ev) => {
    ev.preventDefault();
    setMessage('');

    const payload = {
      name: (nameEl.value || '').trim(),
      description: (descEl.value || '').trim(),
      quantity: parseInt(qtyInput.value || '0', 10),
      categoryID: parseInt(categorySelect.value, 10)
    };

    if (!payload.name)  { setMessage('Name is required.', false); return; }
    if (!Number.isInteger(payload.quantity) || payload.quantity < 0) {
      setMessage('Quantity must be a non-negative integer.', false); return;
    }
    if (!Number.isInteger(payload.categoryID)) {
      setMessage('Please choose a category.', false); return;
    }

    submitBtn.disabled = true;
    try {
      if (isEdit) {
        payload.inventoryID = parseInt(idHidden.value, 10);
        await apiSend(`${API}/inventory.php`, 'PUT', payload);
        setMessage('Item updated successfully.');
      } else {
        const res = await apiSend(`${API}/inventory.php`, 'POST', payload);
        const newId = res.inventoryID || (res.data && res.data.inventoryID);
        setMessage(`Item created successfully${newId ? ` (ID ${newId})` : ''}.`);
      }
      setTimeout(() => { window.location.href = `${BASE}/inventory.php`; }, 600);
    } catch (e) {
      setMessage(`Save failed: ${e.message}`, false);
    } finally {
      submitBtn.disabled = false;
    }
  });
});