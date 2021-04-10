import { action, configure, makeAutoObservable, observable } from 'mobx'
configure({
  enforceActions: 'observed',
})
class Main {
  @observable public activeIndex: number = 0
  public constructor() {
    makeAutoObservable(this)
  }
  @action public setActiveIndex = (activeIndex: number) => {
    this.activeIndex = activeIndex
  }
}
export const NavStore = new Main()
