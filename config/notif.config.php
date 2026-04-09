<?php
@session_start();
if(isset($_SESSION['V1c1T2NHTjNQVDA9_notif_status'])){
    if($_SESSION['V1c1T2NHTjNQVDA9_notif_status'] == 'Sukses'){
        $icon = 'success';
        $badge = 'badge-success';
        $notif = '<p style="font-size: 50% !important;"><i class="fa fa-square text-success"></i> Tersimpan</p>';
    }else{
        $icon = 'error';
        $badge = 'badge-danger';
        $notif = '<p style="font-size: 50% !important;"><i class="fa fa-square text-danger"></i> Tidak tersimpan</p>';
    }
    $message = $_SESSION['V1c1T2NHTjNQVDA9_notif_message'];
?>
    <script>
        Swal.fire({
            icon: '<?php echo $icon ?>',
            title: '<strong>Status Penyimpanan</strong>' + '<br><?php echo $notif ?>',
            html: '<span class="badge <?php echo $badge ?>"><?php echo $message ?></span>',
            showConfirmButton: true,
            showCloseButton: false,
            allowOutsideClick: true,
            timer: 2500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    </script>
<?php 
    unset($_SESSION['V1c1T2NHTjNQVDA9_notif_status']);
    unset($_SESSION['V1c1T2NHTjNQVDA9_notif_message']);
} ?>