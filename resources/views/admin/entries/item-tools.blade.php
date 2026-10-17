<span class="a-item-tools">
    <button class="a-btn a-btn-icon" type="button" data-move="up" title="Move up">@include('admin.partials.icon', ['name' => 'up'])<span class="visually-hidden">Move up</span></button>
    <button class="a-btn a-btn-icon" type="button" data-move="down" title="Move down">@include('admin.partials.icon', ['name' => 'down'])<span class="visually-hidden">Move down</span></button>
    @if ($removable)
        <button class="a-btn a-btn-icon a-remove" type="button" data-remove title="Remove">@include('admin.partials.icon', ['name' => 'trash'])<span class="visually-hidden">Remove</span></button>
    @else
        <span class="a-btn a-btn-icon" title="Required: this cannot be removed" aria-label="Required">@include('admin.partials.icon', ['name' => 'lock'])</span>
    @endif
</span>
