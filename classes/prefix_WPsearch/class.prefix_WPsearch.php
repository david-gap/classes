<?php
/**
 * WP ADDON FOR ACF
 * https://github.com/david-gap/classes
 *
 * @author      David Voglgsang
 * @version     2.1
 *
*/

/*=======================================================
Table of Contents:
---------------------------------------------------------
1.0 INIT & VARS
  1.1 CONFIGURATION
  1.2 ON LOAD RUN
  1.3 BACKEND ARRAY
2.0 FUNCTIONS
  2.1 GET SETTINGS FROM CONFIGURATION FILE
  2.2 TAXONOMIES SEARCH
  2.3 ACF SEARCH
3.0 OUTPUT
=======================================================*/


class prefix_WPsearch {

  /*==================================================================================
    1.0 INIT & VARS
  ==================================================================================*/

    /* 1.1 CONFIGURATION
    /------------------------*/
    /**
      * default vars
      * @param private array $WPsearch_acf: list of advanced custom fields you want to search content in
      * @param private array $WPsearch_tax: list of taxonomies you want to search content in
    */
    private $WPsearch_acf = array();
    private $WPsearch_tax = array();


    /* 1.2 ON LOAD RUN
    /------------------------*/
    public function __construct() {
      // update default vars with configuration file
      SELF::updateVars();
      // add acf fields to search query
      if(!empty(is_array($this->WPsearch_acf))):
        add_filter( 'posts_search', array( $this, 'WPsearch_CustomSearch'), 500, 2 );
      endif;
      // add taxonomies search query
      if(!empty(is_array($this->WPsearch_tax))):
        add_action('parse_query', array( $this, 'WPsearch_Taxonomies'), 1 );
      endif;
    }

    /* 1.3 BACKEND ARRAY
    /------------------------*/
    static $classtitle = 'ACF searchable';
    static $classkey = 'WPsearch';
    static $backend = array(
      "acf" => array(
        "label" => "Field slugs",
        "type" => "array_addable"
      ),
      "taxonomies" => array(
        "label" => "Taxonomies",
        "type" => "array_addable"
      )
    );



  /*==================================================================================
    2.0 FUNCTIONS
  ==================================================================================*/


  /* 2.1 GET SETTINGS FROM CONFIGURATION FILE
  /------------------------*/
  private function updateVars(){
    // get configuration
    global $configuration;
    // if configuration file exists && class-settings
    if($configuration && array_key_exists('WPsearch', $configuration)):
      // class configuration
      $myConfig = $configuration['WPsearch'];
      // update vars
      $this->WPsearch_acf = array_key_exists('acf', $myConfig) ? $myConfig['acf'] : $this->WPsearch_acf;
      $this->WPsearch_tax = array_key_exists('taxonomies', $myConfig) ? $myConfig['taxonomies'] : $this->WPsearch_tax;
    endif;
  }


  /* 2.2 TAXONOMIES SEARCH
  /------------------------*/
  public function WPsearch_Taxonomies(&$query)
  {
    if ($query->is_search)
    foreach ($this->WPsearch_tax as $key => $value) {
      $query->set('taxonomy', $value);
    }
  }

  /* 2.3 ACF SEARCH
  /------------------------*/
  public function WPsearch_CustomSearch( $where, $wp_query ) {
    global $wpdb;
    // fallback if target is missing
    if ( empty( $where )):
      return $where;
    endif;
    // get search expression
    $terms = $wp_query->query_vars[ 's' ];
    // explode search expression to get search terms
    $exploded = explode( ' ', $terms );
    if( $exploded === FALSE || count( $exploded ) == 0 )
        $exploded = array( 0 => $terms );
    // reset search in order to rebuilt it as we whish
    $where = '';
    // get searcheable acf fields
    $acf_fields = $this->WPsearch_acf;
    // for each tag
    foreach( $exploded as $tag ) :
      $where .= "
        AND (
          (wp_posts.post_title LIKE '%$tag%')
          OR (wp_posts.post_content LIKE '%$tag%')
          OR EXISTS (
            SELECT * FROM wp_postmeta
              WHERE post_id = wp_posts.ID
                AND (";
      // for each acf
      foreach ($acf_fields as $searcheable_acf) :
        if ($searcheable_acf == $acf_fields[0]):
          $where .= " (meta_key LIKE '%" . $searcheable_acf . "%' AND meta_value LIKE '%$tag%') ";
        else :
          $where .= " OR (meta_key LIKE '%" . $searcheable_acf . "%' AND meta_value LIKE '%$tag%') ";
        endif;
      endforeach;
      // define where to search
      $where .= ")
        )
        OR EXISTS (
          SELECT * FROM wp_comments
          WHERE comment_post_ID = wp_posts.ID
            AND comment_content LIKE '%$tag%'
        )
        OR EXISTS (
          SELECT * FROM wp_terms
          INNER JOIN wp_term_taxonomy
            ON wp_term_taxonomy.term_id = wp_terms.term_id
          INNER JOIN wp_term_relationships
            ON wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id
          WHERE (
      		taxonomy = 'post_tag'
        		OR taxonomy = 'category'
        		OR taxonomy = 'mediathek_sl'
            OR taxonomy = 'fallbeispiele_s_wk_sm'
            OR taxonomy = 'fallbeispiele_anlagentyp'
            OR taxonomy = 'fallbeispiele_sanierungsart'
            OR taxonomy = 'fallbeispiele_massnahmentyp'
            OR taxonomy = 'fallbeispiele_fischregion'
      		)
          	AND object_id = wp_posts.ID
          	AND wp_terms.name LIKE '%$tag%'
        )
    )";
  endforeach;
  // return filter
  return $where;
  }



  /*==================================================================================
    3.0 OUTPUT
  ==================================================================================*/
}
