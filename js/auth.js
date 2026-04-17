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

    // PROFILE PHOTO LIGHTBOX
    $('.photo-lightbox-trigger').on('click', function () {
        var src = $(this).attr('src');
        $('#photo-lightbox-img').attr('src', src);
        $('#photo-lightbox').addClass('open');
    });

    $('#photo-lightbox, #photo-lightbox-close').on('click', function () {
        $('#photo-lightbox').removeClass('open');
    });

    $('#photo-lightbox-img').on('click', function (e) {
        e.stopPropagation(); // clicking the image itself won't close it
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') $('#photo-lightbox').removeClass('open');
    });

    // SHOW / HIDE PASSWORD
    $('.toggle-password').on('click', function(){
        var targetId = $(this).data('target');
        var $input = $('#' + targetId);
        var isHidden = $input.attr('type') === 'password';
        $input.attr('type', isHidden ? 'text' : 'password');
        $(this).text(isHidden ? '⌣' : '👁');
    });

    // MALAYSIAN PHONE VALIDATION
    $('input[name="phone"]').on('input', function () {
        var val = $(this).val().trim();
        var myPhoneRegex = /^(\+?60|0)[0-9]{1,2}[\s\-]?[0-9]{7,8}$/;
        var $error = $(this).siblings('.error-msg');

        if (val === '') {
            $(this).removeClass('input-error');
            $error.remove();
        } else if (!myPhoneRegex.test(val)) {
            $(this).addClass('input-error');
            if ($error.length === 0) {
                $(this).after('<span class="error-msg">Invalid Malaysian phone number. Example: 012-3456789 or +60123456789.</span>');
            }
        } else {
            $(this).removeClass('input-error');
            $error.remove();
        }
    });

    $('#form-profile').on('submit', function (e) {
        var phone = $('input[name="phone"]').val().trim();
        var myPhoneRegex = /^(\+?60|0)[0-9]{1,2}[\s\-]?[0-9]{7,8}$/;
        if (phone !== '' && !myPhoneRegex.test(phone)) {
            e.preventDefault();
            var $input = $('input[name="phone"]');
            $input.addClass('input-error');
            if ($input.siblings('.error-msg').length === 0) {
                $input.after('<span class="error-msg">Invalid Malaysian phone number. Example: 012-3456789 or +60123456789.</span>');
            }
            $input[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    // LOGIN FORM TO PREVENT DOUBLE-SUBMIT
    $('#login-form').on('submit', function(){
        $(this).find('button[type="submit"]').prop('disabled', true).text('Signing in...');
    });
});