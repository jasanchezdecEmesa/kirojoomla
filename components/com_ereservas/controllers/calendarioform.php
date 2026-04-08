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
 * Calendario controller class.
 *
 * @since  1.6
 */
class EreservasControllerCalendarioForm extends \Joomla\CMS\MVC\Controller\FormController
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
		$previousId = (int) $app->getUserState('com_ereservas.edit.calendario.id');
		$editId     = $app->input->getInt('id', 0);

		// Set the user id for the user to edit in the session.
		$app->setUserState('com_ereservas.edit.calendario.id', $editId);

		// Get the model.
		$model = $this->getModel('CalendarioForm', 'EreservasModel');

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
		$this->setRedirect(Route::_('index.php?option=com_ereservas&view=calendarioform&layout=edit', false));
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
		$model = $this->getModel('CalendarioForm', 'EreservasModel');

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

		$validarfechas = false;

		if($data['frecuencia'] == 'diaria' && (strlen($data['fecha_inicio']) != 19 || strlen($data['fecha_fin']) != 19)) {
		    $data = false;
		    $validarfechas = true;
        }

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

			if($validarfechas) $app->enqueueMessage('La fecha de inicio y de fin es obligatoria', 'warning');

			$input = $app->input;
			$jform = $input->get('jform', array(), 'ARRAY');

			// Save the data in the session.
			$app->setUserState('com_ereservas.edit.calendario.data', $jform);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_ereservas.edit.calendario.id');
			$this->setRedirect(Route::_('index.php?option=com_ereservas&view=calendarioform&layout=edit&id=' . $id, false));

			$this->redirect();
		}

		//Si el formato de fecha no lleva :, añadimos al menos los minutos para que se guarde correctamente.
        if(!strpos($data['hora_inicio'],':')) $data['hora_inicio'] = $data['hora_inicio'].':00';
        if(!strpos($data['hora_final'],':')) $data['hora_final'] = $data['hora_final'].':00';



        if($data['frecuencia'] == 'mensual'){
            $data['dias_mes'] = $data['diaspuntuales'];
        }

		// Attempt to save the data.
		$return = $model->save($data);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_ereservas.edit.calendario.data', $data);

			// Redirect back to the edit screen.
			$id = (int) $app->getUserState('com_ereservas.edit.calendario.id');
			$this->setMessage(Text::sprintf('Save failed', $model->getError()), 'warning');
			$this->setRedirect(Route::_('index.php?option=com_ereservas&view=calendarioform&layout=edit&id=' . $id, false));
		}

		// Check in the profile.
		if ($return)
		{
		    //Generamos las visitas
            $data['id_calendario'] = $return;
            EreservasVisitas::generarVisitas($data);

			$model->checkin($return);
		}

		// Clear the profile id from the session.
		$app->setUserState('com_ereservas.edit.calendario.id', null);

		// Redirect to the list screen.
		$this->setMessage('Sus visitas se han generado con éxito.');
		$menu = Factory::getApplication()->getMenu();
		$item = $menu->getActive();
		$url  = (empty($item->link) ? 'index.php?option=com_ereservas&view=calendarios' : $item->link);
		$this->setRedirect(Route::_($url, false));

		// Flush the data from the session.
		$app->setUserState('com_ereservas.edit.calendario.data', null);
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
		$editId = (int) $app->getUserState('com_ereservas.edit.calendario.id');

		// Get the model.
		$model = $this->getModel('CalendarioForm', 'EreservasModel');

		// Check in the item
		if ($editId)
		{
			$model->checkin($editId);
		}

		$menu = Factory::getApplication()->getMenu();
		$item = $menu->getActive();
		$url  = (empty($item->link) ? 'index.php?option=com_ereservas&view=calendarios' : $item->link);
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
        $model = $this->getModel('CalendarioForm', 'EreservasModel');
        $pk    = $app->input->getInt('id');

        // Attempt to save the data
        try
        {
            $return = $model->delete($pk);

            // Check in the profile
            $model->checkin($return);

            // Clear the profile id from the session.
            $app->setUserState('com_ereservas.edit.calendario.id', null);

            $menu = $app->getMenu();
            $item = $menu->getActive();
            $url = (empty($item->link) ? 'index.php?option=com_ereservas&view=calendarios' : $item->link);

            // Redirect to the list screen
            $this->setMessage(Text::_('COM_ERESERVAS_ITEM_DELETED_SUCCESSFULLY'));
            $this->setRedirect(Route::_($url, false));

            // Flush the data from the session.
            $app->setUserState('com_ereservas.edit.calendario.data', null);
        }
        catch (Exception $e)
        {
            $errorType = ($e->getCode() == '404') ? 'error' : 'warning';
            $this->setMessage($e->getMessage(), $errorType);
            $this->setRedirect('index.php?option=com_ereservas&view=calendarios');
        }
    }




}
