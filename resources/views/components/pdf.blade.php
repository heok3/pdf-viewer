<div id="pdf-view" class="flex-1 flex flex-col border-2">
    <div id="control-buttons" class="flex-none flex justify-between p-0.5 border-b-2">
        <div>
            <button id="{{'zoom-in-' . $index}}" type="button" class="border-2 w-8 h-8 hover:border-4"><span>&#10010;</span></button>
            <button id="{{'zoom-out-' . $index}}" type="button" class="border-2 w-8 h-8 rotate-90 hover:border-4"><span>&#10073;</span></button>
        </div>
        <div>
            <button id="{{'prev-' . $index}}" type="button" class="border-2 h-8 hover:border-4">prev</button>
            <button id="{{'next-' . $index}}" type="button" class="border-2 h-8 hover:border-4">next</button>
            <label>
                Page:
                <input
                    id="{{'page_num-' . $index}}"
                    type="number"
                    step="1"
                    class="w-12"
                    min="1"
                    onChange="renderPage(parseInt(this.value))"
                /> / <span id="{{'page_count-' . $index}}"></span>
            </label>
        </div>
    </div>
    <div id="{{'pdf-screen-' . $index}}"
         class="relative overflow-auto flex-1 items-center text-center border-0 hover:bg-gray-200 hover:cursor-move">
        <canvas id="{{'pdf-canvas-' . $index}}" class="relative overflow-auto border-2 mx-auto"></canvas>
    </div>
</div>
<script src="//mozilla.github.io/pdf.js/build/pdf.js"></script>
<script>
    {
        const MIN_SCALE = 0.25;
        const MAX_SCALE = 4;
        const SCALE_STEP = 2;

        // If absolute URL from the remote server is provided, configure the CORS
        // header on that server.
        var url = 'https://raw.githubusercontent.com/mozilla/pdf.js/ba2edeae/web/compressed.tracemonkey-pldi-09.pdf';

        // Loaded via <script> tag, create shortcut to access PDF.js exports.
        var pdfjsLib = window['pdfjs-dist/build/pdf'];

        // The workerSrc property shall be specified.
        pdfjsLib.GlobalWorkerOptions.workerSrc = '//mozilla.github.io/pdf.js/build/pdf.worker.js';

        let pdfDoc = null,
            pageNum = 1,
            pageRendering = false,
            pageNumPending = null,
            scale = 1,
            canvas = document.getElementById('{{'pdf-canvas-' . $index}}'),
            ctx = canvas.getContext('2d');

        /**
         * Get page info from document, resize canvas accordingly, and render page.
         * @param num Page number.
         */
        function renderPage(num) {
            pageRendering = true;
            // Using promise to fetch the page
            pdfDoc.getPage(num).then(function (page) {
                let desiredWidth = document.getElementById('{{'pdf-screen-' . $index}}').clientWidth;
                let desiredHeight = document.getElementById('{{'pdf-screen-' . $index}}').clientHeight;
                let viewport = page.getViewport({scale: 1,});
                let widthScale = desiredWidth / viewport.width;
                let heightScale = desiredHeight / viewport.height;
                let scaledViewport = page.getViewport({scale: widthScale * scale,});
                if (widthScale > heightScale) {
                    scaledViewport = page.getViewport({scale: heightScale * scale,});
                }

                // Prepare canvas using PDF page dimensions
                canvas.height = scaledViewport.height;
                canvas.width = scaledViewport.width;

                // Render PDF page into canvas context
                let renderContext = {
                    canvasContext: ctx,
                    viewport: scaledViewport
                };
                let renderTask = page.render(renderContext);

                // Wait for rendering to finish
                renderTask.promise.then(function () {
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        // New page rendering is pending
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                });

            });

            // Update page counters
            document.getElementById('{{'page_num-' . $index}}').value = num;
        }

        /**
         * If another page rendering in progress, waits until the rendering is
         * finised. Otherwise, executes rendering immediately.
         */
        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        /**
         * Displays previous page.
         */
        function onPrevPage() {
            if (pageNum <= 1) {
                return;
            }
            pageNum--;
            queueRenderPage(pageNum);
        }

        document.getElementById('{{'prev-' . $index}}').addEventListener('click', onPrevPage);

        /**
         * Displays next page.
         */
        function onNextPage() {
            if (pageNum >= pdfDoc.numPages) {
                return;
            }
            pageNum++;
            queueRenderPage(pageNum);
        }

        document.getElementById('{{'next-' . $index}}').addEventListener('click', onNextPage);

        function onZoomIn() {
            if(scale >= MAX_SCALE) {
                return;
            }

            scale = scale * SCALE_STEP;
            queueRenderPage(pageNum);
        }

        document.getElementById('{{'zoom-in-' . $index}}').addEventListener('click', onZoomIn);

        function onZoomOut() {
            if(scale <= MIN_SCALE) {
                return;
            }

            scale = scale / SCALE_STEP;
            queueRenderPage(pageNum);
        }

        document.getElementById('{{'zoom-out-' . $index}}').addEventListener('click', onZoomOut);

        /**
         * Asynchronously downloads PDF.
         */
        pdfjsLib.getDocument(url).promise.then(function (pdfDoc_) {
            pdfDoc = pdfDoc_;
            document.getElementById('{{'page_count-' . $index}}').textContent = pdfDoc.numPages;
            document.getElementById('{{'page_num-' . $index}}').max = pdfDoc.numPages;

            // Initial/first page rendering
            renderPage(pageNum);
            setCanvasContainerSize();
        });

        function setCanvasContainerSize() {
            let element = document.getElementById('{{'pdf-screen-' . $index}}');
            let fixedWidth = element.clientWidth;
            let fixedHeight = element.clientHeight;
            element.style.height = fixedHeight.toString() + "px";
            element.style.width = fixedWidth.toString() + "px";
            document.getElementById('{{'pdf-screen-' . $index}}').classList.remove("flex-1");
        }
    }
</script>
