<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_status
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Extension\ExtensionHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\Database\DatabaseInterface;
use Joomla\Module\Multilangstatus\Administrator\Helper\MultilangstatusAdminHelper;

$db       = Factory::getContainer()->get(DatabaseInterface::class);
$user     = $app->getIdentity();
$sitename = htmlspecialchars($app->get('sitename', ''), ENT_QUOTES, 'UTF-8');

// Try to get the items from the post-installation model
try
{
	$messagesModel = $app->bootComponent('com_postinstall')->getMVCFactory()->createModel('Messages', 'Administrator', ['ignore_request' => true]);
	$messages      = $messagesModel->getItems();
}
catch (RuntimeException $e)
{
	$messages = [];

	// Still render the error message from the Exception object
	$app->enqueueMessage($e->getMessage(), 'error');
}

$joomlaFilesExtensionId = ExtensionHelper::getExtensionRecord('files_joomla')->extension_id;

// Load the com_postinstall language file
$app->getLanguage()->load('com_postinstall', JPATH_ADMINISTRATOR, 'en-GB', true);

$multilanguageStatusModuleOutput = '';

// Check if the multilangstatus module is present and enabled in the site
if (class_exists(MultilangstatusAdminHelper::class) && MultilangstatusAdminHelper::isEnabled($app, $db))
{
	// Publish and display the module
	MultilangstatusAdminHelper::publish($app, $db);

	if (Multilanguage::isEnabled($app, $db)) 
	{
		$module                          = ModuleHelper::getModule('mod_multilangstatus');
		$multilanguageStatusModuleOutput = ModuleHelper::renderModule($module);
	}
}

require ModuleHelper::getLayoutPath('mod_status', $params->get('layout', 'default'));
