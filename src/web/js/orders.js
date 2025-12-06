
// Enable Edit Order button only when a radio is selected
const editBtn = document.getElementById("edit-order-btn");
let selectedOrderId = null;
let prevSelectedOrderId = null;

document.addEventListener("change", function(e) {
    if (e.target && e.target.classList && e.target.classList.contains("select-order-radio")) {
        selectedOrderId = e.target.value;
        
        // Enable edit button
        if (selectedOrderId) {
            editBtn.disabled = false;
            editBtn.style.opacity = 1;
        } else {
            editBtn.disabled = true;
            editBtn.style.opacity = 0.5;
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
});

editBtn.addEventListener("click", function() {
    if (!selectedOrderId) return;
    // Navigate to the order edit page with the selected order id
    window.location.href = "addorder.php?id=" + encodeURIComponent(selectedOrderId);
});
