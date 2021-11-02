<?php

/**
 * Virtual Pages
 */

use EC\VirtualPages\PageControllerInterface;
use EC\VirtualPages\PageController;

use EC\VirtualPages\TemplateLoaderInterface;
use EC\VirtualPages\TemplateLoader;

use EC\VirtualPages\PageInterface;

$controller = new PageController ( new TemplateLoader );

add_action( 'init', array( $controller, 'init' ) );

add_filter( 'do_parse_request', array( $controller, 'dispatch' ), PHP_INT_MAX, 2 );

add_action( 'loop_end', function( \WP_Query $query ) {
    if ( isset( $query->virtual_page ) && ! empty( $query->virtual_page ) ) {
        $query->virtual_page = NULL;
    }
} );

add_filter( 'the_permalink', function( $plink ) {
    global $post, $wp_query;
    if (
        $wp_query->is_page && isset( $wp_query->virtual_page )
        && $wp_query->virtual_page instanceof Page
        && isset( $post->is_virtual ) && $post->is_virtual
    ) {
        $plink = home_url( $wp_query->virtual_page->getUrl() );
    }
    return $plink;
} );

add_action( 'ec_virtual_pages', function( $controller ) {
  /**
   *  Page: /
   */
  $controller->addPage( new \EC\VirtualPages\Page( "/" ) )
    ->setTitle( 'Accueil' )
    ->setContent( '
    <!-- wp:tvconnecteeamu/schedule -->
    test
    <!-- /wp:tvconnecteeamu/schedule -->
    ' )
    ->setTemplate( 'page.php' );

  $controller->addPage( new \EC\VirtualPages\Page( "/tv-mode" ) )
    ->setTitle( 'Accueil' )
    ->setContent( '
    <!-- wp:tvconnecteeamu/tv-mode -->
    test
    <!-- /wp:tvconnecteeamu/tv-mode -->
    ' )
    ->setTemplate( 'page-tv.php' );

    /**
     *  Page: /creer-utilisateur
     */
  $controller->addPage( new \EC\VirtualPages\Page( "/creer-utilisateur" ) )
    ->setTitle( 'Créer un utilisateur' )
    ->setContent( '
    <!-- wp:tvconnecteeamu/creation-user -->
    test
    <!-- /wp:tvconnecteeamu/creation-user -->
    ' )
    ->setTemplate( 'page.php' );

    /**
     *  Page: /liste-utilisateur
     */
  $controller->addPage( new \EC\VirtualPages\Page( "/liste-utilisateur" ) )
    ->setTitle( 'Liste des utilisateurs' )
    ->setContent( '
    <!-- wp:tvconnecteeamu/management-user -->
    test
    <!-- /wp:tvconnecteeamu/management-user -->
    ' )
    ->setTemplate( 'page.php' );

  /**
   *  Page: /creer-information
   */
  $controller->addPage( new \EC\VirtualPages\Page( "/creer-information" ) )
    ->setTitle( 'Créer une information' )
    ->setContent( '
    <!-- wp:tvconnecteeamu/add-information -->
    test
    <!-- /wp:tvconnecteeamu/add-information -->
    ' )
    ->setTemplate( 'page.php' );

    /**
     *  Page: /creer-une-alerte
     */
  $controller->addPage( new \EC\VirtualPages\Page( "/creer-une-alerte" ) )
    ->setTitle( 'Créer une alerte' )
    ->setContent( '
    <!-- wp:tvconnecteeamu/add-alert -->
    test
    <!-- /wp:tvconnecteeamu/add-alert -->

    <!-- wp:html -->
    <a href="/gerer-les-alertes/">Gérer les alertes</a>
    <!-- /wp:html -->
    ' )
    ->setTemplate( 'page.php' );

    /**
     *  Page: /emploi-du-temps
     */
  $controller->addPage( new \EC\VirtualPages\Page( "/gerer-les-informations" ) )
    ->setTitle( 'Gérer les informations' )
    ->setContent( '
    <!-- wp:tvconnecteeamu/manage-information -->
    test
    <!-- /wp:tvconnecteeamu/manage-information -->
    ' )
    ->setTemplate( 'page.php' );

    /**
     *  Page: /gerer-les-informations/modification-information
     */
  $controller->addPage( new \EC\VirtualPages\Page( "/gerer-les-informations/modification-information" ) )
    ->setTitle( 'Modifier une information' )
    ->setContent( '
    <!-- wp:heading {"level":1} -->
    <h1>Modifier une information</h1>
    <!-- /wp:heading -->

    <!-- wp:tvconnecteeamu/modify-information -->
    test
    <!-- /wp:tvconnecteeamu/modify-information -->
    ' )
    ->setTemplate( 'page.php' );

    /**
     *  Page: /emploi-du-temps
     */
  $controller->addPage( new \EC\VirtualPages\Page( "/emploi-du-temps" ) )
    ->setTitle( 'Emploi du temps' )
    ->setContent( '
    <!-- wp:tvconnecteeamu/schedules -->
    test
    <!-- /wp:tvconnecteeamu/schedules -->
    ' )
    ->setTemplate( 'page.php' );

    /**
     *  Page: /mon-compte
     */
  $controller->addPage( new \EC\VirtualPages\Page( "/mon-compte" ) )
    ->setTitle( 'Mon compte' )
    ->setContent( '
    <!-- wp:tvconnecteeamu/choose-account -->
    test
    <!-- /wp:tvconnecteeamu/choose-account -->
    ' )
    ->setTemplate( 'page.php' );

    /**
     *  Page: /gestion-codes-ade/modification-code-ade
     */
  $controller->addPage( new \EC\VirtualPages\Page( "/gestion-codes-ade/modification-code-ade" ) )
    ->setTitle( 'Modifier un code ADE' )
    ->setContent( '
    <!-- wp:heading {"level":1} -->
    <h1>Modifier code ADE</h1>
    <!-- /wp:heading -->

    <!-- wp:tvconnecteeamu/modify-code -->
    test
    <!-- /wp:tvconnecteeamu/modify-code -->
    ' )
    ->setTemplate( 'page.php' );
    /**
     *  Page: /gestion-codes-ade
     */
  $controller->addPage( new \EC\VirtualPages\Page( "/gestion-codes-ade" ) )
    ->setTitle( 'Gestion des codes ADE' )
    ->setContent( '
    <!-- wp:tvconnecteeamu/manage-codes -->
      <!-- wp:tvconnecteeamu/add-code -->
      test
      <!-- /wp:tvconnecteeamu/add-code -->
    <!-- /wp:tvconnecteeamu/manage-codes -->
    ' )
    ->setTemplate( 'page.php' );


  /**
   *  Page: /gerer-les-alertes
   */
  $controller->addPage( new \EC\VirtualPages\Page( "/gerer-les-alertes" ) )
    ->setTitle( 'Gérer les alertes' )
    ->setContent( '
    <!-- wp:tvconnecteeamu/manage-alert -->
    test
    <!-- /wp:tvconnecteeamu/manage-alert -->
    ' )
    ->setTemplate( 'page.php' );


  /**
   *  Page: /tablet-view
   */
  $controller->addPage( new \EC\VirtualPages\Page( "/tablet-view" ) )
    ->setTitle( 'tablet-view' )
    ->setContent( '
    <!-- wp:tvconnecteeamu/tablet-select-year -->
    test
    <!-- /wp:tvconnecteeamu/tablet-select-year -->
    ' )
    ->setTemplate( 'page-tablet.php' );

  /**
   *  Page: /tablet-view/schedule
   */
  $controller->addPage( new \EC\VirtualPages\Page( "/tablet-view/emploi-du-temps" ) )
    ->setTitle( 'tablet-view' )
    ->setContent( '
    <!-- wp:tvconnecteeamu/tablet-schedule -->
    test
    <!-- /wp:tvconnecteeamu/tablet-schedule -->
    ' )
    ->setTemplate( 'page-tablet.php' );
} );
