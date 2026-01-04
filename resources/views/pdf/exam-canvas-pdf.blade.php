<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>{{ $exam->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');
        
        body { margin: 0; padding: 0; font-family: 'Roboto', sans-serif; background: #525659; }

        .page {
            width: 210mm;
            height: 297mm;
            background: white;
            position: relative;
            overflow: hidden;
            margin: 0 auto;
            page-break-after: always;
        }
        
        .page:last-child { page-break-after: avoid; }

        .element {
            position: absolute;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            overflow: visible; 
            line-height: 1.4;
        }

        .options-list { list-style-type: none; padding: 0; margin: 0; }
        .options-list li { margin-bottom: 4px; }
    </style>
</head>
<body>

    @php $questionCounter = 1; @endphp

    @foreach($pages as $pageNum => $items)
        <div class="page">
            
            @foreach($items as $item)

                @php
                    $styleString = "";
                    if(isset($item['styles'])) {
                        $s = $item['styles'];
                        $styleString .= "color: " . ($s['color'] ?? 'black') . ";";
                        $styleString .= "background-color: " . ($s['backgroundColor'] ?? 'transparent') . ";";
                        $styleString .= "font-size: " . ($s['fontSize'] ?? 14) . "px;";
                        $styleString .= "font-weight: " . ($s['fontWeight'] ?? 'normal') . ";";
                        $styleString .= "text-align: " . ($s['textAlign'] ?? 'left') . ";";
                        $styleString .= "border-width: " . ($s['borderWidth'] ?? 0) . "px;";
                        $styleString .= "border-color: " . ($s['borderColor'] ?? 'transparent') . ";";
                        $styleString .= "border-style: solid;";
                        $styleString .= "border-radius: " . ($s['borderRadius'] ?? 0) . "px;";
                        $styleString .= "z-index: " . ($s['zIndex'] ?? 1) . ";";
                    }
                    
             
                    $positionStyle = "left: {$item['x']}px; top: {$item['y']}px; width: {$item['w']}px; height: {$item['h']}px;";
                    
             
                    $isQuestion = in_array($item['type'], ['multiple_choice', 'open_ended', 'true_false']);
                @endphp

                <div class="element" style="{{ $positionStyle }} {{ $styleString }}">
                    
             
                    @if($item['type'] == 'header_block')
                        <div class="flex flex-col items-center justify-center h-full w-full text-center">
                            <h1 class="text-xl font-bold uppercase my-1 block">{{ $item['content']['title'] ?? '' }}</h1>
                            <span class="text-sm block">{{ $item['content']['faculty'] ?? '' }}</span>
                            <span class="text-xs font-semibold block">{{ $item['content']['term'] ?? '' }}</span>
                        </div>

                    @elseif($item['type'] == 'student_info')
                        <div class="grid grid-cols-2 gap-2 p-2 h-full w-full items-center text-sm">
                            <div><span class="font-bold">{{ $item['content']['label1'] ?? 'Adı Soyadı:' }}</span> {{ $item['content']['val1'] }}</div>
                            <div><span class="font-bold">{{ $item['content']['label2'] ?? 'Numara:' }}</span> {{ $item['content']['val2'] }}</div>
                            <div><span class="font-bold">{{ $item['content']['label3'] ?? 'İmza:' }}</span> {{ $item['content']['val3'] }}</div>
                            <div><span class="font-bold">{{ $item['content']['label4'] ?? 'Puan:' }}</span> {{ $item['content']['val4'] }}</div>
                        </div>

             
                    @elseif($item['type'] == 'image')
                        @php
                            $imgSrc = $item['content'];
                            if (str_contains($imgSrc, url('/'))) {
                                $relativePath = str_replace(url('/'), '', $imgSrc);
                                $imgSrc = public_path($relativePath);
                            }
                        @endphp
                        <img src="{{ $imgSrc }}" class="w-full h-full object-contain">

                    @elseif($item['type'] == 'multiple_choice')
                        <div class="w-full h-full p-2 flex flex-col">
                            <div class="flex justify-between items-start mb-2 w-full">
                                <div class="flex-1 pr-2">
                                    <span class="font-bold mr-1">{{ $questionCounter }}.</span>
                                    {!! nl2br($item['content']['question'] ?? '') !!}
                                </div>
                                @if(isset($item['content']['point']) && (string)$item['content']['point'] !== '')
                                    <span class="font-bold text-sm whitespace-nowrap">({{ $item['content']['point'] }}p)</span>
                                @endif
                            </div>

                 
                            <div class="pl-4 flex flex-col gap-1">
                                @if(isset($item['content']['options']) && is_array($item['content']['options']))
                                    @foreach($item['content']['options'] as $index => $opt)
                                        @php
                                            // 0 -> A, 1 -> B, 2 -> C ... (ASCII 65 = 'A')
                                            $letter = chr(65 + $index);
                                        @endphp
                                        <div class="flex items-start gap-2">
                                      
                                            <span class="font-bold min-w-[20px]">{{ $letter }})</span>
                                            
                                        
                                            <span>{{ $opt }}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        @php $questionCounter++; @endphp

                    @elseif($item['type'] == 'open_ended')
                        <div class="w-full h-full p-2 flex flex-col">
                            <div class="flex justify-between items-start mb-1 w-full">
                                <div class="flex-1 pr-2">
                                    <span class="font-bold mr-1">{{ $questionCounter }}.</span>
                                    {!! nl2br($item['content']['question'] ?? '') !!}
                                </div>
                              
                                @if(isset($item['content']['point']) && (string)$item['content']['point'] !== '')
                                    <span class="font-bold text-sm whitespace-nowrap">({{ $item['content']['point'] }}p)</span>
                                @endif
                            </div>
                  
                            <div class="flex-1 w-full mt-1 opacity-50" 
                                 style="background-image: linear-gradient(#999 1px, transparent 1px); background-size: 100% 1.5em;">
                            </div>
                        </div>
                        @php $questionCounter++; @endphp

            
                    @elseif($item['type'] == 'true_false')
                        <div class="w-full h-full p-2 flex justify-between items-center">
                        
                            <div class="flex-1 pr-4 flex items-center">
                                <span class="font-bold mr-1">{{ $questionCounter }}.</span>
                                <span>{!! nl2br($item['content']['question'] ?? '') !!}</span>
                            </div>
                            
                          
                            <div class="flex items-center gap-2">
                                @if(isset($item['content']['point']) && (string)$item['content']['point'] !== '')
                                    <span class="font-bold text-sm whitespace-nowrap">({{ $item['content']['point'] }}p)</span>
                                @endif
                                
                                <div class="px-2 py-1 font-bold min-w-[50px] text-center whitespace-nowrap">
                                    {{ $item['content']['format'] ?? '( D / Y )' }}
                                </div>
                            </div>
                        </div>
                        @php $questionCounter++; @endphp
                  
                    @elseif($item['type'] == 'fill_in_blanks')
                        <div class="w-full h-full p-2 flex flex-col">
                            <div class="flex justify-between items-start w-full">
                              
                                <div class="flex-1 pr-2">
                                    <span class="font-bold mr-1">{{ $questionCounter }}.</span>
                                    {!! nl2br($item['content']['question'] ?? '') !!}
                                </div>

                                @if(isset($item['content']['point']) && (string)$item['content']['point'] !== '')
                                    <span class="font-bold text-sm whitespace-nowrap ml-2">({{ $item['content']['point'] }}p)</span>
                                @endif
                            </div>
                        </div>
                    @php $questionCounter++; @endphp    

                    @else
                        <div class="w-full h-full flex items-center {{ isset($item['styles']['textAlign']) && $item['styles']['textAlign'] == 'center' ? 'justify-center' : 'justify-start' }}">
                            {{ is_string($item['content']) ? $item['content'] : ($item['content']['text'] ?? '') }}
                        </div>
                    @endif

                </div>
            @endforeach

        </div>
    @endforeach
</body>
</html>