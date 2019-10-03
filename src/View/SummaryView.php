<?php

namespace View;

class SummaryView extends View
{
	protected $return;

	protected $action;

	/**
	 * @return mixed
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @param mixed $action
	 */
	public function setAction($action)
	{
		$this->action = $action;
	}

	public function format(array $data)
	{
		$this->return = '<html><head><title>List of test report</title></head><body>';
		$this->return .= '<ul>';
		foreach ($data as $line) {
			$this->return .= '<li><a href="?action='.$this->getAction().'&id='.$line['id'].'&view=report">'.$line['name'].'</a></li>';
		}
		$this->return .= '</ul>';
		$this->return .= '</body></html>';

		return $this;
	}
}
