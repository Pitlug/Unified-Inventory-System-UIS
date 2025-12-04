  document.getElementById('categoryForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        const formData = new FormData(this); // Get form data
        const data = {};

        formData.forEach((value, key) => {
            data[key] = value;
        });

        const catAPI = data['categoryAPI']
        delete data['categoryAPI']
        fetch(catAPI, {
            method: 'POST', // Or 'PUT', 'PATCH', etc., depending on your API
            headers: {
                'Content-Type': 'application/json', // Specify content type
            },
            body: JSON.stringify(data) // Convert data to JSON string
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response; // Parse JSON response
        })
        .then(result => {
            console.log('Success:', result);
            window.location.href = window.location.pathname + '?alert=edit';
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });