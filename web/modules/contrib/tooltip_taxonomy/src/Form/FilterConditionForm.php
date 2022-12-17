<?php

namespace Drupal\tooltip_taxonomy\Form;

use Drupal\Component\Plugin\Factory\FactoryInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\field\FieldStorageConfigInterface;
use Drupal\tooltip_taxonomy\Services\FieldTypeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * FilterConditionForm class.
 *
 * @author Mingsong Hu
 */

/**
 * Form handler for the FilterCondition entity add and edit forms.
 */
class FilterConditionForm extends EntityForm {
  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity type bundle info.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * Drupal Condition plugin for path.
   *
   * @var \Drupal\Core\Condition\ConditionPluginBase
   */
  protected $pathCondition;

  /**
   * Drupal condition plugin for content type.
   *
   * @var \Drupal\Core\Condition\ConditionPluginBase
   */
  protected $contentTypeCondition;

  /**
   * The field type plugin manager.
   *
   * @var \Drupal\Core\Field\FieldTypePluginManagerInterface
   */
  protected $fieldTypePluginManager;

  /**
   * The field type manager.
   *
   * @var \Drupal\tooltip_taxonomy\Services\FieldTypeManager
   */
  protected $fieldTypeManager;

  /**
   * Constructs an FilterConditionForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entityTypeManager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   Entity type bundle info service instance.
   * @param \Drupal\Component\Plugin\Factory\FactoryInterface $plugin_factory
   *   Plugin factory service instance.
   * @param \Drupal\Core\Field\FieldTypePluginManagerInterface $field_type_plugin_manager
   *   Field type plugin manager service instance.
   * @param \Drupal\tooltip_taxonomy\Services\FieldTypeManager $field_type_manager
   *   Field type manager service instance.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, EntityTypeBundleInfoInterface $entity_type_bundle_info, FactoryInterface $plugin_factory, FieldTypePluginManagerInterface $field_type_plugin_manager, FieldTypeManager $field_type_manager) {
    $this->entityTypeManager = $entityTypeManager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->pathCondition = $plugin_factory->createInstance('request_path');
    $this->contentTypeCondition = $plugin_factory->createInstance('node_type');
    $this->fieldTypePluginManager = $field_type_plugin_manager;
    $this->fieldTypeManager = $field_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
        $container->get('entity_type.manager'),
        $container->get('entity_type.bundle.info'),
        $container->get('plugin.manager.condition'),
        $container->get('plugin.manager.field.field_type'),
        $container->get('tooltip_taxonomy.field_type_manager')
        );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $condition = $this->entity;

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 255,
      '#default_value' => $condition->label(),
      '#description' => $this->t("Name for the filter condition."),
      '#required' => TRUE,
    ];

    $form['cid'] = [
      '#type' => 'machine_name',
      '#default_value' => $condition->id(),
      '#machine_name' => [
        'exists' => [$this, 'exist'],
        'source' => ['name'],
      ],
      '#disabled' => !$condition->isNew(),
    ];

    // Vocabularies of taxonomy terms.
    $voca_options = [];
    foreach ($this->entityTypeBundleInfo->getBundleInfo('taxonomy_term') as $voc_name => $voc_info) {
      $voca_options[$voc_name] = $voc_info['label'];
    }

    $form['vids'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Vocabularies for tooltips.'),
      '#options' => $voca_options,
      '#default_value' => $condition->get('vids'),
      '#required' => TRUE,
      '#description' => $this->t('Selected vocabularies will be used as the content text of tooltips in following pages or content types for a certain field. If none is checked, there will not be tooltip created.'),
    ];
    
    $formats = filter_formats();
    $format_options = [];
    foreach ($formats as $format) {
      $format_options[$format->id()] = $format->label();
    }
    
    $form['formats'] = [
        '#type' => 'checkboxes',
        '#title' => $this->t('Text formats.'),
        '#options' => $format_options,
        '#default_value' => $condition->get('formats'),
        '#required' => TRUE,
        '#description' => $this->t('Select which formats does this condition applys to.'),
    ];

    // Get all view modes.
    $view_mode_ids = \Drupal::entityQuery('entity_view_mode')
      ->condition('targetEntityType', 'node')
      ->execute();

    $view_modes = \Drupal::entityTypeManager()->getStorage('entity_view_mode')->loadMultiple($view_mode_ids);
    $view_mode_options = [];
    foreach ($view_modes as $mode) {
      $id = $mode->id();
      $view_mode_options[substr($id, 5)] = $mode->label();
    }

    // View mode settings.
    $form['view'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('View modes'),
      '#options' => $view_mode_options,
      '#default_value' => $condition->get('view'),
      '#description' => $this->t('If none is selected, this condition will be applied to all view modes.'),
    ];
    // Load the default value for path.
    $this->pathCondition->setConfiguration($condition->get('path'));
    // Build the form element for path settings.
    $form['path'] = $this->pathCondition->buildConfigurationForm([], $form_state);
    // Content types applied to this condition.
    $this->contentTypeCondition->setConfiguration($condition->get('contentTypes'));
    // Build the form element for content type settings.
    $form['node'] = $this->contentTypeCondition->buildConfigurationForm([], $form_state);
    // Remove the negate checkbox for content type setting.
    unset($form['node']['negate']);
    // All existing text fields that can be used for a node.
    $fields = $this->getExistingFieldStorageOptions();

    if (!empty($fields)) {
      // Fields applied to this condition.
      $form['field'] = [
        '#type' => 'select',
        '#title' => $this->t('Field'),
        '#options' => $fields,
        '#default_value' => $condition->get('field'),
        '#empty_option' => $this->t('- Select an existing field -'),
        '#multiple' => TRUE,
        '#description' => $this->t('If none is selected, this condition will be applied to all fields.'),
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $condition = $this->entity;
    $this->pathCondition->submitConfigurationForm($form['path'], $form_state);
    $this->contentTypeCondition->submitConfigurationForm($form['node'], $form_state);
    // The content type negate should be always false.
    $this->contentTypeCondition->setConfig('negate', 0);

    foreach ($form_state->getValues() as $key => $value) {
      if ($key === 'vids') {
        $vids = [];
        foreach ($value as $vid) {
          if (!empty($vid)) {
            $vids[] = $vid;
          }
        }
        // Vocabularies settings.
        $condition->set('vids', $vids);
      }
      else {
        $condition->set($key, $value);
      }
    }

    // Path setting.
    $condition->set('path', $this->pathCondition->getConfiguration());
    // Content type settings.
    $condition->set('contentTypes', $this->contentTypeCondition->getConfiguration());
    $status = $condition->save();

    if ($status) {
      $this->messenger()->addMessage($this->t('Saved the %label Example.', [
        '%label' => $condition->label(),
      ]));
    }
    else {
      $this->messenger()->addMessage($this->t('The %label Example was not saved.', [
        '%label' => $condition->label(),
      ]), MessengerInterface::TYPE_ERROR);
    }

    // We need to invalidate all node pages by node_view tag
    // to apply the changes make here.
    \Drupal::service('cache_tags.invalidator')->invalidateTags(['node_view']);

    $form_state->setRedirect('entity.tooltip_taxonomy.config');
  }

  /**
   * Helper function to check whether an Example configuration entity exists.
   */
  public function exist($id) {
    $entity = $this->entityTypeManager->getStorage('filter_condition')->getQuery()
      ->condition('cid', $id)
      ->execute();
    return (bool) $entity;
  }

  /**
   * Returns an array of existing field storages that can be added to node.
   *
   * @return array
   *   An array of existing field storages keyed by name.
   */
  protected function getExistingFieldStorageOptions() {
    $options = [];
    // Load the field_storages and build the list of options.
    $field_types = $this->fieldTypePluginManager->getDefinitions();
    $fields = $this->entityTypeManager->getStorage('field_storage_config')->loadByProperties(
        [
          'deleted' => FALSE,
         // 'type' => 'entity_reference',.
          'status' => 1,
        ]
        );
    foreach ($fields as $field_name => $field_storage) {
      // Do not show:
      // - non-configurable field storages,
      // - locked field storages,
      // - non-text field,.
      $field_type = $field_storage->getType();
      $field_key_name = str_replace('.', '-', $field_name);
      if ($field_storage instanceof FieldStorageConfigInterface
          && $this->fieldTypeManager->isContentField($field_storage->getName())
          && $this->fieldTypeManager->isTextField($field_type)
          && !$field_storage->isLocked()) {
        $options[$field_key_name] = $this->t('@type: @field', [
          '@type' => $field_types[$field_type]['label'],
          '@field' => $field_name,
        ]);
      }
    }
    asort($options);

    return $options;
  }

}
