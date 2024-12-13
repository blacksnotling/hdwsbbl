function UpdateSPP(theId) {
  /*		Calcuate the players SPP		*/
  var tot_td = document.getElementById('bblm_td' + theId).value * 3;
  var tot_cas = document.getElementById('bblm_cas' + theId).value * 2;
  var tot_comp = document.getElementById('bblm_comp' + theId).value * 1;
  var tot_ttm = document.getElementById('bblm_ttm' + theId).value * 1;
  var tot_int = document.getElementById('bblm_int' + theId).value * 2;
  var tot_def = document.getElementById('bblm_def' + theId).value * 1;
  var tot_mvp = document.getElementById('bblm_mvp' + theId).value * 4;
  var tot_ptn = document.getElementById('bblm_ptn' + theId).value;
  var tot_spp = parseInt(tot_td) + parseInt(tot_cas) + parseInt(tot_comp) + parseInt(tot_int) + parseInt(tot_mvp) + parseInt(tot_ttm) + parseInt(tot_def) + parseInt(tot_ptn);
  document.getElementById('bblm_spp'+ theId).value = tot_spp;

  /*		Highlight and fill Increase Box		*/
  var inc_col = "#5EFB6E"
  var old_spp = document.getElementById('bblm_oldspp' + theId).value;
  var new_SPP = Number(old_spp) + Number(tot_spp);
  var inc_count = Number(document.getElementById('bblm_incnum' + theId).value);
  if ( inc_count = 0 && new_SPP >= 3 ) {
    document.getElementById('bblm_increased' + theId).style.backgroundColor = inc_col;
  }
  if ( inc_count = 1 && new_SPP >= 4 ) {
    document.getElementById('bblm_increased' + theId).style.backgroundColor = inc_col;
  }
  if ( inc_count = 2 && new_SPP >= 6 ) {
    document.getElementById('bblm_increased' + theId).style.backgroundColor = inc_col;
  }
  if ( inc_count = 3 && new_SPP >= 8 ) {
    document.getElementById('bblm_increased' + theId).style.backgroundColor = inc_col;
  }
  if ( inc_count = 4 && new_SPP >= 10 ) {
    document.getElementById('bblm_increased' + theId).style.backgroundColor = inc_col;
  }
  if ( inc_count = 5 && new_SPP >= 15 ) {
    document.getElementById('bblm_increased' + theId).style.backgroundColor = inc_col;
  }
}
