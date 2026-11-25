<div class="admin-repeater__row" data-repeater-row
     style="grid-template-columns: minmax(0,2fr) minmax(0,2fr) 110px minmax(0,1.5fr) 40px;">
    <div>
        <input type="text" name="milestones[{{ $index }}][title]" class="admin-input" placeholder="Milestone title"
               value="{{ $milestone['title'] ?? '' }}">
    </div>
    <div>
        <input type="text" name="milestones[{{ $index }}][description]" class="admin-input" placeholder="Description"
               value="{{ $milestone['description'] ?? '' }}">
    </div>
    <div>
        <input type="number" step="0.01" min="0" max="100" name="milestones[{{ $index }}][percentage]"
               class="admin-input" placeholder="%" value="{{ $milestone['percentage'] ?? '' }}">
    </div>
    <div>
        <input type="text" name="milestones[{{ $index }}][due_condition]" class="admin-input"
               placeholder="Due on signing / delivery" value="{{ $milestone['due_condition'] ?? '' }}">
    </div>
    <div>
        <button type="button" class="admin-icon-btn admin-icon-btn--danger" data-repeater-remove title="Remove milestone">
            <i class="fas fa-trash"></i>
        </button>
    </div>
</div>
