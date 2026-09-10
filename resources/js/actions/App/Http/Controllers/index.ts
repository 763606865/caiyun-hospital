import ConsultationRequestController from './ConsultationRequestController'
import CmsPreviewController from './CmsPreviewController'
import SitemapController from './SitemapController'

const Controllers = {
    ConsultationRequestController: Object.assign(ConsultationRequestController, ConsultationRequestController),
    CmsPreviewController: Object.assign(CmsPreviewController, CmsPreviewController),
    SitemapController: Object.assign(SitemapController, SitemapController),
}

export default Controllers