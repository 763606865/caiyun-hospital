import adminRoles from './admin-roles'
import adminUsers from './admin-users'
import branches from './branches'
import categories from './categories'
import chargeOrders from './charge-orders'
import contents from './contents'
import dailySettlements from './daily-settlements'
import drugs from './drugs'
import hsCampuses from './hs-campuses'
import hsDepartmentCategories from './hs-department-categories'
import hsDepartments from './hs-departments'
import hsDoctors from './hs-doctors'
import hsPatients from './hs-patients'
import inventoryBatches from './inventory-batches'
import inventoryMovements from './inventory-movements'
import inventoryStocks from './inventory-stocks'
import media from './media'
import medicalRecords from './medical-records'
import operationLogs from './operation-logs'
import organizationMembers from './organization-members'
import paymentTransactions from './payment-transactions'
import prescriptions from './prescriptions'
import systemSettings from './system-settings'
import tags from './tags'
import users from './users'
import visits from './visits'

const resources = {
    adminRoles: Object.assign(adminRoles, adminRoles),
    adminUsers: Object.assign(adminUsers, adminUsers),
    branches: Object.assign(branches, branches),
    categories: Object.assign(categories, categories),
    chargeOrders: Object.assign(chargeOrders, chargeOrders),
    contents: Object.assign(contents, contents),
    dailySettlements: Object.assign(dailySettlements, dailySettlements),
    drugs: Object.assign(drugs, drugs),
    hsCampuses: Object.assign(hsCampuses, hsCampuses),
    hsDepartmentCategories: Object.assign(hsDepartmentCategories, hsDepartmentCategories),
    hsDepartments: Object.assign(hsDepartments, hsDepartments),
    hsDoctors: Object.assign(hsDoctors, hsDoctors),
    hsPatients: Object.assign(hsPatients, hsPatients),
    inventoryBatches: Object.assign(inventoryBatches, inventoryBatches),
    inventoryMovements: Object.assign(inventoryMovements, inventoryMovements),
    inventoryStocks: Object.assign(inventoryStocks, inventoryStocks),
    media: Object.assign(media, media),
    medicalRecords: Object.assign(medicalRecords, medicalRecords),
    operationLogs: Object.assign(operationLogs, operationLogs),
    organizationMembers: Object.assign(organizationMembers, organizationMembers),
    paymentTransactions: Object.assign(paymentTransactions, paymentTransactions),
    prescriptions: Object.assign(prescriptions, prescriptions),
    systemSettings: Object.assign(systemSettings, systemSettings),
    tags: Object.assign(tags, tags),
    users: Object.assign(users, users),
    visits: Object.assign(visits, visits),
}

export default resources