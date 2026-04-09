<?php
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
    require $documentRoot.'config/connection.config.php';
    require $documentRoot.'config/utility.config.php';
    signInCheck();
    include ('akses.php');
    $q = new AKSES();
    
    $id = $_GET['id'];
    $jml = $q->cek_child($id);
if($jml > 0){
    $qsubGroup = $q->get_sub_group_menu($id);?>
    <div id="divSub">
        <div class="form-group">
            <label>Sub Group Menu Aplikasi</label>
            <select name="id_sub_group_aplikasi" id="id_sub_group_aplikasi" class="form-control select2 wajib">
                <option></option>
                <?php
                while($dtSubMenu = $qsubGroup->fetch_object()){
                $idSubGroup = $dtSubMenu->group != "0" ? $dtSubMenu->id : $dtSubMenu->group;
                echo '<option value="'.$dtSubMenu->id.'" data-sub-group="'.$idSubGroup.'">'.$dtSubMenu->nama.'</option>';
                }
                ?>
            </select>
            <span class="error invalid-feedback">Sub Group harus dipilih</span>
        </div>
        <div id="menu_aplikasi"></div>
    </div>
    <script>
        $("#id_sub_group_aplikasi").on('select2:select', function (e) {
            let id = this.value
            $('#sub_group').val($(this).find(":selected").data("sub-group"))
            if(id != ''){
                rowMenu(id);
            }else{
                $('#menu_aplikasi').html("");
            }
        });
    </script>
    <?php
}else{
    $group = $q->cek_group($id);
    if($group->group == 1){ ?>
        <div id="menu_aplikasi"></div>
        <script>
            $(document).ready(function () {
                rowMenu('<?= $id ?>');
            });
        </script>
        <?php 
    }else{
    ?>
    <div id="divChkMenu" class="form-group">
    <label>Daftar Menu</label>
    <table class="table">
        <thead>
            <tr>
                <th>
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" id="chk-all-menu">
                        <label for="chk-all-menu" class="custom-control-label">Menu</label>
                    </div>
                </th>
                <th>Create</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input chkMenu" type="checkbox" id="chk-<?=$group->id ?>" value="" name="menu[]">
                        <label for="chk-<?=$group->id ?>" class="custom-control-label"><?=$group->nama ?></label>
                    </div>
                </td>
                <td>
                    <div class="custom-control custom-checkbox divChk-<?= $group->id ?>" style="display: none;">
                        <input class="custom-control-input chkCreate" type="checkbox" id="chkCreate-<?=$group->id ?>" value="1">
                        <label for="chkCreate-<?=$group->id ?>" class="custom-control-label"></label>
                    </div>
                    <input type="hidden" id="txtCreate-<?=$group->id ?>" name="create[]" value="0" disabled>
                </td>
                <td>
                    <div class="custom-control custom-checkbox divChk-<?= $group->id ?>" style="display: none;">
                        <input class="custom-control-input chkUpdate" type="checkbox" id="chkUpdate-<?=$group->id ?>" value="1">
                        <label for="chkUpdate-<?=$group->id ?>" class="custom-control-label"></label>
                    </div>
                    <input type="hidden" id="txtUpdate-<?=$group->id ?>" name="update[]" value="0" disabled>
                </td>
                <td>
                    <div class="custom-control custom-checkbox divChk-<?= $group->id ?>" style="display: none;">
                        <input class="custom-control-input chkDelete" type="checkbox" id="chkDelete-<?=$group->id ?>" value="1">
                        <label for="chkDelete-<?=$group->id ?>" class="custom-control-label"></label>
                    </div>
                    <input type="hidden" id="txtDelete-<?=$group->id ?>" name="delete[]" value="0" disabled>
                </td>
            </tr>
        </tbody>
    </table>
    </div>
    <script>
        
    $(".chkMenu").change(function() {
        check(this)
    });
    
    $(".chkCreate").change(function() {
        var id = this.id.substr(this.id.indexOf("-") + 1)
        // console.log(id)
        if(this.checked) {
            $("#txtCreate-"+id).val(1)
        }else{
            $("#txtCreate-"+id).val(0)
        }
    });

    $(".chkUpdate").change(function() {
        var id = this.id.substr(this.id.indexOf("-") + 1)
        // console.log(id)
        if(this.checked) {
            $("#txtUpdate-"+id).val(1)
        }else{
            $("#txtUpdate-"+id).val(0)
        }
    });

    $(".chkDelete").change(function() {
        var id = this.id.substr(this.id.indexOf("-") + 1)
        // console.log(id)
        if(this.checked) {
            $("#txtDelete-"+id).val(1)
        }else{
            $("#txtDelete-"+id).val(0)
        }
    });

    function check(e){
        var id = e.id.substr(e.id.indexOf("-") + 1)
        e.value = id
        // console.log(id)
        if(e.checked) {
            $(".divChk-"+id).show()
            $("#txtCreate-"+id).prop('disabled', false);
            $("#txtUpdate-"+id).prop('disabled', false);
            $("#txtDelete-"+id).prop('disabled', false);
        }else{
            $(".divChk-"+id).hide()
            $("#chkCreate-"+id).prop('checked', false);
            $("#chkUpdate-"+id).prop('checked', false);
            $("#chkDelete-"+id).prop('checked', false);
            $("#txtCreate-"+id).prop('disabled', true);
            $("#txtUpdate-"+id).prop('disabled', true);
            $("#txtDelete-"+id).prop('disabled', true);
        }
    }

    $("#chk-all-menu").change(function() {
        if(this.checked) {
            $(".chkMenu").prop('checked', true);
            $('.chkMenu').each(function(i, obj) {
                check(obj)
            });
        }else{
            $(".chkMenu").prop('checked', false);
            $('.chkMenu').each(function(i, obj) {
                check(obj)
            });
        }
    });
    </script>
    <?php 
    }
} ?>

<script>
    $(function () {
        $('.select2').select2({
            placeholder: "--Silahkan pilih--",
            allowClear: true,
            width: '100%',
        });
    })
    function rowMenu(id) {
        $.ajax({
            type: 'GET',
            data: {
                id:id
            },
            url: 'menuAplikasi.php',
            beforeSend: function () {
                
                $('#menu_aplikasi').html('<div class="d-flex justify-content-center"><div class="line-wobble"></div></div>');
            },
            success: function (res) {
                <?php if(!isset($_SESSION['V1c1T2NHTjNQVDA9_notif_status'])){ ?>
                    Swal.close();
                <?php }?>
                $('#menu_aplikasi').html(res);
            },
        });
    }
</script>