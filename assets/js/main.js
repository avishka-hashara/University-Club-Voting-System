// assets/js/main.js - minimal JS
document.addEventListener('DOMContentLoaded', function(){
  // Confirmation for delete-like actions
  document.querySelectorAll('.confirm-delete').forEach(function(btn){
    btn.addEventListener('click', function(e){
      if (!confirm('Are you sure?')) e.preventDefault();
    });
  });
});
