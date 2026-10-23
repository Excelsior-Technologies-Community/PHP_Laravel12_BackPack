<?php

namespace App\Http\Controllers\Admin;

use App\Models\AuditLog;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class AuditLogCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(AuditLog::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/audit-log');
        CRUD::setEntityNameStrings('Audit Log', 'Audit Logs');
        $this->crud->setDefaultPageLength(15);
        $this->crud->orderBy('created_at', 'DESC');
    }

    protected function setupListOperation()
    {
        CRUD::column('id')->label('Log ID');

        CRUD::column('created_at')
            ->label('Timestamp')
            ->type('datetime');

        CRUD::column('user_name')
            ->label('User / Admin')
            ->default('System');

        CRUD::addColumn([
            'name' => 'action',
            'label' => 'Action',
            'type' => 'closure',
            'escaped' => false,
            'function' => function ($entry) {
                $badge = match ($entry->action) {
                    'CREATED' => 'bg-success',
                    'UPDATED' => 'bg-primary',
                    'STOCK_ADJUSTED' => 'bg-warning text-dark',
                    'DELETED' => 'bg-danger',
                    default => 'bg-secondary',
                };
                return '<span class="badge ' . $badge . '">' . e($entry->action) . '</span>';
            },
        ]);

        CRUD::addColumn([
            'name' => 'model_name',
            'label' => 'Entity',
            'type' => 'closure',
            'escaped' => false,
            'function' => function ($entry) {
                $classBase = class_basename($entry->auditable_type ?? 'Model');
                return '<span class="badge bg-light text-dark border">' . e($classBase) . '</span>';
            },
        ]);

        CRUD::column('auditable_name')
            ->label('Target Name')
            ->wrapper([
                'element' => 'strong',
            ]);

        CRUD::column('description')->label('Summary');

        CRUD::column('ip_address')->label('IP Address');
    }

    protected function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);

        CRUD::column('id')->label('Audit Log ID');
        CRUD::column('created_at')->label('Timestamp')->type('datetime');
        CRUD::column('user_name')->label('Admin / User');
        CRUD::column('action')->label('Action Verb');
        CRUD::column('auditable_type')->label('Target Model Class');
        CRUD::column('auditable_id')->label('Record ID');
        CRUD::column('auditable_name')->label('Record Identifier');
        CRUD::column('description')->label('Description');
        CRUD::column('ip_address')->label('IP Address');

        CRUD::addColumn([
            'name' => 'diff_viewer',
            'label' => 'Attribute Changes & Diff Ledger',
            'type' => 'closure',
            'escaped' => false,
            'function' => function ($entry) {
                $old = $entry->old_values ?? [];
                $new = $entry->new_values ?? [];
                $keys = array_unique(array_merge(array_keys($old), array_keys($new)));

                if (empty($keys)) {
                    return '<div class="alert alert-secondary">No raw attribute changes captured.</div>';
                }

                $html = '<div class="table-responsive"><table class="table table-bordered table-striped mt-2">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 25%">Field / Attribute</th>
                            <th style="width: 37.5%">Previous Value (Old)</th>
                            <th style="width: 37.5%">Updated Value (New)</th>
                        </tr>
                    </thead>
                    <tbody>';

                foreach ($keys as $k) {
                    $oldVal = isset($old[$k]) ? (is_array($old[$k]) ? json_encode($old[$k]) : (string)$old[$k]) : '<span class="text-muted"><em>null</em></span>';
                    $newVal = isset($new[$k]) ? (is_array($new[$k]) ? json_encode($new[$k]) : (string)$new[$k]) : '<span class="text-muted"><em>null</em></span>';

                    $changed = ($oldVal !== $newVal);
                    $rowClass = $changed ? 'table-warning' : '';

                    $html .= '<tr class="' . $rowClass . '">
                        <td><code>' . e($k) . '</code></td>
                        <td class="text-break">' . ($changed ? '<del class="text-danger">' . e($oldVal) . '</del>' : e($oldVal)) . '</td>
                        <td class="text-break">' . ($changed ? '<strong class="text-success">' . e($newVal) . '</strong>' : e($newVal)) . '</td>
                    </tr>';
                }

                $html .= '</tbody></table></div>';
                return $html;
            },
        ]);
    }
}
