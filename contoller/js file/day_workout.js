

$(document).ready(function(){
     var last_touched = '';
     var value = ''
     var updateOutput = function(e)
     {
     	var list = e.length ? e : $(e.target),
         output = list.data('output');
        if (window.JSON) {
           value = (window.JSON.stringify(list.nestable('serialize')));
            $.ajax({
            type : 'POST',
            url  : site_url + "admin/programs/addsortvalue",
            dataType : "JSON",
            data : { 'whichnest' : last_touched,'value' : value},
            success : function(data){
                console.log(value);
            }
         });
            
        }
        else {
        output.val('JSON browser support required for this demo.');
      }
 
        
    }; 
  
     $('#nestable').nestable({
        group: 1
    })
    .on('change', function(){ last_touched = 'nestable'; })
    .on('change', updateOutput)
    	


$(document).on('click', '.delete-program', function(){
	if( confirm('Are you sure, you want to delete this program?') ){
		$.ajax({
			type: 'POST',
			url:   $(this).data('url'),
			success: function(data){
				table.api().ajax.reload();
			}
		});
	}
});

$(document).on('click', '.btn-save', function(){
	$error = 0;
	$('#title-error, #workout_id-error').html('');
	if( $('#title').val() == '' ) {
		$('#title-error').html('Workout Name required');
		$error++;
	} else if( $('#workout_id').val() == '' ) {
		$('#workout_id-error').html('Exercise required');
		$error++;
	} else if( $('#steps').val() == '' ) {
		alert('Steps required');
		$error++;
	} else if( $('#reps').val() == '' && $('#time').val() == '' ) {
		alert('Reps or Seconds required');
		$error++;
	}
	if( $error == 0 ) {
		$.ajax({
			type: 'POST',
			url:   $(this).data('url'),
			data: $('#frm').serialize(),
			success: function(data){
				$('#tbl-result').html(data.html);
				$('#workout_id, #steps, #reps').val('');
				$('#nestable').nestable();
			}
		});
	}
});

$(document).on('click', '.btn-delete-exercise', function(){
   if( confirm('Are you sure?') ) {
       $id = $(this).data('id');
       $url = $(this).data('url');
       $('#pe_' + $id).html('');
       $.ajax({
			type: 'POST',
			url:   $url,
			success: function(data){
			   $("li[data-id='" + $id +"']").remove();
			}
		});
   } 
});



});

