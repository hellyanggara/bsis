<?php
    @session_start();
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
?>
<script src="<?php echo $httpHost ?>vendor/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/select2/js/select2.setfocus.minified.js"></script>
<script src="<?php echo $httpHost ?>vendor/dist/js/adminlte.min.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/moment/moment.min.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/inputmask/jquery.inputmask.min.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/daterangepicker/daterangepicker.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/chart.js/Chart.min.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/jszip/jszip.min.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/autosize/dist/autosize.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/sweetalert2/sweetalert2@11.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/html2pdf/html2pdf.bundle.min.js"></script>
<script src="<?php echo $httpHost ?>vendor/plugins/pdfjs/pdf.min.js"></script><script src="<?php echo $httpHost ?>vendor/plugins/html2canvas/html2canvas.min.js"></script>

<script>
    $(function () {
        bsCustomFileInput.init();
        $('.my-colorpicker2').colorpicker()

        $('.my-colorpicker2').on('colorpickerChange', function(event) {
            $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
        })
    });
    autosize($('textarea'));
    $(document).ready(function() {
        notif()
    });
    function notif(){
        $('#notif').load("<?php echo $httpHost ?>config/notif.config.php");
    }
    
    function setInputFilter(textbox, inputFilter, errMsg) {
        [ "input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop", "focusout" ].forEach(function(event) {
            textbox.addEventListener(event, function(e) {
            if (inputFilter(this.value)) {
                // Accepted value.
                if ([ "keydown", "mousedown", "focusout" ].indexOf(e.type) >= 0){
                this.classList.remove("input-error");
                this.setCustomValidity("");
                }

                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            }
            else if (this.hasOwnProperty("oldValue")) {
                // Rejected value: restore the previous one.
                this.classList.add("input-error");
                this.setCustomValidity(errMsg);
                this.reportValidity();
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            }
            else {
                // Rejected value: nothing to restore.
                this.value = "";
            }
            });
        });
    }
    
    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? rupiah : '');
    }

    function savePageAsPDF(url, qrcode) {
        return new Promise(function(resolve, reject) {
            var decodedUrl = atob(url);
            // Buka halaman B di jendela baru
            var newWindow = window.open(decodedUrl+qrcode, '_blank');

            newWindow.onload = function() {
                var element = newWindow.document.querySelector('.page');
                html2canvas(element, { scale: 1 }).then(function(canvas) {
                    canvas.toBlob(function(blob) {
                        var formData = new FormData();
                        formData.append('image', blob, qrcode + '.png');

                        fetch('../../save-image.php', {
                            method: 'POST',
                            body: formData
                        }).then(response => response.text()).then(result => {
                            console.log(result);
                        }).catch(error => {
                            console.error('Error:', error);
                        });
                        newWindow.close();
                        resolve();
                    }, 'image/png');
                }).catch(function(error) {
                    console.error('Error:', error);
                    newWindow.close();
                    reject(error);
                });
            };
        });
    }

    function printPage(url, qrcode) {
        // Panggil window.print untuk halaman A
        window.print();

        // Simpan halaman B sebagai PDF
        savePageAsPDF(url, qrcode);
    }
</script>
<div id="notif"></div>

