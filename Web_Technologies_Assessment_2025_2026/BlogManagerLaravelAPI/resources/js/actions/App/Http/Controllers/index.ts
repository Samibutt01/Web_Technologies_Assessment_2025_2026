import CategoryController from './CategoryController'
import PostController from './PostController'
import UserController from './UserController'
import Settings from './Settings'
const Controllers = {
    CategoryController: Object.assign(CategoryController, CategoryController),
PostController: Object.assign(PostController, PostController),
UserController: Object.assign(UserController, UserController),
Settings: Object.assign(Settings, Settings),
}

export default Controllers