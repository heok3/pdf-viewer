<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{--        <link href="/css/app.css" rel="stylesheet">--}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>    <title>Pdf multiple view</title>

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
                @foreach($selectedPdfIds as $selectedId)
                    @include('components.pdf', [
                        'index' => $selectedId,
                        'pdf' => $pdfList->find($selectedId),
                    ])
                @endforeach
            </div>
            <div id="pdf-list" class="flex flex-col w-1/4 border-2">
                <div class="flex flex-none justify-between items-center pl-1 border-b-2">
                    <div>
                        <span>PDF list</span>
                    </div>
                    <div class="flex">
                        <form id="form_open" method="GET" action="/" class="hidden"></form>
                        <button
                            id="open"
                            type="submit"
                            form="form_open"
                            class="border-2 p-1 hover:bg-gray-200 hover:cursor-pointer"
                        > OPEN</button>
                        <form
                            id="form_upload"
                            method="POST"
                            action="/pdfs"
                            class="hidden"
                            enctype="multipart/form-data"
                        >
                            @csrf
                            <input
                                id="upload_file"
                                type="file"
                                name="pdf_file"
                                required
                                accept="application/pdf"
                            />
                       </form>
                        <label
                            id="upload"
                            for="upload_file"
                            type="button"
                            class="border-2 p-1 hover:bg-gray-200 hover:cursor-pointer"
                        > UPLOAD</label>
                    </div>
                </div>
                <div class="flex flex-col flex-1">
                    <p
                        class="flex-none text-center text-gray-200 bg-indigo-400 border-b-2 border-gray-200"
                    >Multiple selection: ctrl + left click</p>
                    <div class="flex-1">
                        <select name="pdf_files[]" form="form_open"
                            class="h-full w-full" size="1" multiple="multiple">
                            @foreach($pdfList as $pdf)
                                <option
                                    class="border-b-2 p-2 text-lg hover:cursor-pointer hover:bg-gray-500 hover:text-gray-200"
                                    value="{{$pdf->id}}"
                                    {{in_array($pdf->id, $selectedPdfIds) ? 'selected' : ''}}
                                >{{$pdf->original_file_name . '.pdf'}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex flex-col">
        <div class="flex grow border-2">
            <textarea id="note" class="w-full h-full resize-none p-2" placeholder="Write here..."></textarea>
        </div>
        <div class="text-center">
            <button id="save_note" type="button" class="border-2 py-1 px-4">Save</button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        const fileIds = {{ Illuminate\Support\Js::from($selectedPdfIds) }};
        loadNote();

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
            $('#timer').text(getRemainingTimeString(endedAt));
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

        $('#upload_file').change(function (event) {
            $('#form_upload').submit();
        })

        $('#save_note').click(function (event) {
            const url = '/note';
            const note = $('#note').val();
            const data = {
                text: note,
                file_ids: fileIds,
            };

            if (Array.isArray(fileIds) && fileIds.length === 0) {
                alert('Open some files first!');
                return;
            }

            $.ajax({
                type: 'POST',
                url: url,
                data: JSON.stringify(data),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).fail(function (res) {
                alert('Cannot save your note. Contact me.');
            });
        })

        function loadNote() {
            let url = '/note';
            let data = {
                pdf_files: fileIds
            };
            $.ajax({
                type: 'GET',
                url: url,
                data: data,
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).done(function(payload) {
                $('#note').val(payload.note);
            }).fail(function (res) {
                alert('Cannot load your note. Contact me.');
            });
        }
    });
</script>
</body>
</html>
