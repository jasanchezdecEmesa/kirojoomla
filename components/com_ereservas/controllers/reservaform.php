<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Ereservas
 * @author     Equipos Mecanizados,S.L. <rsainz@emesa.com>
 * @copyright  2019 Equipos Mecanizados,S.L.
 * @license    Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Session\Session;
use \Joomla\CMS\Language\Text;
JLoader::registerPrefix('Ereservas', JPATH_LIBRARIES . '/ereservas');
/**
 * Reserva controller class.
 *
 * @since  1.6
 */
class EreservasControllerReservaForm extends \Joomla\CMS\MVC\Controller\FormController
{
	/**
	 * Method to check out an item for editing and redirect to the edit form.
	 *
	 * @return void
	 *
	 * @since    1.6
     *
     * @throws Exception
	 */
	public function edit($key = NULL, $urlVar = NULL)
	{
		$app = Factory::getApplication();

		// Get the previous edit id (if any) and the current edit id.
		$previousId = (int) $app->getUserState('com_ereservas.edit.reserva.id');
		$editId     = $app->input->getInt('id', 0);

		// Set the user id for the user to edit in the session.
		$app->setUserState('com_ereservas.edit.reserva.id', $editId);

		// Get the model.
		$model = $this->getModel('ReservaForm', 'EreservasModel');

		// Check out the item
		if ($editId)
		{
			$model->checkout($editId);
		}

		// Check in the previous user.
		if ($previousId)
		{
			$model->checkin($previousId);
		}

		// Redirect to the edit screen.
		$this->setRedirect(Route::_('index.php?option=com_ereservas&view=reservaform&layout=edit', false));
	}

	/**
	 * Method to save a user's profile data.
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since  1.6
	 */
	public function save($key = NULL, $urlVar = NULL)
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app   = Factory::getApplication();
		$model = $this->getModel('ReservaForm', 'EreservasModel');

		// Get the user data.
		$data = Factory::getApplication()->input->get('jform', array(), 'array');

		// Validate the posted data.
		$form = $model->getForm();

		if (!$form)
		{
			throw new Exception($model->getError(), 500);
		}

		// Validate the posted data.
		$data = $model->validate($form, $data);

		// Check for errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			$input = $app->input;
			$jform = $input->get('jform', array(), 'ARRAY');

			// Save the data in the session.
			$app->setUserState('com_ereservas.edit.reserva.data', $jform);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_ereservas.edit.reserva.id');
			$this->setRedirect(Route::_('index.php?option=com_ereservas&view=reservaform&layout=edit&id=' . $id, false));

			$this->redirect();
		}
		//Guardamos en variables los datos insertados para poder tratarlos antes de insertar
		$tarjeta= $data['tarjeta'];
		$personas= $data['personas'];
		$id_visita= $data['id_visita'];
		$data['tarjeta']="";

		// Convertimos la fecha de modificacion en datetime, y si estamos creando, la fecha de creacion tamien

		$data['fecha_modificacion'] = date("Y-m-d H:i:s");
		if($data['id'] < 1) {
			$data['fecha_creacion'] = date("Y-m-d H:i:s");
		}

		$return = $model->save($data);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_ereservas.edit.reserva.data', $data);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_ereservas.edit.reserva.id');
			$this->setMessage(Text::sprintf('Save failed', $model->getError()), 'warning');
			$this->setRedirect(Route::_('index.php?option=com_ereservas&view=reservaform&layout=edit&id=' . $id, false));
		}

		// Check in the profile.
		if ($return)
		{
			$model->checkin($return);
			//una vez hemos sabido que id se ha insertado, podemos cifrar la tarjeta y recalcular el aforo
			$id_insertado= $return;
			EreservasTarjeta::cifrareditar($id_insertado,$tarjeta);
			EreservasRecalculo::recalcularaforo($id_visita);
		}

		// Clear the profile id from the session.
		$app->setUserState('com_ereservas.edit.reserva.id', null);

		// Redirect to the list screen.
		$this->setMessage(Text::_('COM_ERESERVAS_ITEM_SAVED_SUCCESSFULLY'));
		$menu = Factory::getApplication()->getMenu();
		$item = $menu->getActive();
		$url  = (empty($item->link) ? 'index.php?option=com_ereservas&view=calendarioreservas' : $item->link);
        $this->setRedirect(Route::_('index.php?option=com_ereservas&view=calendarioreservas', false));
		// Flush the data from the session.
		$app->setUserState('com_ereservas.edit.reserva.data', null);
	}

	/**
	 * Method to abort current operation
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function cancel($key = NULL)
	{
		$app = Factory::getApplication();

		// Get the current edit id.
		$editId = (int) $app->getUserState('com_ereservas.edit.reserva.id');

		// Get the model.
		$model = $this->getModel('ReservaForm', 'EreservasModel');

		// Check in the item
		if ($editId)
		{
			$model->checkin($editId);
		}

		$menu = Factory::getApplication()->getMenu();
		$item = $menu->getActive();
		$url  = (empty($item->link) ? 'index.php?option=com_ereservas&view=reservas' : $item->link);
		$this->setRedirect(Route::_($url, false));
	}

	/**
	 * Method to remove data
	 *
	 * @return void
	 *
	 * @throws Exception
     *
     * @since 1.6
	 */
	public function remove()
    {
        $app   = Factory::getApplication();
        $model = $this->getModel('ReservaForm', 'EreservasModel');
        $pk    = $app->input->getInt('id');

        // Attempt to save the data
        try
        {
            $return = $model->delete($pk);

            // Check in the profile
            $model->checkin($return);

            // Clear the profile id from the session.
            $app->setUserState('com_ereservas.edit.reserva.id', null);

            $menu = $app->getMenu();
            $item = $menu->getActive();
            $url = (empty($item->link) ? 'index.php?option=com_ereservas&view=reservas' : $item->link);

            // Redirect to the list screen
            $this->setMessage(Text::_('COM_ERESERVAS_ITEM_DELETED_SUCCESSFULLY'));
            $this->setRedirect(Route::_($url, false));

            // Flush the data from the session.
            $app->setUserState('com_ereservas.edit.reserva.data', null);
        }
        catch (Exception $e)
        {
            $errorType = ($e->getCode() == '404') ? 'error' : 'warning';
            $this->setMessage($e->getMessage(), $errorType);
            $this->setRedirect('index.php?option=com_ereservas&view=reservas');
        }
    }
}
