const Uppy = require('@uppy/core')
const Dashboard = require('@uppy/dashboard')
const DragDrop = require('@uppy/drag-drop')
const Tus = require('@uppy/tus')

let uppy = null;

(function ($) {
    $.fn.dropBox = function (options) {
        let $dropBox = this;
        let $dropBoxModal = $(options.modalSelector);
        let xhrRequests = [];

        uppy = new Uppy({autoProceed: false})
            .use(DragDrop, {
                target: "#" + $dropBox.attr("id"),
                note: 'test'
            })
            .use(Dashboard, {
                trigger: "#" + $dropBox.attr("id"),
                closeAfterFinish: true
            })
            .use(Tus, {
                endpoint: CCM_DISPATCHER_FILENAME + '/ccm/drop_box/upload',
                resume: true,
                chunkSize: 1000000, /* 1mb */
                autoRetry: true,
                retryDelays: [0, 1000, 3000, 5000]
            })

        uppy.on('file-added', (file) => {
            $dropBoxModal.find(".drop-box-file-list").html("");
        })

        uppy.on('upload-success', (file, response) => {
            if (options.displayUrlToUploadedFile) {
                xhrRequests.push($.getJSON({
                    url: CCM_DISPATCHER_FILENAME + '/ccm/drop_box/resolve_download_url/' + response.uploadURL.split("/").pop()
                }, (json) => {
                    $dropBoxModal.find(".drop-box-file-list").append(
                        $("<li></li>")
                            .html(
                                $("<a></a>")
                                    .attr("href", json.downloadUrl)
                                    .attr("target", "_blank")
                                    .html(json.fileName)
                            )
                    );
                }));
            }
        })

        uppy.on('complete', () => {
            Promise.all(xhrRequests).then(() => {
                $dropBoxModal.modal('show');
            });
        })

        return this;
    };
}(jQuery));