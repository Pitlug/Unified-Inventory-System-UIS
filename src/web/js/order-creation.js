
// Initialize item row handlers
function attachItemRowHandlers() {
    const removeButtons = document.querySelectorAll(".item-remove-btn");
    removeButtons.forEach(btn => {
        btn.onclick = function() {
            this.closest("tr").remove();
        };
    });
}

function runWithOrder(){
    const addItemBtn = document.getElementById("addItemBtn");
    if (addItemBtn) {
        addItemBtn.onclick = function(e) {
            e.preventDefault();
            const table = document.getElementById("itemsTable");
            const rowIdx = table.children.length;
            const newRow = document.createElement("tr");
            newRow.className = "item-row";
            newRow.dataset.itemIdx = rowIdx;
            newRow.innerHTML = `
                        <td><input type="text" class="item-name" value="" style="width:100%;" /></td>
                        <td><input type="number" class="item-qty" value="1" min="1" style="width:100%;" /></td>
                        <td><input type="number" class="item-price" value="0.0" min="0" step="0.01" style="width:100%;" /></td>
                        <td><button type="button" class="item-remove-btn" style="cursor:pointer;color:red;">Remove</button></td>
                    `;
            table.appendChild(newRow);
            attachItemRowHandlers();
        };
    }

    attachItemRowHandlers();

    const btn = document.getElementById("submitBtn");
    if (!btn) return;
    btn.addEventListener("click", async function(){
        const orderName = document.getElementById("orderName").value || null;
        const orderDate = document.getElementById("orderDate").value || null;
        const notes = document.getElementById("notes").value || null;
        const status = document.getElementById("status").value || null;
        
        // Parse items from table rows
        const itemRows = document.querySelectorAll("#itemsTable .item-row");
        const items = [];
        itemRows.forEach(row => {
            const nameInput = row.querySelector(".item-name");
            const qtyInput = row.querySelector(".item-qty");
            const priceInput = row.querySelector(".item-price");
            
            const nameVal = nameInput && nameInput.value ? nameInput.value : null;
            const qtyVal = qtyInput && qtyInput.value ? parseInt(qtyInput.value, 10) || 1 : 1;
            const priceVal = priceInput && priceInput.value ? parseFloat(priceInput.value) || 0.0 : 0.0;
            
            // Skip rows where name is empty
            if (nameVal === null) return;
            
            items.push({
                name: nameVal,
                quantity: qtyVal,
                price: priceVal
            });
        });
        
        const payload = {
            orderName: orderName,
            orderStatus: status,
            date: orderDate,
            notes: notes,
            items: items
        };
        try {
            // If editing, call the API router with method PUT; otherwise use POST
            const orderIdEl = document.getElementById("orderID");
            const apiEndpoint = "../../api/orders/api_orders.php";
            const method = (orderIdEl && orderIdEl.value) ? "PUT" : "POST";
            if (orderIdEl && orderIdEl.value) payload.orderID = parseInt(orderIdEl.value, 10);
            
            const resp = await fetch(apiEndpoint, {
                method: method,
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            });
            const data = await resp.json();
            if (resp.ok) {
                const msg = (orderIdEl && orderIdEl.value) ? "Order updated successfully" : "Order created successfully (ID: " + (data.orderID || "") + ")";
                alert(msg);
                // redirect to orders listing
                window.location.href = "../../web/orders/landingpage.php";
            } else {
                alert("Error: " + (data.error || JSON.stringify(data)));
            }
        } catch (err) {
            alert("Request failed: " + err.message);
        }
    })
}
runWithOrder();