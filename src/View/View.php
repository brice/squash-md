<?php

namespace View;

use League\CommonMark\Converter;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use Webuni\CommonMark\TableExtension\TableExtension;

class View
{
    protected $return;

    /**
     * @param $data
     * @return $this
     */
    public function format(array $data)
    {
		$this->addLine('# Plan de test');

		$this->formatTableOfContent($data);
		$this->formatRequirements($data);

        return $this;
    }

    /**
     * @return mixed
     */
    public function write()
    {
        return $this->return;
    }

    /**
     * @return string
     */
    public function writeHTML()
    {
		$environment = Environment::createCommonMarkEnvironment();
		$environment->addExtension(new TableExtension());

		$converter = new Converter(new DocParser($environment), new HtmlRenderer($environment));

        return $converter->convertToHtml($this->return);
    }

    /**
     * @param $line
     */
    private function addLine(string $line) {
        $this->return .= $line."\n";
    }

	public function formatTableOfContent(array $data)
	{
		$index = 1;

		$this->addLine('## Sommaire');

		foreach ($data as $requirement) {
			$line = $index.'. ['.$requirement['name'].'](?action=report&id=25&view=report&requirement='.$requirement['id'].')';
			if ($requirement['reference']) {
				list($ref, ) = explode(' ', $requirement['reference']);
				list(, $taigaId) = explode('#',$ref);
				$line = $index.'. ['.$requirement['name'].']'.'(https://tree.taiga.io/project/sqwib-ami-software/us/'.$taigaId.')';
				$line .= ' - [Squash](http://ami-qa-master:8080/squash/requirement-versions/'.$requirement['id'].'/info)';
			}
			$this->addLine($line);
			$subIndex = 1;
			foreach ($requirement['cases'] as $case) {
				$this->addLine("\t".$subIndex.'. '.$case['reference'].' - '.$case['name']);
			}
			$index++;
		}
		return $this;
    }
	/**
	 * @param $data
	 */
	public function formatRequirements(array $data)
	{
		foreach ($data as $datum) {
			$this->formatChapter($datum, 2);
			if (isset ($datum['cases'])) {
				// $this->addLine('### Cas de test');
				$this->formatTestCases($datum['cases']);
			}
		}
		return $this;
	}

	/**
	 * @param $cases
	 */
	public function formatTestCases(array $cases)
	{
		if (count($cases) == 0) {
			$this->addLine('Pas de cas de test lié à cette exigence');
		}
		foreach ($cases as $case) {
			$this->formatChapter($case, 2);
			if ($case['prerequisite']) {
				$this->addLine("__Pré requis :__ ");
				$this->addLine($case['prerequisite']);
			}
			if (isset($case['step'])) {
				$this->formatTestCaseStep($case['step']);
			}
		}
		return $this;
	}

	public function formatTestCaseStep(array $steps)
	{
		if (count($steps) == 0) {
			$this->addLine('Pas d\'étapes pour ce cas de test');
			return;
		} else {
			$this->addLine('__Étapes du test :__ ');
			$this->addLine('');
		}
		$i = 1;
		$this->addLine('| - | Action | Résultat attendu | OK<br/>NOK | Commentaires | ');
		$this->addLine('| - | ------ | ---------------- | ---------- | ------------ |');
		foreach ($steps as $step) {
			$actionText = strip_tags(trim($step['action']));

			$this->addLine('| '.$i.' | '.$this->formatTestCaseContent($step['action']).' | '.$this->formatTestCaseContent($step['expectedResult']).' |||');
			$i++;
		}
		$this->addLine('');

		return $this;
	}

	protected function formatTestCaseContent($content)
	{
		return str_replace("\n", "<br/>", strip_tags(trim($content)));
	}

	/**
	 * @param $data
	 * @param int $level
	 */
	protected function formatChapter(array $data, int $level = 1)
	{
		if (isset ($data['reference']) && $data['reference'] != '') {
			$this->addLine(str_repeat('#', $level) .' '.$data['reference'].' - '.$data['name']);
		} else {
			$this->addLine(str_repeat('#', $level) .' '.$data['name']);
		}

		$this->addLine($data['description']);
	}
}
