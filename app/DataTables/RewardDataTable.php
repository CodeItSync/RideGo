<?php

namespace App\DataTables;

use App\Models\User;
use App\Models\Reward;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use App\Traits\DataTableTrait;

class RewardDataTable extends DataTable
{
    use DataTableTrait;
    
    
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
     
    public function dataTable($query)
    {
        if (!$this->userType) {
            return datatables()
                ->eloquent($query)
                ->editColumn('created_at', function($query) {
                    return date('Y/m/d',strtotime($query->created_at));
                })
                ->editColumn('amount', function($query) {
                    return $query->amount. ' QAR';
                })
                ->addIndexColumn()
                ->addColumn('action', 'reward.action')
                ->rawColumns(['action','status']);
        } else {
            return datatables()
                ->eloquent($query)
                ->editColumn('created_at', function($query) {
                    return date('Y/m/d',strtotime($query->created_at));
                })
                ->addIndexColumn()
                ->addColumn('action', 'reward.action')
                ->rawColumns(['action','status']);
        }
    }
    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        if (!$this->userType) {
            $model = Reward::with('user');
        } else {
            $model = User::where([
                'status' => 'active',
                'user_type' => $this->userType,
            ]);
        }

        return $this->applyScopes($model);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        if (!$this->userType) {
            return [
                Column::make('DT_RowIndex')
                    ->searchable(false)
                    ->title(__('message.srno'))
                    ->orderable(false)
                    ->width(60),
                Column::make('user.display_name')->title( __('message.name') ),
                Column::make('user.address')->title( __('message.address') ),
                Column::make('user.contact_number')->title( __('message.contact_number') ),
                Column::make('amount')
                ->title( __('message.amount') ),
                Column::make('created_at')->title( __('message.date') ),
            ];
        }
        return [
            Column::make('DT_RowIndex')
                ->searchable(false)
                ->title(__('message.srno'))
                ->orderable(false)
                ->width(60),
            Column::make('display_name')->title( __('message.name') ),
            Column::make('address')->title( __('message.address') ),
            Column::make('contact_number')->title( __('message.contact_number') ),
            Column::computed('action')->title( __('message.options') )
                  ->exportable(true)
                  ->printable(true)
                  ->width(60)
                  ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'driver_' . date('YmdHis');
    }
}
