<?php

class GrooveShark extends gsAPI
{
	/**
	 * Constructor
	 */
	function __construct()
	{
		//$this->buildSessionData();
		parent::__construct();
	}

	/**
	 * Build session data
	 *
	 * Builds out session data for the GrooveShark API
	 */
	private function buildSessionData()
	{

		// Session caching
		if (isset($_SESSION['gsSession']) && !empty($_SESSION['gsSession']))
		{
			$this->setSession($_SESSION['gsSession']);
		}
		else {
			$_SESSION['gsSession'] = $this->startSession();
		}
		
		// Country caching
		if (isset($_SESSION['gsCountry']) && !empty($_SESSION['gsCountry']))
		{
			$this->setCountry($_SESSION['gsCountry']);
		}
		else {
			$_SESSION['gsCountry'] = $this->getCountry();
		}
		$country = $this->getCountry();
	}
}