$(document).ready(function () {
    $('.open-modal').on('click', function (e) {
        e.preventDefault(); 

        var imgSrc = $(this).attr('data-img'); 
        console.log("Image Source:", imgSrc); // Debugging

        if (imgSrc && imgSrc.startsWith("data:image")) { 
            $('#modalImage').attr('src', imgSrc); 
            $('#imageModal').modal('show'); 
        } else {
            alert('No valid document available.');
        }
    });
});