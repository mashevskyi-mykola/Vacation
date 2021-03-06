<?php

namespace Drupal\Tests\entity\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Symfony\Component\Routing\Route;

/**
 * Tests some integration of the revision overview:
 *
 * - Are the routes added properly.
 * - Are the local tasks added properly.
 *
 * @group entity
 */
class RevisionOverviewIntegrationTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['node', 'entity_module_test', 'entity', 'user', 'system'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installSchema('system', 'router');

    \Drupal::service('router.builder')->rebuild();
  }

  public function testIntegration() {
    /** @var \Drupal\Core\Menu\LocalTaskManagerInterface $local_tasks_manager */
    $local_tasks_manager = \Drupal::service('plugin.manager.menu.local_task');

    $tasks = $local_tasks_manager->getDefinitions();
    $this->assertArrayHasKey('entity.revisions_overview:entity_test_enhanced', $tasks);
    $this->assertArrayNotHasKey('entity.revisions_overview:node', $tasks, 'Node should have been excluded because it provides their own');

    $this->assertEquals('entity.entity_test_enhanced.version_history', $tasks['entity.revisions_overview:entity_test_enhanced']['route_name']);
    $this->assertEquals('entity.entity_test_enhanced.canonical', $tasks['entity.revisions_overview:entity_test_enhanced']['base_route']);

    /** @var \Drupal\Core\Routing\RouteProviderInterface $route_provider */
    $route_provider = \Drupal::service('router.route_provider');

    $route = $route_provider->getRouteByName('entity.entity_test_enhanced.version_history');
    $this->assertInstanceOf(Route::class, $route);
    $this->assertEquals('\Drupal\entity\Controller\RevisionOverviewController::revisionOverviewController', $route->getDefault('_controller'));
  }

}
