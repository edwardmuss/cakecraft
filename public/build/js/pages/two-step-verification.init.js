/*
Template Name: Cloud Rebue Solutions
Author: Edward Muss
Website: https://edwardmuss/
Contact:edwardmuss5@gmail.com
File: Two step verification Init Js File
*/

// move next

function getInputElement(index) {
    return document.getElementById("digit" + index + "-input");
}

function moveToNext(index, event) {
    const eventCode = event.which || event.keyCode;
    const inputElement = getInputElement(index);

    // Move to next input if current input has a value and is not the last input
    if (inputElement.value.length === 1) {
        if (index < 6) {
            getInputElement(index + 1).focus();
        } else {
            // Submit code
            console.log("submit code");
            document.getElementById("login-otp-form").submit();
        }
    }

    // Move to previous input on backspace
    if (eventCode === 8 && index > 1) {
        getInputElement(index - 1).focus();
    }
}
