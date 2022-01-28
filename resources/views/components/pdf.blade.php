<div id="pdf-view" class="flex-1 flex flex-col border-2">
    <div id="control-buttons" class="flex-none flex justify-between items-center p-0.5 border-b-2">
        <div>
            <button id="{{'zoom-in-' . $index}}" type="button" class="border-2 w-8 h-8 hover:border-4">
                <span>&#10010;</span></button>
            <button id="{{'zoom-out-' . $index}}" type="button" class="border-2 w-8 h-8 rotate-90 hover:border-4"><span>&#10073;</span>
            </button>
        </div>
        <div class="font-bold text-xl">{{$pdf->original_file_name}}</div>
        <div>
            <button id="{{'prev-' . $index}}" type="button" class="border-2 h-8 hover:border-4">prev</button>
            <button id="{{'next-' . $index}}" type="button" class="border-2 h-8 hover:border-4">next</button>
            <form id="{{'page_num_form-' . $index}}" class="inline-block">
                Page:
                <input
                    id="{{'page_num-' . $index}}"
                    type="number"
                    step="1"
                    class="w-12"
                    min="1"
                /> / <span id="{{'page_count-' . $index}}"></span>
            </form>
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
        class PdfModule {
            MIN_SCALE = 0.25;
            MAX_SCALE = 4;
            SCALE_STEP = 2;

            constructor(
                url,
                pdfjsLib,
                pdfDoc,
                pageNum,
                pageRendering,
                pageNumPending,
                scale,
                canvas,
                ctx,
                screenElement,
                pageNumElement,
            ) {
                this.url = url
                this.pdfjsLib = pdfjsLib;
                this.pdfDoc = pdfDoc;
                this.pageNum = pageNum;
                this.pageRendering = pageRendering;
                this.pageNumPending = pageNumPending;
                this.scale = scale;
                this.canvas = canvas;
                this.ctx = ctx;
                this.screenElement = screenElement;
                this.pageNumElement = pageNumElement;
            }

            /**
             * Get page info from document, resize canvas accordingly, and render page.
             * @param num Page number.
             */
            renderPage = (num) => {
                this.pageRendering = true;
                // Using promise to fetch the page
                this.pdfDoc.getPage(num).then(
                    (function (currentObject) {
                        return function (page) {
                            let containerWidth = currentObject.screenElement.clientWidth;
                            let containerHeight = currentObject.screenElement.clientHeight;
                            let viewport = page.getViewport({scale: 1,});
                            let widthScale = containerWidth / viewport.width;
                            let heightScale = containerHeight / viewport.height;
                            let scaledViewport = page.getViewport({scale: widthScale * currentObject.scale,});
                            if (widthScale > heightScale) {
                                scaledViewport = page.getViewport({scale: heightScale * currentObject.scale,});
                            }

                            // Prepare canvas using PDF page dimensions
                            currentObject.canvas.height = scaledViewport.height;
                            currentObject.canvas.width = scaledViewport.width;

                            // Render PDF page into canvas context
                            let renderContext = {
                                canvasContext: currentObject.ctx,
                                viewport: scaledViewport
                            };
                            let renderTask = page.render(renderContext);

                            // Wait for rendering to finish
                            renderTask.promise.then(
                                (function (currentObject) {
                                    return function () {
                                        currentObject.pageRendering = false;
                                        if (currentObject.pageNumPending !== null) {
                                            // New page rendering is pending
                                            currentObject.renderPage(currentObject.pageNumPending);
                                            currentObject.pageNumPending = null;
                                        }
                                    };
                                })(currentObject)
                            );

                        }
                    })(this)
                );
                // Update page counters
                pageNumElement.value = num;
            }

            /**
             * If another page rendering in progress, waits until the rendering is
             * finised. Otherwise, executes rendering immediately.
             */
            queueRenderPage = (num) => {
                if (this.pageRendering) {
                    this.pageNumPending = num;
                } else {
                    this.renderPage(num);
                }
            }

            /**
             * Displays previous page.
             */
            onPrevPage = e => {
                if (this.pageNum <= 1) {
                    alert('This is the first page.');
                    return;
                }
                this.pageNum--;
                this.queueRenderPage(this.pageNum);
            }

            /**
             * Displays next page.
             */
            onNextPage = e => {
                if (this.pageNum >= this.pdfDoc.numPages) {
                    alert('This is the last page.');
                    return;
                }
                this.pageNum++;
                this.queueRenderPage(this.pageNum);
            }

            onZoomIn = e => {
                if (this.scale >= this.MAX_SCALE) {
                    alert('This is the biggest view.');
                    return;
                }

                this.scale = this.scale * this.SCALE_STEP;
                this.queueRenderPage(this.pageNum);
            }

            onZoomOut = e => {
                if (this.scale <= this.MIN_SCALE) {
                    alert('This is the smallest view.');
                    return;
                }

                this.scale = this.scale / this.SCALE_STEP;
                this.queueRenderPage(this.pageNum);
            }

            setCanvasContainerSize = e => {
                let element = this.screenElement;
                let fixedWidth = element.clientWidth;
                let fixedHeight = element.clientHeight;
                element.style.height = fixedHeight.toString() + "px";
                element.style.width = fixedWidth.toString() + "px";
                this.screenElement.classList.remove("flex-1");
            }

            onPageNumberInput = (event) => {
                event.preventDefault();
                let pageNum = parseInt(this.pageNumElement.value);
                if (pageNum) {
                    pdfModule.renderPage(parseInt(this.pageNumElement.value));
                    this.pageNum = pageNum;
                    return;
                }

                alert(`Please enter page from 1 to ${this.pdfDoc.numPages}`);
            }
        }

        // If absolute URL from the remote server is provided, configure the CORS
        // header on that server.
        let url = '{{asset('storage/' . $pdf->unique_file_name . '.pdf')}}';

        // Loaded via <script> tag, create shortcut to access PDF.js exports.
        let pdfjsLib = window['pdfjs-dist/build/pdf'];

        // The workerSrc property shall be specified.
        pdfjsLib.GlobalWorkerOptions.workerSrc = '//mozilla.github.io/pdf.js/build/pdf.worker.js';

        let pdfDoc = null,
            pageNum = 1,
            pageRendering = false,
            pageNumPending = null,
            scale = 1,
            canvas = document.getElementById('{{'pdf-canvas-' . $index}}'),
            ctx = canvas.getContext('2d');

        let screenElement = document.getElementById('{{'pdf-screen-' . $index}}');
        let pageNumElement = document.getElementById('{{'page_num-' . $index}}');

        let pdfModule = new PdfModule(
            url, pdfjsLib, pdfDoc, pageNum, pageRendering, pageNumPending,
            scale, canvas, ctx, screenElement, pageNumElement,
        );

        document.getElementById('{{'prev-' . $index}}').addEventListener('click', pdfModule.onPrevPage);
        document.getElementById('{{'next-' . $index}}').addEventListener('click', pdfModule.onNextPage);
        document.getElementById('{{'zoom-in-' . $index}}').addEventListener('click', pdfModule.onZoomIn);
        document.getElementById('{{'zoom-out-' . $index}}').addEventListener('click', pdfModule.onZoomOut);
        document.getElementById('{{'page_num_form-' . $index}}').addEventListener('submit', pdfModule.onPageNumberInput);
        document.getElementById('{{'page_num_form-' . $index}}').addEventListener('change', pdfModule.onPageNumberInput);

        /**
         * Asynchronously downloads PDF.
         */
        pdfjsLib.getDocument(url).promise.then(function (pdfDoc_) {
            pdfModule.pdfDoc = pdfDoc_;
            document.getElementById('{{'page_count-' . $index}}').textContent = pdfModule.pdfDoc.numPages;
            pageNumElement.max = pdfModule.pdfDoc.numPages;

            // Initial/first page rendering
            pdfModule.renderPage(pageNum);
            pdfModule.setCanvasContainerSize();
        });
    }
</script>
