<?php

namespace App\Http\Controllers\Admin;

use App\Models\Client;
use App\Support\Crud\Column;
use App\Support\Crud\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * The client record powers both the CRM and the website "our clients" section,
 * so the logo / description / status fields stay editable here.
 */
class ClientController extends AdminResourceController
{
    protected string $model = Client::class;

    protected string $route = 'clients';

    protected string $permission = 'clients';

    protected string $singular = 'Client';

    protected string $plural = 'Clients';

    protected string $description = 'Company and contact records, with their full project and billing history.';

    protected string $icon = 'fa-user-tie';

    protected string $uploadDirectory = 'clients';

    protected array $searchable = ['client_code', 'name', 'company_name', 'email', 'phone'];

    protected array $withCount = ['projects', 'quotations', 'invoices'];

    protected bool $hasShow = true;

    protected function columns(): array
    {
        return [
            Column::code('client_code', 'Code'),
            Column::image('logo', 'Logo'),
            Column::text('name', 'Contact'),
            Column::text('company_name', 'Company'),
            Column::text('email', 'Email'),
            Column::text('phone', 'Phone'),
            Column::badge('type', 'Type', Client::TYPES),
            Column::number('projects_count', 'Projects'),
            Column::boolean('status', 'Status', ['true' => 'Active', 'false' => 'Inactive']),
        ];
    }

    protected function fields(): array
    {
        return [
            Field::heading('Identity'),
            Field::select('type', 'Client Type', Client::TYPES, ['required' => true]),
            Field::text('name', 'Client / Contact Name', ['required' => true]),
            Field::text('company_name', 'Company Name'),
            Field::text('contact_person', 'Contact Person'),
            Field::text('industry', 'Industry'),

            Field::heading('Contact Details'),
            Field::email('email', 'Email'),
            Field::tel('phone', 'Phone'),
            Field::tel('whatsapp', 'WhatsApp'),
            Field::url('website', 'Website'),
            Field::textarea('address', 'Address', ['rows' => 2]),
            Field::text('city', 'City'),
            Field::text('country', 'Country'),
            Field::text('postal_code', 'Postal Code'),

            Field::heading('Billing'),
            Field::select('currency', 'Billing Currency', $this->options->currencies()),
            Field::text('tax_name', 'Tax Name', ['placeholder' => 'VAT / GST / NTN']),
            Field::text('tax_number', 'Tax / Registration Number'),

            Field::heading('Website Display'),
            Field::file('logo', 'Client Logo', [
                'accept' => 'image/*',
                'directory' => 'clients',
                'help' => 'Shown in the client logo strip on the public site.',
            ]),
            Field::textarea('description', 'Public Description'),
            Field::checkbox('status', 'Active', [
                'help' => 'Active clients appear on the website and in dropdowns.',
            ]),

            Field::heading('Internal'),
            Field::textarea('notes', 'Internal Notes'),
        ];
    }

    protected function filters(): array
    {
        return [
            ['key' => 'type', 'label' => 'Type', 'options' => Client::TYPES],
            ['key' => 'status', 'label' => 'Status', 'type' => 'boolean', 'options' => ['1' => 'Active', '0' => 'Inactive']],
            ['key' => 'country', 'label' => 'Country', 'input' => 'text', 'type' => 'like'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'type' => ['required', Rule::in(array_keys(Client::TYPES))],
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:30'],
            'currency' => ['nullable', 'string', 'size:3'],
            'tax_name' => ['nullable', 'string', 'max:60'],
            'tax_number' => ['nullable', 'string', 'max:80'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'description' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['boolean'],
        ];
    }

    /** Full 360 view: projects, quotations, invoices, payments, meetings, tickets. */
    public function show(Client $client): View
    {
        $this->authorizeAction('view');

        $client->load([
            'lead',
            'projects' => fn ($query) => $query->latest('id'),
            'quotations' => fn ($query) => $query->latest('id'),
            'invoices' => fn ($query) => $query->latest('id'),
            'payments' => fn ($query) => $query->latest('payment_date'),
            'meetings' => fn ($query) => $query->latest('scheduled_at'),
            'followUps.assignee',
            'tickets' => fn ($query) => $query->latest('id'),
            'contracts' => fn ($query) => $query->latest('id'),
            'assets',
        ]);

        $totals = [
            'invoiced' => (float) $client->invoices->whereNotIn('status', ['draft', 'cancelled'])->sum('total'),
            'received' => (float) $client->payments->where('status', 'completed')->sum('amount'),
            'outstanding' => $client->outstandingBalance(),
            'projects_active' => $client->projects->whereIn('status', ['planning', 'active', 'on_hold', 'testing'])->count(),
        ];

        return view('admin.clients.show', compact('client', 'totals'));
    }

    public function toggleStatus(Client $client): RedirectResponse
    {
        $this->authorizeAction('edit');

        $client->update(['status' => ! $client->status]);

        return back()->with('success', 'Client status updated successfully.');
    }
}
