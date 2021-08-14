import {
  action,
  configure,
  makeObservable,
  observable,
  runInAction,
} from 'mobx'

configure({
  enforceActions: 'observed',
})
class Main {
  @observable public isOpen = false

  @observable public msg: any = ''

  public constructor() {
    makeObservable(this)
  }

  @action public setMsg = (msg: any) => {
    this.msg = msg
  }

  @action public close = (dalaySeconds = 0) => {
    setTimeout(() => {
      runInAction(() => {
        this.isOpen = false
      })
    }, dalaySeconds * 1000)
  }

  @action public open = (msg?: any) => {
    this.msg = msg
    this.isOpen = true
  }
}
export const ToastStore = new Main()
