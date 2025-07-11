<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span></button>
	<h4 class="modal-title"><?php echo $form_title; ?></h4>
</div>
<div class="modal-body">
<?php echo $resources_scripts;?>
<?php if (!$download_id) { ?>
		<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		<?php if( $form0 ){ ?>
			<div class="panel panel-default">
				<div class="panel-heading" role="tab">
					<label class="h4 heading">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
							<?php echo $text_select_shared_downloads; ?>
						</a>
					</label>
					<i class="pull-right fa fa-chevron-down"></i>
				</div>
				<div id="collapseOne" class="panel-collapse collapse in">
			<?php echo $form0['form_open']; ?>
				<div class="panel-body panel-body-nopadding">
                    <?php $name = 'shared'; ?>
					<div class="form-inline form-group <?php echo $error[$name] ? "has-error" : ''; ?>">
						<label class="control-label col-sm-3 col-xs-12"
                               for="<?php echo $form0['shared']->element_id; ?>">
                            <?php echo $text_select_shared_downloads; ?>
                        </label>
						<div class="input-group afield col-sm-7 col-xs-12">
							<?php echo $form0['shared']; ?>
						</div>
						<button class="btn btn-primary">
							<i class="fa fa-plus"></i> <?php echo $button_add; ?>
						</button>
						<?php if ($error[$name]) { ?>
						<span class="help-block field_err"><?php echo $error[$name]; ?></span>
						<?php } ?>
					</div>
				</div>
			</form>
				</div>
			</div>
		<?php } ?>
			<?php // insert collapses when create new product file to split form to 2 part - create from shared and create new ?>
			<div class="panel panel-default">
				<?php if( $form0 ){ ?>
				<div class="panel-heading" role="tab">
					<label class="h4 heading">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"><?php echo $text_new_file;?></a>
					</label>
					<i class="pull-right fa fa-chevron-up"></i>
				</div>
				<div id="collapseTwo" class="panel-collapse collapse <?php echo !$form0 ? 'in' : ''; ?>">
				<?php }?>
<?php } ?>
				<?php echo $form['form_open']; ?>
					<div class="panel-body panel-body-nopadding">
						<?php foreach ($form['fields'] as $section => $fields) { ?>
						<label class="h4 heading"><?php echo ${'tab_' . $section}; ?></label>
							<?php foreach ($fields as $name => $field) {
								if( $field->type=='hidden' ){ echo $field; continue;	}
								//Logic to calculate fields width
								$widthCssClasses = "col-sm-7";
								if ( str_contains((string)$field->style, 'medium-field') || str_contains((string)$field->style, 'date') ) {
									$widthCssClasses = "col-sm-5";
								} else if ( str_contains((string)$field->style, 'small-field') ) {
									$widthCssClasses = "col-sm-3";
								} else if ( str_contains((string)$field->style, 'btn_switch') ) {
									$widthCssClasses = "col-sm-6";
								}
								$widthCssClasses .= " col-xs-12";
							?>
						<div class="form-group <?php echo $error[$name] ? "has-error" : ''; ?>">
							<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>">
                                <?php echo ${'entry_' . $name}; ?>
                            </label>
							<?php if($name=='shared' && $map_list){ ?>
							<div class="btn-group toolbar">
                                <button data-toggle="dropdown" type="button" class="btn btn-default dropdown-toggle">
                                    <i class="fa fa-link fa-fw"></i>
                                    <?php echo $text_shared_with?> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                <?php foreach($map_list as $i){?>
                                    <li><a href="<?php echo $i['href'];?>" target="_blank"><?php echo $i['text']?></a></li>
                                <?php } ?>
                                </ul>
							</div>
							<?php } else { ?>
							<div class="input-group afield <?php echo $widthCssClasses.' '.($name == 'description' ? 'ml_ckeditor' : '')?>">
                                <?php if ($field->type == 'checkboxgroup') {
                                    echo '<div class="c_wrapper">'.$field.'</div>';
                                    $field = '';
                                }
                                echo $field;
                                if($section=='attributes' && $field->type=='radio'){ echo '<a class="btn uncheck">[x]</a>';} ?>
							</div>
							<?php } ?>

							<?php if ($error[$name]) { ?>
							    <span class="help-block field_err"><?php echo $error[$name]; ?></span>
							<?php } ?>
						</div>
                        <?php if($name=='resource' && $preview['path']){ ?>
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-xs-12" for="">
                                <?php echo $entry_date_added; ?>
                            </label>
                            <div class="input-group afield <?php echo $widthCssClasses; ?>">
                                <div class="control-label pull-left"> <?php echo $date_added; ?> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-xs-12" for="">
                                <?php echo $entry_date_modified; ?>
                            </label>
                            <div class="input-group afield <?php echo $widthCssClasses; ?>">
                                <div class="control-label pull-left" > <?php echo $date_modified; ?> </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-3 col-xs-12" for="">
                                <?php echo $text_path; ?>
                            </label>
                            <div class="input-group afield <?php echo $widthCssClasses; ?>">
                                <div class="control-label pull-left" >
                                    <a href="<?php echo $preview['href']?>" target="_blank">
                                        <i class="fa fa-download m5"></i> <?php echo $preview['path']; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php }
						 }
                        }  ?>
					</div>
					<div class="panel-footer">
						<div class="center">
							<div class="col-sm-12">
								<a class="btn btn-default" data-dismiss="modal" href="<?php echo $cancel; ?>">
									<i class="fa fa-refresh"></i> <?php echo $form['cancel']->text; ?>
								</a>
                                <button class="btn btn-primary lock-on-click">
                                    <i class="fa fa-save"></i> <?php echo $form['submit']->text; ?>
                                </button>
							</div>
						</div>
					</div>
				</form>
		<?php
		// close parent div for collapses when create
		if(!$download_id){
            if($form0){?>
				</div>
            <?php } ?>
			</div>
		</div>
		<?php }?>
</div>

<script type="application/javascript">
    $(document).ready(function(){
        //manage icons in the accordion
        $('.collapse').on('hidden.bs.collapse', function () {
           $(this).parent().find(".fa-chevron-down").removeClass("fa-chevron-down").addClass("fa-chevron-up");
        }).on('shown.bs.collapse', function () {
           $(this).parent().find(".fa-chevron-up").removeClass("fa-chevron-up").addClass("fa-chevron-down");
        });

        $('#downloadFrm_activate').on('change', function () {
            if ($(this).val() !== 'order_status') {
                $('#downloadFrm_activate_order_status_id').parent('.c_wrapper').fadeOut();

                var parent = $('#downloadFrm_max_downloads, #downloadFrm_expire_days').parents('.form-group');
                if($(this).val() === 'before_order'){
                    parent.fadeOut();
                }else{
                    parent.fadeIn();
                }
            } else {
                $('#downloadFrm_activate_order_status_id').parent('.c_wrapper').fadeIn();
                $('#downloadFrm_max_downloads, #downloadFrm_expire_days').parents('.form-group').fadeIn();
            }
        });
        $('#downloadFrm_activate').change();
        $('#downloadFrm').submit(function () {
            $.ajax(
                    {   url: '<?php echo $form['form_open']->action; ?>',
                        type: 'POST',
                        data: $('#downloadFrm').serializeArray(),
                        dataType: 'json',
                        success: function (data) {
                            if (data.result_text !== '') {
                            <?php if(!$download_id){?>
                                    goTo('<?php echo $file_list_url; ?>');
                            <?php } else { ?>
                                $('#file_modal').scrollTop(0);
                                success_alert(data.result_text, true, "#downloadFrm");
                                //close modal and reload parent page.
                                location.reload();
                            <?php } ?>
                            }
                        }
                    });
            return false;
        });

        $('.uncheck').on('click',function(){
            var radio = $(this).parents('.input-group').find('input[type=radio]');
            radio.removeAttr('checked');
            $(this).html('[x]<input type="hidden" name="'+radio.attr('name')+'" value="">');
        });

        $('#downloadFrm input[type=radio]').on('click',function(){
            $(this).parents('.input-group').find('.uncheck').html('[x]');
        });
    });
</script>