<H1>{{ $articleTitle }} [<a href="https://en.wikipedia.org/wiki/{{$articleTitle}}" target="Blank">Article</a>]</H1>

@if(count($output) > 0)
    <ul>
        @foreach($output as $data)
            <li>{{ $data['content'] }} => {{ $data['possibleFix'] }}
                @if(isset($data["citation"]["parameters"]["url"]))
                    => <a href="{{$data["citation"]["parameters"]["url"]}}" target="_blank">[link]</a>
                @endif
            </li>
        @endforeach
    </ul>

    <H2>Fixed Wiki Markup</H2>
<pre>
{{ $wikitext }}
</pre>
@else
    No errors found on this page.
@endif
