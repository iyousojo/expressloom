<?php
?>
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <form action="" id="manage_user">
                <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
                <div class="row">
                    <div class="col-md-6 border-right">
                        <b class="text-muted">Personal Information</b>
                        <div class="form-group">
                            <label for="" class="control-label">First Name</label>
                            <input type="text" name="firstname" class="form-control form-control-sm" required
                                value="<?php echo isset($firstname) ? $firstname : '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Middle Name</label>
                            <input type="text" name="middlename" class="form-control form-control-sm"
                                value="<?php echo isset($middlename) ? $middlename : '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Last Name</label>
                            <input type="text" name="lastname" class="form-control form-control-sm" required
                                value="<?php echo isset($lastname) ? $lastname : '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Contact No.</label>
                            <input type="text" name="contact" class="form-control form-control-sm" required
                                value="<?php echo isset($contact) ? $contact : '' ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Address</label>
                            <textarea name="address" id="" cols="30" rows="4" class="form-control"
                                required><?php echo isset($address) ? $address : '' ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="control-label">Avatar</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile" name="img"
                                    onchange="displayImg(this,$(this))">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>
                        <div class="form-group d-flex justify-content-center">
                            <img src="<?php echo isset($avatar) && is_file('../assets/uploads/'.$avatar) ? '../assets/uploads/'.$avatar : 'assets/userimg/defualt.jpg' ?>"
                                alt="User Avatar" id="cimg" class="img-fluid img-thumbnail">
                        </div>
                        <b class="text-muted">System Credentials</b>
                        <?php if($_SESSION['login_type'] == 1): ?>
                        <div class="form-group">
                            <label for="" class="control-label">Account Type</label>
                            <select name="account_type" id="account_type" class="custom-select custom-select-sm">
                                <option value="personal"
                                    <?php echo isset($account_type) && $account_type == 'personal' ? 'selected' : '' ?>>
                                    Personal</option>
                                <option value="business"
                                    <?php echo isset($account_type) && $account_type == 'business' ? 'selected' : '' ?>>
                                    Business</option>
                            </select>
                        </div>
                        <?php else: ?>
                        <input type="hidden" name="account_type" value="personal">
                        <?php endif; ?>
                        <div class="form-group">
                            <label class="control-label">Email</label>
                            <input type="email" class="form-control form-control-sm" name="email" required
                                value="<?php echo isset($email) ? $email : '' ?>">
                            <small id="#msg"></small>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Password</label>
                            <input type="text" class="form-control form-control-sm" name="password"
                                value="<?php echo isset($password) ? $password : '' ?>"
                                <?php echo !isset($id) ? "required":'' ?>>
                            <small><i><?php echo isset($id) ? "Leave this blank if you don't want to change your password":'' ?></i></small>
                        </div>
                        <div class="form-group">
                            <label class="label control-label">Confirm Password</label>
                            <input type="password" class="form-control form-control-sm" name="cpass"
                                <?php echo !isset($id) ? 'required' : '' ?>>
                            <small id="pass_match" data-status=''></small>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="col-lg-12 text-right justify-content-center d-flex">
                    <button class="btn btn-primary mr-2">Save</button>
                    <button class="btn btn-secondary" type="button"
                        onclick="location.href = 'index.php?page=user_list'">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
img#cimg {
    max-height: 15vh;
    /*max-width: 6vw;*/
}
</style>
<script>
$('[name="password"],[name="cpass"]').keyup(function() {
    var pass = $('[name="password"]').val()
    var cpass = $('[name="cpass"]').val()
    if (cpass == '' || pass == '') {
        $('#pass_match').attr('data-status', '')
    } else {
        if (cpass == pass) {
            $('#pass_match').attr('data-status', '1').html('<i class="text-success">Password Matched.</i>')
        } else {
            $('#pass_match').attr('data-status', '2').html(
                '<i class="text-danger">Password does not match.</i>')
        }
    }
})

function displayImg(input, _this) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#cimg').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
$('#manage_user').submit(function(e) {
    e.preventDefault()
    $('input').removeClass("border-danger")
    start_load()
    $('#msg').html('')
    if ($('[name="password"]').val() != '' && $('[name="cpass"]').val() != '') {
        if ($('#pass_match').attr('data-status') != 1) {
            if ($("[name='password']").val() != '') {
                $('[name="password"],[name="cpass"]').addClass("border-danger")
                end_load()
                return false;
            }
        }
    }
    $.ajax({
        url: 'ajax.php?action=save_user',
        data: new FormData($(this)[0]),
        cache: false,
        contentType: false,
        processData: false,
        method: 'POST',
        type: 'POST',
        success: function(resp) {
            if (resp == 1) {
                alert_toast('Data successfully saved.', "success");
                setTimeout(function() {
                    location.replace('index.php?page=user_list')
                }, 750)
            } else if (resp == 2) {
                $('#msg').html("<div class='alert alert-danger'>Email already exist.</div>");
                $('[name="email"]').addClass("border-danger")
                end_load()
            }
        }
    })
})
</script>