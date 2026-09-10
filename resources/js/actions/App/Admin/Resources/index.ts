import Organizations from './Organizations'
import ConsultationRequests from './ConsultationRequests'
import AdminUsers from './AdminUsers'
import AdminRoles from './AdminRoles'
import Users from './Users'
import Contents from './Contents'
import Categories from './Categories'
import Tags from './Tags'
import Media from './Media'
import SystemSettings from './SystemSettings'
import OperationLogs from './OperationLogs'

const Resources = {
    Organizations: Object.assign(Organizations, Organizations),
    ConsultationRequests: Object.assign(ConsultationRequests, ConsultationRequests),
    AdminUsers: Object.assign(AdminUsers, AdminUsers),
    AdminRoles: Object.assign(AdminRoles, AdminRoles),
    Users: Object.assign(Users, Users),
    Contents: Object.assign(Contents, Contents),
    Categories: Object.assign(Categories, Categories),
    Tags: Object.assign(Tags, Tags),
    Media: Object.assign(Media, Media),
    SystemSettings: Object.assign(SystemSettings, SystemSettings),
    OperationLogs: Object.assign(OperationLogs, OperationLogs),
}

export default Resources