<?php
namespace Articles\Service;

use Articles\Model\CategoryModel;

class CategoryService
{
	private $categoryModel;

	private function getCategoryModel()
	{
		return $this->categoryModel = new CategoryModel();
	}

	/**
	 * OBTEMOS TODAS LAS CATEGORIAS PADRE
	 */
	public function getAllCategoryParent()
	{
		$categorys = $this->getCategoryModel()->getAllCategoryParent();

		return $categorys;
	}

	/**
	 * OBTEMOS TODAS LAS CATEGORIAS POR ID DE PADRE
	 */
	public function getAllSubCategory($id_parent)
	{
		$subCategorys = $this->getCategoryModel()->getAllSubCategory($id_parent);

		return $subCategorys;
	}


}