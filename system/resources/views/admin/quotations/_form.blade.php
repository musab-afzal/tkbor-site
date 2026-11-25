@include('admin.partials.validation-summary')

@php
    // Rebuild the item rows from old() after a validation failure so nothing the
    // user typed is lost, otherwise use the stored items.
    $itemRows = old('items', $quotation->items->map(fn ($item) => [
        'pricing_item_id' => $item->pricing_item_id,
        'section' => $item->section,
        'name' => $item->name,
        'description' => $item->description,
        'features' => $item->features,
        'quantity' => $item->quantity,
        'unit_price' => $item->unit_price,
        'internal_cost' => $item->internal_cost,
        'estimated_days' => $item->estimated_days,
        'is_optional' => $item->is_optional,
        'is_free' => $item->is_free,
    ])->all());

    $milestoneRows = old('milestones', $quotation->milestones->map(fn ($milestone) => [
        'title' => $milestone->title,
        'description' => $milestone->description,
        'percentage' => $milestone->percentage,
        'due_condition' => $milestone->due_condition,
    ])->all());

    if (empty($itemRows)) {
        $itemRows = [['section' => 'main', 'quantity' => 1, 'unit_price' => 0]];
    }
@endphp

<div class="admin-card">
    <div class="admin-card__header"><h2>Quotation Details</h2></div>
    <div class="admin-card__body">
        <div class="admin-form-grid">
            <div class="admin-form-group">
                <label class="admin-label" for="client_id">Client <span class="req">*</span></label>
                <select id="client_id" name="client_id" class="admin-select @error('client_id') is-invalid @enderror" required>
                    <option value="">Select client...</option>
                    @foreach ($clients as $id => $name)
                        <option value="{{ $id }}" @selected((string) old('client_id', $quotation->client_id) === (string) $id)>{{ $name }}</option>
                    @endforeach
                </select>
                @error('client_id') <div class="admin-error-text">{{ $message }}</div> @enderror
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="lead_id">Linked Lead</label>
                <select id="lead_id" name="lead_id" class="admin-select">
                    <option value="">None</option>
                    @foreach ($leads as $id => $name)
                        <option value="{{ $id }}" @selected((string) old('lead_id', $quotation->lead_id) === (string) $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="project_name">Project Name <span class="req">*</span></label>
                <input type="text" id="project_name" name="project_name" class="admin-input @error('project_name') is-invalid @enderror"
                       value="{{ old('project_name', $quotation->project_name) }}" required>
                @error('project_name') <div class="admin-error-text">{{ $message }}</div> @enderror
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="project_type">Project Type</label>
                <input type="text" id="project_type" name="project_type" class="admin-input"
                       value="{{ old('project_type', $quotation->project_type) }}" placeholder="Web Application, Branding...">
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="quotation_date">Quotation Date <span class="req">*</span></label>
                <input type="date" id="quotation_date" name="quotation_date" class="admin-input" required
                       value="{{ old('quotation_date', $quotation->quotation_date?->format('Y-m-d')) }}">
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="valid_until">Valid Until</label>
                <input type="date" id="valid_until" name="valid_until" class="admin-input"
                       value="{{ old('valid_until', $quotation->valid_until?->format('Y-m-d')) }}">
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="currency">Currency <span class="req">*</span></label>
                <select id="currency" name="currency" class="admin-select" required>
                    @foreach ($currencies as $code => $label)
                        <option value="{{ $code }}" @selected(old('currency', $quotation->currency) === $code)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="prepared_by">Prepared By</label>
                <select id="prepared_by" name="prepared_by" class="admin-select">
                    <option value="">Select employee...</option>
                    @foreach ($employees as $id => $name)
                        <option value="{{ $id }}" @selected((string) old('prepared_by', $quotation->prepared_by) === (string) $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="status">Status <span class="req">*</span></label>
                <select id="status" name="status" class="admin-select" required>
                    @foreach (\App\Models\Quotation::STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $quotation->status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <input type="hidden" name="website_inquiry_id" value="{{ old('website_inquiry_id', $quotation->website_inquiry_id) }}">
        </div>
    </div>
</div>

<div class="admin-card" data-repeater>
    <div class="admin-card__header">
        <h2>Quotation Items</h2>
        <button type="button" class="admin-btn admin-btn--ghost admin-btn--sm" data-repeater-add>
            <i class="fas fa-plus"></i> Add Item
        </button>
    </div>
    <div class="admin-card__body">
        @error('items') <div class="admin-error-text" style="margin-bottom:10px">{{ $message }}</div> @enderror

        <div class="admin-alert-inline">
            <i class="fas fa-circle-info"></i>
            <span>
                Pick an item from the price book to auto-fill its price, duration and features — every value stays
                editable for this quotation only. Free items show their value but are not charged; optional items are
                listed separately and excluded from the total.
            </span>
        </div>

        <div class="admin-scroll-x">
            <div class="admin-repeater__head">
                <div>Module / Service</div>
                <div>Qty</div>
                <div>Unit Price</div>
                <div>Days</div>
                <div>Line Total</div>
                <div></div>
            </div>

            <div data-repeater-rows>
                @foreach ($itemRows as $index => $item)
                    @include('admin.quotations._item-row', ['index' => $index, 'item' => $item])
                @endforeach
            </div>
        </div>

        <template data-repeater-template>
            @include('admin.quotations._item-row', ['index' => '__INDEX__', 'item' => ['section' => 'main', 'quantity' => 1, 'unit_price' => 0]])
        </template>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card__header"><h2>Discount, Tax & Totals</h2></div>
    <div class="admin-card__body">
        <div class="admin-form-grid">
            <div class="admin-form-group">
                <label class="admin-label" for="discount_type">Discount Type</label>
                <select id="discount_type" name="discount_type" class="admin-select">
                    <option value="percentage" @selected(old('discount_type', $quotation->discount_type) === 'percentage')>Percentage (%)</option>
                    <option value="fixed" @selected(old('discount_type', $quotation->discount_type) === 'fixed')>Fixed Amount</option>
                </select>
            </div>
            <div class="admin-form-group">
                <label class="admin-label" for="discount_value">Discount Value</label>
                <input type="number" step="0.01" min="0" id="discount_value" name="discount_value" class="admin-input"
                       value="{{ old('discount_value', $quotation->discount_value ?? 0) }}">
            </div>
            <div class="admin-form-group">
                <label class="admin-label" for="tax_percentage">Tax (%)</label>
                <input type="number" step="0.01" min="0" max="100" id="tax_percentage" name="tax_percentage" class="admin-input"
                       value="{{ old('tax_percentage', $quotation->tax_percentage ?? 0) }}">
            </div>
        </div>

        <div class="admin-totals">
            <div class="admin-totals__row"><span>Subtotal</span><span data-total-subtotal>0.00</span></div>
            <div class="admin-totals__row"><span>Discount</span><span data-total-discount>0.00</span></div>
            <div class="admin-totals__row"><span>Tax</span><span data-total-tax>0.00</span></div>
            <div class="admin-totals__row"><span>Free services value</span><span data-total-free>0.00</span></div>
            <div class="admin-totals__row"><span>Optional items</span><span data-total-optional>0.00</span></div>
            <div class="admin-totals__row admin-totals__row--grand"><span>Grand Total</span><span data-total-grand>0.00</span></div>
        </div>
        <p class="admin-help-text" style="text-align:right">Totals are recalculated on the server when you save.</p>
    </div>
</div>

<div class="admin-card" data-repeater>
    <div class="admin-card__header">
        <h2>Payment Milestones</h2>
        <button type="button" class="admin-btn admin-btn--ghost admin-btn--sm" data-repeater-add>
            <i class="fas fa-plus"></i> Add Milestone
        </button>
    </div>
    <div class="admin-card__body">
        <p class="admin-help-text" style="margin-bottom:12px">
            Percentages are costed against the grand total when the quotation is saved.
        </p>

        <div data-repeater-rows>
            @foreach ($milestoneRows as $index => $milestone)
                @include('admin.quotations._milestone-row', ['index' => $index, 'milestone' => $milestone])
            @endforeach
        </div>

        <template data-repeater-template>
            @include('admin.quotations._milestone-row', ['index' => '__INDEX__', 'milestone' => []])
        </template>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card__header"><h2>Proposal Content</h2></div>
    <div class="admin-card__body">
        <div class="admin-form-grid">
            <div class="admin-form-group admin-form-group--full">
                <label class="admin-label" for="project_summary">Project Summary</label>
                <textarea id="project_summary" name="project_summary" rows="4" class="admin-textarea">{{ old('project_summary', $quotation->project_summary) }}</textarea>
            </div>
            <div class="admin-form-group admin-form-group--full">
                <label class="admin-label" for="requirements">Requirements</label>
                <textarea id="requirements" name="requirements" rows="4" class="admin-textarea">{{ old('requirements', $quotation->requirements) }}</textarea>
            </div>
            <div class="admin-form-group admin-form-group--full">
                <label class="admin-label" for="scope">Scope of Work</label>
                <textarea id="scope" name="scope" rows="4" class="admin-textarea">{{ old('scope', $quotation->scope) }}</textarea>
            </div>
            <div class="admin-form-group admin-form-group--full">
                <label class="admin-label" for="proposed_solution">Proposed Solution</label>
                <textarea id="proposed_solution" name="proposed_solution" rows="4" class="admin-textarea">{{ old('proposed_solution', $quotation->proposed_solution) }}</textarea>
            </div>
            <div class="admin-form-group admin-form-group--full">
                <label class="admin-label" for="technologies">Technologies</label>
                <input type="text" id="technologies" name="technologies" class="admin-input" placeholder="Laravel, Vue, MySQL"
                       value="{{ old('technologies', is_array($quotation->technologies) ? implode(', ', $quotation->technologies) : '') }}">
                <div class="admin-help-text">Comma separated.</div>
            </div>
            <div class="admin-form-group admin-form-group--full">
                <label class="admin-label" for="deliverables">Deliverables</label>
                <textarea id="deliverables" name="deliverables" rows="4" class="admin-textarea">{{ old('deliverables', $quotation->deliverables) }}</textarea>
            </div>
            <div class="admin-form-group admin-form-group--full">
                <label class="admin-label" for="project_timeline">Project Timeline</label>
                <textarea id="project_timeline" name="project_timeline" rows="3" class="admin-textarea">{{ old('project_timeline', $quotation->project_timeline) }}</textarea>
            </div>
            <div class="admin-form-group">
                <label class="admin-label" for="revisions_included">Revisions Included</label>
                <input type="number" min="0" id="revisions_included" name="revisions_included" class="admin-input"
                       value="{{ old('revisions_included', $quotation->revisions_included) }}">
            </div>
            <div class="admin-form-group">
                <label class="admin-label" for="support_period">Support Period</label>
                <input type="text" id="support_period" name="support_period" class="admin-input" placeholder="3 months free support"
                       value="{{ old('support_period', $quotation->support_period) }}">
            </div>
            <div class="admin-form-group admin-form-group--full">
                <label class="admin-label" for="hosting_domain_info">Hosting & Domain Information</label>
                <textarea id="hosting_domain_info" name="hosting_domain_info" rows="3" class="admin-textarea">{{ old('hosting_domain_info', $quotation->hosting_domain_info) }}</textarea>
            </div>
            <div class="admin-form-group admin-form-group--full">
                <label class="admin-label" for="third_party_charges">Third-Party Charges</label>
                <textarea id="third_party_charges" name="third_party_charges" rows="3" class="admin-textarea">{{ old('third_party_charges', $quotation->third_party_charges) }}</textarea>
            </div>
            <div class="admin-form-group admin-form-group--full">
                <label class="admin-label" for="client_responsibilities">Client Responsibilities</label>
                <textarea id="client_responsibilities" name="client_responsibilities" rows="3" class="admin-textarea">{{ old('client_responsibilities', $quotation->client_responsibilities) }}</textarea>
            </div>
            <div class="admin-form-group admin-form-group--full">
                <label class="admin-label" for="terms_conditions">Terms & Conditions</label>
                <textarea id="terms_conditions" name="terms_conditions" rows="6" class="admin-textarea">{{ old('terms_conditions', $quotation->terms_conditions) }}</textarea>
            </div>
            <div class="admin-form-group admin-form-group--full">
                <label class="admin-label" for="internal_notes">Internal Notes</label>
                <textarea id="internal_notes" name="internal_notes" rows="3" class="admin-textarea">{{ old('internal_notes', $quotation->internal_notes) }}</textarea>
                <div class="admin-help-text">Never shown to the client or included in the printed quotation.</div>
            </div>
        </div>
    </div>
</div>

<div class="admin-form-actions">
    <button type="submit" class="admin-btn admin-btn--primary"><i class="fas fa-check"></i> Save Quotation</button>
    <a href="{{ route('admin.quotations.index') }}" class="admin-btn admin-btn--ghost">Cancel</a>
</div>
