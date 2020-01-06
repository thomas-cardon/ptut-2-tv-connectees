<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clés secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C’est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define( 'DB_NAME', 'ecran_connecte' );

/** Utilisateur de la base de données MySQL. */
define( 'DB_USER', 'root' );

/** Mot de passe de la base de données MySQL. */
define( 'DB_PASSWORD', '' );

/** Adresse de l’hébergement MySQL. */
define( 'DB_HOST', 'localhost' );

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Type de collation de la base de données.
  * N’y touchez que si vous savez ce que vous faites.
  */
define('DB_COLLATE', '');

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '1N0yk1apqR#wXS%#YFC*3,RDwl,`e5O>5fWIE}bz4yZ.A!+u8]=N?~nwG+X&&mi<' );
define( 'SECURE_AUTH_KEY',  'Nh;Q&?onBw<w8h!!?[xtsMf[({:?C2>AAXK#/l@W_;Jed(CF2gHL7R/CG7ppXOvr' );
define( 'LOGGED_IN_KEY',    '(Hiw#owg ?E[+t}2w.KHf/so9/eyr|/@.P<GU{+_&uh*^zweJ!>I,wtfvKWbWQA`' );
define( 'NONCE_KEY',        'XFW<m=+6RVc&1CK|n!HnLkw~&{GO`x_HY`rgF3]z2`Egv)zu1lH+(.%6PR0buC1,' );
define( 'AUTH_SALT',        '-jZW/:]N;i#+ZA~+2/g^o15jBl@_6@ls)jYy+Z{E{/+<9AgM]}a7Rz,PFXg#FnEl' );
define( 'SECURE_AUTH_SALT', 'Ze#*2_ZXn6;XHkGIFz^>zt&v *P4$zr #9!`4nrl@Paxs2Cim4JH3wLq& |H(xqt' );
define( 'LOGGED_IN_SALT',   '^#!o5;,TIuG?r +<$tbh%Nf2HsLoakTCorb[bBA%]]&X^b+S%ZM!vDjCU#A?%WM,' );
define( 'NONCE_SALT',       'A=a `5|tn7hl)aSqNduv/ 5bS}xm] DV. IDoo(hm|GV0mU}2bJ56m-wD,Zi*b_d' );
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortemment recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* C’est tout, ne touchez pas à ce qui suit ! Bonne publication. */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');
