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

