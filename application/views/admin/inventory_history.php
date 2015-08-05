<?php echo form_open(); ?>

<?php

echo toolbar_open('Uniforms Stock History');
?>
<div class='content'>  
		
	<div class='container text-center'><?php echo $pagination_links; ?></div>		

		
<table cellpadding='0' cellspacing='0' class="DataRows">
<tr>
	<th width="5%">#</th>
    <th width='15%'>Action</th>
  	<th width='35%'>Type</th>
    <th width='15%'>Name</th>
    <th width='15%'>Quantity</th>
    <th width='15%'>Time</th>
    
</tr>
<?php 
$i=($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
foreach ( $rows as $row) 
		{
			$i++;
			?>
		<tr>	
        <td><?php echo $i; ?></td>
        <td><?php echo $row->type; ?></td>
        <td><?php echo $row->title; ?></td>
        <td><?php echo $row->first_name; ?></td>
        <td><?php echo $row->qty; ?></td>
        <td><?php echo $row->issuedate; ?></td>
		</tr>
  <?php } ?>
</table>
<div class='container text-center'><?php echo $pagination_links; ?></div>
</div>

<?php echo form_close(); ?>
