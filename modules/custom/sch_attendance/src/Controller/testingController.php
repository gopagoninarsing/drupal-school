<?php

namespace Drupal\sch_attendance\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\sch_attendance\Entity\testingInterface;

/**
 * Class testingController.
 *
 *  Returns responses for Testing routes.
 */
class testingController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Testing  revision.
   *
   * @param int $testing_revision
   *   The Testing  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($testing_revision) {
    $testing = $this->entityManager()->getStorage('testing')->loadRevision($testing_revision);
    $view_builder = $this->entityManager()->getViewBuilder('testing');

    return $view_builder->view($testing);
  }

  /**
   * Page title callback for a Testing  revision.
   *
   * @param int $testing_revision
   *   The Testing  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($testing_revision) {
    $testing = $this->entityManager()->getStorage('testing')->loadRevision($testing_revision);
    return $this->t('Revision of %title from %date', ['%title' => $testing->label(), '%date' => format_date($testing->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Testing .
   *
   * @param \Drupal\sch_attendance\Entity\testingInterface $testing
   *   A Testing  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(testingInterface $testing) {
    $account = $this->currentUser();
    $langcode = $testing->language()->getId();
    $langname = $testing->language()->getName();
    $languages = $testing->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $testing_storage = $this->entityManager()->getStorage('testing');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $testing->label()]) : $this->t('Revisions for %title', ['%title' => $testing->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all testing revisions") || $account->hasPermission('administer testing entities')));
    $delete_permission = (($account->hasPermission("delete all testing revisions") || $account->hasPermission('administer testing entities')));

    $rows = [];

    $vids = $testing_storage->revisionIds($testing);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\sch_attendance\testingInterface $revision */
      $revision = $testing_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $testing->getRevisionId()) {
          $link = $this->l($date, new Url('entity.testing.revision', ['testing' => $testing->id(), 'testing_revision' => $vid]));
        }
        else {
          $link = $testing->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.testing.translation_revert', ['testing' => $testing->id(), 'testing_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.testing.revision_revert', ['testing' => $testing->id(), 'testing_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.testing.revision_delete', ['testing' => $testing->id(), 'testing_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['testing_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
