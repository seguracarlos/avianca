<?php
namespace Articles\Service;

use Articles\Model\ColorModel;

class ColorService
{
	private $colorModel;

	private function getColorModel()
	{
		return $this->colorModel = new ColorModel();
	}

	/**
	 * OBTEMOS TODOS LOS COLORES
	 */
	public function getAllColor()
	{
		$colors = $this->getColorModel()->getAllColor();

		return $colors;
	}


}