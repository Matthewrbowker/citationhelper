<x-base>
    <x-slot name="title">Home</x-slot>
    <x-slot name="content">
        <h1>CitationHelper</h1>
        <ul>
            @foreach($categories as $style => $categoriesInStyle)
                @foreach($categoriesInStyle as $class=> $category)
                    <li><a href="{{ route('fix.index', ['style'=>$style, 'category' => $class]) }}">{{ $category }}</a></li>
                @endforeach
            @endforeach
        </ul>
    </x-slot>
</x-base>
