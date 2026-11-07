/*
Template Name: Cloud Rebue Solutions
Author: Edward Muss
Version: 4.1.0
Website: https://edwardmuss.tech/
Contact: edwardmuss5@gmail.com
File: Common Plugins Js File
*/

//Common plugins
if (
    document.querySelectorAll("[toast-list]").length ||
    document.querySelectorAll("[data-choices]").length ||
    document.querySelectorAll("[data-provider]").length
) {
    document.writeln(
        "<script type='text/javascript' src='https://cdn.jsdelivr.net/npm/toastify-js'></script>"
    );
    document.writeln(
        "<script type='text/javascript' src='{{ asset('build/libs/choices.js/public/assets/scripts/choices.min.js') }}'></script>"
    );
    document.writeln(
        "<script type='text/javascript' src='{{ asset('build/libs/flatpickr/flatpickr.min.js') }}'></script>"
    );
}
