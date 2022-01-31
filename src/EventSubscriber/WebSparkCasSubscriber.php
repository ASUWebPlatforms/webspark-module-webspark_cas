<?php

namespace Drupal\webspark_cas\EventSubscriber;

use Drupal\cas\Subscriber\CasSubscriber;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Drupal\Core\Site\Settings;

/**
 * WebSpark CAS event subscriber.
 */
class WebSparkCasSubscriber extends CasSubscriber {

  /**
   * {@inheritdoc}
   */
  public function handle(GetResponseEvent $event) {
    if ($this->isElasticCrawlerRequest()) {
      return;
    }

    return parent::handle($event);
  }

  /**
   * Checks if it is Elastic Crawler request.
   *
   * @return bool
   *   The check result.
   */
  protected function isElasticCrawlerRequest(): bool {
    $current_request = $this->requestStack->getCurrentRequest();

    $defaultPattern = '/^Elastic-Crawler .*$/';

    // Get the regex from $settings if available.
    $elasticPattern = Settings::get('webspark_cas_elastic_crawler_regex', $defaultPattern);

    $agent = $current_request->server->get('HTTP_USER_AGENT');
    if (empty($agent)) {
      return FALSE;
    }

    if (\preg_match($elasticPattern, $agent)) {
      // Allow the Elastic crawler.
      return TRUE;
    }

    return FALSE;
  }

}
