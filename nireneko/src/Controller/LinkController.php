<?php

namespace Drupal\nireneko\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LinkController
 *
 * Codigo de ejemplo para el articulo:
 *
 * https://nireneko.com/articulo/generar-links-drupal-8
 *
 * @package Drupal\nireneko\Controller
 */
class LinkController extends ControllerBase {

  /**
   * LinkController constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entityTypeManager
   */
  public function __construct(EntityTypeManager $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Callback de la ruta nireneko.neko
   */
  public function view() {

    //Creamos un objeto Url a un sitio externo
    $url_basica = Url::fromUri('http://nireneko.com');

    //Creamos el link con el obketo Url a un sitio externo
    $link_basico = Link::fromTextAndUrl('Link basico', $url_basica);

    /*****************************************************************/

    //Cargamos el nodo 1
    /** @var \Drupal\node\NodeInterface $node */
    $node = current($this->entityTypeManager->getStorage('node')->loadMultiple());

    //Crear un link a una entidad, el metodo ->toUrl() devuelve un objeto Url
    $link_entidad = Link::fromTextAndUrl('Link entidad', $node->toUrl());

    /*****************************************************************/

    //Creamos un objeto Url a la ruta nireneko.parameter la cual requiere un parametro llamado "node".
    $url_con_parametro = Url::fromRoute('nireneko.parameter', ['node' => $node->id()]);

    //Creamos un link a la url con parametro creada anteriormente.
    $link_url_parametro = Link::fromTextAndUrl('Link url parametro', $url_con_parametro);

    /*****************************************************************/

    //Creamos un link directamente utilizando una ruta.
    $link_con_parametro = Link::createFromRoute('Link con parametro','nireneko.parameter', ['node' => $node->id()]);

    /*****************************************************************/

    //Link a una ruta de la entidad nodo
    $link_ruta_entidad = Link::createFromRoute('Link ruta entidad', 'entity.node.canonical', ['node' => $node->id()]);

    /*****************************************************************/

    //Creamos un objeto Url al que añadiremos opciones
    $url_opciones = Url::fromUri('http://nireneko.com');

    //Creamos un array con las opciones que vamos a añadir, en este caso unas clases
    $opciones = [
      'attributes' => [
        'class' => [
          'nireneko',
          'nireneko-link'
        ],
      ],
    ];

    //Añadimos las opciones al objeto Url
    $url_opciones->setOptions($opciones);

    //Creamos un link con el objeto Url que contiene las opciones
    $link_opciones = Link::fromTextAndUrl('Link con opciones', $url_opciones);

    $links = [
      'link_basico' => $link_basico,
      'link_entidad' => $link_entidad,
      'link_url_parametro' => $link_url_parametro,
      'link_con_parametro' => $link_con_parametro,
      'link_ruta_entidad' => $link_ruta_entidad,
      'link_opciones' => $link_opciones,
    ];

    return [
      '#theme' => 'nireneko_links',
      '#links' => $links
    ];

  }

  /**
   * Callback de la ruta nireneko.parameter
   *
   * @param \Drupal\node\NodeInterface $node
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function parameter(NodeInterface $node) {
    return new Response($node->label());
  }
}
