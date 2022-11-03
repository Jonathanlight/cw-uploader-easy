document.addEventListener('DOMContentLoaded', function() {
    console.log('document is ready. I can sleep now');

    let input = document.getElementById("cw_uploader_easy_inputTag");
    let imageName = document.getElementById("cw_uploader_easy_imageName");
    let btnClick = document.getElementsByClassName("cw_uploader_easy_button");
    let btnSpinner = document.getElementsByClassName("cw_uploader_easy_button_spinner");

    input.addEventListener("change", function() {
        let inputImage = document.querySelector("input[type=file]").files[0];
        imageName.innerText = inputImage.name;
    });

    btnSpinner[0].style.display = "none";

    btnClick[0].addEventListener("click", function() {
        btnClick[0].style.display = "none";
        btnSpinner[0].style.display = "block";
    });
});