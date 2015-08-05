

function clickOnCheckids(check, name)
{
	$('[name^="' + name + '\["]').prop('checked', check.checked);
}


function toggleIsPublished(id, name)
{

	var find_name =  name + '[' + id + ']' ;

	var checkbox = $('input[name="' + find_name + '"]');
	if ( checkbox ) {
		checkbox.prop('checked', true);
		$(':hidden[name="toolbar_task_value"]').val('toggle_published');
		$(':hidden[name="toolbar_task_value"]').parents('form').submit();
//		$(this).parents('form').submit();
	}
}

function toggleImage(id, name)
{
	var find_name =  name + '[' + id + ']' ;

	var checkbox = $('input[name="' + find_name + '"]');
	if ( checkbox ) {
		checkbox.attr('checked', true);
		$(':hidden[name="toolbar_task_value"]').val('toggle_image');
		$(this).parents('form').submit();
	}
}
	
	
function sort_change_direction(name, direction)
{
	$(':hidden[name="toolbar_task_value"]').val('sort_order_changed');
	$(':hidden[name="sort_order"]').val(name + ' '+ direction);
	$(':hidden[name="toolbar_task_value"]').parents('form').submit();
	
}

