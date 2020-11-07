export default function guest ({ next, store }){
  if(store.getters['users/token']){
    return next({
      name: 'custom.form'
    })
  }

  return next()
}