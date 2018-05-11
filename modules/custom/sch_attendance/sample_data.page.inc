<?php

/**
 * @file
 * Contains sample_data.page.inc.
 *
 * Page callback for Sample_data entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Sample_data templates.
 *
 * Default template: sample_data.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_sample_data(array &$variables) {
  // Fetch sample_data Entity Object.
  $sample_data = $variables['elements']['#sample_data'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
