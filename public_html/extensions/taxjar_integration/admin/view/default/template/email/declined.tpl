<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php echo $title; ?></title>
</head>
<body>
<a href="<?php echo $store_url; ?>" title="<?php echo $store_name; ?>">
    <?php if($logo_uri){ ?>
    <img src="<?php echo $logo_uri; ?>" alt="<?php echo $store_name; ?>" style="border: none;">
    <?php }elseif($logo_html){
				echo $logo_html;
			 } ?>
    <br>
</a>
<?php echo $message;?>
    <br>
<?php echo $edit_url; ?>
</body>
</html>