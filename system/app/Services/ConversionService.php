<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\Quotation;
use App\Models\WebsiteInquiry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Every step of inquiry -> lead -> client -> quotation -> project. Each method
 * runs in a transaction and links the new record back to its origin instead of
 * copying data into a disconnected row.
 */
class ConversionService
{
    public function __construct(protected DocumentNumberGenerator $numbers) {}

    public function inquiryToLead(WebsiteInquiry $inquiry): Lead
    {
        if ($inquiry->converted_lead_id) {
            return $inquiry->convertedLead;
        }

        return DB::transaction(function () use ($inquiry) {
            $lead = Lead::create([
                'name' => $inquiry->name,
                'company' => $inquiry->company,
                'email' => $inquiry->email,
                'phone' => $inquiry->phone,
                'whatsapp' => $inquiry->whatsapp,
                'source' => Lead::SOURCE_WEBSITE,
                'website_inquiry_id' => $inquiry->id,
                'interested_service' => $inquiry->service,
                'priority' => $inquiry->priority,
                'assigned_to' => $inquiry->assigned_to,
                'notes' => $inquiry->message,
                'status' => Lead::STATUS_NEW,
            ]);

            // Linkage columns are deliberately not mass-assignable, so they are
            // written directly rather than through update().
            $inquiry->forceFill([
                'converted_lead_id' => $lead->id,
                'status' => WebsiteInquiry::STATUS_CONVERTED,
            ])->save();

            $this->logLead($lead, 'converted', 'Created from website inquiry '.$inquiry->inquiry_number);

            return $lead;
        });
    }

    public function leadToClient(Lead $lead): Client
    {
        if ($lead->client_id) {
            return $lead->client;
        }

        return DB::transaction(function () use ($lead) {
            $client = Client::create([
                'name' => $lead->name,
                'company_name' => $lead->company,
                'contact_person' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'whatsapp' => $lead->whatsapp,
                'city' => $lead->city,
                'country' => $lead->country,
                'lead_id' => $lead->id,
                'type' => $lead->company ? Client::TYPE_COMPANY : Client::TYPE_INDIVIDUAL,
                'notes' => $lead->notes,
                'status' => true,
            ]);

            $lead->forceFill([
                'client_id' => $client->id,
                'status' => Lead::STATUS_WON,
                'converted_at' => now(),
            ])->save();

            if ($inquiry = $lead->websiteInquiry) {
                $inquiry->forceFill(['converted_client_id' => $client->id])->save();
            }

            $this->logLead($lead, 'converted', 'Converted to client '.$client->displayName());

            return $client;
        });
    }

    /**
     * Turn an approved quotation into a delivery project, carrying the quoted
     * scope, budget, technologies and payment milestones across.
     */
    public function quotationToProject(Quotation $quotation): Project
    {
        if ($quotation->converted_project_id) {
            return $quotation->convertedProject;
        }

        return DB::transaction(function () use ($quotation) {
            $project = Project::create([
                'client_id' => $quotation->client_id,
                'quotation_id' => $quotation->id,
                'lead_id' => $quotation->lead_id,
                'name' => $quotation->project_name,
                'type' => $quotation->project_type,
                'description' => $quotation->project_summary,
                'technologies' => $quotation->technologies,
                'start_date' => now()->toDateString(),
                'deadline' => $quotation->total_estimated_days > 0
                    ? now()->addDays($quotation->total_estimated_days)->toDateString()
                    : null,
                'budget' => $quotation->grand_total,
                'currency' => $quotation->currency,
                'status' => Project::STATUS_PLANNING,
                'priority' => Project::PRIORITY_MEDIUM,
                'project_manager_id' => $quotation->prepared_by,
            ]);

            foreach ($quotation->milestones as $index => $milestone) {
                ProjectMilestone::create([
                    'project_id' => $project->id,
                    'title' => $milestone->title,
                    'description' => $milestone->description,
                    'amount' => $milestone->amount,
                    'is_payment_milestone' => true,
                    'status' => ProjectMilestone::STATUS_PENDING,
                    'sort_order' => $index,
                ]);
            }

            $quotation->forceFill(['converted_project_id' => $project->id])->save();

            if ($lead = $quotation->lead) {
                $this->logLead($lead, 'converted', 'Quotation '.$quotation->quotation_number.' converted to project '.$project->project_number);
            }

            if ($inquiry = $quotation->websiteInquiry) {
                $inquiry->forceFill(['converted_project_id' => $project->id])->save();
            }

            return $project;
        });
    }

    /**
     * Direct lead -> project for work that never needed a formal quotation. A
     * client record is created first so the project always has an owner.
     */
    public function leadToProject(Lead $lead): Project
    {
        return DB::transaction(function () use ($lead) {
            $client = $lead->client ?? $this->leadToClient($lead);

            $project = Project::create([
                'client_id' => $client->id,
                'lead_id' => $lead->id,
                'name' => $lead->interested_service ?: $lead->name.' project',
                'description' => $lead->notes,
                'budget' => $lead->estimated_budget ?? 0,
                'start_date' => now()->toDateString(),
                'status' => Project::STATUS_PLANNING,
                'priority' => $lead->priority,
            ]);

            $this->logLead($lead, 'converted', 'Converted to project '.$project->project_number);

            return $project;
        });
    }

    /**
     * Build the next revision of a quotation (QT-2026-0001-R1) with a full copy
     * of its items and milestones, leaving the original intact for history.
     */
    public function reviseQuotation(Quotation $quotation): Quotation
    {
        return DB::transaction(function () use ($quotation) {
            $root = $quotation->parent_id ? $quotation->parent : $quotation;
            $revision = (int) $root->revisions()->max('revision') + 1;

            $copy = $quotation->replicate([
                'quotation_number', 'parent_id', 'revision', 'status', 'sent_at',
                'decided_at', 'converted_project_id', 'client_accepted_by',
                'client_accepted_date', 'client_remarks',
            ]);

            // quotation_number, parent_id and revision are not mass-assignable,
            // so the revision metadata is written directly.
            $copy->forceFill([
                'quotation_number' => $root->baseNumber().'-R'.$revision,
                'parent_id' => $root->id,
                'revision' => $revision,
                'status' => Quotation::STATUS_DRAFT,
                'quotation_date' => now()->toDateString(),
            ])->save();

            foreach ($quotation->items as $item) {
                $line = $item->replicate();
                $line->quotation_id = $copy->id;
                $line->save();
            }

            foreach ($quotation->milestones as $milestone) {
                $stage = $milestone->replicate();
                $stage->quotation_id = $copy->id;
                $stage->save();
            }

            app(QuotationCalculator::class)->recalculate($copy);

            return $copy;
        });
    }

    protected function logLead(Lead $lead, string $type, string $title): void
    {
        LeadActivity::create([
            'lead_id' => $lead->id,
            'admin_id' => Auth::guard('admin')->id(),
            'type' => $type,
            'title' => $title,
        ]);
    }
}
