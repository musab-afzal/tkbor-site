<div class="admin-repeater__row" data-repeater-row>
    <div>
        <select class="admin-select" data-pricing-picker>
            <option value="">— Pick from price book —</option>
            @foreach ($pricingItems as $pricingItem)
                <option value="{{ $pricingItem->id }}"
                        data-name="{{ $pricingItem->name }}"
                        data-price="{{ $pricingItem->default_price }}"
                        data-days="{{ $pricingItem->estimated_days }}"
                        data-cost="{{ $pricingItem->internal_cost }}"
                        data-description="{{ $pricingItem->short_description }}"
                        data-features="{{ $pricingItem->detailed_features }}"
                        @selected((string) ($item['pricing_item_id'] ?? '') === (string) $pricingItem->id)>
                    {{ $pricingItem->category?->name ? $pricingItem->category->name.' / ' : '' }}{{ $pricingItem->name }}
                </option>
            @endforeach
        </select>

        <input type="hidden" name="items[{{ $index }}][pricing_item_id]" value="{{ $item['pricing_item_id'] ?? '' }}"
               data-line-pricing-id>

        <input type="text" name="items[{{ $index }}][name]" class="admin-input" placeholder="Module or service name"
               value="{{ $item['name'] ?? '' }}" data-line-name required>

        <input type="text" name="items[{{ $index }}][description]" class="admin-input" placeholder="Short description"
               value="{{ $item['description'] ?? '' }}" data-line-description>

        <textarea name="items[{{ $index }}][features]" class="admin-textarea" rows="2"
                  placeholder="Features, one per line" data-line-features>{{ $item['features'] ?? '' }}</textarea>

        <div class="admin-repeater__flags">
            <select name="items[{{ $index }}][section]" class="admin-select" style="max-width:190px">
                @foreach (\App\Models\Quotation::SECTIONS as $value => $label)
                    <option value="{{ $value }}" @selected(($item['section'] ?? 'main') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <label>
                <input type="checkbox" name="items[{{ $index }}][is_optional]" value="1"
                       @checked($item['is_optional'] ?? false) data-line-optional> Optional
            </label>
            <label>
                <input type="checkbox" name="items[{{ $index }}][is_free]" value="1"
                       @checked($item['is_free'] ?? false) data-line-free> Free
            </label>
        </div>
    </div>

    <div>
        <input type="number" step="0.01" min="0" name="items[{{ $index }}][quantity]" class="admin-input"
               value="{{ $item['quantity'] ?? 1 }}" data-line-qty required>
    </div>

    <div>
        <input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_price]" class="admin-input"
               value="{{ $item['unit_price'] ?? 0 }}" data-line-price required>
        @if ($canSeeCost)
            <input type="number" step="0.01" min="0" name="items[{{ $index }}][internal_cost]" class="admin-input"
                   value="{{ $item['internal_cost'] ?? '' }}" placeholder="Internal cost" data-line-cost>
        @endif
    </div>

    <div>
        <input type="number" min="0" name="items[{{ $index }}][estimated_days]" class="admin-input"
               value="{{ $item['estimated_days'] ?? '' }}" data-line-days>
    </div>

    <div>
        <strong data-line-total>0.00</strong>
    </div>

    <div>
        <button type="button" class="admin-icon-btn admin-icon-btn--danger" data-repeater-remove title="Remove item">
            <i class="fas fa-trash"></i>
        </button>
    </div>
</div>
