function BBLM_UpdateGate() {
  /*		Calcuate the players SPP		*/
  var tot_a = document.getElementById('tAatt').value;
  var tot_b = document.getElementById('tBatt').value;
  var tot_att = Number(tot_a) + Number(tot_b);
  document.getElementById('gate').value = tot_att;
}

jQuery(document).ready(function() {
    jQuery('.custom_date').datepicker({
    dateFormat : 'yy-mm-dd'
    });
});
