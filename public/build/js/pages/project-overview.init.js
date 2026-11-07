/*
Template Name: Cloud Rebue Solutions
Author: Edward Muss
Website: https://edwardmuss/
Contact:edwardmuss5@gmail.com
File: Project overview init js
*/

// favourite btn
var favouriteBtn = document.querySelectorAll(".favourite-btn");
if (favouriteBtn) {
    Array.from(document.querySelectorAll(".favourite-btn")).forEach(function (
        item
    ) {
        item.addEventListener("click", function (event) {
            this.classList.toggle("active");
        });
    });
}
