import AuthController from './AuthController'
import DeviceController from './DeviceController'
import ClientVersionController from './ClientVersionController'
import SystemSettingController from './SystemSettingController'
import CmsController from './CmsController'
import FileController from './FileController'
import UserController from './UserController'

const Controllers = {
    AuthController: Object.assign(AuthController, AuthController),
    DeviceController: Object.assign(DeviceController, DeviceController),
    ClientVersionController: Object.assign(ClientVersionController, ClientVersionController),
    SystemSettingController: Object.assign(SystemSettingController, SystemSettingController),
    CmsController: Object.assign(CmsController, CmsController),
    FileController: Object.assign(FileController, FileController),
    UserController: Object.assign(UserController, UserController),
}

export default Controllers