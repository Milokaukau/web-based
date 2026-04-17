$(document).ready(function(){

    /* PROFILE TABS */

    $('.tab-btn').on('click', function(){
        var target = $(this).data('tab');

        // UPDATE BUTTONS
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');

        // UPDATE CONTENT PANELS
        $('.tab-content').removeClass('active');
        $('#' + target).addClass('active');
    });

    // PHOTO PREVIEW

    $('#photo').on('change', function(){
        var file = this.files[0];
        if (!file) return;

        var allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if(allowed.indexOf(file.type) === -1){
            alert('Only JPG, PNG, GIF, WEBP images are allowed.');
            $(this).val('');
            return;
        }

        if (file.size > 2 * 1024 * 1024){
            alert('Photo must be under 2MB.');
            $(this).val('');
            return;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            $('#photo-preview').attr('src', e.target.result);
            $('#avatar-preview').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
    });

    // SHOW / HIDE PASSWORD
   // SHOW / HIDE PASSWORD
// SHOW / HIDE PASSWORD
$('.toggle-password').on('click', function(){
    var $input = $(this).closest('.password-group').find('input');
    var isHidden = $input.attr('type') === 'password';
    $input.attr('type', isHidden ? 'text' : 'password');
    $(this).text(isHidden ? '⌣' : '👁');
});

    // LOGIN FORM TO PREVENT DOUBLE-SUBMIT
    $('#login-form').on('submit', function(){
        $(this).find('button[type="submit"]').prop('disabled', true).text('Signing in...');
    });
});