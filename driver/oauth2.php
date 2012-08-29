<?php
/**
 * Part of the Sentry Social application.
 *
 * NOTICE OF LICENSE
 *
 * @package    Sentry Social
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    http://getplatform.com/manuals/sentrysocial/license
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace SentrySocial;

use Config;
use Input;

/**
 * Oauth2 Driver Class
 *
 * @package  SentrySocial
 */
class Driver_OAuth2 extends SentrySocial
{
	/**
	 * Authenticate
	 */
	public function authenticate()
	{
		// set callback
		$callback_url = \URL::base().'/'.str_finish(Config::get('sentrysocial::sentrysocial.url.callback'), '/').$this->provider->name;
		$this->provider->callback = $callback_url;

		// authorize
		return \Redirect::to($this->provider->authorize(array(
			'redirect_uri' => $this->provider->callback
		)));
	}

	/**
	 * Callback
	 *
	 * @return  object  provider token object
	 */
	public function callback()
	{
		$code = Input::get('code');

		if ( ! $code)
		{
			switch (Input::get('error'))
			{
				case 'access_denied':
					return \Redirect::to(Config::get('sentrysocial::sentrysocial.url.cancel'))->send();
				break;
				default:
					throw new SentrySocialException(Input::get('error'));
				break;
			}
		}

		return $this->provider->access($code);
	}

	/**
	 * Get User Information
	 *
	 * @param   object  provider access token object
	 * @return  array   user information
	 */
	 public function get_user_info($token)
	 {
	 	return $this->provider->get_user_info($token);
	 }

}