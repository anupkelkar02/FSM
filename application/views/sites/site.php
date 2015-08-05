<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html >
<head>
    <title><?php echo $title; ?></title>
    <meta http-equiv="Content-Language" content="<?php echo isset($lang) ? $lang : 'en';?>" />
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset; ?>" />
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="pragma-directive" content="no-cache">
	<meta http-equiv="cache-directive" content="no-cache">
	<meta http-equiv="expires" content="0">		

<?php foreach($meta as $name=>$content): ?>
    <meta name="<?php echo $name; ?>" content="<?php echo $content; ?>" />
<?php endforeach; ?>
<?php if(count($rdf) > 0): ?>
<!--
    <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:ddc="http://purl.org/net/ddc#">
        <rdf:Description rdf:about="<?php echo base_url(); ?>">
<?php foreach($rdf as $name=>$content): ?>
            <<?php echo $name; ?>><?php echo $content; ?></<?php echo $name; ?>>
<?php endforeach;?>
        </rdf:Description>
    </rdf:RDF>
-->
<?php endif;?>

<?php foreach($javascript as $javascript_file): ?>
    <script language="JavaScript" type="text/javascript" src="<?php echo $javascript_file;?>"></script>
<?php endforeach; ?>
<?php foreach ($css as $css_file): ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $css_file; ?>" >
<?php endforeach; ?>
<?php if (isset($fav_icon)) :?>
    <link rel='shortcut icon' type="image/x-icon" href='<?php echo base_url($fav_icon); ?>' >
<?php endif; ?>

<?php  echo $inline_scripting ?>
        <script>
            var SITE = '<?php echo base_url(); ?>';
        </script>
</head>
<body>
<div class='container'>
	<div class="navbar navbar-static-top navbar-fixed-top navbar-inverse">
		<?php echo anchor(base_url(), $this->config->item('title'), "class='navbar-brand pull-left'");?>		
		<?php echo $menu_bar; ?>						
	</div>
</div>
<br>
<div class='container'>
	<?php echo message_note(); ?>
	<?php echo $output; ?>
</div>

</body>
</html>
