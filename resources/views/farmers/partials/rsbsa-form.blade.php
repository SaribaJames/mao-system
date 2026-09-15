{{-- On-screen facsimile of the RSBSA form, built by overlaying the farmer's
     data on top of the actual template images at the same coordinates the
     old PDF print feature used. View-only — nothing here is printable or
     exportable; that stays retired per DA/MAO's handwritten-form rule. --}}
@php
    $scale = 794 / 595; // pt -> px
    $pageW = 595 * $scale;
    $pageH = 893 * $scale;
@endphp

<style>
    .rsbsa-form-wrap {
        overflow-x: auto;
        padding-bottom: 8px;
    }

    .rsbsa-form-page {
        position: relative;
        width: {{ $pageW }}px;
        height: {{ $pageH }}px;
        margin: 0 auto 20px auto;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: top left;
        border: 1px solid #d8dfd8;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        font-family: Helvetica, Arial, sans-serif;
        color: #000;
    }

    .rsbsa-item {
        position: absolute;
        white-space: nowrap;
        font-weight: 700;
        line-height: 1;
    }

    .rsbsa-item.center {
        text-align: center;
    }

    .rsbsa-mark {
        position: absolute;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }

    .rsbsa-photo {
        position: absolute;
        object-fit: cover;
    }
</style>

<div class="rsbsa-form-wrap">
    @foreach(['page1' => 'page-1', 'page2' => 'page-2'] as $key => $imageName)
        <div class="rsbsa-form-page" style="background-image:url('{{ asset('images/rsbsa-form/' . $imageName . '.png') }}');">
            @foreach($overlay[$key] as $item)
                @php
                    $left = $item['x'] * $scale;
                    $top = $item['y'] * $scale;
                @endphp
                @if($item['type'] === 'text')
                    <span class="rsbsa-item" style="left:{{ $left }}px; top:{{ $top }}px; font-size:{{ $item['size'] * $scale }}px;">{{ $item['text'] }}</span>
                @elseif($item['type'] === 'digit')
                    <span class="rsbsa-item center" style="left:{{ $left }}px; top:{{ $top }}px; width:{{ 8 * $scale }}px; font-size:{{ $item['size'] * $scale }}px;">{{ $item['text'] }}</span>
                @elseif($item['type'] === 'mark')
                    <span class="rsbsa-mark" style="left:{{ $left }}px; top:{{ $top }}px; width:{{ $item['w'] * $scale }}px; height:{{ $item['h'] * $scale }}px; font-size:{{ $item['size'] * $scale }}px;">&#10005;</span>
                @elseif($item['type'] === 'photo')
                    <img src="{{ $item['src'] }}" class="rsbsa-photo" style="left:{{ $left }}px; top:{{ $top }}px; width:{{ $item['w'] * $scale }}px; height:{{ $item['h'] * $scale }}px;">
                @endif
            @endforeach
        </div>
    @endforeach
</div>
