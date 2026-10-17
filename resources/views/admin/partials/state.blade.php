{{-- Publication state of one language: $row (CmsContent), optional $short. --}}
@if (!$row->exists)
    <span class="a-badge a-badge-off">Not written</span>
@elseif ($row->published === null)
    <span class="a-badge a-badge-off">Not published</span>
@elseif ($row->draft !== null)
    <span class="a-badge a-badge-draft">{{ ($short ?? false) ? 'Changes' : 'Unpublished changes' }}</span>
@else
    <span class="a-badge a-badge-live">Published</span>
@endif
