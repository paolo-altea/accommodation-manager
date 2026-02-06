<?php
/**
 * @version    3.1.0
 * @package    Com_Accommodation_manager
 * @author     Altea Software Srl <web@altea.it>
 * @copyright  Copyright (C) 2024. Tutti i diritti riservati.
 * @license    GNU General Public License versione 2 o successiva; vedi LICENSE.txt
 */

namespace Accomodationmanager\Component\Accommodation_manager\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Response\JsonResponse;
use Joomla\Utilities\ArrayHelper;

/**
 * Base list controller class for Accommodation Manager.
 * Provides common functionality for duplicate and saveOrderAjax actions.
 *
 * @since  3.1.0
 */
abstract class BaseListController extends AdminController
{
	/**
	 * The view name for redirects after actions.
	 *
	 * @var    string
	 * @since  3.1.0
	 */
	protected string $listView = '';

	/**
	 * The model name for getModel().
	 *
	 * @var    string
	 * @since  3.1.0
	 */
	protected string $modelName = '';

	/**
	 * Method to clone existing items.
	 *
	 * @return  void
	 *
	 * @throws  \Exception
	 * @since   3.1.0
	 */
	public function duplicate(): void
	{
		$this->checkToken();

		$pks = $this->input->post->get('cid', [], 'array');

		try {
			if (empty($pks)) {
				throw new \Exception(Text::_('COM_ACCOMMODATION_MANAGER_NO_ITEM_SELECTED'));
			}

			ArrayHelper::toInteger($pks);
			$model = $this->getModel();
			$model->duplicate($pks);
			$this->setMessage(Text::_('COM_ACCOMMODATION_MANAGER_ITEMS_SUCCESS_DUPLICATED'));
		} catch (\Exception $e) {
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		$this->setRedirect('index.php?option=com_accommodation_manager&view=' . $this->listView);
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    Optional. Model name
	 * @param   string  $prefix  Optional. Class prefix
	 * @param   array   $config  Optional. Configuration array for model
	 *
	 * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel
	 *
	 * @since   3.1.0
	 */
	public function getModel($name = '', $prefix = 'Administrator', $config = [])
	{
		$name = $name ?: $this->modelName;

		return parent::getModel($name, $prefix, ['ignore_request' => true]);
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @throws  \Exception
	 * @since   3.1.0
	 */
	public function saveOrderAjax(): void
	{
		$app   = Factory::getApplication();
		$input = $app->getInput();
		$pks   = $input->post->get('cid', [], 'array');
		$order = $input->post->get('order', [], 'array');

		ArrayHelper::toInteger($pks);
		ArrayHelper::toInteger($order);

		$model  = $this->getModel();
		$return = $model->saveorder($pks, $order);

		$app->mimeType = 'application/json';
		$app->setHeader('Content-Type', $app->mimeType . '; charset=' . $app->charSet);
		$app->sendHeaders();

		echo new JsonResponse($return);
		$app->close();
	}
}
