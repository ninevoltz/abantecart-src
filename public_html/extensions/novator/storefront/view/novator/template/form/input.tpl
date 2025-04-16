<?php if (!$no_wrapper || $type=='password' ){ ?>
<div class="input-group">
<?php } ?>
    <input type="<?php echo $type ?>" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>"
           placeholder="<?php echo $placeholder ?>" class="form-control <?php echo $style; ?>"
        <?php echo $attr;
        echo $regexp_pattern ? ' pattern="' . $regexp_pattern . '"' : '';
        echo $error_text ? ' title="' . $error_text . '"' : '';
        if ($list) {
            echo ' list="' . $id . '_list"';
        }
        if ($required) {
            echo ' required';
        } ?>/>
    <?php
    if ($list) { ?>
        <datalist id="<?php echo $id . '_list' ?>">
            <?php foreach ((array)$list as $l) {
                echo '<option value="' . htmlspecialchars($l, ENT_QUOTES, 'UTF-8') . '">';
            } ?>
        </datalist>
    <?php }
if($type=='password'){ ?>
    <div class="input-group-text rounded-end">
        <a href="Javascript:void(0);"><i class="bi bi-eye-slash-fill" aria-hidden="true"></i></a>
    </div>
    <script type="application/javascript">
        $(document).ready(function() {
            const pwdwrp = $("#<?php echo $id ?>").parent();
            const pwd = $('#<?php echo $id ?>');
            pwdwrp.find("a").on('click', function(event) {
                event.preventDefault();
                if(pwd.attr("type") === "text"){
                    pwd.attr('type', 'password');
                    pwdwrp.find('i')
                        .addClass( "bi-eye-slash-fill" )
                        .removeClass( "bi-eye-fill" );
                }else if($('#<?php echo $id ?>').attr("type") === "password"){
                    pwd.attr('type', 'text');
                    pwdwrp.find('i')
                        .removeClass( "bi-eye-slash-fill" )
                        .addClass( "bi-eye-fill" );
                }
            });
            $(document).on('keypress', function ( e ) {
                if(e.target.getAttribute('id') !== '<?php echo $id ?>'){
                    return;
                }
                e = e || window.event;
                var s = String.fromCharCode( e.keyCode || e.which );
                if ( (s.toUpperCase() === s) !== e.shiftKey ) {
                    pwdwrp.find('.pwdhelp').removeClass('d-none').addClass('d-block');
                }else{
                    pwdwrp.find('.pwdhelp').removeClass('d-block').addClass('d-none');
                }
            });
        });</script>
<?php }
    if ($required) { ?>
        <span class="input-group-text text-danger rounded-end">*</span>
    <?php }
    if($type=="password"){ ?>
        <div class="pwdhelp text-dark fw-bold form-text d-none w-100">
            <?php echo $this->language->get('warning_capslock')?>
        </div>
    <?php }
    if (!$no_wrapper || $type=='password'){ ?>
</div>
<?php }

