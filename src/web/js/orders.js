document.addEventListener('DOMContentLoaded', function () {
    // Enable Edit/Delete Order buttons only when a radio is selected
    const editBtn = document.getElementById("edit-order-btn");
    const deleteBtn = document.getElementById("delete-order-btn");
    let selectedOrderId = null;
    let prevSelectedOrderId = null;

    function setSelection(orderId) {
        selectedOrderId = orderId;
        if (selectedOrderId) {
            if (editBtn) { editBtn.disabled = false; editBtn.style.opacity = 1; }
            if (deleteBtn) { deleteBtn.disabled = false; deleteBtn.style.opacity = 1; }
        } else {
            if (editBtn) { editBtn.disabled = true; editBtn.style.opacity = 0.5; }
            if (deleteBtn) { deleteBtn.disabled = true; deleteBtn.style.opacity = 0.5; }
        }

        // Toggle row highlight
        try {
            if (prevSelectedOrderId) {
                const prevRow = document.getElementById("order_row_" + prevSelectedOrderId);
                if (prevRow) prevRow.classList.remove("selected-row");
            }
            const newRow = document.getElementById("order_row_" + selectedOrderId);
            if (newRow) newRow.classList.add("selected-row");
            prevSelectedOrderId = selectedOrderId;
        } catch (err) {
            // ignore DOM errors
        }
    }

    // Delegate change events for radio inputs
    document.addEventListener("change", function (e) {
        if (e.target && e.target.classList && e.target.classList.contains("select-order-radio")) {
            setSelection(e.target.value);
        }
    });

    // Fallback: clicking on the row label should also set selection
    document.addEventListener('click', function (e) {
        const label = e.target.closest('label');
        if (!label) return;
        const radio = label.querySelector('.select-order-radio');
        if (radio) {
            // delay to let radio become checked
            setTimeout(() => setSelection(radio.value), 10);
        }
    });

    if (editBtn) {
        editBtn.addEventListener("click", function () {
            if (!selectedOrderId) return;
            // Navigate to the order edit page with the selected order id
            window.location.href = "addorder.php?id=" + encodeURIComponent(selectedOrderId);
        });
    }

    // Delete button handler
    if (deleteBtn) {
        deleteBtn.addEventListener('click', async function () {
            if (!selectedOrderId) return;
            const ok = confirm('Delete order #' + selectedOrderId + '? This cannot be undone.');
            if (!ok) return;

            try {
                const resp = await fetch('../../api/orders/api_orders.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ orderID: parseInt(selectedOrderId, 10) })
                });
                const data = await resp.json();
                if (resp.ok) {
                    alert('Order deleted');
                    // Remove row from table if present
                    const row = document.getElementById('order_row_' + selectedOrderId);
                    if (row) row.remove();
                    // Reset selection
                    setSelection(null);
                } else {
                    alert('Delete failed: ' + (data.error || JSON.stringify(data)));
                }
            } catch (err) {
                alert('Request failed: ' + err.message);
            }
        });
    }
});

