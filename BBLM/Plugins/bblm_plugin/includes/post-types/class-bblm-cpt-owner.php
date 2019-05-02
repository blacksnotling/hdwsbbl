<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Owner (Coaches) CPT functions
 *
 * Defines the functions related to the Owner CPT (archive page logic, display functions etc)
 * For the Meta-Boxes, see the meta-boxes directory
 * For admin functions reloated to the CPT see the includes/admin/post-types directory
 *
 * @class 		BBLM_CPT_OWNER
 * @author 		Blacksnotling
 * @category 	Admin
 * @package 	BBowlLeagueMan/CPT
 * @version   1.0
 */

class BBLM_CPT_Owner {

	/**
	 * Constructor
	 */
	public function __construct() {

    add_action( 'pre_get_posts', array( $this, 'manage_archives' ) );

	}

	/**
	 * stops the CPT archive pages pagenating and changes the display order
	 *
	 * @param wordpress $query
	 * @return none
	 */
	 public function manage_archives( $query ) {

	    if( is_post_type_archive( 'bblm_owner' ) && !is_admin() && $query->is_main_query() ) {
	        $query->set( 'posts_per_page', -1 );
	        $query->set( 'orderby', 'title' );
	        $query->set( 'order', 'asc' );
          $query->set( 'post_status', 'publish' );
	    }

	  }

 /**
  * returns the number of teams this Owner manages
  *
  * @param wordpress $query
  * @return string
  */
  public function get_number_teams() {
    global $post;
    global $wpdb;

    $teams = 0;

    $teamcontsql = 'SELECT COUNT(*) AS CONT FROM '.$wpdb->prefix.'team WHERE ID = '.get_the_ID();
    $teamcont = $wpdb->get_var( $teamcontsql );
    if ( $teamcont > 0) {

      $teams = $teamcont;

    }

    return $teams;

  }

 /**
  * returns the number of games this Owner has played
  *
  * @param wordpress $query
  * @return string
  */
  public function get_number_games() {
    global $post;
    global $wpdb;

    $games = 0;

    $gamesplydsql = 'SELECT COUNT(*) AS PLYD FROM '.$wpdb->prefix.'team T, '.$wpdb->prefix.'match_team M WHERE M.t_id = T.t_id AND T.ID = '.get_the_ID();
    $gamesplyd = $wpdb->get_var( $gamesplydsql );
    if ( $gamesplyd > 0) {

      $games = $gamesplyd;

    }

    return $games;

  }

 /**
  * returns the number of championships this Owner has won
  *
  * @param wordpress $query
  * @return string
  */
  public function get_number_championships() {
    global $post;
    global $wpdb;

    $wins = 0;

    $teamcupssql = 'SELECT COUNT(*) AS WINS FROM '.$wpdb->prefix.'awards_team_comp A, '.$wpdb->prefix.'team T WHERE T.t_id = A.t_id AND A.a_id = 1 AND T.ID = '.get_the_ID();
    $teamcup = $wpdb->get_var( $teamcupssql );
    if ( $teamcup > 0) {

      $wins = $teamcup;

    }

    return $wins;

  }

  /**
   * returns the number of teams this owner has won champsionships with
   *
   * @param wordpress $query
   * @return string
   */
   public function get_number_championship_teams() {
     global $post;
     global $wpdb;

     $teams = 0;

     $teamcupssql = 'SELECT COUNT(*) AS WINS FROM (SELECT COUNT(*) AS AWA FROM '.$wpdb->prefix.'awards_team_comp A, '.$wpdb->prefix.'team T WHERE T.t_id = A.t_id AND A.a_id = 1 AND T.ID = '. get_the_ID() .' GROUP BY A.t_id) X';
     $teamcup = $wpdb->get_var( $teamcupssql );
     if ( $teamcup > 0) {

       $teams = $teamcup;

     }

     return $teams;

   }

 /**
  * Echos the statistics descrption for an individual owner
  *
  * @param wordpress $query
  * @return string
  */
  public function individual_stat_desc() {
     global $post;
     global $wpdb;

     //Initialize Vars
     $output = "<p>";
     $numteams = $this->get_number_teams();
     $numcups = $this->get_number_championships();
     $numgames = $this->get_number_games();

     //Determine the values that won't be used througout the rest of the class
     //Number of players
     $playernumsql = 'SELECT COUNT(*) AS CONT FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T WHERE P.t_id = T.t_id AND T.ID = '.get_the_ID();
     $playernum = $wpdb->get_var( $playernumsql );
     //Number of dead players!
     $playerdeadsql = 'SELECT COUNT(*) AS CONT FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'player_fate F WHERE F.p_id = P.p_id AND ( F.f_id = 1 OR F.f_id = 6 ) AND P.t_id = T.t_id AND T.ID = '.get_the_ID();
     $playerdead = $wpdb->get_var( $playerdeadsql );
     //Number of Matches Won
     $matchwonsql = 'SELECT SUM(T.tc_W) AS OW FROM '.$wpdb->prefix.'team_comp T,'.$wpdb->prefix.'team C WHERE T.t_id = C.t_id AND T.tc_played > 0 AND C.ID = '.get_the_ID(); //splitting the line for length reasons!
     $matchwon = $wpdb->get_var( $matchwonsql );
     //Number of Stars hired
     $options = get_option('bblm_config');
     $bblm_star_team = htmlspecialchars($options['team_star'], ENT_QUOTES);
     $starplayerusesql = 'SELECT COUNT(*) AS CONT FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T WHERE M.p_id = P.p_id AND P.t_id = '.$bblm_star_team.' AND M.t_id = T.t_id AND T.ID = '.get_the_ID();
     $starplayeruniqsql = 'SELECT COUNT( DISTINCT(M.p_id) ) AS CONT FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T WHERE M.p_id = P.p_id AND P.t_id = '.$bblm_star_team.' AND M.t_id = T.t_id AND T.ID = '.get_the_ID();
     $starplayeruse = $wpdb->get_var( $starplayerusesql );
     $starplayeruniq = $wpdb->get_var( $starplayeruniqsql );
     //Number of Kills
     $playerkillsql = 'SELECT COUNT(*) AS CONT FROM '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'player_fate F WHERE F.pf_killer = P.p_id AND ( F.f_id = 1 OR F.f_id = 6 ) AND P.t_id = T.t_id AND T.ID = '.get_the_ID();
     $playerkill = $wpdb->get_var( $playerkillsql );
     //Number of awards
     $asql1 = 'SELECT COUNT(*) AS CONT FROM '.$wpdb->prefix.'awards_player_comp A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'player P WHERE A.p_id = P.p_id AND P.t_id = T.t_id AND T.ID = '.get_the_ID();
     $asql2 = 'SELECT COUNT(*) AS CONT FROM '.$wpdb->prefix.'awards_player_sea A, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'player P WHERE A.p_id = P.p_id AND P.t_id = T.t_id AND T.ID = '.get_the_ID();
     $asql3 = 'SELECT COUNT(*) AS CONT FROM '.$wpdb->prefix.'awards_team_sea A, '.$wpdb->prefix.'team T WHERE A.t_id = T.t_id AND T.ID = '.get_the_ID();
     $asql4 = 'SELECT COUNT(*) AS CONT FROM '.$wpdb->prefix.'awards_team_comp A, '.$wpdb->prefix.'team T WHERE A.t_id = T.t_id AND T.ID = '.get_the_ID();
     $a1 = $wpdb->get_var( $asql1 );
     $a2 = $wpdb->get_var( $asql2 );
     $a3 = $wpdb->get_var( $asql3 );
     $a4 = $wpdb->get_var( $asql4 );
     $numawards = $a1+$a2+$a3+$a4;
     //biggest rival



     $output .=  get_the_title() ." has managed <strong>" . $numteams . "</strong> team(s) in the ". bblm_get_league_name() .".";
     if ( $numteams > 0 ) { //should also check if games played
       //This owner has at least one team

       $output = " In their time in the League they have:</p><ul>";

       if ( $numcups > 0 ) {
         //This owner has won at least one championship

         $output .= "<li>Won <strong>". $numcups ."</strong> Championship(s)";
         $numcupteams = $this->get_number_championship_teams();

         if ( $numcupteams > 1 ) {
           //They have won a championship with more than one team
           $output .= " with <strong>". $numcupteams ."</strong> different team(s)</li>";

         }
         else {

           $output .= "</li>";

         }

       }
       else {

         //They have not won any championships
         $output .= "<li>Won <strong>0</strong> Championships!</li>";

       }

       $output .= " <li>Coached <strong>". $playernum ."</strong> different players, <strong>". $playerdead ."</strong> of who died (<strong>". number_format( ( $playerdead/$playernum )*100 ) ."%</strong>)!</li>
                    <li>Played <strong>". $numgames ."</strong> games, <strong>". number_format( ( $matchwon/$numgames )*100 ) ."%</strong> of which are victories</li>
                    <li>Hired <strong>". $starplayeruniq ."</strong> different Star Player(s)";
       if ( $starplayeruniq > 0 ) {

         $output .= " <strong>".$starplayeruse ."</strong> times";

       }
       $output .= " </li>
                    <li>Killed <strong>". $playerkill ."</strong> opposing players</li>
                    <li>Has won <strong>". $numawards ."</strong> Awards</li>
                    <!-- <li>Has a rilvary with <strong>X</strong> (If I can get the SQL working!)</li> -->
                    </ul>";
     }

     echo __( $output, 'bblm');

   }

  /**
   * Echos the statistics for a coach and returns a PARTIAL html table
   * Returns Played, W/L/D, TDf/a, CASf/a, Comps, Ints and win %
   *
   * @param wordpress $query
   * @return string
   */
   public function individual_stat_tbl_part() {
     global $post;
     global $wpdb;

     $output = "";
     $games = $this->get_number_games();

     //quick check to make sure this owner has played any games
     if ( $games > 0 ) {

       $gamestatsql = 'SELECT SUM(T.tc_played) AS OP, SUM(T.tc_W) AS OW, SUM(T.tc_L) AS OL, SUM(T.tc_D) AS OD, SUM(T.tc_tdfor) AS OTF, SUM(T.tc_tdagst) AS OTA, SUM(T.tc_comp) AS OC, SUM(T.tc_casfor) AS OCASF, SUM(T.tc_casagst) AS OCASA, SUM(T.tc_int) AS OINT FROM ';
       $gamestatsql .= $wpdb->prefix.'team_comp T,'.$wpdb->prefix.'team C WHERE T.t_id = C.t_id AND T.tc_played > 0 AND C.ID = '.get_the_ID(); //splitting the line for length reasons!

       if ( $gs = $wpdb->get_row( $gamestatsql ) ) {

         $output .=  '<td>'. $games .'</td>
                    <td>'. $gs->OW .'</td>
                    <td>'. $gs->OL .'</td>
                    <td>'. $gs->OD .'</td>
                    <td>'. $gs->OTF .'</td>
                    <td>'. $gs->OTA .'</td>
                    <td>'. $gs->OCASF .'</td>
                    <td>'. $gs->OCASA .'</td>
                    <td>'. $gs->OC .'</td>
                    <td>'. $gs->OINT .'</td>
                    <td>'. number_format( ( $gs->OW/$games )*100 ) .'%</td>';
       }

     }
     else {

       //They have not played any games - save time and output zeros
       $output .= '<td>0</td>
       <td colspan="9">Not played any games!</td>
       <td>0</td>';

     }

     echo __( $output, 'bblm');

   }

   /**
    * Echos the statistics for a coach's teams and returns a html table ROW
    * Returns Team Name, Played, W/L/D, TDf/a, CASf/a, Comps, Ints and win %
    *
    * @param wordpress $query
    * @return string
    */
    public function team_stat_tbl_row() {
      global $post;
      global $wpdb;

      $output = "";
      $games = $this->get_number_games();

      //quick check to make sure this owner has played any games
      if ( $games > 0 ) {

        $gamestatsql = 'SELECT J.pid, SUM(T.tc_played) AS OP, SUM(T.tc_W) AS OW, SUM(T.tc_L) AS OL, SUM(T.tc_D) AS OD, SUM(T.tc_tdfor) AS OTF, SUM(T.tc_tdagst) AS OTA, SUM(T.tc_comp) AS OC, SUM(T.tc_casfor) AS OCASF, SUM(T.tc_casagst) AS OCASA, SUM(T.tc_int) AS OINT FROM ';
        $gamestatsql .= $wpdb->prefix.'team_comp T,'.$wpdb->prefix.'team C, '.$wpdb->prefix.'bb2wp J WHERE J.prefix = "t_" AND J.tid = C.t_id AND T.t_id = C.t_id AND T.tc_played > 0 AND C.ID = '.get_the_ID() . ' GROUP BY C.t_id ORDER BY C.t_id ASC'; //splitting the line for length reasons!

        if ( $gs = $wpdb->get_results( $gamestatsql ) ) {

					$c = true;
					foreach ($gs as $g) {

          $output .=  '<tr'. (($c = !$c)?' class="tbl_alt"':'') .'>
                     <td><a href="'. get_post_permalink( $g->pid ). '" title="Learn more about ' .esc_html( get_the_title( $g->pid ) ).' ">' .esc_html( get_the_title( $g->pid ) ).'</a></td>
                     <td>'. $g->OP .'</td>
                     <td>'. $g->OW .'</td>
                     <td>'. $g->OL .'</td>
                     <td>'. $g->OD .'</td>
                     <td>'. $g->OTF .'</td>
                     <td>'. $g->OTA .'</td>
                     <td>'. $g->OCASF .'</td>
                     <td>'. $g->OCASA .'</td>
                     <td>'. $g->OC .'</td>
                     <td>'. $g->OINT .'</td>
                     <td>'. number_format( ( $g->OW/$g->OP )*100 ) .'%</td>
                    </tr>';
          }

        }

      }
      else {

        //They have not played any games - save time and output zeros
        $output .= '<td>0</td>
        <td colspan="9">Not played any games!</td>
        <td>0</td>';

      }

      echo __( $output, 'bblm');

    }

    /**
     * Echos the statistics for a coach's races and returns a html table ROW
     * Returns Team Name, Played, W/L/D, TDf/a, CASf/a, Comps, Ints and win %
     *
     * @param wordpress $query
     * @return string
     */
     public function race_stat_tbl_row() {
       global $post;
       global $wpdb;

       $output = "";
       $games = $this->get_number_games();

       //quick check to make sure this owner has played any games
       if ( $games > 0 ) {

         $gamestatsql = 'SELECT J.pid, SUM(T.tc_played) AS OP, SUM(T.tc_W) AS OW, SUM(T.tc_L) AS OL, SUM(T.tc_D) AS OD, SUM(T.tc_tdfor) AS OTF, SUM(T.tc_tdagst) AS OTA, SUM(T.tc_comp) AS OC, SUM(T.tc_casfor) AS OCASF, SUM(T.tc_casagst) AS OCASA, SUM(T.tc_int) AS OINT FROM ';
         $gamestatsql .= $wpdb->prefix.'team_comp T,'.$wpdb->prefix.'team C, '.$wpdb->prefix.'bb2wp J WHERE J.prefix = "r_" AND J.tid = C.r_id AND T.t_id = C.t_id AND T.tc_played > 0 AND C.ID = '.get_the_ID() . ' GROUP BY C.r_id ORDER BY C.r_id ASC'; //splitting the line for length reasons!

         if ( $gs = $wpdb->get_results( $gamestatsql ) ) {

					 $c = true;
           foreach ($gs as $g) {

           $output .=  '<tr'. (($c = !$c)?' class="tbl_alt"':'') .'>
                      <td><a href="'. get_post_permalink( $g->pid ). '" title="Learn more about ' .esc_html( get_the_title( $g->pid ) ).' ">' .esc_html( get_the_title( $g->pid ) ).'</a></td>
                      <td>'. $g->OP .'</td>
                      <td>'. $g->OW .'</td>
                      <td>'. $g->OL .'</td>
                      <td>'. $g->OD .'</td>
                      <td>'. $g->OTF .'</td>
                      <td>'. $g->OTA .'</td>
                      <td>'. $g->OCASF .'</td>
                      <td>'. $g->OCASA .'</td>
                      <td>'. $g->OC .'</td>
                      <td>'. $g->OINT .'</td>
                      <td>'. number_format( ( $g->OW/$g->OP )*100 ) .'%</td>
                     </tr>';
           }

         }

       }
       else {

         //They have not played any games - save time and output zeros
         $output .= '<td>0</td>
         <td colspan="9">Not played any games!</td>
         <td>0</td>';

       }

       echo __( $output, 'bblm');

     }

     /**
      * Echos the statistics for a coach's Season and returns a html table ROW
      * Returns Team Name, Played, W/L/D, TDf/a, CASf/a, Comps, Ints and win %
      *
      * @param wordpress $query
      * @return string
      */
      public function season_stat_tbl_row() {
        global $post;
        global $wpdb;

        $output = "";
        $games = $this->get_number_games();

        //quick check to make sure this owner has played any games
        if ( $games > 0 ) {

          $gamestatsql = 'SELECT J.pid, Z.pid AS TID, SUM(T.tc_played) AS OP, SUM(T.tc_W) AS OW, SUM(T.tc_L) AS OL, SUM(T.tc_D) AS OD, SUM(T.tc_tdfor) AS OTF, SUM(T.tc_tdagst) AS OTA, SUM(T.tc_comp) AS OC, SUM(T.tc_casfor) AS OCASF, SUM(T.tc_casagst) AS OCASA, SUM(T.tc_int) AS OINT FROM ';
          $gamestatsql .= $wpdb->prefix.'team_comp T, '.$wpdb->prefix.'team C, '.$wpdb->prefix.'comp X, '.$wpdb->prefix.'bb2wp J, '.$wpdb->prefix.'bb2wp Z WHERE Z.prefix = "t_" AND Z.tid = T.t_id AND J.prefix = "sea_" AND J.tid = X.sea_id AND T.t_id = C.t_id AND X.c_id = T.c_id AND T.tc_played > 0 AND C.ID = '.get_the_ID() . ' GROUP BY X.sea_id ORDER BY X.sea_id ASC'; //splitting the line for length reasons!

          if ( $gs = $wpdb->get_results( $gamestatsql ) ) {

						$c = true;
            foreach ($gs as $g) {

            $output .=  '<tr'. (($c = !$c)?' class="tbl_alt"':'') .'>
                       <td><a href="'. get_post_permalink( $g->pid ). '" title="Learn more about ' .esc_html( get_the_title( $g->pid ) ).' ">' .esc_html( get_the_title( $g->pid ) ).'</a> (as ' .esc_html( get_the_title( $g->TID ) ).')</td>
                       <td>'. $g->OP .'</td>
                       <td>'. $g->OW .'</td>
                       <td>'. $g->OL .'</td>
                       <td>'. $g->OD .'</td>
                       <td>'. $g->OTF .'</td>
                       <td>'. $g->OTA .'</td>
                       <td>'. $g->OCASF .'</td>
                       <td>'. $g->OCASA .'</td>
                       <td>'. $g->OC .'</td>
                       <td>'. $g->OINT .'</td>
                       <td>'. number_format( ( $g->OW/$g->OP )*100 ) .'%</td>
                      </tr>';
            }

          }

        }
        else {

          //They have not played any games - save time and output zeros
          $output .= '<td>0</td>
          <td colspan="9">Not played any games!</td>
          <td>0</td>';

        }

        echo __( $output, 'bblm');

      }

      /**
       * Echos the statistics for a coach's Cup runs and returns a html table ROW
       * Returns Team Name, Played, W/L/D, TDf/a, CASf/a, Comps, Ints and win %
       *
       * @param wordpress $query
       * @return string
       */
       public function cup_stat_tbl_row() {
         global $post;
         global $wpdb;

         $output = "";
         $games = $this->get_number_games();

         //quick check to make sure this owner has played any games
         if ( $games > 0 ) {

           $gamestatsql = 'SELECT J.pid, SUM(T.tc_played) AS OP, SUM(T.tc_W) AS OW, SUM(T.tc_L) AS OL, SUM(T.tc_D) AS OD, SUM(T.tc_tdfor) AS OTF, SUM(T.tc_tdagst) AS OTA, SUM(T.tc_comp) AS OC, SUM(T.tc_casfor) AS OCASF, SUM(T.tc_casagst) AS OCASA, SUM(T.tc_int) AS OINT FROM ';
           $gamestatsql .= $wpdb->prefix.'team_comp T, '.$wpdb->prefix.'team C, '.$wpdb->prefix.'comp X, '.$wpdb->prefix.'bb2wp J WHERE J.prefix = "series_" AND J.tid = X.series_id AND T.t_id = C.t_id AND X.c_id = T.c_id AND T.tc_played > 0 AND C.ID = '.get_the_ID() . ' GROUP BY X.series_id ORDER BY X.series_id ASC'; //splitting the line for length reasons!

           if ( $gs = $wpdb->get_results( $gamestatsql ) ) {

						 $c = true;
             foreach ($gs as $g) {

             $output .=  '<tr'. (($c = !$c)?' class="tbl_alt"':'') .'>
                        <td><a href="'. get_post_permalink( $g->pid ). '" title="Learn more about ' .esc_html( get_the_title( $g->pid ) ).' ">' .esc_html( get_the_title( $g->pid ) ).'</a></td>
                        <td>'. $g->OP .'</td>
                        <td>'. $g->OW .'</td>
                        <td>'. $g->OL .'</td>
                        <td>'. $g->OD .'</td>
                        <td>'. $g->OTF .'</td>
                        <td>'. $g->OTA .'</td>
                        <td>'. $g->OCASF .'</td>
                        <td>'. $g->OCASA .'</td>
                        <td>'. $g->OC .'</td>
                        <td>'. $g->OINT .'</td>
                        <td>'. number_format( ( $g->OW/$g->OP )*100 ) .'%</td>
                       </tr>';
             }

           }

         }
         else {

           //They have not played any games - save time and output zeros
           $output .= '<td>0</td>
           <td colspan="9">Not played any games!</td>
           <td>0</td>';

         }

         echo __( $output, 'bblm');

       }

      /**
       * Echos the statistics for a coach's races and returns a html table ROW
       * Returns Team Name, Played, W/L/D, TDf/a, CASf/a, Comps, Ints and win %
       *
       * @param wordpress $query
       * @return string
       */
       public function comp_stat_tbl_row() {
         global $post;
         global $wpdb;

         $output = "";
         $games = $this->get_number_games();

         //quick check to make sure this owner has played any games
         if ( $games > 0 ) {

           $gamestatsql = 'SELECT J.pid, Z.pid AS TID, SUM(T.tc_played) AS OP, SUM(T.tc_W) AS OW, SUM(T.tc_L) AS OL, SUM(T.tc_D) AS OD, SUM(T.tc_tdfor) AS OTF, SUM(T.tc_tdagst) AS OTA, SUM(T.tc_comp) AS OC, SUM(T.tc_casfor) AS OCASF, SUM(T.tc_casagst) AS OCASA, SUM(T.tc_int) AS OINT FROM ';
           $gamestatsql .= $wpdb->prefix.'team_comp T,'.$wpdb->prefix.'team C,'.$wpdb->prefix.'bb2wp J, '.$wpdb->prefix.'bb2wp Z WHERE Z.prefix = "t_" AND Z.tid = T.t_id AND J.prefix = "c_" AND J.tid = T.c_id AND T.t_id = C.t_id AND T.tc_played > 0 AND C.ID = '.get_the_ID() . ' GROUP BY T.c_id ORDER BY T.c_id ASC'; //splitting the line for length reasons!

           if ( $gs = $wpdb->get_results( $gamestatsql ) ) {

						 $c = true;
             foreach ($gs as $g) {

             $output .=  '<tr'. (($c = !$c)?' class="tbl_alt"':'') .'>
                        <td><a href="'. get_post_permalink( $g->pid ). '" title="Learn more about ' .esc_html( get_the_title( $g->pid ) ).' ">' .esc_html( get_the_title( $g->pid ) ).'</a> (as ' .esc_html( get_the_title( $g->TID ) ).')</td>
                        <td>'. $g->OP .'</td>
                        <td>'. $g->OW .'</td>
                        <td>'. $g->OL .'</td>
                        <td>'. $g->OD .'</td>
                        <td>'. $g->OTF .'</td>
                        <td>'. $g->OTA .'</td>
                        <td>'. $g->OCASF .'</td>
                        <td>'. $g->OCASA .'</td>
                        <td>'. $g->OC .'</td>
                        <td>'. $g->OINT .'</td>
                        <td>'. number_format( ( $g->OW/$g->OP )*100 ) .'%</td>
                       </tr>';
             }

           }

         }
         else {

           //They have not played any games - save time and output zeros
           $output .= '<td>0</td>
           <td colspan="9">Not played any games!</td>
           <td>0</td>';

         }

         echo __( $output, 'bblm');

       }

			 /**
			 	* Echos the star players that a team has hired
       	* Returns formatted unordered list
        *
        * @param wordpress $query
        * @return string
        */
        public function star_stat_tbl_row() {
          global $post;
          global $wpdb;

          $output = "";
          $games = $this->get_number_games();
					//Number of Stars hired
					$options = get_option('bblm_config');
					$bblm_star_team = htmlspecialchars($options['team_star'], ENT_QUOTES);

          //quick check to make sure this owner has played any games
          if ( $games > 0 ) {

						$starstatsql = 'SELECT J.pid, COUNT(*) AS VISITS FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'bb2wp J, '.$wpdb->prefix.'player X, '.$wpdb->prefix.'team T WHERE M.t_id = T.t_id AND J.prefix = "p_" AND J.tid = X.p_id AND M.p_id = X.p_id AND T.ID = '.get_the_ID() . ' AND X.t_id = '. $bblm_star_team;
						$starstatsql .= ' GROUP BY M.p_id ORDER BY VISITS DESC'; //splitting the line for length reasons!

            if ( $gs = $wpdb->get_results( $starstatsql ) ) {

							$output .= '<ul>';

              foreach ($gs as $g) {

								$output .= '<li><a href="' .get_post_permalink( $g->pid ). '" title="Learn more about ' .esc_html( get_the_title( $g->pid ) ) .' ">'. esc_html( get_the_title( $g->pid ) ) .'</a> (x'. $g->VISITS .')</li>';

							}

							$output .= '</ul>';

            }
						else {

							$output .= '<p>Not hired any Star Players!</p>';

						}

          }
          else {

            //They have not played any games - save time and output zeros
						$output .= '<p>Not hired any Star Players!</p>';

          }

          echo __( $output, 'bblm');

        }

				/**
	       * Echos the top players that an owner has coached
	       * Returns Player, Position, team, and SPP
	       *
	       * @param wordpress $query
	       * @return string
	       */
	       public function player_stat_tbl_row() {
	         global $post;
	         global $wpdb;

	         $output = "";
	         $games = $this->get_number_games();
					 $options = get_option('bblm_config');
					 $bblm_star_team = htmlspecialchars($options['team_star'], ENT_QUOTES);
					 $stat_limit = htmlspecialchars($options['display_stats'], ENT_QUOTES);

	         //quick check to make sure this owner has played any games
	         if ( $games > 0 ) {

						 $playerstatsql = 'SELECT J.pid AS PID, T.WPID AS TID, R.pos_name, SUM(M.mp_spp) AS VALUE FROM '.$wpdb->prefix.'match_player M, '.$wpdb->prefix.'position R, '.$wpdb->prefix.'player P, '.$wpdb->prefix.'team T, '.$wpdb->prefix.'bb2wp J WHERE ' ;
						 $playerstatsql .= 'J.prefix = "p_" AND J.tid = P.p_id AND T.t_id = M.t_id AND P.p_id = M.p_id AND P.pos_id = R.pos_id AND M.mp_counts = 1 AND M.mp_spp > 0 AND ';//splitting the line for length reasons!
						 $playerstatsql .= 'P.t_id != '.$bblm_star_team.' AND T.ID = '.get_the_ID() . ' GROUP BY P.p_id ORDER BY VALUE DESC LIMIT '.$stat_limit;

	           if ( $gs = $wpdb->get_results( $playerstatsql ) ) {

							 $num = 1;
							 $c = true;
	             foreach ($gs as $g) {

	             $output .=  '<tr class"'. (($c = !$c)?' tbl_alt':'') .''. (($num > 10)?' tb_hide':'') .'">
							 						<td>'.$num.'</td>
	                        <td><a href="'. get_post_permalink( $g->PID ). '" title="Learn more about ' .esc_html( get_the_title( $g->PID ) ).' ">' .esc_html( get_the_title( $g->PID ) ).'</a></td>
	                        <td>'. esc_html( $g->pos_name ) .'</td>
	                        <td><a href="'. get_post_permalink( $g->TID ). '" title="Learn more about ' .esc_html( get_the_title( $g->TID ) ).' ">' .esc_html( get_the_title( $g->TID ) ).'</a></td>
	                        <td>'. $g->VALUE .'</td>
	                       </tr>';
												 $num++;
	             }

	           }

	         }
	         else {

	           //They have not played any games - save time and output zeros
	           $output .= '<td>0</td>
	           <td colspan="9">Not played any games!</td>
	           <td>0</td>';

	         }

	         echo __( $output, 'bblm');

	       }


}

new BBLM_CPT_Owner();
