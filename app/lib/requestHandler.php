<?php


	class Status

	{

		private $_actionSuccess ;

		private  $_statusMessage ;

		

		public function __construct()

		{

				 

		}

		public function setActionSuccess($actionSuccess)

		{

			$this->_actionSuccess=$actionSuccess;

		}

		public function getActionSuccess()

		{

			return $this->_actionSuccess;

		}

		public function setStatusMessage($statusMessage)

		{

			$this->_statusMessage=$statusMessage;

		}

		public function getStatusMessage()

		{

			return $this->_statusMessage;

		}

	}

	

	 function handle_post_request($action,$sql_statement,$DatabaseCo)

	 {

				$STATUS_MESSAGE = "";

				$statusObj = new Status();

				$SQL_STATEMENT = $sql_statement;

				switch($action)

				{

				case 'REGISTER':

					if(($DatabaseCo->updateData($SQL_STATEMENT)))

					{

						$STATUS_MESSAGE="A verification code has been sent to your email id.Please Enter verification code in below text box.";

						$statusObj->setActionSuccess(true);

						$statusObj->setStatusMessage($STATUS_MESSAGE);

					}

					else

					{

						$STATUS_MESSAGE="There is a problem while adding record.";

						

						$statusObj->setActionSuccess(false);

						$statusObj->setStatusMessage($STATUS_MESSAGE);								

					}

					break;

					

				case 'ADD':

							if(($DatabaseCo->updateData($SQL_STATEMENT)))

							{

								$STATUS_MESSAGE="Record is added successfully.";

								$statusObj->setActionSuccess(true);

								$statusObj->setStatusMessage($STATUS_MESSAGE);

							}

							else

							{

								$STATUS_MESSAGE="There is a problem while adding record.";

								

								$statusObj->setActionSuccess(false);

								$statusObj->setStatusMessage($STATUS_MESSAGE);								

							}

					break;

					

				case 'UPDATE':

							if(($DatabaseCo->updateData($SQL_STATEMENT)))

							{

								$STATUS_MESSAGE = "Record is updated successfully.";

								$statusObj->setActionSuccess(true);

								$statusObj->setStatusMessage($STATUS_MESSAGE);							}

							else

							{

								$STATUS_MESSAGE = "There is a problem while updating record.";

								$statusObj->setActionSuccess(false);

								$statusObj->setStatusMessage($STATUS_MESSAGE);								

							}							

					break;

					

				case 'DELETE':

							if(($DatabaseCo->updateData($SQL_STATEMENT)))

							{

								$STATUS_MESSAGE = "Record is deleted successfully.";

								$statusObj->setActionSuccess(true);

								$statusObj->setStatusMessage($STATUS_MESSAGE);							}

							else

							{

								$STATUS_MESSAGE = "There is a problem while deleting record.";

								$statusObj->setActionSuccess(false);

								$statusObj->setStatusMessage($STATUS_MESSAGE);								

							}												

					break;

				

				case 'LOGIN':

							$DatabaseCo->dbResult = $DatabaseCo->getSelectQueryResult($SQL_STATEMENT);

								

							if($DatabaseCo->dbRow = mysqli_fetch_object($DatabaseCo->dbResult))

							{

								$STATUS_MESSAGE = "Logged in successfully.";

								$statusObj->setActionSuccess(true);

								$statusObj->setStatusMessage($STATUS_MESSAGE);							}

							else

							{

								$STATUS_MESSAGE = "User Name or Password does not match, Please try again!";

								$statusObj->setActionSuccess(false);

								$statusObj->setStatusMessage($STATUS_MESSAGE);								

							}											

							break;

				case 'FORGET':

							$DatabaseCo->dbResult = $DatabaseCo->getSelectQueryResult($SQL_STATEMENT);

								

							if($DatabaseCo->dbRow = mysqli_fetch_object($DatabaseCo->dbResult))

							{

								$STATUS_MESSAGE = "Password Sent to your Email-ID successfully.";

								$statusObj->setActionSuccess(true);

								$statusObj->setStatusMessage($STATUS_MESSAGE);							}

							else

							{

								$STATUS_MESSAGE = "Password is not Sent to your Email-ID, Please try again!";

								$statusObj->setActionSuccess(false);

								$statusObj->setStatusMessage($STATUS_MESSAGE);								

							}											

							break;

				case 'SEARCH':

							$DatabaseCo->dbResult = $DatabaseCo->getSelectQueryResult($SQL_STATEMENT);

							if($DatabaseCo->dbRow = mysqli_fetch_object($DatabaseCo->dbResult))

							{

								$STATUS_MESSAGE = "Password Sent to your Email-ID successfully.";

								$statusObj->setActionSuccess(true);

								$statusObj->setStatusMessage($STATUS_MESSAGE);							}

							else

							{

								$STATUS_MESSAGE = "Password is not Sent to your Email-ID, Please try again!";

								$statusObj->setActionSuccess(false);

								$statusObj->setStatusMessage($STATUS_MESSAGE);								

							}											

							break;	

				}

		return $statusObj;

	}

	function getSelectForCountry($selected,$arg)

	{

		if($selected == $arg)

			echo "SELECTED=SELECTED";

		else

			echo "";	

	}

	function getRowCount($sqlForCount,$DatabaseCo)

	{

		$rowCount = 0;

		

		$DatabaseCo->dbResult=$DatabaseCo->getSelectQueryResult($sqlForCount);

		

		while($DatabaseCo->dbRow = mysqli_fetch_array($DatabaseCo->dbResult))

		{

			$rowCount = $DatabaseCo->dbRow[0];	

		}

		return $rowCount;

		

	}

	function getWhereClauseForStatus($status)

	{

		$where = "";

		switch($status)

		{

			case 'approved':

				$where = " WHERE STATUS='APPROVED'";	

				break;

			

			case 'unapproved':

				$where = " WHERE STATUS='UNAPPROVED'";

				break;

			

			case 'all':

				$where = "";

				break;

			default:

				$where = "";

			

		}

		return $where;

	}

		



?>