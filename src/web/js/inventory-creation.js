/* Item Amount Incrementer */
document.addEventListener("DOMContentLoaded",function() {

    var stepper = document.getElementById('itemQuantity');
    var stepperInput = stepper.getElementsByTagName('input')[0];

    function incrementStepperInput(amount) {
        stepperInput.value = ((parseInt(stepperInput.value) || 0) + amount);
    }

    var stepperDecrement = stepper.getElementsByTagName('button')[0];
    stepperDecrement.addEventListener("click",function(){incrementStepperInput(-1)});

    var stepperInputIncrement = stepper.getElementsByTagName('button')[1];
    stepperInputIncrement.addEventListener("click",function(){incrementStepperInput(1)});
});

document.getElementById('itemForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this); 
        const data = {};
        console.log("Submitted");

        formData.forEach((value, key) => {
            data[key] = value;
            console.log("key: ", key);
            console.log("value: ", value);
        });
        const invAPI = data['inventoryAPI']
        delete data['inventoryAPI']
        fetch(invAPI, {
            method: 'POST', 
            headers: {
                'Content-Type': 'application/json', 
            },
            body: JSON.stringify(data) 
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response; 
        })
        .then(result => {
            console.log('Success:', result);
            window.location.href = window.location.pathname + '?alert=edit';
        })
        .catch(error => {
            console.error('Error:', error);
        });
});