import { observable, action, runInAction, configure } from 'mobx'
import { ReactNode } from 'react'

configure({
  enforceActions: 'observed',
})

class ToastStore {
  @observable public isOpen: boolean = false
  @observable public msg: ReactNode = ''

  @action
  public setMsg = (msg: ReactNode) => {
    this.msg = msg
  }

  @action
  public close = (dalaySeconds: number = 0) => {
    setTimeout(() => {
      runInAction(() => {
        this.isOpen = false
      })
    }, dalaySeconds * 1000)
  }

  @action
  public open = (msg?: ReactNode) => {
    this.msg = msg
    this.isOpen = true
  }
}

export default new ToastStore()
