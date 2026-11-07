/*
Template Name: Cloud Rebue Solutions
Author: Edward Muss
Website: https://edwardmuss/
Contact:edwardmuss5@gmail.com
File: Form editor Js File
*/

// ckeditor
var ckClassicEditor = document.querySelectorAll(".ckeditor-classic");
if (ckClassicEditor) {
    Array.from(ckClassicEditor).forEach(function (editorElement) {
        ClassicEditor.create(editorElement, {
            // toolbar: {
            //     items: [
            //         "sourceEditing", // Add the Source button
            //         "bold",
            //         "italic",
            //         "link",
            //         "bulletedList",
            //         "numberedList",
            //         "|",
            //         "blockQuote",
            //         "undo",
            //         "redo",
            //     ],
            // },
            ckfinder: {
                // Specify the endpoint for uploading images
                uploadUrl: "/api/upload",

                // Optional: Add custom headers, such as CSRF tokens
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            },
        })
            .then(function (editor) {
                editor.ui.view.editable.element.style.height = "200px";
            })
            .catch(function (error) {
                console.error(error);
            });
    });
}

// Snow theme
var snowEditor = document.querySelectorAll(".snow-editor");
if (snowEditor) {
    Array.from(snowEditor).forEach(function (item) {
        var snowEditorData = {};
        var issnowEditorVal = item.classList.contains("snow-editor");
        if (issnowEditorVal == true) {
            (snowEditorData.theme = "snow"),
                (snowEditorData.modules = {
                    toolbar: [
                        [
                            {
                                font: [],
                            },
                            {
                                size: [],
                            },
                        ],
                        ["bold", "italic", "underline", "strike"],
                        [
                            {
                                color: [],
                            },
                            {
                                background: [],
                            },
                        ],
                        [
                            {
                                script: "super",
                            },
                            {
                                script: "sub",
                            },
                        ],
                        [
                            {
                                header: [false, 1, 2, 3, 4, 5, 6],
                            },
                            "blockquote",
                            "code-block",
                        ],
                        [
                            {
                                list: "ordered",
                            },
                            {
                                list: "bullet",
                            },
                            {
                                indent: "-1",
                            },
                            {
                                indent: "+1",
                            },
                        ],
                        [
                            "direction",
                            {
                                align: [],
                            },
                        ],
                        ["link", "image", "video"],
                        ["clean"],
                    ],
                });
        }
        new Quill(item, snowEditorData);
    });
}

//buuble theme
var bubbleEditor = document.querySelectorAll(".bubble-editor");
if (bubbleEditor) {
    Array.from(bubbleEditor).forEach(function (element) {
        var bubbleEditorData = {};
        var isbubbleEditorVal = element.classList.contains("bubble-editor");
        if (isbubbleEditorVal == true) {
            bubbleEditorData.theme = "bubble";
        }
        new Quill(element, bubbleEditorData);
    });
}
