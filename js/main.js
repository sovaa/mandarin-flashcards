$(document).ready(function() 
{ 
  // blur input when not focused
  {   
    $('input[type="text"]').addClass("idleField");

    $('input[type="text"]').focus(function() {
      $(this).removeClass("idleField").addClass("focusField");
    }); 

    $('input[type="text"]').blur(function() {
      $(this).removeClass("focusField").addClass("idleField");
    }); 
  }   
}); 
