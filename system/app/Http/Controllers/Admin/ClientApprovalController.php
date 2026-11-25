<?php

namespace App\Http\Controllers\Admin;

use App\Models\ClientApproval;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientApprovalController extends AdminResourceController
{
    protected string $model = ClientApproval::class;

    protected string $route = 'client-approvals';

    protected string $permission = 'client_approvals';

    protected string $singular = 'Client Approval';

    protected string $plural = 'Client Approvals';

    protected string $description = 'Designs, content and changes sent to clients for sign-off.';

    protected string $icon = 'fa-circle-check';

    protected array $searchable = ['title', 'description', 'client_remarks'];

    protected array $with = ['client', 'project'];

    protected string $defaultSort = 'sent_date';

    protected function columns(): array
    {
        return [
            Column::text('title', 'Item'),
            Column::relation('client.company_name', 'Client'),
            Column::relation('project.name', 'Project'),
            Column::badge('item_type', 'Type', ClientApproval::ITEM_TYPES),
            Column::date('sent_date', 'Sent'),
            Column::date('responded_date', 'Responded'),
            Column::badge('status', 'Status', ClientApproval::STATUSES),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::text('title', 'Item Title', ['required' => true]),
            Field::select('client_id', 'Client', $this->options->clients(), ['required' => true]),
            Field::select('project_id', 'Project', $this->options->projects()),
            Field::select('item_type', 'Item Type', ClientApproval::ITEM_TYPES, ['required' => true]),
            Field::select('status', 'Status', ClientApproval::STATUSES, ['required' => true]),
            Field::date('sent_date', 'Sent Date'),
            Field::date('responded_date', 'Responded Date'),
            Field::textarea('description', 'Description'),
            Field::textarea('client_remarks', 'Client Remarks'),
            Field::file('attachment', 'Attachment', ['attachment' => true]),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'status', 'label' => 'Status', 'options' => ClientApproval::STATUSES],
            ['key' => 'item_type', 'label' => 'Type', 'options' => ClientApproval::ITEM_TYPES],
            ['key' => 'client_id', 'label' => 'Client', 'options' => $this->options->clients()],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'client_id' => ['required', 'exists:clients,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'item_type' => ['required', Rule::in(array_keys(ClientApproval::ITEM_TYPES))],
            'status' => ['required', Rule::in(array_keys(ClientApproval::STATUSES))],
            'sent_date' => ['nullable', 'date'],
            'responded_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'client_remarks' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ];
    }

    protected function afterSave(Model $record, Request $request, bool $created): void
    {
        if ($request->hasFile('attachment')) {
            $this->uploader->attachTo($record, $request->file('attachment'), 'approvals');
        }
    }

    public function decide(Request $request, ClientApproval $clientApproval): RedirectResponse
    {
        $this->authorizeAction('edit');

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(ClientApproval::STATUSES))],
            'client_remarks' => ['nullable', 'string'],
        ]);

        $clientApproval->update($data + ['responded_date' => now()->toDateString()]);

        return back()->with('success', 'Approval updated.');
    }
}
