import {checkPermission} from "../utils";

export default ({store, redirect, route}) => {
    if (!store.getters['auth/check']) {
        return redirect('/login')
    } else {
        if (!store.getters['auth/user'].admin) {

            if (!checkPermission(route.name, store.getters['auth/user'].permissions))
                return redirect('/')
        }
    }
}