import { configure, makeAutoObservable, runInAction } from 'mobx'
configure({
  enforceActions: 'observed',
})
class Main {
  public isOpen = false
  public msg: any = ''
  public constructor() {
    makeAutoObservable(this)
  }
  public setMsg = (msg: any) => {
    this.msg = msg
  }
  public close = (dalaySeconds = 0) => {
    setTimeout(() => {
      runInAction(() => {
        this.isOpen = false
      })
    }, dalaySeconds * 1000)
  }
  public open = (msg?: any) => {
    this.msg = msg
    this.isOpen = true
  }
}
export const ToastStore = new Main()
