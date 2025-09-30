<div><?php echo $text_nexus_information;?></div>
<div id="html">
<?php foreach($nexus as $address) { ?>
    <?php echo $address['region'].','.$address['country']."<br>";?>
<?php } ?>
</div>
<div>
    <button id="sync" class="btn btn-primary lock-on-click"><?php echo $button_sync;?></button>&nbsp;<?php echo $text_or;?>&nbsp;<a class="btn btn-primary" href="<?php echo $manage_nexus_url;?>" target="_blank"><?php echo $text_manage;?></a>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#sync').on('click', function(e){
            e.preventDefault();
            $.ajax({
              url: '<?php echo $sync_url; ?>',
              type: 'post',
              dataType: 'json',
              success: function(result) {
                 console.log(result.error);
                 if (result.error===false) {
                     resetLockBtn();
                     window.location.href = '<?php echo $redirect; ?>';
                 }
              }
            });
        });
    });
</script>
