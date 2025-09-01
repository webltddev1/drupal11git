<?php

declare(strict_types=1);

namespace Drupal\Tests\drupal_cms_accessibility_tools\Functional;

use Drupal\FunctionalTests\Core\Recipe\RecipeTestTrait;
use Drupal\Tests\BrowserTestBase;

/**
 * @group drupal_cms_accessibility_tools
 */
class ComponentValidationTest extends BrowserTestBase {

  use RecipeTestTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  public function test(): void {
    $dir = realpath(__DIR__ . '/../../..');

    // The recipe should apply cleanly.
    $this->applyRecipe($dir);
    // Apply it again to prove that it is idempotent.
    $this->applyRecipe($dir);
  }

}
