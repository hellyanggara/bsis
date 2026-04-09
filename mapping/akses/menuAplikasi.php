<?php
    $codeSource = 'bsis';
    $httpHost = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http')).'://'.$_SERVER['HTTP_HOST'].'/'.$codeSource.'/';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'].'/'.$codeSource.'/';
    require $documentRoot.'config/connection.config.php';
    require $documentRoot.'config/utility.config.php';
    signInCheck();
    include ('akses.php');
    $q = new akses();
    
    $id = $_GET['id'];
    $qMenu = $q->get_menu($id);
?>
<hr>
<div id="divMenu">
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
                <?php
                while($dtMenu = $qMenu->fetch_object()){?>
                <tr>
                    <td>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input chkMenu" type="checkbox" id="chk-<?=$dtMenu->id ?>" value="" name="menu[]">
                            <label for="chk-<?=$dtMenu->id ?>" class="custom-control-label"><?=$dtMenu->nama ?></label>
                        </div>
                    </td>
                    <td>
                        <div class="custom-control custom-checkbox divChk-<?= $dtMenu->id ?>" style="display: none;">
                            <input class="custom-control-input chkCreate" type="checkbox" id="chkCreate-<?=$dtMenu->id ?>" value="1">
                            <label for="chkCreate-<?=$dtMenu->id ?>" class="custom-control-label"></label>
                        </div>
                        <input type="hidden" id="txtCreate-<?=$dtMenu->id ?>" name="create[]" value="0" disabled>
                    </td>
                    <td>
                        <div class="custom-control custom-checkbox divChk-<?= $dtMenu->id ?>" style="display: none;">
                            <input class="custom-control-input chkUpdate" type="checkbox" id="chkUpdate-<?=$dtMenu->id ?>" value="1">
                            <label for="chkUpdate-<?=$dtMenu->id ?>" class="custom-control-label"></label>
                        </div>
                        <input type="hidden" id="txtUpdate-<?=$dtMenu->id ?>" name="update[]" value="0" disabled>
                    </td>
                    <td>
                        <div class="custom-control custom-checkbox divChk-<?= $dtMenu->id ?>" style="display: none;">
                            <input class="custom-control-input chkDelete" type="checkbox" id="chkDelete-<?=$dtMenu->id ?>" value="1">
                            <label for="chkDelete-<?=$dtMenu->id ?>" class="custom-control-label"></label>
                        </div>
                        <input type="hidden" id="txtDelete-<?=$dtMenu->id ?>" name="delete[]" value="0" disabled>
                    </td>
                </tr>
                <?php }?>
            </tbody>
        </table>
        <span id="err_chk_menu" class="error invalid-feedback">Pilih minimal 1 menu</span>
    </div>
</div>
<script src="<?php echo $httpHost ?>vendor/dist/js/custom.js"></script>
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