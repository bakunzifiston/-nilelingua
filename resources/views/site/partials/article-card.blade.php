<a class="nll-article-card" href="/knowledge-center/{{ $key }}" data-reveal data-topic="{{ $article['category'] }}" data-search="{{ \Illuminate\Support\Str::lower($article['title'].' '.$article['summary'].' '.$article['category']) }}">
    <span class="nll-article-meta">{{ $article['category'] }} • {{ $article['read_time'] }} • Nile Lingua Lab</span>
    <h3>{{ $article['title'] }}</h3>
    <p>{{ $article['summary'] ?? $article['intro'] }}</p>
    <span class="nll-link">{{ $article['link_label'] ?? __('Read the article') }}<span class="visually-hidden">: {{ $article['title'] }}</span> <span class="nll-arrow" aria-hidden="true">→</span></span>
</a>
