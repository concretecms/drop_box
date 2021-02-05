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
                target: "#" + $dropBox.attr("id")
            })
            .use(Dashboard, {
                trigger: "#" + $dropBox.attr("id"),
                closeAfterFinish: true,
                proudlyDisplayPoweredByUppy: false
            })
            .use(Tus, {
                endpoint: CCM_DISPATCHER_FILENAME + '/ccm/drop_box/upload',
                resume: true,
                chunkSize: 1000000, /* 1mb */
                autoRetry: true,
                limit: 10,
                retryDelays: [1000, 3000, 5000, 8000]
            })

        uppy.on('file-added', (file) => {
            $dropBoxModal.find(".drop-box-file-list").html("");
        })

        uppy.on('upload-success', (file, response) => {
            if (options.displayUrlToUploadedFile) {
                xhrRequests.push($.getJSON({
                    url: CCM_DISPATCHER_FILENAME + '/ccm/drop_box/resolve_download_url/' + response.uploadURL.split("/").pop()
                }, (json) => {
                    var header = $("<h5 />")
                    var input = $("<input />")
                    var resultList = $("<li></li>")
                    resultList.attr('class', 'mb-4')
                    header.text(json.fileName)
                    input
                        .attr('class', 'form-control')
                        .attr('value', json.downloadUrl)
                    resultList.append(header).append(input)
                    $dropBoxModal.find(".drop-box-file-list").append(resultList)
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