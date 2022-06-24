let input = document.getElementById("cw_uploader_easy_inputTag");
let imageName = document.getElementById("cw_uploader_easy_imageName");

input.addEventListener("change", () => {
    let inputImage = document.querySelector("input[type=file]").files[0];

    imageName.innerText = inputImage.name;
});