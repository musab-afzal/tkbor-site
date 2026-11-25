@include('admin.partials.validation-summary')

@php
    $itemRows = old('items', $invoice->exists
        ? $invoice->items->map(fn ($item) => [
            'pricing_item_id' => $item->pricing_item_id,
            'name' => $item->name,
            'description' => $item->description,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
        ])->all()
        : $prefillItems);

    if (empty($itemRows)) {
        $itemRows = [['quantity' => 1, 'unit_price' => 0]];
    }
@endphp

<div class="admin-card">
    <div class="admin-card__header"><h2>Invoice Details</h2></div>
    <div class="admin-card__body">
        <div class="admin-form-grid">
            <div class="admin-form-group">
                <label class="admin-label" for="client_id">Client <span class="req">*</span></label>
                <select id="client_id" name="client_id" class="admin-select @error('client_id') is-invalid @enderror" required>
                    <option value="">Select client...</option>
                    @foreach ($clients as $id => $name)
                        <option value="{{ $id }}" @selected((string) old('client_id', $invoice->client_id) === (string) $id)>{{ $name }}</option>
                    @endforeach
                </select>
                @error('client_id') <div class="admin-error-text">{{ $message }}</div> @enderror
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="project_id">Project</label>
                <select id="project_id" name="project_id" class="admin-select">
                    <option value="">None</option>
                    @foreach ($projects as $id => $name)
                        <option value="{{ $id }}" @selected((string) old('project_id', $invoice->project_id) === (string) $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="quotation_id">Quotation Reference</label>
                <select id="quotation_id" name="quotation_id" class="admin-select">
                    <option value="">None</option>
                    @foreach ($quotations as $id => $name)
                        <option value="{{ $id }}" @selected((string) old('quotation_id', $invoice->quotation_id) === (string) $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="milestone_id">Payment Milestone</label>
                <select id="milestone_id" name="milestone_id" class="admin-select">
                    <option value="">None</option>
                    @foreach ($milestones as $id => $name)
                        <option value="{{ $id }}" @selected((string) old('milestone_id', $invoice->milestone_id) === (string) $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="invoice_date">Invoice Date <span class="req">*</span></label>
                <input type="date" id="invoice_date" name="invoice_date" class="admin-input" required
                       value="{{ old('invoice_date', $invoice->invoice_date?->format('Y-m-d')) }}">
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="due_date">Due Date</label>
                <input type="date" id="due_date" name="due_date" class="admin-input"
                       value="{{ old('due_date', $invoice->due_date?->format('Y-m-d')) }}">
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="currency">Currency <span class="req">*</span></label>
                <select id="currency" name="currency" class="admin-select" required>
                    @foreach ($currencies as $code => $label)
                        <option value="{{ $code }}" @selected(old('currency', $invoice->currency) === $code)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-form-group">
                <label class="admin-label" for="status">Status <span class="req">*</span></label>
                <select id="status" name="status" class="admin-select" required>
                    @foreach (\App\Models\Invoice::STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $invoice->status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <div class="admin-help-text">Paid, partial and overdue are set automatically from recorded payments.</div>
            </div>
        </div>
    </div>
</div>

<div class="admin-card" data-repeater>
    <div class="admin-card__header">
        <h2>Invoice Items</h2>
        <button type="button" class="admin-btn admin-btn--ghost admin-btn--sm" data-repeater-add><i class="fas fa-plus"></i> Add Item</button>
    </div>
    <div class="admin-card__body">
        @error('items') <div class="admin-error-text" style="margin-bottom:10px">{{ $message }}</div> @enderror

        <div class="admin-scroll-x">
            <div class="admin-repeater__head" style="grid-template-columns: minmax(0,2.4fr) 90px 130px 130px 40px;">
                <div>Item</div><div>Qty</div><div>Unit Price</div><div>Line Total</div><div></div>
            </div>

            <div data-repeater-rows>
                @foreach ($itemRows as $index => $item)
                    @include('admin.invoices._item-row', ['index' => $index, 'item' => $item])
                @endforeach
            </div>
        </div>

        <template data-repeater-template>
            @include('admin.invoices._item-row', ['index' => '__INDEX__', 'item' => ['quantity' => 1, 'unit_price' => 0]])
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
                    <option value="percentage" @selected(old('discount_type', $invoice->discount_type) === 'percentage')>Percentage (%)</option>
                    <option value="fixed" @selected(old('discount_type', $invoice->discount_type) === 'fixed')>Fixed Amount</option>
                </select>
            </div>
            <div class="admin-form-group">
                <label class="admin-label" for="discount_value">Discount Value</label>
                <input type="number" step="0.01" min="0" id="discount_value" name="discount_value" class="admin-input"
                       value="{{ old('discount_value', $invoice->discount_value ?? 0) }}">
            </div>
            <div class="admin-form-group">
                <label class="admin-label" for="tax_percentage">Tax (%)</label>
                <input type="number" step="0.01" min="0" max="100" id="tax_percentage" name="tax_percentage" class="admin-input"
                       value="{{ old('tax_percentage', $invoice->tax_percentage ?? 0) }}">
            </div>
        </div>

        <div class="admin-totals">
            <div class="admin-totals__row"><span>Subtotal</span><span data-total-subtotal>0.00</span></div>
            <div class="admin-totals__row"><span>Discount</span><span data-total-discount>0.00</span></div>
            <div class="admin-totals__row"><span>Tax</span><span data-total-tax>0.00</span></div>
            <div class="admin-totals__row admin-totals__row--grand"><span>Total</span><span data-total-grand>0.00</span></div>
        </div>
        <p class="admin-help-text" style="text-align:right">Totals are recalculated on the server when you save.</p>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card__header"><h2>Notes & Terms</h2></div>
    <div class="admin-card__body">
        <div class="admin-form-grid">
            <div class="admin-form-group admin-form-group--full">
                <label class="admin-label" for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="3" class="admin-textarea">{{ old('notes', $invoice->notes) }}</textarea>
            </div>
            <div class="admin-form-group admin-form-group--full">
                <label class="admin-label" for="terms">Terms</label>
                <textarea id="terms" name="terms" rows="4" class="admin-textarea">{{ old('terms', $invoice->terms) }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="admin-form-actions">
    <button type="submit" class="admin-btn admin-btn--primary"><i class="fas fa-check"></i> Save Invoice</button>
    <a href="{{ route('admin.invoices.index') }}" class="admin-btn admin-btn--ghost">Cancel</a>
</div>
