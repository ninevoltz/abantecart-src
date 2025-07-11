<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php echo $category_tabs ?>
<div id="content" class="panel panel-default">
	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<div class="btn-group">
				<a class="btn btn-white tooltips back-to-grid tooltips" data-table-id="category_grid" href="<?php echo $list_url; ?>" data-toggle="tooltip"
                   data-original-title="<?php echo_html2view($text_back_to_list); ?>">
					<i class="fa fa-arrow-left fa-lg"></i>
				</a>
		    </div>
		<?php if( $category_id ) { ?>
			<div class="btn-group mr10 toolbar">
				<a class="btn btn-primary tooltips" href="<?php echo $insert; ?>" title="<?php echo_html2view($button_add); ?>">
				    <i class="fa fa-plus"></i>
				</a>
                <?php echo $this->getHookVar('category_form_toolbar_buttons'); ?>
			</div>
        <?php }
        if ($preview) { ?>
            <div class="btn-group">
                <a class="btn btn-white lock-on-click tooltips" target="_blank" href="<?php echo $preview; ?>"
                   data-toggle="tooltip" data-original-title="<?php echo_html2view($text_view); ?>">
                    <i class="fa fa-external-link"></i>
                </a>
            </div>
        <?php } ?>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">

	<div class="col-md-9 mb10">
		<?php foreach ($form['fields'] as $section => $fields) { ?>
		<label class="h4 heading" id="<?php echo $section;?>"><?php echo ${'tab_' . $section}; ?></label>
			<?php foreach ($fields as $name => $field) {
				//Logic to calculate fields width
				$widthCssClasses = "col-sm-9";
                if ( str_contains((string)$field->style, 'small-field') || str_contains((string)$field->style, 'btn_switch') ) {
					$widthCssClasses = "col-sm-3";
				} else if ( str_contains((string)$field->style, 'tiny-field') ) {
					$widthCssClasses = "col-sm-2";
				}
				$widthCssClasses .= " col-xs-12";
			?>
		<div class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div class="input-group afield <?php echo $widthCssClasses; ?> <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
				<?php if($name == 'keyword') { ?>
				<span class="input-group-btn">
					<?php echo $keyword_button; ?>
				</span>
				<?php }
                echo $field; ?>
			</div>
		    <?php if (!empty($error[$name])) { ?>
		    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
		    <?php } ?>
		</div>
			<?php }
        }  ?>
	</div>
	<div class="col-md-3 mb10">
			<div id="image">
			   <?php
               echo $this->getHookVar('category_form_hook_before_resources');
               if ( !empty($update) ) {
				echo $resources_html;
			}
			// add RL-scripts anyway for ckeditor usage
			echo $resources_scripts; ?>
			</div>
	</div>
		
	</div>

	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary lock-on-click">
			<i class="fa fa-save fa-fw"></i> <?php echo $form['submit']->text; ?>
			</button>
			<a class="btn btn-default" href="<?php echo $cancel; ?>">
			<i class="fa fa-arrow-left fa-fw"></i> <?php echo $form['cancel']->text; ?>
			</a>
		</div>
	</div>
	</form>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#editFrm_generate_seo_keyword').click(function(){
            var seo_name = $('#editFrm_category_description<?php echo $language_id; ?>name').val().replace('%','').replace('&','~');
            $.get('<?php echo $generate_seo_url;?>&seo_name='+seo_name, function(data){
                $('#editFrm_keyword').val(data).change();
            });
        });
    });
</script>