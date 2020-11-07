export default function admin({to, next, store}) {

  if (store.getters['users/check']) {
    if (store.getters['users/user'].admin)
      return next()

    const arrayName = to.name.split('.');
    const right = arrayName[arrayName.length - 1];
    arrayName.pop()

    if (typeof store.getters['users/user'].permissions[arrayName.join('.')] !== "undefined" &&
      store.getters['users/user'].permissions[arrayName.join('.')].includes(right)) {
      return next()
    } else {
      return next({
        name: '/'
      })
    }
  } else {
    return next({
      name: 'login'
    })
  }
}