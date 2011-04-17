<?php
/**
 * opVersionInfoPlugin
 *
 * This source file is subject to the Apache License version 2.0
 * that is bundled with this package in the file LICENSE.
 *
 * @copyright   2011 Kimura Youichi <kim.upsilon@bucyou.net>
 * @license     Apache License 2.0
 */

/**
 * version components.
 *
 * @package    opVersionInfoPlugin
 * @subpackage action
 * @author     Kimura Youichi <kim.upsilon@bucyou.net>
 */
class versionComponents extends sfComponents
{
  public function executeCore(sfWebRequest $request)
  {
    $versions = array(
      'OpenPNE3' => OPENPNE_VERSION,
      'symfony' => SYMFONY_VERSION,
      'PHP' => phpversion(),
    );

    $dbmsInfo = self::getDbmsInfo();
    $versions[$dbmsInfo['name']] = $dbmsInfo['version'];

    $this->versions = $versions;
  }

  static protected function getDbmsInfo()
  {
    $conn = Doctrine_Manager::connection();

    $driverName = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
    switch ($driverName)
    {
      case 'mysql':
        $name = 'MySQL';
        break;
      case 'pgsql':
        $name = 'PostgreSQL';
        break;
      case 'sqlite':
        $name = 'SQLite3';
        break;
      default:
        $name = $driverName;
    }

    return array(
      'name' => $name,
      'version' => $conn->getAttribute(PDO::ATTR_SERVER_VERSION),
    );
  }

  public function executePlugins(sfWebRequest $request)
  {
    $plugins = array(
      'authentication' => array(),
      'skin' => array(),
      'default' => array(),
    );

    $pluginNames = $this->context->getConfiguration()->getPlugins();
    foreach ($pluginNames as $pluginName)
    {
      $plugin = opPlugin::getInstance($pluginName, $this->dispatcher);

      $category = self::getPluginCategory($plugin);
      if (!isset($plugins[$category]))
      {
        $plugins[$category] = array();
      }

      $plugins[$category][$pluginName] = array(
        'name' => $pluginName,
        'category' => $category,
        'version' => $plugin->getVersion(),
        'summary' => $plugin->getSummary(),
        'developers' => self::getPluginDevelopers($plugin),
      );
    }

    $this->plugins = $plugins;
  }

  static protected function getPluginCategory(opPlugin $plugin)
  {
    if ($plugin->isAuthPlugin())
    {
      return 'authentication';
    }
    if ($plugin->isSkinPlugin())
    {
      return 'skin';
    }

    return 'default';
  }

  static protected function getPluginDevelopers(opPlugin $plugin)
  {
    // XXX opPlugin::getPackageInfo() is protected method
    $reflectopnMethod = new ReflectionMethod('opPlugin', 'getPackageInfo');
    $reflectopnMethod->setAccessible(true);
    $packageInfo = $reflectopnMethod->invoke($plugin);

    if (!$packageInfo)
    {
      return '';
    }

    $developers = array();
    foreach (array('lead', 'developer', 'contributor') as $position)
    {
      foreach ($packageInfo->$position as $developer)
      {
        $name = trim($developer->name);
        if (empty($name))
        {
          $name = trim($developer->user);
        }

        if (!empty($name))
        {
          $developers[] = $name;
        }
      }
    }

    return implode(', ', $developers);
  }
}
