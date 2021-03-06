<?php

namespace Framework\Services;

use Framework\Components\Main as MainComponents;
use Framework\Entities\Page;
use Framework\Facades\Request;

class Pages
{

    public static function new(string $table): array
    {
        $components = MainComponents::render($table);
        return [
            'title' => MainComponents::getTitle($table),
            'table' => $table,
            'components' => $components,
        ];
    }

    public static function save(string $table): array
    {
		MainComponents::executeExtraAction($table);
		$page = new Page($table);

		$page->id = null;

		foreach (MainComponents::getFields($table) as $value) {
			$page->{$value['field']} = Request::post($value['field']);
		}

		$return = self::new($table);

		try {
			$page->insert();
			$return['success'] = true;
		} catch (\PDOException $e) {
			$return['success'] = false;
		}

		return $return;
    }

	public static function edit(string $table, int $id): array
	{
		$components = MainComponents::render($table, $id);
		return [
			'title' => MainComponents::getTitle($table),
			'table' => $table,
			'id' => $id,
			'components' => $components,
		];
	}

	public static function update(string $table, int $id): array
	{

		MainComponents::executeExtraAction($table);
		$page = new Page($table);

		$page->id = $id;

		foreach (MainComponents::getFields($table) as $value) {
			$page->{$value['field']} = Request::post($value['field']);
		}

		$return = self::edit($table, $id);

		try {
			$page->update();
			$return['success'] = true;
		} catch (\PDOException $e) {
			$return['success'] = false;
		}

		return $return;

	}

    public static function delete(string $table, int $id): array
    {
		$page = new Page($table);

		try {
			$page->delete($id);
			$return = self::list($table);
			$return['success'] = true;
		} catch (\PDOException $e) {
			$return['success'] = false;
		}

		return $return;
    }

    public static function list(string $table): array
    {
		$page = new Page($table);
		$search_expression = Request::get('search');
		$where[] = '1=1';
		$params = [];

		if ($search_expression) {

			$pieces_search_expression = array_map(function($value) {
				return explode("=", trim($value));
			}, explode(',', $search_expression));

			foreach ($pieces_search_expression as $piece) {
				if ($piece[0]) {
					$where[] = strtolower(trim($piece[0]))."=?";
					$params[] = trim($piece[1]);
				}
			}

		}

		$records = $page->paginate(getenv('RECORDS_PAGINATION'), implode(" and ", $where), $params);
		$records['search'] = $search_expression;

		return [
			'title' => MainComponents::getTitle($table),
			'fields' => MainComponents::getFields($table),
			'table' => $table,
			'records' => $records,
		];
    }

}
