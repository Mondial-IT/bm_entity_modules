<?php

declare(strict_types=1);

namespace Drupal\bm_paragraph;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\node\NodeInterface;
use Drupal\paragraphs\ParagraphInterface;

/**
 * Helper utilities for working with paragraphs and related entities.
 */
final class ParagraphHelper {

  public function __construct(private DateFormatterInterface $dateFormatter) {
  }

  /**
   * Get term field values from paragraphs referenced by a node field.
   *
   * @return array
   *   An array of field values from terms referenced by the paragraphs.
   */
  public function getNodeParagraphsTermsField(NodeInterface $node, string $nodeFieldName, string $paragraphFieldName, string $termFieldName): array {
    $paragraphs = $node->get($nodeFieldName)->referencedEntities();
    $terms = [];
    foreach ($paragraphs as $paragraph) {
      $terms = $paragraph->get($paragraphFieldName)->referencedEntities();
    }
    $termData = [];
    foreach ($terms as $term) {
      $termData[] = $term->get($termFieldName);
    }
    return $termData;
  }

  /**
   * Get term entities referenced by paragraphs on a node field.
   */
  public function getNodeParagraphsTerms(NodeInterface $node, string $nodeFieldName, string $paragraphFieldName): array {
    $referenced = $node->get($nodeFieldName)->referencedEntities();
    $terms = [];
    foreach ($referenced as $paragraph) {
      $terms[] = $paragraph->get($paragraphFieldName)->referencedEntities();
    }
    return $terms;
  }

  /**
   * Get paragraphs referenced by a node field keyed by paragraph ID.
   */
  public function getNodeParagraphs(NodeInterface $node, string $nodeFieldName): array {
    $referencedParagraphs = [];
    foreach ($node->get($nodeFieldName)->referencedEntities() as $paragraph) {
      $referencedParagraphs[$paragraph->id()] = $paragraph;
    }
    return $referencedParagraphs;
  }

  /**
   * Get a field value array from a paragraph.
   */
  public function getParagraphField(ParagraphInterface $paragraph, string $fieldName): array {
    return $paragraph->get($fieldName)->getValue();
  }

  /**
   * Get a paragraph ID.
   */
  public function getParagraphId(ParagraphInterface $paragraph): int {
    return $paragraph->id();
  }

  /**
   * Get the paragraph bundle machine name.
   */
  public function getParagraphType(ParagraphInterface $paragraph): string {
    return $paragraph->getType();
  }

  /**
   * Get term names from a paragraph entity reference field.
   */
  public function getParagraphTermsName(ParagraphInterface $paragraph, string $paragraphField): array {
    $terms = $paragraph->get($paragraphField)->referencedEntities();
    $termNames = [];
    foreach ($terms as $term) {
      $termNames[] = $term->getName();
    }
    return $termNames;
  }

  /**
   * Get a locale-formatted date from a paragraph date field.
   */
  public function getParagraphDatefieldLocale(ParagraphInterface $paragraph, string $paragraphFieldName): string {
    $dateField = $paragraph->get($paragraphFieldName)->getValue();
    if (empty($dateField[0]['value'])) {
      return '';
    }
    $date = strtotime($dateField[0]['value']);
    if (!$date) {
      return '';
    }
    return $this->dateFormatter->format($date, 'custom', 'l, F j, Y');
  }

  /**
   * Get the created timestamp of a paragraph.
   */
  public function getParagraphCreatedTimestamp(ParagraphInterface $paragraph): int {
    return $paragraph->getCreatedTime();
  }

  /**
   * Get a locale-formatted created date of a paragraph.
   */
  public function getParagraphCreatedLocale(ParagraphInterface $paragraph): string {
    return $this->dateFormatter->format($paragraph->getCreatedTime(), 'custom', 'l, F j, Y');
  }

}
