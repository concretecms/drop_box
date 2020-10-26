const Uppy = require('@uppy/core')
const Dashboard = require('@uppy/dashboard')
const DragDrop = require('@uppy/drag-drop')
const Tus = require('@uppy/tus')

let uppy = null;

(function ($) {
    $.fn.dropBox = function (options) {
        let $dropBox = this;

        uppy = new Uppy({autoProceed: false})
            .use(DragDrop, {
                target: "#" + $dropBox.attr("id"),
                note: 'test'
            })
            .use(Dashboard, {
                trigger: "#" + $dropBox.attr("id"),
                showLinkToFileUploadResult: options.displayUrlToUploadedFile
            })
            .use(Tus, {
                endpoint: CCM_DISPATCHER_FILENAME + '/ccm/drop_box/upload',
                resume: true,
                chunkSize: 1000000, /* 1mb */
                autoRetry: true,
                retryDelays: [0, 1000, 3000, 5000]
            })

        uppy.on('upload-success', (file, response) => {
            $.getJSON({
                url: CCM_DISPATCHER_FILENAME + '/ccm/drop_box/resolve_download_url/' + response.uploadURL.split("/").pop()
            }, (json) => {
                uppy.getState().files[file.id].uploadURL = json.downloadUrl;
            });
        })

        uppy.on('complete', () => {
            let $alert = $("<div></div>")
                .addClass("alert alert-success alert-dismissible fade show")
                .attr("role", "alert")
                .html(options.uploadCompleteResponse);

            let $button = $("<button></button>")
                .addClass("close")
                .attr("type", "button")
                .attr("data-dismiss", "alert")
                .attr("data-dismiss", "close");

            let $span = $("<span></span>")
                .attr("aria-hidden", "true")
                .html("&times;");

            $button.append($span);
            $alert.append($button);
            $dropBox.prepend($alert);
        })

        return this;
    };
}(jQuery));