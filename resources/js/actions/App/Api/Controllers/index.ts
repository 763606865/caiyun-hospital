import AuthController from './AuthController'
import SystemSettingController from './SystemSettingController'
import CmsController from './CmsController'
import FileController from './FileController'
import UserController from './UserController'
import PatientController from './PatientController'

const Controllers = {
    AuthController: Object.assign(AuthController, AuthController),
    SystemSettingController: Object.assign(SystemSettingController, SystemSettingController),
    CmsController: Object.assign(CmsController, CmsController),
    FileController: Object.assign(FileController, FileController),
    UserController: Object.assign(UserController, UserController),
    PatientController: Object.assign(PatientController, PatientController),
}

export default Controllers