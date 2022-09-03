<?php

class Mail
{
  private static $twig;
  public static $templatesLocation;

  public function __construct()
  {
    self::$twig = Timber::get_context();
  }

  public function sendTo($emails, $subject, $content, $template, $from = null)
  {
    try {
      $message = Timber::compile(self::$templatesLocation . '/' . $template, array_merge(self::$twig, $content));
      $success = 0;

      if ($from) {
        add_filter('wp_mail_from', function ($email) use ($from) {
          return $from['email'] ?: $email;
        });

        add_filter('wp_mail_from_name', function ($name) use ($from) {
          return $from['name'] ?: $name;
        });
      }

      if (!is_array($emails)) {
        $emails = [$emails];
      }

      foreach ($emails as $email) {
        $success += wp_mail($email, $subject, $message) ? 1 : 0;
      }

      if ($success) {
        return true;
      }
    } catch (Exception $e) {
      echo 'Mail error';
//      echo $e->getMessage();
    }

    return false;
  }
}
