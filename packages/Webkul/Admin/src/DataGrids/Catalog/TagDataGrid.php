<?php

namespace Webkul\Admin\DataGrids\Catalog;

use Illuminate\Support\Facades\DB;
use Webkul\Core\Models\Locale;
use Webkul\DataGrid\DataGrid;

class TagDataGrid extends DataGrid
{
    /**
     * Index.
     *
     * @var string
     */
    protected $primaryColumn = 'tag_id';

    /**
     * Contains the keys for which extra filters to show.
     *
     * @var string[]
     */
    protected $extraFilters = [
        'locales',
    ];

    /**
     * Prepare query builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function prepareQueryBuilder()
    {
        if (core()->getRequestedLocaleCode() === 'all') {
            $whereInLocales = Locale::query()->pluck('code')->toArray();
        } else {
            $whereInLocales = [core()->getRequestedLocaleCode()];
        }

        $queryBuilder = DB::table('tags as tag')
            ->select(
                'tag.id as tag_id',
                'tt.name as name',
                'tt.locale'
            )
            ->leftJoin('tag_translations as tt', function ($leftJoin) use ($whereInLocales) {
                $leftJoin->on('tag.id', '=', 'tt.tag_id')
                    ->whereIn('tt.locale', $whereInLocales);
            })
            ->groupBy('tag.id', 'tt.name', 'tt.locale');

        $this->addFilter('tag_id', 'tag.id');

        return $queryBuilder;
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        $this->addColumn([
            'index'      => 'tag_id',
            'label'      => trans('admin::app.catalog.tags.index.datagrid.id'),
            'type'       => 'integer',
            'searchable' => false,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.catalog.tags.index.datagrid.name'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);
    }

    public function prepareActions()
    {
        if (bouncer()->hasPermission('catalog.tags.edit')) {
            $this->addAction([
                'icon'   => 'icon-edit',
                'title'  => trans('admin::app.catalog.tags.index.datagrid.edit'),
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.catalog.tags.edit', $row->tag_id);
                },
            ]);
        }

        if (bouncer()->hasPermission('catalog.tags.delete')) {
            $this->addAction([
                'icon'   => 'icon-delete',
                'title'  => trans('admin::app.catalog.tags.index.datagrid.delete'),
                'method' => 'DELETE',
                'url'    => function ($row) {
                    return route('admin.catalog.tags.delete', $row->tag_id);
                },
            ]);
        }

        if (bouncer()->hasPermission('catalog.tags.delete')) {
            $this->addMassAction([
                'title'  => trans('admin::app.catalog.tags.index.datagrid.delete'),
                'method' => 'POST',
                'url'    => route('admin.catalog.tags.mass_delete'),
            ]);
        }

        if (bouncer()->hasPermission('catalog.tags.edit')) {
            $this->addMassAction([
                'title'   => trans('admin::app.catalog.tags.index.datagrid.update-status'),
                'method'  => 'POST',
                'url'     => route('admin.catalog.tags.mass_update'),
                'options' => [
                    [
                        'label' => trans('admin::app.catalog.tags.index.datagrid.active'),
                        'value' => 1,
                    ],
                    [
                        'label' => trans('admin::app.catalog.tags.index.datagrid.inactive'),
                        'value' => 0,
                    ],
                ],
            ]);
        }
    }

}
