<?php
 /**
  * @version		
  * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
  * @license		GNU General Public License version 2 or later; see LICENSE.txt
  */

  defined ('_JEXEC') or die('Resticted Aceess');

  use Joomla\CMS\Factory;
  use Joomla\CMS\Form\Form;
  use Joomla\CMS\Language\Text;
  use Joomla\CMS\Filesystem\File;
  use Joomla\CMS\Plugin\CMSPlugin;
  use Joomla\CMS\Filesystem\Folder;
  use Joomla\Utilities\ArrayHelper;
  

  /**
   * An example custom profile plugin.
   *
   * @package		Joomla.Plugins
   * @subpackage	user.profile
   * @version		1.6
   */
  class PlgUserSimpleterms extends CMSPlugin
  {
	/**
	 * @param	string	The context for the data
	 * @param	int		The user id
	 * @param	object
	 * @return	boolean
	 * @since	1.6
	 */
	function onContentPrepareData($context, $data)
	{
		// Check we are manipulating a valid form.
		if (!in_array($context, array('com_users.profile','com_users.registration','com_users.user','com_admin.profile'))){
			return true;
		}

		if (is_object($data))
		{
			$userId = $data->id ?? 0;

			if (!isset($data->simpleterms) && $userId > 0)
			{
				// Load the profile data from the database.
				$db = Factory::getDbo();
				$query = $db->getQuery(true)
					->select(
						[
							$db->quoteName('profile_key'),
							$db->quoteName('profile_value'),
						]
					)
					->from($db->quoteName('#__user_profiles'))
					->where($db->quoteName('user_id') . ' = ' . $db->quote($userId))
					->where($db->quoteName('profile_key') . ' LIKE ' . $db->quote('simpleterms.%'))
					->order($db->quoteName('ordering'));

				$db->setQuery($query);
				$results = $db->loadRowList();

				// Merge the simpleterms data.
				$data->simpleterms = [];

				foreach ($results as $v)
				{
					$k = str_replace('simpleterms.', '', $v[0]);
					$data->simpleterms[$k] = json_decode($v[1], true);

					if ($data->simpleterms[$k] === null)
					{
						$data->simpleterms[$k] = $v[1];
					}
				}
			}
		}

		return true;
	}

	/**
	 * @param	JForm	The form to be altered.
	 * @param	array	The associated data for the form.
	 * @return	boolean
	 * @since	1.6
	 */
	function onContentPrepareForm($form, $data)
	{
		// Load user_profile plugin language
		$lang = Factory::getLanguage();
		$lang->load('plg_user_simpleterms', JPATH_ADMINISTRATOR);

		if (!($form instanceof Form))
		{
			Factory::getApplication()->enqueueMessage('JERROR_NOT_A_FORM');
			return false;
		}
		// Check we are manipulating a valid form.
		if (!in_array($form->getName(), array('com_users.profile', 'com_users.registration','com_users.user','com_admin.profile')))
		{
			return true;
		}

		if ($form->getName()=='com_users.profile')
		{
			// Add the profile fields to the form.
			Form::addFormPath(dirname(__FILE__).'/profiles');
			$form->loadFile('profile', true);
	
			// Toggle whether the simpleterms field is required.
			if ($this->params->get('profile-require_simpleterms', 1) > 0)
			{
				$form->setFieldAttribute('agreeterms', 'required', $this->params->get('profile-require_simpleterms') == 2, 'simpleterms');
			}
			else
			{
				$form->removeField('agreeterms', 'simpleterms');
			}
		}
	
		//In this example, we treat the frontend registration and the back end user create or edit as the same. 
		elseif ($form->getName()=='com_users.registration' || $form->getName()=='com_users.user' )
		{		
			// Add the registration fields to the form.
			Form::addFormPath(dirname(__FILE__).'/profiles');
			$form->loadFile('profile', true);

			// Toggle whether the simpleterms field is required.
			if ($this->params->get('register-require_simpleterms', 1) > 0)
			{
				$form->setFieldAttribute('agreeterms', 'required', $this->params->get('register-require_simpleterms') == 2, 'simpleterms');
			}
			else
			{
				$form->removeField('agreeterms', 'simpleterms');
			}
		}
	}


	/**
     * Method is called before user data is stored in the database
     *
     * @param   array    $user   Holds the old user data.
     * @param   boolean  $isNew  True if a new user is stored.
     * @param   array    $data   Holds the new user data.
     *
     * @return  boolean
     *
     * @since   3.9.0
     * @throws  \InvalidArgumentException on missing required data.
     */
    public function onUserBeforeSave($user, $isNew, $data)
    {
          // // Only check for front-end user registration
        if (Factory::getApplication()->isClient('administrator')) {
            return true;
        }

        $userId = ArrayHelper::getValue($user, 'id', 0, 'int');

        // User already registered, no need to check it further
        if ($userId > 0) {
            return true;
        }

        // Load plugin language files
        $this->loadLanguage();

        // Check that the terms is checked if required ie only in registration from frontend.
        $input  = Factory::getApplication()->getInput();
        $option = $input->get('option');
        $task   = $input->post->get('task');
        $form   = $input->post->get('jform', [], 'array');

        if ($option == 'com_users' && \in_array($task, ['registration.register']) && empty($form['simpleterms']['agreeterms'])) {
            throw new \InvalidArgumentException(Factory::getApplication()->getLanguage()->_('PLG_USER_SIMPLETERMS_FIELD_ERROR'));
        }

        return true;
    }

	function onUserAfterSave($data, $isNew, $result, $error)
	{
		$userId	= ArrayHelper::getValue($data, 'id', 0, 'int');

		if ($userId && $result && isset($data['simpleterms']) && (count($data['simpleterms'])))
		{
			
			$db = Factory::getDbo();

			$query = $db->getQuery(true)
				->delete($db->quoteName('#__user_profiles'))
				->where($db->quoteName('user_id') . ' = ' . $userId)
				->andWhere($db->quoteName('profile_key') . ' LIKE ' . $db->quote('simpleterms.%'));
			$db->setQuery($query);
			$db->execute();


			$query->clear()
			->select($db->quoteName('ordering'))
			->from($db->quoteName('#__user_profiles'))
			->where($db->quoteName('user_id') . ' = ' . $userId);
			$db->setQuery($query);
			$usedOrdering = $db->loadColumn();


			$order = 1;
			$query->clear()
				->insert($db->quoteName('#__user_profiles'));

			foreach ($data['simpleterms'] as $k => $v)
			{
				while (in_array($order, $usedOrdering))
				{
					$order++;
				}

				$query->values(implode(',',[$userId, $db->quote('simpleterms.' . $k), $db->quote(json_encode($v)), $order++]));
			}

			$db->setQuery($query);
			$db->execute();
		
		}

		return true;
	}

	/**
	 * Remove all user profile information for the given user ID
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param	array		$user		Holds the user data
	 * @param	boolean		$success	True if user was succesfully stored in the database
	 * @param	string		$msg		Message
	 */
	function onUserAfterDelete($user, $success, $msg)
	{
		if (!$success) {
			return false;
		}
 
		$userId	= ArrayHelper::getValue($user, 'id', 0, 'int');
 
		if ($userId)
		{
			$db = Factory::getDbo();

			$query = $db->getQuery(true)
				->delete($db->quoteName('#__user_profiles'))
				->where($db->quoteName('user_id') . ' = ' . $userId)
				->andWhere($db->quoteName('profile_key') . ' LIKE ' . $db->quote('simpleterms.%'));
			$db->setQuery($query);
			$db->execute();
		}
 
		return true;
	}


 }