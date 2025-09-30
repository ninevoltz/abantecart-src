<?php
if ($show_payment == false) { ?>
</form>
<?php }
if($login_form['form_open']){
$login_form['form_open']->style = ' needs-validation ';
//block native browser validation messages
$login_form['form_open']->attr .= ' novalidate ';
echo $login_form['form_open']; ?>

<?php echo $this->getHookVar('login_extension');
}
?>
