<H1>{{ $articleTitle }}</H1>

@if(count($output) > 0)
    @foreach($output as $data)
        {{ $data['content'] }} => {{ $data['possibleFix'] }}
    @endforeach

    <H2>Fixed Wiki Markup</H2>
    <pre>
@foreach($wikitext as $line)
{{ $line }}
@endforeach
    </pre>
@else
    No errors found on this page.
@endif
