<?php

namespace Drupal\sch_attendance\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\sch_attendance\Entity\editableInterface;

/**
 * Class editableController.
 *
 *  Returns responses for Editable routes.
 */
class editableController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Editable  revision.
   *
   * @param int $editable_revision
   *   The Editable  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($editable_revision) {
    $editable = $this->entityManager()->getStorage('editable')->loadRevision($editable_revision);
    $view_builder = $this->entityManager()->getViewBuilder('editable');

    return $view_builder->view($editable);
  }

  /**
   * Page title callback for a Editable  revision.
   *
   * @param int $editable_revision
   *   The Editable  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($editable_revision) {
    $editable = $this->entityManager()->getStorage('editable')->loadRevision($editable_revision);
    return $this->t('Revision of %title from %date', ['%title' => $editable->label(), '%date' => format_date($editable->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Editable .
   *
   * @param \Drupal\sch_attendance\Entity\editableInterface $editable
   *   A Editable  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(editableInterface $editable) {
    $account = $this->currentUser();
    $langcode = $editable->language()->getId();
    $langname = $editable->language()->getName();
    $languages = $editable->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $editable_storage = $this->entityManager()->getStorage('editable');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $editable->label()]) : $this->t('Revisions for %title', ['%title' => $editable->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all editable revisions") || $account->hasPermission('administer editable entities')));
    $delete_permission = (($account->hasPermission("delete all editable revisions") || $account->hasPermission('administer editable entities')));

    $rows = [];

    $vids = $editable_storage->revisionIds($editable);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\sch_attendance\editableInterface $revision */
      $revision = $editable_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $editable->getRevisionId()) {
          $link = $this->l($date, new Url('entity.editable.revision', ['editable' => $editable->id(), 'editable_revision' => $vid]));
        }
        else {
          $link = $editable->link($date);
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
              Url::fromRoute('entity.editable.translation_revert', ['editable' => $editable->id(), 'editable_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.editable.revision_revert', ['editable' => $editable->id(), 'editable_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.editable.revision_delete', ['editable' => $editable->id(), 'editable_revision' => $vid]),
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

    $build['editable_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
