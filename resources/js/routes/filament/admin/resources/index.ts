import organizations from './organizations'
import consultationRequests from './consultation-requests'
import adminUsers from './admin-users'
import adminRoles from './admin-roles'
import users from './users'
import contents from './contents'
import categories from './categories'
import tags from './tags'
import media from './media'
import systemSettings from './system-settings'
import operationLogs from './operation-logs'

const resources = {
    organizations: Object.assign(organizations, organizations),
    consultationRequests: Object.assign(consultationRequests, consultationRequests),
    adminUsers: Object.assign(adminUsers, adminUsers),
    adminRoles: Object.assign(adminRoles, adminRoles),
    users: Object.assign(users, users),
    contents: Object.assign(contents, contents),
    categories: Object.assign(categories, categories),
    tags: Object.assign(tags, tags),
    media: Object.assign(media, media),
    systemSettings: Object.assign(systemSettings, systemSettings),
    operationLogs: Object.assign(operationLogs, operationLogs),
}

export default resources