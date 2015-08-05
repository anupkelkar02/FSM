 
  function CheckStock()
 {//alert(transID);
 
 // var surl = 'http://fsm.plug-point.com/index.php/admin/uniforms/checkstock';
	 var surl = 'http://localhost/plug-point/index.php/admin/uniforms/checkstock';
	 var type_id=$('#type_id').val();
	 var s_id=$('#size_id').val();
	 var data = 'type_id='+type_id+"&size_id="+s_id;
	 $.ajax({
	   url:surl,
	   type:'POST',
	   data:data,
	   success: function(result){
		
		$('#stock').val(result);
		$('#availstock').html(result);
		
		 
	   }
	 });
	
 }
 