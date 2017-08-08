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
    private function addLine($line) {
        $this->return .= $line."\n";
    }

	/**
	 * @param $data
	 */
	public function formatRequirements($data)
	{
		foreach ($data as $datum) {
			$this->formatChapter($datum, 2);
			if (isset ($datum['cases'])) {
				$this->addLine('### Cas de test');
				$this->formatTestCases($datum['cases']);
			}
		}
	}

	/**
	 * @param $cases
	 */
	public function formatTestCases($cases)
	{
		if (count($cases) == 0) {
			$this->addLine('Pas de cas de test lié à cette exigence');
		}
		foreach ($cases as $case) {
			$this->formatChapter($case, 4);
			if ($case['prerequisite']) {
				$this->addLine($case['prerequisite']);
			}
			if (isset($case['step'])) {
				$this->formatTestCaseStep($case['step']);
			}
		}
	}

	public function formatTestCaseStep($steps)
	{
		if (count($steps) == 0) {
			$this->addLine('Pas d\'étapes pour ce cas de test');
			return;
		} else {
			$this->addLine('Étapes du test : ');
			$this->addLine();
		}
		$i = 1;
		$this->addLine('| Num |   | Action | Résultat attendu |');
		$this->addLine('| --- | - | ------ | ---------------- |');
		foreach ($steps as $step) {
			$this->addLine('| '.$i.' | <input type="checkbox"> | '.strip_tags(trim($step['action'])).' | '.strip_tags(trim($step['expectedResult'])).' |');
			$i++;
		}
		$this->addLine();
	}

	/**
	 * @param $data
	 * @param int $level
	 */
	protected function formatChapter($data, $level = 1)
	{
		$this->addLine(str_repeat('#', $level) .' '.$data['name']);

		$this->addLine($data['description']);
	}
}