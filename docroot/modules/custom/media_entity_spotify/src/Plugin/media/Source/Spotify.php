<?php

namespace Drupal\media_entity_spotify\Plugin\media\Source;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\media\MediaInterface;
use Drupal\media\MediaSourceBase;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides media type plugin for Spotify.
 *
 * @MediaSource(
 *   id = "spotify",
 *   label = @Translation("Spotify"),
 *   description = @Translation("Provides business logic and metadata for Spotify."),
 *   allowed_field_types = {"link", "string", "string_long"},
 *   default_thumbnail_filename = "spotify.png",
 * )
 */
class Spotify extends MediaSourceBase {

  /**
   * @var array
   */
  protected $spotify;

  /**
   * Guzzle client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * Constructs a new class instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager service.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   Entity field manager service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory service.
   * @param \Drupal\Core\Field\FieldTypePluginManagerInterface $field_type_manager
   *   The field type plugin manager service.
   * @param \GuzzleHttp\ClientInterface $httpClient
   *   Guzzle client.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager, ConfigFactoryInterface $config_factory, FieldTypePluginManagerInterface $field_type_manager, ClientInterface $httpClient) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager, $entity_field_manager, $field_type_manager, $config_factory);
    $this->httpClient = $httpClient;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager'),
      $container->get('config.factory'),
      $container->get('plugin.manager.field.field_type'),
      $container->get('http_client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getMetadataAttributes() {
    return [
      'uri' => $this->t('The URI'),
      'html' => $this->t('HTML embed code'),
      'thumbnail_uri' => t('URI of the thumbnail'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getMetadata(MediaInterface $media, $attribute_name) {
    if (($url = $this->getMediaUrl($media)) && ($data = $this->oEmbed($url))) {
      switch ($attribute_name) {
        case 'html':
          return $data['html'];

        case 'thumbnail_uri':
          if (isset($data['thumbnail_url'])) {
            $destination = $this->configFactory->get('media_entity_spotify.settings')->get('thumbnail_destination');
            $local_uri = $destination . '/' . pathinfo($data['thumbnail_url'], PATHINFO_BASENAME);

            // Save the file if it does not exist.
            if (!file_exists($local_uri)) {
              file_prepare_directory($destination, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS);

              $image = file_get_contents($data['thumbnail_url']);
              file_unmanaged_save_data($image, $local_uri, FILE_EXISTS_REPLACE);

              return $local_uri;
            }
          }
          break;

        case 'uri':
          // Test for track.
          preg_match('/^https?:\/\/(?:open|play)\.spotify\.com\/track\/([\w\d]+)$/i', $url, $matches);
          if (count($matches)) {
            return 'spotify:track:' . $matches[1];
          }

          // Test for playlist.
          preg_match('/^https?:\/\/(?:open|play)\.spotify\.com\/user\/([\w\d]+)\/playlist\/([\w\d]+)$/i', $url, $matches);
          if (count($matches)) {
            return 'spotify:user:' . $matches[1] . ':playlist:' . $matches[2];
          }

          // Test for album.
          preg_match('/^https?:\/\/(?:open|play)\.spotify\.com\/album\/([\w\d]+)$/i', $url, $matches);
          if (count($matches)) {
            return 'spotify:album:' . $matches[1];
          }
          break;

        case 'type':
          return preg_match('/^spotify\:(track)/', $this->getMetadata($media, 'uri')) ? 'track' : 'playlist';
      }
    }

    // Fallback to the parent's default name if everything else failed.
    return parent::getMetadata($media, $attribute_name);
  }

  /**
   * Returns the url from the source_url_field.
   *
   * @param \Drupal\media\MediaInterface $media
   *   The media entity.
   *
   * @return string|bool
   *   The track if from the source_url_field if found. False otherwise.
   */
  protected function getMediaUrl(MediaInterface $media) {
    $source_field_name = $media->bundle->entity->getSource()->getSourceFieldDefinition($media->bundle->entity)->getName();
    if ($media->hasField($source_field_name)) {
      /** @var \Drupal\Core\Field\FieldItemInterface $item */
      $item = $media->get($source_field_name)->first();
      $property_name = $item->mainPropertyName();
      return $item->{$property_name};
    }
    return FALSE;
  }

  /**
   * Returns oembed data for a Spotify url.
   *
   * @param string $url
   *   The Spotify Url.
   *
   * @return array
   *   An array of oembed data.
   */
  protected function oEmbed($url) {
    $this->spotify = &drupal_static(__FUNCTION__);

    if (!isset($this->spotify)) {
      $url = 'https://embed.spotify.com/oembed/?url=' . $url;
      $response = $this->httpClient->get($url);
      $this->spotify = json_decode((string) $response->getBody(), TRUE);
    }

    return $this->spotify;
  }

}
