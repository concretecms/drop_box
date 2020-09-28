const Uppy = require('@uppy/core')
const Dashboard = require('@uppy/dashboard')
const DragDrop = require('@uppy/drag-drop')
const Tus = require('@uppy/tus')

const uppy = new Uppy({ autoProceed: false })
  .use(DragDrop, { target: '.drop-box' })
  .use(Dashboard, { trigger: '.drop-box' })
  .use(Tus, { endpoint: CCM_DISPATCHER_FILENAME + '/ccm/tus_server/files' })