<?php
if ($show_payment == false) { ?>
</form>
<?php }
if($login_form['form_open']){
$login_form['form_open']->style = ' needs-validation ';
//block native browser validation messages
$login_form['form_open']->attr .= ' novalidate ';
echo $login_form['form_open']; ?>
<div id="login_error_container">
    <?php if (in_array($action, ['', 'login']) && $error) {
        include($this->templateResource('/template/responses/checkout/alerts.tpl'));
     } ?>
</div>
<p class="text-center"> <?php echo $fast_checkout_text_please_login; ?>:</p>
<fieldset>
    <div class="mb-3 col-10 col-sm-6 mx-auto">
        <?php
        $login_form['loginname']->placeholder = $fast_checkout_text_login_or_email;
        $login_form['loginname']->attr .= ' autocomplete="username email" required aria-required="true"';
        echo $login_form['loginname']; ?>
    </div>
    <div class="mb-3 col-10 col-sm-6 mx-auto">
        <?php
        $login_form['password']->placeholder = $fast_checkout_text_password;
        $login_form['password']->attr .= ' autocomplete="current-password" aria-required="true" required';
        echo $login_form['password']; ?>
    </div>
</fieldset>
<br>
<div class=" text-center">
	<a href="<?php echo $reset_url; ?>" class="btn btn-outline-success" target="_login">
		<i class="fa fa-user-plus fa-fw"></i> <?php echo $fast_checkout_button_reset_login; ?>
	</a>
    <button id="LoginFrm_Submit" class="btn btn-primary me-2" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo_html2view($fast_checkout_text_authenticating); ?>">
        <i class="fa fa-lock fa-fw"></i> <?php echo $button_submit; ?>
    </button>
</div>
</form>
<?php echo $this->getHookVar('login_extension');
}
?>
