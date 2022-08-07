var cropper;
var image;
function getUploadImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $("#previewImage").attr("src", e.target.result);
            // console.log(e.target.result);
            image = document.getElementById("previewImage");
            cropper = new Cropper(image, {
                aspectRatio: 16 / 9,
                crop(e) {
                    console.log(e.detail);
                },
            });
        };

        reader.readAsDataURL(input.files[0]);
    }
}

$("#uploadImageInput").change(function () {
    getUploadImage(this);
});

$("#cropImageButton").on("click", function (e) {
    var croppedimg = image.cropper.getCroppedCanvas().toDataURL("image/png");
    $("#modalCanvasImage").attr("src", croppedimg);
});