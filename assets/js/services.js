//$("document").ready(function(){$('input[type="email"]').on("input",function(){fetchBlob(`/forms/login.img.php?usuario=${this.value}`,function(e){null!==e?($(".image-user").after('<div class="img-rotate-overlay"></div>'),$(".image-user").attr("src",URL.createObjectURL(e)).css("filter","grayscale(100%)")):($(".img-rotate-overlay").remove(),$(".image-user").attr("src","/assets/images/user1.png").removeAttr("style"))})}),$("#product-catalog").on("change",function(e){var t=$(this)[0].files[0];new Upload(t,"doc").then(function(e){"success"===e.status?($("#product-catalog").attr("disabled","true"),$("#product-catalog").css("border","2px solid green"),$("#product_catalog_name").val(e.path),Swal.close()):(Swal.fire(e),$("#product-catalog").removeAttr("disabled"),$("#product-catalog").css("border","2px solid red"))})}),$("#product-image").on("change",function(e){var t=$(this)[0].files[0];new Upload(t,"doc").then(function(e){"success"===e.status?($("#product-image").attr("disabled","true"),$("#product-image").css("border","2px solid green"),$("#product_image_name").val(e.name),Swal.close()):(Swal.fire(e),$("#product-image").removeAttr("disabled"),$("#product-image").css("border","2px solid red"))})}),$(".form-select").each(function(){$(this).trigger("change")}),setInterval(function(){$('input[type="search"]').val("").trigger("input")});var Upload=function(e,t){this.file=e,this.name=t};Upload.prototype.getType=function(){return this.file.type},Upload.prototype.getSize=function(){return this.file.size},Upload.prototype.getName=function(){return this.file.name},Upload.prototype.then=function(e){var t=new FormData;t.append("doc",this.file,this.getName()),$.ajax({url:"/forms/upload.php",cache:!1,contentType:!1,processData:!1,data:t,type:"POST",beforeSend:function(){preloader()},success:function(t){e(t)},error:function(e){Swal.close()},always:function(){Swal.close()}})},Upload.prototype.progressHandling=function(e){var t=0,o=e.loaded||e.position,a=e.total;e.lengthComputable&&(t=Math.ceil(o/a*100))};const toBase64=e=>new Promise((t,o)=>{let a=new FileReader;a.readAsDataURL(e),a.onload=()=>t(a.result),a.onerror=o});


$("document").ready(function () {
    // Event handler for input changes on email fields
    $('input[type="email"]').on("input", function () {
        fetchBlob(`/forms/login.img.php?usuario=${this.value}`, function (e) {
            if (e !== null) {
                // If image is found, show image and apply grayscale filter
                $(".image-user").after('<div class="img-rotate-overlay"></div>');
                $(".image-user").attr("src", URL.createObjectURL(e)).css("filter", "grayscale(100%)");
            } else {
                // If no image, remove overlay and reset image
                $(".img-rotate-overlay").remove();
                $(".image-user").attr("src", "/assets/images/user1.png").removeAttr("style");
            }
        });
    });

    // Event handler for file change on product catalog input
    $("#product-catalog").on("change", function (e) {
        var t = $(this)[0].files[0];
        new Upload(t, "doc").then(function (e) {
            if (e.status === "success") {
                // If upload successful, disable input and set border to green
                $("#product-catalog").attr("disabled", "true");
                $("#product-catalog").css("border", "2px solid green");
                $("#product_catalog_name").val(e.path);
                Swal.close();
            } else {
                // If upload failed, show error and reset styles
                Swal.fire(e);
                $("#product-catalog").removeAttr("disabled");
                $("#product-catalog").css("border", "2px solid red");
            }
        });
    });

    // Event handler for file change on product image input
    $("#product-image").on("change", function (e) {
        var t = $(this)[0].files[0];
        new Upload(t, "doc").then(function (e) {
            if (e.status === "success") {
                // If upload successful, disable input and set border to green
                $("#product-image").attr("disabled", "true");
                $("#product-image").css("border", "2px solid green");
                $("#product_image_name").val(e.name);
                Swal.close();
            } else {
                // If upload failed, show error and reset styles
                Swal.fire(e);
                $("#product-image").removeAttr("disabled");
                $("#product-image").css("border", "2px solid red");
            }
        });
    });

    // Trigger change event for each select field
    $(".form-select").each(function () {
        $(this).trigger("change");
    });

    // Reset search input every interval
    setInterval(function () {
        //$('input[type="search"]').val("").trigger("input");
    });

    // Upload function constructor
    var Upload = function (e, t) {
        this.file = e;
        this.name = t;
    };

    // Methods for the Upload object
    Upload.prototype.getType = function () {
        return this.file.type;
    };

    Upload.prototype.getSize = function () {
        return this.file.size;
    };

    Upload.prototype.getName = function () {
        return this.file.name;
    };

    // Handling the response of the upload request
    Upload.prototype.then = function (e) {
        var t = new FormData();
        t.append("doc", this.file, this.getName());

        $.ajax({
            url: "/forms/upload.php",
            cache: false,
            contentType: false,
            processData: false,
            data: t,
            type: "POST",
            beforeSend: function () {
                preloader(); // Preloader function call before sending request
            },
            success: function (t) {
                e(t); // Call the callback with the response
            },
            error: function (e) {
                Swal.close(); // Close the Swal alert in case of an error
            },
            always: function () {
                Swal.close(); // Ensure Swal is closed after completion
            }
        });
    };

    // Progress handling method for upload (not fully utilized in this snippet)
    Upload.prototype.progressHandling = function (e) {
        var t = 0;
        var o = e.loaded || e.position;
        var a = e.total;
        if (e.lengthComputable) {
            t = Math.ceil(o / a * 100);
        }
    };

    // Function to convert a file to a Base64 string
    const toBase64 = e => new Promise((t, o) => {
        let a = new FileReader();
        a.readAsDataURL(e);
        a.onload = () => t(a.result);
        a.onerror = o;
    });
});
