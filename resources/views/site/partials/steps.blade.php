{{-- Numbered process timeline. Expects $steps: [['title' => ..., 'body' => ...], ...] --}}
<ol class="nll-steps" style="--steps: {{ count($steps) }}">
    @foreach ($steps as $step)
        <li data-reveal>
            <span class="nll-step-marker" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
            <h3>{{ $step['title'] }}</h3>
            <p>{{ $step['body'] }}</p>
        </li>
    @endforeach
</ol>
