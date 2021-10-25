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
    Yo
    <!-- /wp:tvconnecteeamu/schedule -->
    ' )
    ->setTemplate( 'page.php' );

    /**
     *  Page: /inscription
     */
  $controller->addPage( new \EC\VirtualPages\Page( "/inscription" ) )
    ->setTitle( 'Inscription' )
    ->setContent( '
    <!-- wp:heading {"level":1} -->
    <h1>Inscription</h1>
    <!-- /wp:heading -->

    <!-- wp:tvconnecteeamu/inscr-student -->
    test
    <!-- /wp:tvconnecteeamu/inscr-student -->
    ' )
    ->setTemplate( 'page.php' );

  /**
   *  Page: /creer-information
   */
  $controller->addPage( new \EC\VirtualPages\Page( "/creer-information" ) )
    ->setTitle( 'Créer une information' )
    ->setContent( '
    <!-- wp:heading {"level":1} -->
    <h1>Créer une information</h1>
    <!-- /wp:heading -->

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
    <!-- wp:heading {"level":1} -->
    <h1>Créer une alerte</h1>
    <!-- /wp:heading -->

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
    <!-- wp:heading {"level":1} -->
    <h1>Mon compte</h1>
    <!-- /wp:heading -->

    <!-- wp:spacer {"height":34} -->
    <div style="height:34px" aria-hidden="true" class="wp-block-spacer"></div>
    <!-- /wp:spacer -->

    <!-- wp:tvconnecteeamu/subscription -->
    test
    <!-- /wp:tvconnecteeamu/subscription -->

    <!-- wp:tvconnecteeamu/choose-account -->
    test
    <!-- /wp:tvconnecteeamu/choose-account -->

    <!-- wp:html -->
    <center>
      <a href="/politique-de-confidentialite"> Mention légales</a>
    </center>
    <!-- /wp:html -->
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
    ' )
    ->setTemplate( 'page.php' );


  /**
   *  Page: /gerer-les-alertes
   */
  $controller->addPage( new \EC\VirtualPages\Page( "/gerer-les-alertes" ) )
    ->setTitle( 'Gérer les informations' )
    ->setContent( '
    <!-- wp:heading {"level":1} -->
    <h1>Gestion des alertes</h1>
    <!-- /wp:heading -->

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
    ->setContent( '' )
    ->setTemplate( 'page-tablet.php' );

  /**
   *  Page: /tablet-view/schedule
   */
  $controller->addPage( new \EC\VirtualPages\Page( "/tablet-view/schedule" ) )
    ->setTitle( 'tablet-view' )
    ->setContent( '
    <!-- wp:tvconnecteeamu/tablet-schedule -->
    test
    <!-- /wp:tvconnecteeamu/tablet-schedule -->
    ' )
    ->setTemplate( 'page-tablet.php' );
} );
