
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html >
<head>
    <title>Login</title>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>css/message_note.css" >
    <style>

body {
	margin: 0px;
	padding: 0px;
	width: 100%;
	height: 100%;
	font: 10pt Arial, Verdana, sans-serif;
	
}

DIV.header {
	background: #FFFFF0;
	padding: 2em 0px 2em 0px;
	text-align: left;
	margin: 0px;
}

DIV.header IMG {
	padding-left: 1em;
}

DIV.menubar {
	background: #242526;
	height: 32px;
	box-shadow: 0px 4px 10px #A0A0A0;
	font: bold 18pt Arial, Verdana, sans-serif;
	color: white;
	padding: 4px 0px 4px 12px;
	margin: 0px 0px 1em 0px;
}

DIV.login {
	height: 400px;
	vertical-align: middle;
	align: center;
}

DIV.login TABLE {
	margin-left: auto;
	margin-right: auto;
	margin-top: 4em;
	border: 2px solid #A0A0A0;
	border-radius: 4px;
	background: #FFFFF0;
	padding: 2em;
	box-shadow: 2px 2px 2px #D0D0D0;
}
INPUT.login {
	background: #606060;
	color: white;
	padding: 6px 12px 6px 12px;
	border: 0px solid white;
	border-radius: 4px;
}
INPUT.login:hover {
	background: #202020;
	cursor: hand;
}


    </style>
</head>
    <body>
 <?php echo form_open_multipart('admin/staff/import_staffexcel'); ?>       
 <?php echo message_note(); ?>	
<div class='content'>
		
	
<fieldset class='filter'>
    
<legend>Import Staff</legend>
<table cellpadding='0' cellspacing='0' class='filter'>
   
	<tr>
		<th>Upload File:</th>
                <td><?php echo form_upload('file', set_value('file')); ?></td>&nbsp;
	</tr>
    <tr>
        <td>
            &nbsp;
        </td>
    </tr>
        <tr>
            
            <td colspan='2' align='right'><?php echo form_submit('uploadfile', 'Uploadfile', 'class="login"'); ?></td>&nbsp;
        </tr>
</table>
</fieldset>
		

<!--<div class='container text-center'><?php echo $pagination_links; ?></div>-->
</div>

<?php echo form_close(); ?>
</body>
</html>
