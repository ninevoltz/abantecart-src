<?php foreach ($form['fields'] as $name => $field) { ?>
<?php
				//Logic to calculate fields width
				$widthcasses = "col-sm-7";
				if ( is_int(stripos($field->style, 'large-field')) ) {
$widthcasses = "col-sm-7";
} else if ( is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date')) ) {
$widthcasses = "col-sm-5";
} else if ( is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch')) ) {
$widthcasses = "col-sm-3";
} else if ( is_int(stripos($field->style, 'tiny-field')) ) {
$widthcasses = "col-sm-2";
}
$widthcasses .= " col-xs-12";
?>
<div id="<?php echo $name;?>" class="form-group <?php if (!empty($error[$name])) { echo " has-error "; } ?>">
<label class="control-label col-sm-4 col-xs-12"
       for="<?php echo $field->element_id; ?>"><?php echo ${'text_' . $name}; ?></label>
<div class="input-group afield <?php echo $widthcasses; ?>">
    <?php echo $field; ?>
</div>
</div>
<?php } ?>