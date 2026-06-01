<?php

error_reporting(1);

include('./include/header.php');

?>



<div class="py-5 bg-light m-3 rounded-4">

    <div class="container py-4">

        <div class="row">

            <div class="col-6">

                <div class="text-center mt-4">

                    <a href="assets/form/ramakoti_first_page (1).docx" download="Ramakoti_Form.docx" class="btn btn-primary mb-5">

                        <i class="fas fa-file-word"></i> Download File

                    </a>

                     <button class="btn btn-success mb-5"  onclick="printPDF()">
                        <i class="fas fa-print"></i> Print
                    </button>

                </div>

                <!-- start profile card -->

                <div>

                    <img src="assets/form/ramakoti_first_page.jpg" class="img-fluid" alt="">

                </div>

                <!-- end /. profile card -->

            </div>

            <div class="col-6">

                <div class="text-center mt-4">

                    <a href="assets/form/ramakoti_second_page (1).docx" download="Ramakoti_Form.docx" class="btn btn-primary mb-5">

                        <i class="fas fa-file-word"></i> Download File

                    </a>

                     <button class="btn btn-success mb-5" onclick="printPDF2()">
                        <i class="fas fa-print"></i> Print
                    </button>

                </div>

                <!-- start profile card -->

                <div>

                    <img src="assets/form/ramakoti_second_page.jpg" class="img-fluid" alt="">

                </div>

                <!-- end /. profile card -->

            </div>

        </div>

    </div>

</div>



<?php include('./include/footer.php'); ?>
<script>
function printPDF() {
    const pdfUrl = "assets/form/Ramakoti_Form-1.pdf";
    const win = window.open(pdfUrl, "_blank");

    // Wait for the PDF to load, then trigger print
    win.onload = function () {
        win.print();
    };
}
</script>

<script>
function printPDF2() {
    const pdfUrl = "assets/form/Ramakoti_Form-2.pdf";
    const win = window.open(pdfUrl, "_blank");

    // Wait for the PDF to load, then trigger print
    win.onload = function () {
        win.print();
    };
}
</script>