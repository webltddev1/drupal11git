<?php

declare(strict_types=1);

namespace Drupal\Tests\drupal_cms_person\Functional;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\FunctionalTests\Core\Recipe\RecipeTestTrait;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\drupal_cms_content_type_base\ContentModelTestTrait;

/**
 * @group drupal_cms_person
 */
class ComponentValidationTest extends BrowserTestBase {

  use ContentModelTestTrait;
  use RecipeTestTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $dir = realpath(__DIR__ . '/../../..');
    // The recipe should apply cleanly.
    $this->applyRecipe($dir);
    // Apply it again to prove that it is idempotent.
    $this->applyRecipe($dir);

    $this->ensureFileExists('5a635060-8540-4be7-bad9-8a51414731ad');
  }

  public function testContentModel(): void {
    /** @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository */
    $display_repository = $this->container->get(EntityDisplayRepositoryInterface::class);

    $form_display = $display_repository->getFormDisplay('node', 'person');
    $this->assertFalse($form_display->isNew());
    $this->assertNull($form_display->getComponent('url_redirects'));
    $this->assertFieldsInOrder($form_display, [
      'title',
      'field_description',
      'field_featured_image',
      'field_person__role_job_title',
      'field_person__phone_number',
      'field_person__email',
      'field_content',
      'field_tags',
    ]);

    $default_display = $display_repository->getViewDisplay('node', 'person');
    $this->assertNull($default_display->getComponent('links'));
    $this->assertFieldsInOrder($default_display, [
      'field_featured_image',
      'field_person__role_job_title',
      'field_person__phone_number',
      'field_person__email',
      'content_moderation_control',
      'field_content',
      'field_tags',
    ]);
    $this->assertSharedFieldsInSameOrder($form_display, $default_display);

    $card_display = $display_repository->getViewDisplay('node', 'person', 'card');
    $this->assertNull($card_display->getComponent('links'));
    $this->assertFieldsInOrder($card_display, [
      'field_featured_image',
      'field_person__role_job_title',
      'field_description',
    ]);
    $featured_image = $card_display->getComponent('field_featured_image');
    $this->assertSame('entity_reference_entity_view', $featured_image['type']);

    $teaser_display = $display_repository->getViewDisplay('node', 'person', 'teaser');
    $this->assertNull($teaser_display->getComponent('links'));
    $this->assertFieldsInOrder($teaser_display, [
      'field_featured_image',
      'field_description',
    ]);

    $this->assertContentModel([
      'person' => [
        'title' => [
          'type' => 'string',
          'cardinality' => 1,
          'required' => TRUE,
          'translatable' => TRUE,
          'label' => 'Name',
          'input type' => 'text',
          'help text' => '',
        ],
        'field_description' => [
          'type' => 'string_long',
          'cardinality' => 1,
          'required' => TRUE,
          'translatable' => TRUE,
          'label' => 'Description',
          'input type' => 'textarea',
          'help text' => 'Describe the page content. This appears as the description in search engine results.',
        ],
        'field_person__role_job_title' => [
          'type' => 'string_long',
          'cardinality' => 1,
          'required' => FALSE,
          'translatable' => TRUE,
          'label' => 'Role or job title',
          'input type' => 'textarea',
          'help text' => 'Include a role or job title.',
        ],
        'field_person__email' => [
          'type' => 'email',
          'cardinality' => 5,
          'required' => FALSE,
          'translatable' => FALSE,
          'label' => 'Email',
          'input type' => 'email',
          'help text' => 'Include up to 5 email addresses.',
        ],
        'field_person__phone_number' => [
          'type' => 'telephone',
          'cardinality' => 5,
          'required' => FALSE,
          'translatable' => FALSE,
          'label' => 'Phone number',
          'input type' => 'tel',
          'help text' => 'Include up to 5 phone numbers.',
        ],
        'field_featured_image' => [
          'type' => 'entity_reference',
          'cardinality' => 1,
          'required' => FALSE,
          'translatable' => FALSE,
          'label' => 'Featured image',
          'input type' => 'media library',
          'help text' => 'Include an image. This appears as the image in search engine results.',
        ],
        'field_content' => [
          'type' => 'text_long',
          'cardinality' => 1,
          'required' => FALSE,
          'translatable' => TRUE,
          'label' => 'Biography',
          'input type' => 'wysiwyg',
          'help text' => '',
        ],
        'field_tags' => [
          'type' => 'entity_reference',
          'cardinality' => FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED,
          'required' => FALSE,
          'translatable' => FALSE,
          'label' => 'Tags',
          'input type' => 'tagify',
          'help text' => 'Include tags for relevant topics.',
        ],
      ],
    ]);
  }

  public function testPathAliasPatternPrecedence(): void {
    $dir = realpath(__DIR__ . '/../../../../drupal_cms_seo_basic');
    $this->applyRecipe($dir);

    // Confirm that person profiles have the expected path aliases.
    $node = $this->drupalCreateNode([
      'type' => 'person',
      'title' => 'Test Person profile',
    ]);
    $this->assertStringEndsWith("/people/test-person-profile", $node->toUrl()->toString());
  }

}
