<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{--        <link href="/css/app.css" rel="stylesheet">--}}
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Pdf multi view</title>

    <style>
        #print { display: none; }
        @media print {
            #print { display: block; }
            #no-print { display: none; }
        }
    </style>
</head>
<body id="body">
<div id="print" class="text-3xl mx-auto text-red-500">Do not even try</div>
<div id="no-print" class="container flex flex-col mx-auto h-screen">
    <div class="flex-1 flex flex-col">
        <h2 class="text-3xl font-bold text-center">Let's start to compare pdfs</h2>
        <div class="py-1">
            <span id="timer" class="p-1 border-2"></span>
        </div>
        <div class="flex flex-1">
            <div class="flex-1 flex flex-col border-2">
                @include('components.pdf', ['index' => 1])
                @include('components.pdf', ['index' => 2])
            </div>
            <div id="pdf-list" class="w-48 border-2">
                <div class="flex justify-between items-center pl-1 border-b-2">
                    <div>
                        <span>PDF list</span>
                    </div>
                    <div class="flex">
                        <button type="button" class="border-2 p-1 hover:bg-gray-200 hover:cursor-pointer">
                            <span>Add</span></button>
                        <button type="button" class="border-2 p-1 hover:bg-gray-200 hover:cursor-pointer">
                            <span>Delete</span></button>
                    </div>
                </div>
                <ul>
                    @foreach($pdfList as $pdf)
                        <li class="border-b-2 p-2 text-lg hover:cursor-pointer hover:bg-gray-500 hover:text-gray-200">{{$pdf->original_file_name . '.pdf'}}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="flex flex-col">
        <div class="flex grow border-2">
            <textarea class="w-full h-full resize-none p-2" placeholder="Write here..."></textarea>
        </div>
        <div class="text-center">
            <button type="button" class="border-2 py-1 px-4">Save</button>
        </div>
    </div>
</div>
<script>
    window.onload = function () {
        const now = new Date();
        const DURATION_HOUR = 1;
        const INTERVAL_MS = 1000;
        const MINUTE_TO_MS = 60 * 1000;

        let endedAt = new Date(localStorage.getItem('ended_at'));
        let afterAnHour = new Date();
        afterAnHour.setHours(now.getHours() + DURATION_HOUR);

        if (endedAt < now || endedAt > afterAnHour) {
            endedAt = afterAnHour;
            localStorage.setItem('ended_at', endedAt.toISOString());
        }

        let clock = setInterval(timer, INTERVAL_MS);

        function timer() {
            document.getElementById('timer').innerHTML = getRemainingTimeString(endedAt);
        }

        function getRemainingTimeString(endedAt) {
            const now = new Date();
            const endingTime = new Date(endedAt);
            if (now > endingTime) {
                clearInterval(clock);
                return 'Time is over';
            }

            const timeDiff = endingTime.getTime() - now.getTime();
            const minLeft = Math.floor(timeDiff / MINUTE_TO_MS);
            const secondLeft = Math.floor((timeDiff % MINUTE_TO_MS) / 1000);
            return `${twoDigitNumber(minLeft)}:${twoDigitNumber(secondLeft)}`;
        }

        function twoDigitNumber(number) {
            return ("0" + number).slice(-2);
        }

        window.onbeforeprint = function(event) {
            alert('Print is not allowed');
        }
    };
</script>
</body>
</html>
