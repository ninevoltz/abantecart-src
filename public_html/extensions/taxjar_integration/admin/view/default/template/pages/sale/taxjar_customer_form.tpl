<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>
<?php if ($exist) { ?>
<div class="alert alert-danger" role="alert"><?php echo $exist;?></div>
<?php } ?>
<ul class="nav nav-tabs nav-justified nav-profile">
	<?php
	foreach ($tabs as $tab) {
		if ($tab['active']) {
			$classname = 'active';
		} else {
			$classname = '';
		}
		?>
		<li class="<?php echo $classname; ?>">
			<a <?php echo($tab['href'] ? 'href="' . $tab['href'] . '" ' : ''); ?>><strong><?php echo $tab['text']; ?></strong></a>
		</li>
	<?php } ?>

	<?php echo $this->getHookVar('extension_tabs'); ?>
</ul>

<div id="content" class="panel panel-default">
	<?php echo $form['form_open'];
	foreach($form['fields'] as $section=>$fields){
	?>

	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		<label class="h4 heading"><?php echo ${'tab_customer_' . $section}; ?></label>
		<?php foreach ($fields as $name => $field) { ?>
		<?php
		//Logic to calculate fields width
		$widthcasses = "col-sm-7";
		if (is_int(stripos($field->style, 'large-field'))) {
			$widthcasses = "col-sm-7";
		} else if (is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date'))) {
			$widthcasses = "col-sm-5";
		} else if (is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch'))) {
			$widthcasses = "col-sm-3";
		} else if (is_int(stripos($field->style, 'tiny-field'))) {
			$widthcasses = "col-sm-2";
		}
		$widthcasses .= " col-xs-12";
		?>
		<div class="form-group <?php if (!empty($error[$name])) {
			echo "has-error";
		} ?>">
			<label class="control-label col-sm-3 col-xs-12"
				   for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>

			<div class="input-group afield <?php echo $widthcasses; ?> <?php echo($name == 'description' ? 'ml_ckeditor' : '') ?>">
				<?php if($name == 'email') { ?>
				<span class="input-group-btn">
					<a type="button" title="mailto" class="btn btn-info" href="mailto:<?php echo $field->value; ?>">
					<i class="fa fa-envelope-o fa-fw"></i>
					</a>
				</span>
				<?php } ?>
				<?php echo $field; ?>
			</div>
			<?php if (!empty($error[$name])) { ?>
				<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>
		<?php } ?><!-- <div class="fieldset"> -->
	<input type="hidden" value="<?php echo ($update==='1') ? 1 : 0 ?>" name="update"/>
	</div>
<?php } ?>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary lock-on-click">
				<i class="fa fa-save fa-fw"></i> <?php echo $form['submit']->text; ?>
			</button>
			<?php if ($update==='1') { ?>
			<button class="btn btn-primary lock-on-click">
				<i class="fa fa-save fa-fw"></i> <?php echo $form['update']->text; ?>
			</button>
			<?php } ?>
			<button class="btn btn-default" type="reset">
				<i class="fa fa-refresh fa-fw"></i> <?php echo $button_reset; ?>
			</button>
			<?php if($form['delete']){ ?>
				<a class="btn btn-danger" data-confirmation="delete"
				   href="<?php echo $form['delete']->href; ?>">
					<i class="fa fa-trash-o"></i> <?php echo $form['delete']->text; ?>
				</a>
			<?php } ?>
		</div>
	</div>	
	</form>

</div>