<?php
/**
 * @version    CVS: 2.0.1
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2019. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

/**
 * Managerrates list controller class.
 *
 * @since  2.0.1
 */
class ManagerratesController extends AdminController
{
	/**
	 * Save rates grid (bulk update).
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	public function saveGrid(): void
	{
		// Check for request forgeries
		$this->checkToken();

		$app  = Factory::getApplication();
		$user = $app->getIdentity();

		// Access check
		if (!$user->authorise('core.edit', 'com_accommodation_manager')) {
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			$this->setRedirect(Route::_('index.php?option=com_accommodation_manager&view=managerrates', false));
			return;
		}

		// Get the rates data from POST
		$ratesData = $this->input->post->get('rates', [], 'array');

		if (empty($ratesData)) {
			$app->enqueueMessage(Text::_('COM_ACCOMMODATION_MANAGER_NO_RATES_TO_SAVE'), 'warning');
			$this->setRedirect(Route::_('index.php?option=com_accommodation_manager&view=managerrates', false));
			return;
		}

		/** @var ManagerratesModel $model */
		$model = $this->getModel('Managerrates', 'Administrator', ['ignore_request' => true]);

		try {
			$result = $model->saveRatesGrid($ratesData, $user->id);

			if ($result) {
				$app->enqueueMessage(Text::_('COM_ACCOMMODATION_MANAGER_RATES_SAVED_SUCCESSFULLY'), 'success');
			}
		} catch (\Exception $e) {
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		$this->setRedirect(Route::_('index.php?option=com_accommodation_manager&view=managerrates', false));
	}

	/**
	 * Method to clone existing Managerrates
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 */
	public function duplicate()
	{
		// Check for request forgeries
		$this->checkToken();

		// Get id(s)
		$pks = $this->input->post->get('cid', array(), 'array');

		try
		{
			if (empty($pks))
			{
				throw new \Exception(Text::_('COM_ACCOMMODATION_MANAGER_NO_ELEMENT_SELECTED'));
			}

			ArrayHelper::toInteger($pks);
			$model = $this->getModel();
			$model->duplicate($pks);
			$this->setMessage(Text::_('COM_ACCOMMODATION_MANAGER_ITEMS_SUCCESS_DUPLICATED'));
		}
		catch (\Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		$this->setRedirect('index.php?option=com_accommodation_manager&view=managerrates');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    Optional. Model name
	 * @param   string  $prefix  Optional. Class prefix
	 * @param   array   $config  Optional. Configuration array for model
	 *
	 * @return  object	The Model
	 *
	 * @since   2.0.1
	 */
	public function getModel($name = 'Managerrate', $prefix = 'Administrator', $config = array())
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}

	

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   2.0.1
	 *
	 * @throws  Exception
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$input = Factory::getApplication()->input;
		$pks   = $input->post->get('cid', array(), 'array');
		$order = $input->post->get('order', array(), 'array');

		// Sanitize the input
		ArrayHelper::toInteger($pks);
		ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		Factory::getApplication()->close();
	}
}
