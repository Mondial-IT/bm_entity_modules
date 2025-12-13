## Mondial-IT Paragraph Helpers

### Overview
Extension module for Drupal Paragraphs providing shared helper utilities for Blue Marloc projects (Drupal 11 / PHP 8.3).

### Features
- Service `bm_paragraph.helper` with helper methods for paragraph traversal, term references, and date formatting.
- Designed to centralize common paragraph logic used across custom modules.

### Installation
1. Ensure Paragraphs module is enabled.
2. Enable this module: `ddev exec drush en bm_paragraph -y`
3. Clear caches: `ddev exec drush cr`

### Usage
```php
$helper = \Drupal::service('bm_paragraph.helper');
$paragraphs = $helper->getNodeParagraphs($node, 'field_sections');
$names = $helper->getParagraphTermsName($paragraphs[123], 'field_tags');
```

### Helpers
- `getNodeParagraphsTermsField($node, $nodeField, $paragraphField, $termField)`
- `getNodeParagraphsTerms($node, $nodeField, $paragraphField)`
- `getNodeParagraphs($node, $nodeField)`
- `getParagraphField($paragraph, $field)`
- `getParagraphId($paragraph)`
- `getParagraphType($paragraph)`
- `getParagraphTermsName($paragraph, $field)`
- `getParagraphDatefieldLocale($paragraph, $field)`
- `getParagraphCreatedTimestamp($paragraph)`
- `getParagraphCreatedLocale($paragraph)`

### Testing
- Cache rebuild: `ddev exec drush cr`
- Coding standards: `ddev exec phpcs web/modules/custom/bm_paragraph --standard=Drupal,DrupalPractice`

### Dependencies
- Drupal Paragraphs module.

### License
- Internal use by Mondial-IT / Blue Marloc (no public license declared).

### Maintainers
- Blue Marloc / Mondial-IT engineering team.
