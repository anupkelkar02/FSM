<?php echo form_open('admin/sites/upload_excel',array('method'=>'post','enctype'=>'multipart/form-data')); ?>
<div class='content'>
    <h4>Upload Excel Sheet For Site</h4>
    <div class="form-group">
        <div class="col-md-10">
        <input type="file" name="site_sheet" class="form-control" /><br/>
        <a href="">Download Sample File</a>
        </div>
        <br/>
        <div><input type="submit" class="btn btn-primary" value="Upload" /></div>
    </div>
    
</div>
<?php echo form_close(); ?>