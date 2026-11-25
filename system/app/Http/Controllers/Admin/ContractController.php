<?php

namespace App\Http\Controllers\Admin;

use App\Models\Contract;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContractController extends AdminResourceController
{
    protected string $model = Contract::class;

    protected string $route = 'contracts';

    protected string $permission = 'contracts';

    protected string $singular = 'Contract';

    protected string $plural = 'Contracts';

    protected string $description = 'Signed agreements, retainers and NDAs with their value and term.';

    protected string $icon = 'fa-file-signature';

    protected array $searchable = ['contract_number', 'title', 'signed_by'];

    protected array $with = ['client'];

    protected function columns(): array
    {
        return [
            Column::code('contract_number', 'Contract #'),
            Column::text('title', 'Title'),
            Column::relation('client.company_name', 'Client'),
            Column::badge('type', 'Type', Contract::TYPES),
            Column::money('value', 'Value'),
            Column::date('start_date', 'Start'),
            Column::date('end_date', 'End'),
            Column::badge('status', 'Status', Contract::STATUSES),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::heading('Contract'),
            Field::text('title', 'Title', ['required' => true]),
            Field::select('client_id', 'Client', $this->options->clients(), ['required' => true]),
            Field::select('type', 'Type', Contract::TYPES, ['required' => true]),
            Field::select('status', 'Status', Contract::STATUSES, ['required' => true]),
            Field::money('value', 'Contract Value'),
            Field::select('currency', 'Currency', $this->options->currencies()),
            Field::date('start_date', 'Start Date'),
            Field::date('end_date', 'End Date'),
            Field::date('signed_date', 'Signed Date'),
            Field::text('signed_by', 'Signed By'),

            Field::heading('Terms'),
            Field::textarea('scope', 'Scope of Work'),
            Field::textarea('terms', 'Terms & Conditions', ['rows' => 6]),
            Field::textarea('notes', 'Internal Notes'),
            Field::file('document', 'Contract Document', [
                'accept' => '.pdf,.doc,.docx',
                'attachment' => true,
                'help' => 'Attach the signed PDF. Stored against this contract.',
            ]),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'status', 'label' => 'Status', 'options' => Contract::STATUSES],
            ['key' => 'type', 'label' => 'Type', 'options' => Contract::TYPES],
            ['key' => 'client_id', 'label' => 'Client', 'options' => $this->options->clients()],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'client_id' => ['required', 'exists:clients,id'],
            'type' => ['required', Rule::in(array_keys(Contract::TYPES))],
            'status' => ['required', Rule::in(array_keys(Contract::STATUSES))],
            'value' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'signed_date' => ['nullable', 'date'],
            'signed_by' => ['nullable', 'string', 'max:255'],
            'scope' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'document' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ];
    }

    protected function afterSave(Model $record, Request $request, bool $created): void
    {
        if ($request->hasFile('document')) {
            $this->uploader->attachTo($record, $request->file('document'), 'contracts');
        }
    }
}
