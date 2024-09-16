<x-slot name="title">{{$articleTitle}} in {{$category}}</x-slot>
<H1>[<a href="{{route("index")}}">Home</a>]{{ $articleTitle }} [<a href="https://en.wikipedia.org/wiki/{{$articleTitle}}" target="Blank">Article</a>] &middot; [<a href="https://en.wikipedia.org/wiki/Category:{{$category}}" target="_blank">{{$category}}</a>]</H1>

@if(count($output) > 0)
    <ul>
        @foreach($output as $data)
            <li>{{ $data['content'] }} => {{ $data['possibleFix'] }}
                @if(isset($data["citation"]["parameters"]["url"]))
                    => [<a href="{{$data["citation"]["parameters"]["url"]}}" target="_blank">link</a>]
                    @if(isset($data["citation"]["parameters"]["archiveurl"]))
                        => [<a href="{{$data["citation"]["parameters"]["archiveurl"]}}" target="_blank">archive</a>]
                    @endif
                    @if(isset($data["citation"]["parameters"]["archive-url"]))
                        => [<a href="{{$data["citation"]["parameters"]["archive-url"]}}" target="_blank">archive</a>]
                    @endif
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
