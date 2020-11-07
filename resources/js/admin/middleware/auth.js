export default function auth ({ next, store }){
  if(!store.getters['users/token']){
    console.log('logijn')
    return next({
      name: 'login'
    })
  }
  return next()
}