document.addEventListener("DOMContentLoaded", function () {
    const deleteButton = document.getElementById("deleteButton");
    if (!deleteButton) return;

    const tableInputs = document.querySelectorAll(".inventory-table .tableCheckbox");
    
    function updateDeleteButton() {
        console.log('update');
        let selectedRowIds = [];

        tableInputs.forEach(input => {
            if (input.checked) {
                const row = input.closest("tr");
                if (row) {
                    const th = row.querySelector("th");
                    if (th) {
                        const rowId = parseInt(th.textContent.trim());
                        if (rowId !== "") {
                            selectedRowIds.push(rowId);
                        }
                    }
                }
            }
        });

        if (selectedRowIds.length > 0) {
            deleteButton.classList.remove("disabled");
            deleteButton.removeAttribute("disabled");
        } else {
            deleteButton.classList.add("disabled");
            deleteButton.setAttribute("disabled", "true");
        }

        deleteButton.dataset.selectedRows = JSON.stringify(selectedRowIds);
    }

    tableInputs.forEach(input => {
        input.addEventListener("change", updateDeleteButton);
    });

    function deleteItems(){
        let ids = JSON.parse(deleteButton.dataset.selectedRows);
        let invAPI = deleteButton.dataset.api;
        console.log(ids);
        console.log(JSON.stringify({'inventoryIDs':ids}));
        fetch(invAPI, {
            method: 'DELETE', 
            headers: {
                'Content-Type': 'application/json', 
            },
            body: JSON.stringify({'inventoryIDs':ids})
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response; 
        })
        .then(result => {
            console.log('Success:', result);
            window.location.href = window.location.pathname + '?alert=deleted';
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    deleteButton.addEventListener("click",deleteItems);

    updateDeleteButton();
});
// Redirect a user to create item page
document.getElementById('createButton').onclick = () => { window.location.href = 'create-edit-item.php'; };