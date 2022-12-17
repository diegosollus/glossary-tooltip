<?php

namespace Drupal\tooltip_taxonomy\Services;

/**
 * The class for the field type manager.
 *
 * @author Mingsong Hu
 */
class FieldTypeManager {

  protected const TEXT_FIELD_TYPES = [
    'text',
    'text_long',
    'text_with_summary',
    'string_long',
  ];

  /**
   * Check if the field is text field,.
   *
   * @param string $field_type
   *   Type of field.
   */
  public function isTextField($field_type) {
    return in_array($field_type, static::TEXT_FIELD_TYPES);
  }

  /**
   * Check if a field is content field.
   *
   * @param string $field_name
   *   The name of the field.
   */
  public function isContentField($field_name) {
    return $field_name === 'body' || (strpos($field_name, 'field_') === 0);
  }

}
