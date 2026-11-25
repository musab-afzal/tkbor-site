<div class="admin-repeater__row" data-repeater-row
     style="grid-template-columns: minmax(0,2.4fr) 90px 130px 130px 40px;">
    <div>
        <select class="admin-select" data-pricing-picker>
            <option value="">— Pick from price book —</option>
            @foreach ($pricingItems as $pricingItem)
                <option value="{{ $pricingItem->id }}"
                        data-name="{{ $pricingItem->name }}"
                        data-price="{{ $pricingItem->default_price }}"
                        data-description="{{ $pricingItem->short_description }}"
                        @selected((string) ($item['pricing_item_id'] ?? '') === (string) $pricingItem->id)>
                    {{ $pricingItem->name }}
                </option>
            @endforeach
        </select>

        <input type="hidden" name="items[{{ $index }}][pricing_item_id]" value="{{ $item['pricing_item_id'] ?? '' }}" data-line-pricing-id>

        <input type="text" name="items[{{ $index }}][name]" class="admin-input" placeholder="Item name"
               value="{{ $item['name'] ?? '' }}" data-line-name required>

        <input type="text" name="items[{{ $index }}][description]" class="admin-input" placeholder="Description"
               value="{{ $item['description'] ?? '' }}" data-line-description>
    </div>

    <div>
        <input type="number" step="0.01" min="0" name="items[{{ $index }}][quantity]" class="admin-input"
               value="{{ $item['quantity'] ?? 1 }}" data-line-qty required>
    </div>

    <div>
        <input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_price]" class="admin-input"
               value="{{ $item['unit_price'] ?? 0 }}" data-line-price required>
    </div>

    <div><strong data-line-total>0.00</strong></div>

    <div>
        <button type="button" class="admin-icon-btn admin-icon-btn--danger" data-repeater-remove title="Remove item">
            <i class="fas fa-trash"></i>
        </button>
    </div>
</div>
